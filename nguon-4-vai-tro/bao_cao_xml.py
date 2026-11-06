# Các hàm dựng XML Word cho phần thân báo cáo, theo đúng định dạng file mẫu đang dùng
# (style u1/u2/u3, Chuthich, bảng LiBang, chú thích có trường STYLEREF/SEQ, mục lục có PAGEREF).
import copy
import re
from xml.sax.saxutils import escape

from docx.oxml.ns import qn
from docx.shared import Cm
from lxml import etree
from PIL import Image

NS = ('xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" '
      'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"')
RONG_DOC = 9072      # twip, bề rộng vùng chữ trang dọc (16 cm)
RONG_NGANG = 14572   # twip, trang ngang (25,7 cm)


def X(s):
    return etree.fromstring(f'<w:goc {NS}>{s}</w:goc>')[0]


def chay(chu, rpr=''):
    """Chuỗi có đánh dấu **đậm**, *nghiêng*, __gạch chân__ thành các w:r."""
    ra = []
    for phan in re.split(r'(\*\*.+?\*\*|__.+?__|\*[^*]+?\*)', chu):
        if not phan:
            continue
        them = rpr
        if phan.startswith('**'):
            phan, them = phan[2:-2], rpr + '<w:b/><w:bCs/>'
        elif phan.startswith('__'):
            phan, them = phan[2:-2], rpr + '<w:u w:val="single"/>'
        elif phan.startswith('*'):
            phan, them = phan[1:-1], rpr + '<w:i/><w:iCs/>'
        r = f'<w:rPr>{them}</w:rPr>' if them else ''
        ra.append(f'<w:r>{r}<w:t xml:space="preserve">{escape(phan)}</w:t></w:r>')
    return ''.join(ra)


class ThanBaoCao:
    """Gom các phần tử phần thân theo thứ tự, ghi nhận tiêu đề và chú thích để làm mục lục."""

    def __init__(self, doc, mau_doc_dau, mau_doc, mau_ngang):
        self.doc = doc
        self.ds = []
        self.muc = []          # (cấp, chữ hiển thị, bookmark, chuỗi tìm trong PDF)
        self.bang, self.hinh = [], []   # (chữ hiển thị, bookmark)
        self.chuong = 0
        self.so = [0, 0, 0]
        self.so_bang = self.so_hinh = 0
        self.bm = 3000
        self.mau = {'doc_dau': mau_doc_dau, 'doc': mau_doc, 'ngang': mau_ngang}
        self.da_ngat_dau = False

    def _bm(self):
        self.bm += 1
        return self.bm, f'_Toc93{self.bm:05d}'

    def them(self, e):
        self.ds.append(e)
        return e

    # ---------- tiêu đề ----------
    def u1(self, ten):
        self.chuong += 1
        self.so = [self.chuong, 0, 0]
        self.so_bang = self.so_hinh = 0
        i, bm = self._bm()
        self.them(X(f'<w:p><w:pPr><w:pStyle w:val="u1"/></w:pPr><w:r><w:br/></w:r><w:bookmarkStart w:id="{i}" w:name="{bm}"/>'
                    f'<w:r><w:t>{escape(ten)}</w:t></w:r><w:bookmarkEnd w:id="{i}"/></w:p>'))
        self.muc.append((1, f'Chương {self.chuong} {ten}', bm, f'chương{self.chuong}' + ten))

    def u2(self, ten):
        self.so[1] += 1
        self.so[2] = 0
        i, bm = self._bm()
        so = f'{self.so[0]}.{self.so[1]}.'
        self.them(X(f'<w:p><w:pPr><w:pStyle w:val="u2"/><w:keepNext/></w:pPr><w:bookmarkStart w:id="{i}" w:name="{bm}"/>'
                    f'<w:r><w:t>{escape(ten)}</w:t></w:r><w:bookmarkEnd w:id="{i}"/></w:p>'))
        self.muc.append((2, f'{so} {ten}', bm, so + ten))

    def u3(self, ten):
        self.so[2] += 1
        i, bm = self._bm()
        so = f'{self.so[0]}.{self.so[1]}.{self.so[2]}.'
        self.them(X(f'<w:p><w:pPr><w:pStyle w:val="u3"/></w:pPr><w:bookmarkStart w:id="{i}" w:name="{bm}"/>'
                    f'<w:r><w:t>{escape(ten)}</w:t></w:r><w:bookmarkEnd w:id="{i}"/></w:p>'))
        self.muc.append((3, f'{so} {ten}', bm, so + ten))

    def tieu_de_rieng(self, ten):
        """Tiêu đề không đánh số (style Tiu), ví dụ Phụ lục."""
        i, bm = self._bm()
        self.them(X(f'<w:p><w:pPr><w:pStyle w:val="Tiu"/></w:pPr><w:bookmarkStart w:id="{i}" w:name="{bm}"/>'
                    f'<w:r><w:t>{escape(ten)}</w:t></w:r><w:bookmarkEnd w:id="{i}"/></w:p>'))
        self.muc.append((1, ten, bm, ten))

    # ---------- đoạn văn ----------
    def p(self, chu):
        return self.them(X(f'<w:p>{chay(chu)}</w:p>'))

    def gach(self, chu, num=20):
        return self.them(X(f'<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="{num}"/></w:numPr>'
                           f'<w:spacing w:after="60"/></w:pPr>{chay(chu)}</w:p>'))

    def tieu_de_phu_luc(self, chu):
        return self.them(X(f'<w:p><w:pPr><w:keepNext/><w:spacing w:before="240"/><w:ind w:firstLine="0"/></w:pPr>'
                           f'{chay(chu, "<w:b/><w:bCs/><w:sz w:val=\"28\"/>")}</w:p>'))

    def nhom(self, chu):
        return self.them(X(f'<w:p><w:pPr><w:keepNext/><w:spacing w:before="120"/><w:ind w:firstLine="0"/></w:pPr>'
                           f'{chay(chu, "<w:b/><w:bCs/><w:i/>")}</w:p>'))

    def giua(self, chu, dam=False):
        return self.them(X(f'<w:p><w:pPr><w:ind w:firstLine="0"/><w:jc w:val="center"/></w:pPr>'
                           f'{chay(chu, "<w:b/><w:bCs/>" if dam else "")}</w:p>'))

    def trong(self):
        return self.them(X('<w:p/>'))

    # ---------- chú thích ----------
    def _chu_thich(self, loai, ten, giu_cung):
        if loai == 'Bảng':
            self.so_bang += 1
            n = self.so_bang
        else:
            self.so_hinh += 1
            n = self.so_hinh
        i, bm = self._bm()
        kn = '<w:keepNext/>' if giu_cung else ''
        self.them(X(
            f'<w:p><w:pPr><w:pStyle w:val="Chuthich"/>{kn}</w:pPr><w:bookmarkStart w:id="{i}" w:name="{bm}"/>'
            f'<w:r><w:t xml:space="preserve">{loai} </w:t></w:r>'
            f'<w:fldSimple w:instr=" STYLEREF 1 \\s "><w:r><w:rPr><w:noProof/></w:rPr><w:t>{self.chuong}</w:t></w:r></w:fldSimple>'
            f'<w:r><w:t>.</w:t></w:r>'
            f'<w:fldSimple w:instr=" SEQ {loai} \\* ARABIC \\s 1 "><w:r><w:rPr><w:noProof/></w:rPr><w:t>{n}</w:t></w:r></w:fldSimple>'
            f'<w:r><w:t xml:space="preserve">. {escape(ten)}</w:t></w:r><w:bookmarkEnd w:id="{i}"/></w:p>'))
        hien = f'{loai} {self.chuong}.{n}. {ten}'
        (self.bang if loai == 'Bảng' else self.hinh).append((hien, bm))
        return f'{loai} {self.chuong}.{n}'

    # ---------- bảng ----------
    def bang_du_lieu(self, ten, tieu_de, hang, rong, giua=(), co=24, gop_cot_dau=False):
        """rong: bề rộng từng cột (twip); giua: chỉ số cột căn giữa; gop_cot_dau: gộp ô trống liên tiếp ở cột đầu."""
        so = self._chu_thich('Bảng', ten, True) if ten else None   # ten=None: bảng phụ lục, không chú thích
        luoi = ''.join(f'<w:gridCol w:w="{w}"/>' for w in rong)
        x = [f'<w:tbl><w:tblPr><w:tblStyle w:val="LiBang"/><w:tblW w:w="{sum(rong)}" w:type="dxa"/><w:jc w:val="center"/>'
             '<w:tblLayout w:type="fixed"/><w:tblCellMar><w:left w:w="90" w:type="dxa"/><w:right w:w="90" w:type="dxa"/></w:tblCellMar>'
             '<w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/></w:tblPr>'
             f'<w:tblGrid>{luoi}</w:tblGrid>']

        def o(chu, w, dau, can, vm=''):
            shd = '<w:shd w:val="clear" w:color="auto" w:fill="E7E6E6"/>' if dau else ''
            rpr = f'{"<w:b/><w:bCs/>" if dau else ""}<w:sz w:val="{co}"/>'
            noi = chay(chu, rpr) if chu else ''
            return (f'<w:tc><w:tcPr><w:tcW w:w="{w}" w:type="dxa"/>{vm}{shd}<w:vAlign w:val="center"/></w:tcPr>'
                    f'<w:p><w:pPr><w:spacing w:before="40" w:after="40" w:line="264" w:lineRule="auto"/><w:ind w:firstLine="0"/>'
                    f'<w:jc w:val="{can}"/></w:pPr>{noi}</w:p></w:tc>')

        x.append('<w:tr><w:trPr><w:cantSplit/><w:tblHeader/><w:jc w:val="center"/></w:trPr>'
                 + ''.join(o(c, rong[k], True, 'center') for k, c in enumerate(tieu_de)) + '</w:tr>')
        for d, h in enumerate(hang):
            o_hang = []
            for k, c in enumerate(h):
                vm = ''
                if gop_cot_dau and k == 0:
                    tiep = d + 1 < len(hang) and hang[d + 1][0] == ''
                    if c == '':
                        vm = '<w:vMerge/>'
                    elif tiep:
                        vm = '<w:vMerge w:val="restart"/>'
                o_hang.append(o(c, rong[k], False, 'center' if k in giua else 'left', vm))
            x.append('<w:tr><w:trPr><w:cantSplit/><w:jc w:val="center"/></w:trPr>' + ''.join(o_hang) + '</w:tr>')
        x.append('</w:tbl>')
        self.them(X(''.join(x)))
        self.trong()
        return so

    # ---------- hình ----------
    def hinh_anh(self, duong_dan, ten, rong=None, cao=None, ngat_ngang=False):
        """Chèn ảnh + chú thích. rong/cao tính bằng cm (giữ tỉ lệ). ngat_ngang: hình nằm trong section ngang riêng."""
        w, h = Image.open(duong_dan).size
        if rong and cao:
            ti = min(rong / w, cao / h)
        elif rong:
            ti = rong / w
        else:
            ti = cao / h
        inline = self.doc.part.new_pic_inline(duong_dan, Cm(w * ti), Cm(h * ti))
        p = X('<w:p><w:pPr><w:keepNext/><w:spacing w:before="120" w:after="60" w:line="240" w:lineRule="auto"/>'
              '<w:ind w:firstLine="0"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:noProof/></w:rPr><w:drawing/></w:r></w:p>')
        p.find(qn('w:r')).find(qn('w:drawing')).append(inline)
        self.them(p)
        so = self._chu_thich('Hình', ten, False)
        return so

    # ---------- ngắt section ----------
    def ket_section(self, loai):
        """Đặt sectPr vào đoạn cuối cùng đã thêm: loai 'doc' kết thúc phần dọc, 'ngang' kết thúc phần ngang."""
        p = self.ds[-1]
        assert p.tag == qn('w:p')
        if loai == 'doc' and not self.da_ngat_dau:
            mau, self.da_ngat_dau = self.mau['doc_dau'], True
        else:
            mau = self.mau[loai]
        ppr = p.find(qn('w:pPr'))
        if ppr is None:
            ppr = etree.SubElement(p, qn('w:pPr'))
            p.insert(0, ppr)
        ppr.append(copy.deepcopy(mau))

    def hinh_ngang(self, gioi_thieu, duong_dan, ten, rong=None, cao=None):
        """Đoạn giới thiệu kết thúc phần dọc, hình + chú thích nằm trên trang ngang."""
        self.p(gioi_thieu)
        self.ket_section('doc')
        so = self.hinh_anh(duong_dan, ten, rong, cao)
        self.ket_section('ngang')
        return so
