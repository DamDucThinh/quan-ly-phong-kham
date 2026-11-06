# DFD mức đỉnh (mức 1) bản 4 vai trò, không có kho thuốc, vẽ tay. Chạy: python3 ve-dfd-muc-1.py
# Luồng với tác nhân dùng đúng nhãn của DFD mức 0 (sơ đồ thầy vẽ); phần trong ngoặc nói rõ phần nào của luồng đó.
import so_do
from so_do import *

so_do.CO.update(nhan=19, tt=18, tn=20, kho=18, r=90)

bat_dau(2500, 1560)
# ---------------- Bố cục ----------------
# Cột: tiến trình 2/3 (x=700), 1/4 (x=1440), 5 (x=2200). Hàng: 400 / 780 / 1160.
tac_nhan('NV', 140, 780, 'Nhân viên', 160)
tac_nhan('BN', 1040, 630, 'Bệnh nhân', 170)
tac_nhan('BS', 1090, 930, 'Bác sĩ', 130)
tac_nhan('QT', 1900, 170, 'Quản trị', 170)

tien_trinh('P2', 700, 400, '2', ['Quản lý', 'lịch hẹn'])
tien_trinh('P3', 700, 1160, '3', ['Quản lý hồ sơ', 'bệnh án'])
tien_trinh('P1', 1440, 400, '1', ['Quản trị', 'hệ thống'])
tien_trinh('P4', 1440, 1160, '4', ['Kê đơn', 'thuốc'])
tien_trinh('P5', 2200, 400, '5', ['Báo cáo', 'thống kê'])

kho('D2', 410, 780, 'Hồ sơ bệnh nhân', 220)
kho('D4', 700, 780, 'Lịch hẹn', 170)
kho('D1', 1070, 400, 'Hồ sơ bác sĩ', 200)
kho('D5', 1070, 1160, 'Hồ sơ bệnh án', 210)
kho('D7', 1440, 170, 'Tài khoản người dùng', 270)
kho('D3', 1440, 780, 'Danh mục thuốc', 220)
kho('D6', 1820, 1160, 'Đơn thuốc', 180)

# ---------------- Luồng ----------------
# Nhân viên
duong('NV', 'P2', 'Sắp xếp lịch|khám chữa bệnh', lech=-10, t=0.45, dx=-62, dy=-8)
duong('P2', 'NV', 'Danh sách lịch|khám chữa bệnh', lech=-10, t=0.75, dx=40, dy=18)
duong('NV', 'P3', 'Tra cứu, tiếp nhận|bệnh nhân', t=0.45, dx=-60, dy=14)
duong('P2', 'D2', 'Bệnh nhân mới', t=0.55, dx=-8)
duong('P3', 'D2', hai_chieu=True)
duong('P2', 'D4', hai_chieu=True)
duong('D4', 'P3', 'Lịch hẹn trong ngày,|trạng thái khám', hai_chieu=True, t=0.5)
# Bệnh nhân
duong('BN', 'P2', 'Đăng ký khám chữa bệnh', lech=10, t=0.5, dx=40, dy=-40)
duong('P2', 'BN', 'Lịch khám chữa bệnh', lech=10, t=0.5, dx=-40, dy=40)
duong('BN', 'P1', 'Đăng ký / đăng nhập', t=0.5, dx=-45, dy=-35)
duong('P3', 'BN', 'Kết quả khám chữa bệnh', o=(868, 702))
duong('P4', 'BN', 'Kết quả khám chữa bệnh|(đơn thuốc)', t=0.5, dx=60)
# Bác sĩ
duong('D1', 'P2', 'Lịch làm việc', t=0.5, dy=-16)
duong('P1', 'D1', hai_chieu=True)
duong('P1', 'BS', 'Thông tin lịch khám|chữa bệnh (lịch làm việc)', t=0.55, dx=60)
duong('P3', 'BS', 'Thông tin lịch|khám chữa bệnh', lech=-10, o=(938, 922))
duong('BS', 'P3', 'Tra cứu lịch khám chữa bệnh,|kết quả khám chữa bệnh', lech=-10, o=(1030, 1083))
duong('BS', 'P4', 'Kết quả khám chữa bệnh|(chỉ định thuốc)', t=0.35, dx=60, dy=15)
duong('P3', 'D5', hai_chieu=True)
duong('D5', 'P4', 'Chẩn đoán', t=0.5, dy=-16)
# Quản trị hệ thống: bác sĩ, lịch làm việc, danh mục thuốc, tài khoản
duong('P1', 'D7', hai_chieu=True)
duong('P1', 'D3', hai_chieu=True)
duong('D3', 'P4', 'Danh mục thuốc', t=0.5, dx=70)
duong('P4', 'D6', 'Đơn thuốc', t=0.5, dy=-16)
duong('QT', 'P1', 'Quản lý thông tin bác sĩ,|lịch làm việc của bác sĩ,|tài khoản người dùng,|danh mục thuốc', t=0.3, dx=-60, dy=22)
# Báo cáo
duong('QT', 'P5', 'Yêu cầu báo cáo', lech=10, t=0.5, dx=-95, dy=-6)
duong('P5', 'QT', 'Báo cáo thống kê', lech=10, t=0.5, dx=95, dy=4)
duong('D3', 'P5', 'Danh mục thuốc', t=0.45, dy=-16)
duong('D6', 'P5', 'Thuốc đã kê', t=0.5, dx=60)
duong('D5', 'P5', 'Lượt khám', qua=[(1070, 1440), (2420, 1440), (2420, 400)], t=0.5, dy=-16)

xuat('03-DFD-muc-1-dinh', 'DFD MỨC ĐỈNH (MỨC 1)', thu_muc_png='../ban-nang-cao-4-vai-tro')
