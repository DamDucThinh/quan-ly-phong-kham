# ERD (lược đồ thực thể kết hợp) và lược đồ CSDL quan hệ, bản 4 vai trò (không có kho thuốc).
#   python3 ve-erd.py   (sinh .gv, .svg và PNG vào ../ban-nang-cao-4-vai-tro)
import subprocess
from xml.sax.saxutils import escape

RA = '../ban-nang-cao-4-vai-tro'


def xuat(ten, dong):
    open(ten + '.gv', 'w', encoding='utf-8').write('\n'.join(dong) + '\n')
    subprocess.run(['dot', '-Tpng', '-Gdpi=170', ten + '.gv', '-o', f'{RA}/{ten}.png'], check=True)
    subprocess.run(['dot', '-Tsvg', ten + '.gv', '-o', ten + '.svg'], check=True)


# ---------------- ERD ----------------
TT = {
    'BS': ('BÁC SĨ', ['Mã bác sĩ', 'Họ tên', 'Chuyên khoa', 'Phòng', 'Số điện thoại', 'Email', 'Trình độ']),
    'CA': ('CA LÀM VIỆC', ['Mã ca', 'Thứ', 'Giờ bắt đầu', 'Giờ kết thúc']),
    'BN': ('BỆNH NHÂN', ['Mã bệnh nhân', 'Họ tên', 'Số điện thoại', 'Năm sinh', 'Giới tính', 'Địa chỉ']),
    'TK': ('TÀI KHOẢN', ['Tên đăng nhập', 'Mật khẩu', 'Vai trò', 'Họ tên', 'Trạng thái hoạt động']),
    'TB': ('THÔNG BÁO', ['Mã thông báo', 'Loại', 'Tiêu đề', 'Nội dung', 'Thời điểm']),
    'LH': ('LỊCH HẸN', ['Mã lịch hẹn', 'Ngày', 'Giờ', 'Loại', 'Lý do khám', 'Trạng thái', 'Lý do hủy', 'Người đặt']),
    'BA': ('BỆNH ÁN', ['Mã bệnh án', 'Ngày khám', 'Mạch, nhiệt độ', 'Huyết áp, cân nặng', 'Kết quả khám', 'Chẩn đoán', 'Lời dặn']),
    'DT': ('ĐƠN THUỐC', ['Mã đơn thuốc', 'Ngày kê']),
    'TH': ('THUỐC', ['Mã thuốc', 'Tên thuốc', 'Hàm lượng', 'Đơn vị', 'Nhóm', 'Liều tối đa', 'Lưu ý']),
}
QH = [  # mã, tên, thực thể 1, bản số 1, thực thể 2, bản số 2
    ('R1', 'Làm việc theo', 'BS', '1', 'CA', 'n'),
    ('R2', 'Khám theo', 'BS', '1', 'LH', 'n'),
    ('R3', 'Gắn với', 'BS', '1', 'TK', '0..1'),
    ('R4', 'Đặt', 'BN', '1', 'LH', 'n'),
    ('R5', 'Sở hữu', 'BN', '1', 'TK', '0..1'),
    ('R6', 'Nhận', 'BN', '1', 'TB', 'n'),
    ('R7', 'Sinh ra', 'LH', '1', 'BA', '0..1'),
    ('R8', 'Có', 'BA', '1', 'DT', '0..1'),
    ('R9', 'Gồm', 'DT', 'n', 'TH', 'n'),
]


def bang_tt(ten, tt):
    hang = ''.join(f'<TR><TD ALIGN="LEFT">{"<U>" + escape(a) + "</U>" if i == 0 else escape(a)}</TD></TR>' for i, a in enumerate(tt))
    return (f'<<TABLE BORDER="2" CELLBORDER="0" CELLSPACING="0" CELLPADDING="5">'
            f'<TR><TD BORDER="1" SIDES="B"><B>{escape(ten)}</B></TD></TR>{hang}</TABLE>>')


d = ['graph G {',
     '  graph [fontname="Arial", bgcolor="white", pad=0.35, labelloc=t, fontsize=24, label=<<B>LƯỢC ĐỒ THỰC THỂ KẾT HỢP (ERD)</B><BR/> >, rankdir=LR, nodesep=0.35, ranksep=0.55, splines=line];',
     '  node [fontname="Arial", fontsize=15];',
     '  edge [fontname="Arial", fontsize=14, penwidth=1.3];']
for ma, (ten, tt) in TT.items():
    d.append(f'  {ma} [shape=plain, label={bang_tt(ten, tt)}];')
for ma, ten, a, ba, b, bb in QH:
    d.append(f'  {ma} [shape=diamond, style=filled, fillcolor="#eeeeee", fontsize=14, height=0.6, margin="0.06,0.02", label="{ten}"];')
    d.append(f'  {a} -- {ma} [headlabel="", taillabel="{ba}", labeldistance=1.8, labelangle=30];')
    goc = 40 if ma == 'R3' else -30  # R3: đưa nhãn "0..1" ra khỏi góc khung TÀI KHOẢN
    d.append(f'  {ma} -- {b} [headlabel="{bb}", labeldistance=1.8, labelangle={goc}];')
d += ['  SL [shape=ellipse, fontsize=14, label="Số lượng"];', '  CD [shape=ellipse, fontsize=14, label="Cách dùng"];',
      '  R9 -- SL [style=dashed];', '  R9 -- CD [style=dashed];',
      '  {rank=same; BS; BN;}', '  {rank=same; CA; LH; TK; TB;}', '}']
xuat('09-ERD-luoc-do-thuc-the-ket-hop', d)

# ---------------- Lược đồ quan hệ ----------------
BANG = {
    'bac_si': ('D1', ['id PK', 'ho_ten', 'chuyen_khoa', 'phong', 'so_dien_thoai', 'email', 'trinh_do']),
    'ca_lam_viec': ('D1', ['id PK', 'bac_si_id FK', 'thu', 'gio_bat_dau', 'gio_ket_thuc']),
    'benh_nhan': ('D2', ['id PK', 'ma (UNIQUE)', 'ho_ten', 'so_dien_thoai (UNIQUE)', 'nam_sinh', 'gioi_tinh', 'dia_chi', 'tao_luc']),
    'tai_khoan': ('D7', ['id PK', 'ten_dang_nhap (UNIQUE)', 'mat_khau', 'vai_tro', 'ho_ten', 'benh_nhan_id FK (UNIQUE)', 'bac_si_id FK (UNIQUE)', 'hoat_dong', 'tao_luc']),
    'thong_bao': ('', ['id PK', 'benh_nhan_id FK', 'loai', 'tieu_de', 'noi_dung', 'tao_luc']),
    'lich_hen': ('D4', ['id PK', 'benh_nhan_id FK', 'bac_si_id FK', 'ngay', 'gio', 'loai', 'ly_do', 'trang_thai', 'ly_do_huy', 'dat_boi', 'tao_luc', 'giu_cho (cột sinh)']),
    'benh_an': ('D5', ['id PK', 'lich_hen_id FK (UNIQUE)', 'benh_nhan_id FK', 'bac_si_id FK', 'ngay_kham', 'mach, nhiet_do', 'huyet_ap, can_nang', 'ket_qua', 'chan_doan', 'loi_dan', 'tao_luc']),
    'don_thuoc': ('D6', ['id PK', 'benh_an_id FK (UNIQUE)', 'tao_luc']),
    'chi_tiet_don_thuoc': ('D6', ['id PK', 'don_thuoc_id FK', 'thuoc_id FK', 'so_luong', 'cach_dung']),
    'thuoc': ('D3', ['id PK', 'ma (UNIQUE)', 'ten', 'ham_luong', 'don_vi', 'nhom', 'lieu_toi_da', 'luu_y']),
}
LIEN_KET = [  # bảng 1 (phía một), bảng n, nhãn
    ('bac_si', 'ca_lam_viec', '1 : n'), ('bac_si', 'lich_hen', '1 : n'), ('bac_si', 'benh_an', '1 : n'), ('bac_si', 'tai_khoan', '1 : 0..1'),
    ('benh_nhan', 'lich_hen', '1 : n'), ('benh_nhan', 'benh_an', '1 : n'), ('benh_nhan', 'tai_khoan', '1 : 0..1'), ('benh_nhan', 'thong_bao', '1 : n'),
    ('lich_hen', 'benh_an', '1 : 0..1'), ('benh_an', 'don_thuoc', '1 : 0..1'), ('don_thuoc', 'chi_tiet_don_thuoc', '1 : n'),
    ('thuoc', 'chi_tiet_don_thuoc', '1 : n'),
]


def bang_qh(ten, kho, cot):
    hang = ''
    for c in cot:
        o = escape(c)
        if c.endswith(' PK'):
            o = f'<B>{o}</B>'
        hang += f'<TR><TD ALIGN="LEFT">{o}</TD></TR>'
    tieu = f'<B>{ten}</B>' + (f'  <FONT POINT-SIZE="13"><B>({kho})</B></FONT>' if kho else '')
    return (f'<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0" CELLPADDING="5">'
            f'<TR><TD BGCOLOR="#e4e4e4">{tieu}</TD></TR>{hang}</TABLE>>')


d = ['graph G {',
     '  graph [fontname="Arial", bgcolor="white", pad=0.35, labelloc=t, fontsize=24, label=<<B>LƯỢC ĐỒ CƠ SỞ DỮ LIỆU QUAN HỆ</B><BR/> >, rankdir=LR, nodesep=0.45, ranksep=1.0, splines=true];',
     '  node [fontname="Arial", fontsize=15];',
     '  edge [fontname="Arial", fontsize=13, penwidth=1.2];']
for ten, (kho, cot) in BANG.items():
    d.append(f'  {ten} [shape=plain, label={bang_qh(ten, kho, cot)}];')
for a, b, n in LIEN_KET:
    d.append(f'  {a} -- {b} [label="{n}"];')
d += ['  {rank=same; bac_si; benh_nhan;}', '  {rank=same; don_thuoc; thuoc;}', '}']
xuat('10-luoc-do-csdl-quan-he', d)
