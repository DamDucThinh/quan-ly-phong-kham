# Userflow bản 4 vai trò (Bệnh nhân, Nhân viên, Bác sĩ, Quản trị), không có kho thuốc.
#   python3 ve-userflow.py   (sinh .gv, .svg và PNG vào ../ban-nang-cao-4-vai-tro)
# Đánh số màn hình:
#  [1] Đăng nhập  [2] Đăng ký  [3] Trang chủ Bệnh nhân  [4] Trang chủ Nhân viên  [5] Hàng chờ khám (Bác sĩ)
#  [6] Trang chủ Quản trị  [7] Danh sách bác sĩ  [8] Thêm, sửa bác sĩ  [9] Lịch làm việc bác sĩ
#  [10] Danh mục thuốc  [11] Thêm, sửa thuốc  [12] Tài khoản người dùng  [13] Thêm, sửa tài khoản
#  [14] Đặt lịch hộ – chọn bệnh nhân  [15] Chọn bác sĩ, ngày khám  [16] Chọn khung giờ trống  [17] Xác nhận lịch hẹn
#  [18] Lịch hẹn của tôi  [19] Xác nhận hủy lịch hẹn  [20] Kết quả khám (Bệnh nhân)  [21] Tiếp nhận bệnh nhân
#  [22] Danh sách lịch hẹn  [23] Hồ sơ bệnh án các lần trước  [24] Ghi kết quả khám và kê đơn
#  [25] Lịch làm việc của tôi (Bác sĩ)  [26] Chọn loại báo cáo  [27] Kết quả thống kê
import subprocess

RA = '../ban-nang-cao-4-vai-tro'
NHO = '<FONT POINT-SIZE="4"> </FONT><BR/>'


def dau(tieu_de, phu, nodesep=0.55, ranksep=0.5):
    return ['digraph G {',
            f'  graph [fontname="Arial", bgcolor="white", pad=0.35, labelloc=t, fontsize=22, label=<<B>{tieu_de}</B><BR/><FONT POINT-SIZE="14">{phu}</FONT><BR/> >, rankdir=TB, nodesep={nodesep}, ranksep={ranksep}, newrank=true];',
            '  node [fontname="Arial", color="black", fontsize=14, penwidth=1.4, margin="0.18,0.08"];',
            '  edge [fontname="Arial", fontsize=12.5, arrowsize=0.85, penwidth=1.2];']


def man(ma, so, ten, ghi=''):
    g = f'<BR/>{NHO}<FONT POINT-SIZE="12"><I>{ghi}</I></FONT>' if ghi else ''
    return f'  {ma} [shape=box, style="rounded,bold", penwidth=1.6, label=<<B>[{so}]</B> {ten}{g}>];'


def ht(ma, ten, ghi=''):
    g = f'<BR/>{NHO}<FONT POINT-SIZE="12"><I>{ghi}</I></FONT>' if ghi else ''
    return f'  {ma} [shape=box, style=filled, fillcolor="#eeeeee", label=<{ten}{g}>];'


def hoi(ma, cau):
    return f'  {ma} [shape=diamond, fontsize=13, label=<{cau}>];'


def tb(ma, chu):
    return f'  {ma} [shape=note, label=<{chu}>];'


def luong(ma, ten, ghi):
    return f'  {ma} [shape=cds, margin="0.22,0.1", label=<{ten}<BR/>{NHO}<FONT POINT-SIZE="12"><I>{ghi}</I></FONT>>];'


def mo(ma, ten, ghi=''):
    g = f'<BR/><FONT POINT-SIZE="12">{ghi}</FONT>' if ghi else ''
    return f'  {ma} [shape=oval, style=bold, penwidth=1.6, label=<<B>{ten}</B>{g}>];'


def e(a, b, nhan='', them=''):
    l = f'label=<{nhan}>' if nhan else ''
    t = ', '.join(x for x in (l, them) if x)
    return f'  {a} -> {b}' + (f' [{t}];' if t else ';')


def chu_thich(gan):
    return ['  subgraph cluster_legend {',
            '    label=<<B>Chú thích</B>>; fontsize=13; style=dashed; color="#888888"; labeljust=l; margin=12;',
            '    node [fontsize=12, margin="0.12,0.05"];',
            '    G1 [shape=oval, style=bold, label=<Bắt đầu / Kết thúc>];',
            '    G2 [shape=box, style="rounded,bold", label=<[n] Màn hình người dùng>];',
            '    G3 [shape=box, style=filled, fillcolor="#eeeeee", label=<Hệ thống xử lý>];',
            '    G4 [shape=diamond, label=<Rẽ nhánh>];',
            '    G5 [shape=note, label=<Thông báo / phiếu>];',
            '    G6 [shape=cds, label=<Sang luồng khác>];',
            '    {rank=same; G1; G2; G3; G4; G5; G6;}',
            '    G1 -> G2 -> G3 -> G4 -> G5 -> G6 [style=invis];',
            '  }', f'  {gan} -> G3 [style=invis];']


def xuat(ten, dong):
    open(ten + '.gv', 'w', encoding='utf-8').write('\n'.join(dong + ['}']) + '\n')
    subprocess.run(['dot', '-Tpng', '-Gdpi=170', ten + '.gv', '-o', f'{RA}/{ten}.png'], check=True)
    subprocess.run(['dot', '-Tsvg', ten + '.gv', '-o', ten + '.svg'], check=True)


# ---------------- Tổng quan ----------------
d = dau('USERFLOW TỔNG QUAN — HỆ THỐNG QUẢN LÝ PHÒNG KHÁM',
        '4 vai trò: Bệnh nhân, Nhân viên, Bác sĩ, Quản trị', 0.45, 0.55)
d += [mo('S', 'Bắt đầu', 'Người dùng mở hệ thống'),
      man('P1', 1, 'Đăng nhập', 'Mọi vai trò'),
      hoi('Q0', 'Đã có<BR/>tài khoản?'),
      man('P2', 2, 'Đăng ký tài khoản', 'Bệnh nhân · số điện thoại'),
      hoi('Q1', 'Thông tin<BR/>đúng?'),
      tb('N1', 'Báo sai tên đăng nhập<BR/>hoặc mật khẩu'),
      hoi('Q2', 'Tài khoản<BR/>bị khóa?'),
      tb('N2', 'Báo tài khoản đã bị khóa,<BR/>liên hệ quản trị'),
      hoi('Q3', 'Vai trò?'),
      man('P3', 3, 'Trang chủ<BR/>Bệnh nhân'), man('P4', 4, 'Trang chủ<BR/>Nhân viên'),
      man('P5', 5, 'Hàng chờ khám'), man('P6', 6, 'Trang chủ<BR/>Quản trị'),
      luong('L2', '<B>Luồng 2</B>: Đặt, đổi, hủy lịch hẹn', 'CN 2 · màn [14]–[19]'),
      luong('L3', '<B>Luồng 3</B>: Tiếp nhận, khám bệnh, kê đơn', 'CN 3, 4 · màn [21]–[24]'),
      luong('L1', '<B>Luồng 1</B>: Quản trị hệ thống', 'CN 1 · màn [7]–[13]'),
      luong('L4', '<B>Luồng 4</B>: Báo cáo thống kê', 'CN 5 · màn [26]–[27]'),
      man('P20', 20, 'Kết quả khám,<BR/>đơn thuốc', 'Bệnh nhân'), man('P25', 25, 'Lịch làm việc<BR/>của tôi', 'Bác sĩ')]
d += [e('S', 'P1'), e('P1', 'Q0'), e('Q0', 'P2', 'Không'), e('P2', 'P3', 'Đăng ký xong,<BR/>vào trang chủ'),
      e('Q0', 'Q1', '  Có'), e('Q1', 'N1', 'Không'), e('N1', 'P1', '', 'constraint=false'), e('Q1', 'Q2', '  Có'),
      e('Q2', 'N2', 'Có'), e('Q2', 'Q3', '  Không'),
      e('Q3', 'P3', 'Bệnh nhân'), e('Q3', 'P4', 'Nhân viên'), e('Q3', 'P5', 'Bác sĩ'), e('Q3', 'P6', 'Quản trị'),
      e('P3', 'L2', 'Tự đặt, đổi,<BR/>hủy lịch'), e('P3', 'P20', 'Xem kết quả'), e('P4', 'L2', 'Đặt hộ'), e('P4', 'L3', 'Tiếp nhận'),
      e('P5', 'L3', 'Gọi vào khám'), e('P5', 'P25'),
      e('P6', 'L1', 'Bác sĩ, thuốc,<BR/>tài khoản'), e('P6', 'L4', 'Báo cáo'),
      '  {rank=same; P3; P4; P5; P6;}', '  {rank=same; L2; L3; L1; L4;}']
d += chu_thich('L3')
xuat('11-userflow-tong-quan', d)

# ---------------- Luồng 1: quản trị hệ thống ----------------
d = dau('USERFLOW — LUỒNG 1: QUẢN TRỊ HỆ THỐNG',
        'Người dùng: Quản trị · Ứng với chức năng 1 (1.1 bác sĩ, 1.2 lịch làm việc, 1.3 danh mục thuốc, 1.4 tài khoản)')
d += [mo('S', 'Bắt đầu', 'Trang chủ Quản trị [6]'), hoi('Q0', 'Quản trị<BR/>chọn'),
      man('P7', 7, 'Danh sách bác sĩ', 'CN 1.1 · đọc D1'),
      man('P8', 8, 'Thêm, sửa bác sĩ', 'Họ tên, chuyên khoa, phòng, SĐT, email, trình độ'),
      hoi('Q1', 'Thông tin<BR/>hợp lệ?'), tb('N1', 'Báo lỗi dưới<BR/>ô nhập sai'),
      ht('W1', 'Lưu hồ sơ bác sĩ', 'Hệ thống · CN 1.1 · ghi D1'),
      man('P9', 9, 'Lịch làm việc bác sĩ', 'CN 1.2 · các ca trong tuần'),
      hoi('Q2', 'Ca hợp lệ,<BR/>không trùng?'), tb('N2', 'Báo thiếu thông tin, giờ kết thúc<BR/>không sau giờ bắt đầu, trùng giờ'),
      ht('W2', 'Lưu lịch làm việc', 'Hệ thống · CN 1.2 · ghi D1'),
      man('P10', 10, 'Danh mục thuốc', 'CN 1.3 · đọc D3 · tìm theo tên, nhóm'),
      man('P11', 11, 'Thêm, sửa thuốc', 'Mã, tên, hàm lượng, đơn vị, nhóm, liều tối đa, lưu ý'),
      hoi('Q5', 'Thông tin<BR/>hợp lệ?'), tb('N5', 'Báo lỗi dưới<BR/>ô nhập sai'),
      ht('W5', 'Lưu thuốc', 'Hệ thống · CN 1.3 · ghi D3'),
      man('P12', 12, 'Tài khoản người dùng', 'CN 1.4 · đọc D7 · lọc theo vai trò'),
      hoi('Q3', 'Thao tác?'),
      ht('W3', 'Khóa / mở khóa tài khoản', 'Hệ thống · ghi D7 · không tự khóa mình'),
      man('P13', 13, 'Thêm, sửa tài khoản', 'Họ tên, tên đăng nhập, vai trò, bác sĩ liên kết, mật khẩu'),
      hoi('Q4', 'Hợp lệ?'),
      tb('N4', 'Tên đăng nhập trùng, bác sĩ<BR/>đã có tài khoản, mật khẩu &lt; 6 ký tự'),
      ht('W4', 'Lưu tài khoản', 'Hệ thống · CN 1.4 · ghi D7'),
      mo('E', 'Kết thúc')]
d += [e('S', 'Q0'), e('Q0', 'P7', 'Bác sĩ'), e('Q0', 'P10', 'Thuốc'), e('Q0', 'P12', 'Tài khoản'),
      e('P7', 'P8', 'Thêm / Sửa'), e('P8', 'Q1'), e('Q1', 'N1', 'Không'), e('N1', 'P8', '', 'constraint=false'),
      e('Q1', 'W1', '  Có'), e('W1', 'P7', '', 'constraint=false'),
      e('P7', 'P9', 'Lịch làm việc'), e('P9', 'Q2'), e('Q2', 'N2', 'Không'), e('N2', 'P9', '', 'constraint=false'),
      e('Q2', 'W2', '  Có'), e('W2', 'E'), e('W1', 'E'),
      e('P10', 'P11', 'Thêm / Sửa'), e('P11', 'Q5'), e('Q5', 'N5', 'Không'), e('N5', 'P11', '', 'constraint=false'),
      e('Q5', 'W5', '  Có'), e('W5', 'P10', '', 'constraint=false'), e('W5', 'E'),
      e('P12', 'Q3'), e('Q3', 'W3', 'Khóa /<BR/>mở khóa'), e('W3', 'P12', '', 'constraint=false'),
      e('Q3', 'P13', 'Thêm / Sửa'), e('P13', 'Q4'), e('Q4', 'N4', 'Không'), e('N4', 'P13', '', 'constraint=false'),
      e('Q4', 'W4', '  Có'), e('W4', 'E')]
d += chu_thich('E')
xuat('12-userflow-luong-1-quan-tri-he-thong', d)

# ---------------- Luồng 2: đặt, đổi, hủy lịch ----------------
d = dau('USERFLOW — LUỒNG 2: ĐẶT, ĐỔI, HỦY LỊCH HẸN',
        'Người dùng: Bệnh nhân (tự đặt) và Nhân viên (đặt hộ người gọi điện, đến quầy) · Ứng với chức năng 2')
d += [mo('S1', 'Bắt đầu', 'Bệnh nhân: Trang chủ [3]'), mo('S2', 'Bắt đầu', 'Nhân viên: Trang chủ [4]'),
      man('P14', 14, 'Đặt lịch hộ – chọn bệnh nhân', 'Nhân viên · tìm theo SĐT, họ tên, mã'),
      hoi('QB', 'Đã có<BR/>hồ sơ?'),
      ht('WB', 'Thêm bệnh nhân mới', 'Hệ thống · ghi D2 · họ tên, SĐT'),
      hoi('Q0', 'Chọn'),
      man('P15', 15, 'Chọn bác sĩ, ngày khám', 'CN 2.1 · 7 ngày tới'),
      ht('K22', 'Kiểm tra lịch trống', 'Hệ thống · CN 2.2 · đọc D1, D4'),
      hoi('Q1', 'Còn khung<BR/>giờ trống?'), tb('N1', 'Báo hết chỗ, gợi ý<BR/>ngày / bác sĩ khác'),
      man('P16', 16, 'Chọn khung giờ trống'),
      man('P17', 17, 'Xác nhận lịch hẹn', 'Ghi lý do khám'),
      ht('X23', 'Kiểm tra lại khung giờ', 'Hệ thống · CN 2.3'),
      hoi('Q3', 'Khung giờ<BR/>vẫn trống?'), tb('N3', 'Báo vừa có người đặt,<BR/>chọn giờ khác'),
      ht('W23', 'Ghi lịch hẹn', 'Hệ thống · CN 2.3 · ghi D4 · người đặt: bệnh nhân / nhân viên'),
      tb('N4', 'Thông báo lịch hẹn<BR/>gửi Bệnh nhân'),
      man('P18', 18, 'Lịch hẹn của tôi', 'Bệnh nhân · CN 2.4 · đọc D4'),
      hoi('Q5', 'Đổi hay<BR/>hủy?'),
      man('P19', 19, 'Xác nhận hủy lịch hẹn', 'Chọn lý do hủy'),
      ht('H24', 'Hủy lịch, trả lại khung giờ', 'Hệ thống · CN 2.4 · ghi D4'),
      mo('E', 'Kết thúc')]
d += [e('S2', 'P14'), e('P14', 'QB'), e('QB', 'WB', 'Không'), e('QB', 'Q0', '  Có'), e('WB', 'Q0'),
      e('S1', 'Q0'),
      e('Q0', 'P15', 'Đặt lịch mới'), e('P15', 'K22'), e('K22', 'Q1'), e('Q1', 'N1', 'Không'), e('N1', 'P15', '', 'constraint=false'),
      e('Q1', 'P16', '  Có'), e('P16', 'P17'), e('P17', 'X23', 'Xác nhận'), e('X23', 'Q3'), e('Q3', 'N3', 'Không'),
      e('N3', 'P16', '', 'constraint=false'), e('Q3', 'W23', '  Có'), e('W23', 'N4'), e('N4', 'E'),
      e('Q0', 'P18', 'Đổi / hủy lịch<BR/>(nhân viên: lịch sắp tới<BR/>ở [14] hoặc [22])'), e('P18', 'Q5'),
      e('Q5', 'P15', 'Đổi: chọn<BR/>giờ mới', 'constraint=false'), e('Q5', 'P19', '  Hủy'), e('P19', 'H24', 'Đồng ý'),
      e('H24', 'N4'),
      '  {rank=same; S1; S2;}']
d += chu_thich('E')
xuat('13-userflow-luong-2-dat-doi-huy-lich-hen', d)

# ---------------- Luồng 3: tiếp nhận, khám, kê đơn ----------------
d = dau('USERFLOW — LUỒNG 3: TIẾP NHẬN, KHÁM BỆNH, KÊ ĐƠN',
        'Người dùng: Nhân viên (tiếp nhận) và Bác sĩ (khám, kê đơn) · Ứng với chức năng 3, 4')
d += [mo('S', 'Bắt đầu', 'Nhân viên: bệnh nhân đến quầy'),
      man('P21', 21, 'Tiếp nhận bệnh nhân', 'Nhân viên · CN 3.1'),
      hoi('Q1', 'Có lịch hẹn<BR/>hôm nay?'),
      ht('V1', 'Tạo lượt khám không hẹn', 'Hệ thống · tạo / cập nhật hồ sơ D2 · ghi D4'),
      ht('T1', 'Lập phiếu tiếp nhận', 'Hệ thống · CN 3.1 · cấp mã BN · lịch sang Chờ khám'),
      man('P22', 22, 'Danh sách lịch hẹn', 'Nhân viên · Tiếp nhận, đổi, hủy, in phiếu'),
      man('P5', 5, 'Hàng chờ khám', 'Bác sĩ · CN 3.2 · chỉ bệnh nhân của mình'),
      ht('GK', 'Gọi vào khám', 'Hệ thống · lịch sang Đang khám'),
      man('P23', 23, 'Hồ sơ bệnh án các lần trước', 'Bác sĩ · CN 3.2 · đọc D5, D6'),
      man('P24', 24, 'Ghi kết quả khám và kê đơn', 'Bác sĩ · CN 3.3, 4.1 · chọn thuốc trong danh mục'),
      ht('K42', 'Kiểm tra thuốc', 'Hệ thống · CN 4.2 · đọc D3'),
      hoi('Q2', 'Đơn<BR/>hợp lệ?'),
      tb('N2', 'Báo thuốc trùng,<BR/>số lượng sai'),
      ht('W33', 'Lưu bệnh án và đơn thuốc', 'Hệ thống · CN 3.3, 4.3 · ghi D5, D6 · lịch Đã khám'),
      tb('N3', 'Phiếu kết quả khám, đơn thuốc (bản in)<BR/>bệnh nhân mang đơn ra ngoài mua thuốc'),
      tb('N4', 'Thông báo kết quả<BR/>gửi Bệnh nhân'),
      mo('E', 'Kết thúc')]
d += [e('S', 'P21'), e('P21', 'Q1'), e('Q1', 'V1', 'Không'), e('Q1', 'T1', '  Có, chọn lịch'), e('V1', 'T1'),
      e('P22', 'P21', 'Tiếp nhận', 'constraint=false'),
      e('T1', 'P5', 'Vào hàng chờ<BR/>của bác sĩ'), e('P5', 'GK'), e('GK', 'P23'), e('P23', 'P24'), e('P24', 'K42', 'Lưu'),
      e('K42', 'Q2'), e('Q2', 'N2', 'Không'), e('N2', 'P24', '', 'constraint=false'), e('Q2', 'W33', '  Có'),
      e('W33', 'N3'), e('W33', 'N4'), e('N3', 'E'), e('N4', 'E'),
      '  {rank=same; P21; P22;}']
d += chu_thich('E')
xuat('14-userflow-luong-3-tiep-nhan-kham-ke-don', d)

# ---------------- Luồng 4: báo cáo ----------------
d = dau('USERFLOW — LUỒNG 4: BÁO CÁO THỐNG KÊ', 'Người dùng: Quản trị · Ứng với chức năng 5')
d += [mo('S', 'Bắt đầu', 'Trang chủ Quản trị [6]'),
      man('P26', 26, 'Chọn loại báo cáo', 'Lượt khám / thuốc đã kê · từ ngày, đến ngày'),
      hoi('Q1', 'Khoảng thời<BR/>gian hợp lệ?'), tb('N1', 'Báo từ ngày phải trước<BR/>đến ngày, tối đa 366 ngày'),
      ht('T5', 'Tổng hợp số liệu', 'Hệ thống · CN 5.1 đọc D5 · 5.2 đọc D6, D3'),
      hoi('Q2', 'Có dữ liệu?'), tb('N2', 'Báo không có dữ liệu<BR/>trong khoảng đã chọn'),
      man('P27', 27, 'Kết quả thống kê', 'Bảng, biểu đồ · in báo cáo'),
      mo('E', 'Kết thúc')]
d += [e('S', 'P26'), e('P26', 'Q1', 'Xem báo cáo'), e('Q1', 'N1', 'Không'), e('N1', 'P26', '', 'constraint=false'),
      e('Q1', 'T5', '  Có'), e('T5', 'Q2'), e('Q2', 'N2', 'Không'), e('Q2', 'P27', '  Có'), e('N2', 'E'),
      e('P27', 'P26', 'Đổi loại / thời gian', 'constraint=false'), e('P27', 'E')]
d += chu_thich('E')
xuat('15-userflow-luong-4-bao-cao-thong-ke', d)
