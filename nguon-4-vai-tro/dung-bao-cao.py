# Dựng lại phần thân báo cáo (Chương 1–4, Phụ lục) theo phương án 4 vai trò (không có kho thuốc), giữ nguyên phần đầu
# (bìa, nhận xét, lời cảm ơn, nhật ký) của file final.
#   python3 dung-bao-cao.py
# Đọc bản gốc ở ../sao-luu/…-truoc-5-vai-tro.docx, ghi ra ../03_BaoCaoDAHP2_NichegHopitalfinal.docx.
import copy
import glob
import os
import re
import subprocess
import sys
import tempfile
import unicodedata

import fitz
from docx import Document
from docx.oxml.ns import qn

from bao_cao_xml import RONG_DOC, ThanBaoCao, X, chay

GOC = '../sao-luu/03_BaoCaoDAHP2_NichegHopitalfinal-truoc-5-vai-tro.docx'
RA = os.environ.get('RA_BAO_CAO', '../03_BaoCaoDAHP2_NichegHopitalfinal.docx')  # đặt RA_BAO_CAO để chạy thử ra file khác
HINH = '../ban-nang-cao-4-vai-tro/'
ANH = 'anh-giao-dien/'
SOFFICE = '/Applications/LibreOffice.app/Contents/MacOS/soffice'

khoa = os.path.join(os.path.dirname(RA), '~$' + os.path.basename(RA)[2:])
if os.path.exists(khoa):
    sys.exit('File báo cáo đang mở trong Word, hãy đóng lại rồi chạy lại.')

doc = Document(GOC)
body = doc.element.body
els = list(body.iterchildren())


def chu(e):
    return ''.join(t.text or '' for t in e.iter(qn('w:t')))


def kieu(e):
    ps = e.find(qn('w:pPr'))
    if ps is not None and ps.find(qn('w:pStyle')) is not None:
        return ps.find(qn('w:pStyle')).get(qn('w:val'))
    return ''


# ---------- mẫu sectPr ----------
sect = [e.find(qn('w:pPr')).find(qn('w:sectPr')) for e in els
        if e.tag == qn('w:p') and e.find(qn('w:pPr')) is not None and e.find(qn('w:pPr')).find(qn('w:sectPr')) is not None]
mau_doc_dau, mau_ngang, mau_doc = sect[2], sect[3], sect[4]
assert mau_ngang.find(qn('w:pgSz')).get(qn('w:orient')) == 'landscape'
assert mau_doc_dau.find(qn('w:pgNumType')) is not None and mau_doc.find(qn('w:pgSz')).get(qn('w:orient')) is None
mau_doc_dau, mau_ngang, mau_doc = (copy.deepcopy(x) for x in (mau_doc_dau, mau_ngang, mau_doc))

# ---------- xóa phần thân cũ ----------
dau = next(i for i, e in enumerate(els) if e.tag == qn('w:p') and kieu(e) == 'u1' and chu(e).strip() == 'Mô tả hệ thống và BFD')
for e in els[dau:]:
    if e.tag != qn('w:sectPr'):
        body.remove(e)

T = ThanBaoCao(doc, mau_doc_dau, mau_doc, mau_ngang)
p, gach, u1, u2, u3 = T.p, T.gach, T.u1, T.u2, T.u3

# Nội dung Chương 1–4 và Phụ lục nằm ở noi-dung-bao-cao.py
exec(open('noi-dung-bao-cao.py', encoding='utf-8').read())

# ---------- gắn phần thân vào văn bản ----------
cuoi = body[-1]
assert cuoi.tag == qn('w:sectPr')
for e in T.ds:
    cuoi.addprevious(e)

# =====================================================================================
# BẢNG KÝ HIỆU, MỤC LỤC, DANH MỤC BẢNG / HÌNH
# =====================================================================================
els = list(body.iterchildren())
tbl_ky_hieu = next(e for e in els if e.tag == qn('w:tbl') and chu(e.find(qn('w:tr'))).startswith('Chữ viết tắt'))
hang = tbl_ky_hieu.findall(qn('w:tr'))


def dat_o(tr, k, gia_tri):
    tc = tr.findall(qn('w:tc'))[k]
    ts = list(tc.iter(qn('w:t')))
    ts[0].text = gia_tri
    for t in ts[1:]:
        t.text = ''


for tr in hang:
    a = chu(tr.findall(qn('w:tc'))[0])
    if a == 'D1 … D6':
        dat_o(tr, 0, 'D1 … D7')
    if a == 'QL':
        dat_o(tr, 0, 'QT')
        dat_o(tr, 1, 'Quản trị (vai trò người dùng)')
    if a == 'BS':
        dat_o(tr, 1, 'Bác sĩ (vai trò người dùng)')
    if a == 'BN':
        moc = tr
if not any(chu(t.findall(qn('w:tc'))[0]) == 'NV' for t in tbl_ky_hieu.findall(qn('w:tr'))):
    moi = copy.deepcopy(moc)
    dat_o(moi, 0, 'NV')
    dat_o(moi, 1, 'Nhân viên (vai trò người dùng)')
    moc.addnext(moi)


def tim_tieu_de(ten):
    return next(i for i, e in enumerate(els) if e.tag == qn('w:p') and kieu(e) == 'Tiu' and chu(e).strip().lower().startswith(ten.lower()))


i_ml, i_db, i_dh = tim_tieu_de('Mục lục'), tim_tieu_de('Danh mục các bảng'), tim_tieu_de('Danh mục các hình')
i_dk = tim_tieu_de('Danh mục các ký hiệu')

# Mục lục: giữ các dòng phần đầu (trước "Chương 1"), thay các dòng của phần thân
ml = [e for e in els[i_ml + 1:i_dk] if e.tag == qn('w:p') and kieu(e).startswith('Mucluc')]
dau_than = next(k for k, e in enumerate(ml) if chu(e).startswith('Chương 1'))
mau_ml = {1: ml[dau_than], 2: next(e for e in ml if kieu(e) == 'Mucluc2'), 3: next(e for e in ml if kieu(e) == 'Mucluc3')}
mau_ml = {k: copy.deepcopy(v) for k, v in mau_ml.items()}
chot = ml[dau_than - 1]
for e in ml[dau_than:]:
    body.remove(e)


def dong_muc(mau, hien, bm, trang='00'):
    e = copy.deepcopy(mau)
    for f in list(e.iter(qn('w:fldChar'))):          # bỏ phần mở trường TOC nếu mẫu là dòng đầu
        if f.get(qn('w:fldCharType')) in ('begin', 'separate', 'end') and f.getparent().getparent() is e:
            e.remove(f.getparent())
    for it in list(e.iter(qn('w:instrText'))):
        if it.getparent().getparent() is e:
            e.remove(it.getparent())
    hl = e.find(qn('w:hyperlink'))
    hl.set(qn('w:anchor'), bm)
    ts = list(hl.iter(qn('w:t')))
    ts[0].text = hien
    ts[-1].text = trang
    for it in hl.iter(qn('w:instrText')):
        it.text = f' PAGEREF {bm} \\h '
    return e


ket_truong = X('<w:r><w:fldChar w:fldCharType="end"/></w:r>')
moi = [dong_muc(mau_ml[c], hien, bm) for c, hien, bm, _ in T.muc]
moi[-1].append(copy.deepcopy(ket_truong))
for e in reversed(moi):
    chot.addnext(e)


def thay_danh_muc(i_tieu_de, ds, loai):
    els2 = list(body.iterchildren())
    i0 = next(k for k, e in enumerate(els2) if e is els[i_tieu_de])
    cu = []
    for e in els2[i0 + 1:]:
        if e.tag == qn('w:p') and kieu(e) == 'Banghinhminhhoa':
            cu.append(e)
        elif cu:
            break
    mo = copy.deepcopy(cu[0])
    mau = copy.deepcopy(cu[1])
    for e in cu:
        body.remove(e)
    dong = [dong_muc(mau, hien, bm) for hien, bm in ds]
    # dòng đầu mang phần mở trường TOC \c, dòng cuối mang phần đóng
    mo_truong = [r for r in mo if r.tag == qn('w:r')][:3]
    for k, r in enumerate(mo_truong):
        dong[0].insert(1 + k, copy.deepcopy(r))
    dong[-1].append(copy.deepcopy(ket_truong))
    neo = els[i_tieu_de]
    for e in reversed(dong):
        neo.addnext(e)
    return dong


dong_bang = thay_danh_muc(i_db, T.bang, 'Bảng')
dong_hinh = thay_danh_muc(i_dh, T.hinh, 'Hình')
dong_ml_dau = [e for e in list(body.iterchildren()) if e.tag == qn('w:p') and kieu(e).startswith('Mucluc')]

doc.save(RA)
print('Đã ghi lần 1:', RA, '·', len(T.muc), 'mục,', len(T.bang), 'bảng,', len(T.hinh), 'hình')

# =====================================================================================
# ĐIỀN SỐ TRANG (xuất PDF bằng LibreOffice, đọc số trang ở đầu trang)
# =====================================================================================


def chuan(s):
    s = unicodedata.normalize('NFC', s).lower()
    return re.sub(r'\s+', '', s)


tam = tempfile.mkdtemp()
subprocess.run([SOFFICE, '--headless', '--convert-to', 'pdf', '--outdir', tam, RA], check=True, capture_output=True)
pdf = fitz.open(glob.glob(os.path.join(tam, '*.pdf'))[0])
trang_chu, so_in = [], []
for pg in pdf:
    trang_chu.append(chuan(pg.get_text()))
    so = None
    for x0, y0, x1, y1, t, *_ in pg.get_text('blocks'):
        if y1 < 80 and t.strip().isdigit():
            so = int(t.strip())
    so_in.append(so)
print('PDF', len(pdf), 'trang; số in ở đầu trang (10 trang đầu):', so_in[:10])


def so_trang(k):
    if so_in[k] is not None:
        return so_in[k]
    # trang không đọc được số: suy từ trang gần nhất có số
    for d in range(1, 5):
        if k - d >= 0 and so_in[k - d] is not None:
            return so_in[k - d] + d
    return k + 1


# trang bắt đầu phần thân: sau trang cuối của danh mục hình
cuoi_dm = chuan(T.hinh[-1][0])
k_than = next(k for k, t in enumerate(trang_chu) if cuoi_dm in t) + 1
ket_qua = {}
# Tiêu đề: lấy trang từ bookmark của PDF (LibreOffice tách dấu của chữ hoa nên không dò được theo chữ)
trang_muc = {}
for _, ten, trang in pdf.get_toc():
    trang_muc.setdefault(chuan(ten), trang - 1)
for c, hien, bm, tim in T.muc:
    ket_qua[bm] = so_trang(trang_muc[chuan(hien)])
# Chú thích bảng, hình: LibreOffice in trường số chương (STYLEREF) thành tên chương và đánh số SEQ liên tục
# cả tài liệu (Word thì đánh lại theo chương), nên dò theo ".<thứ tự trong cả tài liệu>. tên"
for ds in (T.bang, T.hinh):
    k = k_than
    for thu_tu, (hien, bm) in enumerate(ds, 1):
        m = re.match(r'^(?:Bảng|Hình) \d+\.\d+\. (.*)$', hien)
        s = chuan(f'.{thu_tu}. {m.group(1)}')
        while s not in trang_chu[k]:
            k += 1
        ket_qua[bm] = so_trang(k)
# các mục ở phần đầu (Lời cảm ơn, Nhật ký, Mục lục, các danh mục)
for e in dong_ml_dau:
    hl = e.find(qn('w:hyperlink'))
    if hl is None or hl.get(qn('w:anchor')) in ket_qua:
        continue
    ten = chuan(list(hl.iter(qn('w:t')))[0].text).rstrip('.')
    khop = [t for t in trang_muc if t.rstrip('.') == ten]
    if khop:
        ket_qua[hl.get(qn('w:anchor'))] = so_trang(trang_muc[khop[0]])

doc = Document(RA)
dien = 0
for e in doc.element.body.iter(qn('w:hyperlink')):
    bm = e.get(qn('w:anchor'))
    if bm in ket_qua:
        ts = list(e.iter(qn('w:t')))
        ts[-1].text = str(ket_qua[bm])
        dien += 1
doc.save(RA)
print('Đã điền số trang cho', dien, 'dòng; phần thân bắt đầu ở trang', so_trang(k_than), '; tổng', len(pdf), 'trang')
