# Vẽ sơ đồ phân rã chức năng (BFD) bản 4 vai trò (không có kho thuốc). Chạy: python3 ve-bfd.py
from xml.sax.saxutils import escape
CN = [
    ('1. Quản trị hệ thống', ['1.1. Cập nhật thông tin bác sĩ', '1.2. Cập nhật lịch làm việc|bác sĩ', '1.3. Cập nhật danh mục thuốc', '1.4. Quản lý tài khoản|người dùng']),
    ('2. Quản lý lịch hẹn', ['2.1. Tiếp nhận yêu cầu|đặt lịch', '2.2. Kiểm tra lịch trống', '2.3. Xác nhận lịch hẹn', '2.4. Đổi, hủy lịch hẹn']),
    ('3. Quản lý hồ sơ bệnh án', ['3.1. Tiếp nhận bệnh nhân', '3.2. Tra cứu lịch khám,|lịch sử khám', '3.3. Cập nhật bệnh án']),
    ('4. Kê đơn thuốc', ['4.1. Lập đơn thuốc', '4.2. Kiểm tra thuốc', '4.3. Lưu và in đơn thuốc']),
    ('5. Báo cáo thống kê', ['5.1. Thống kê lượt khám', '5.2. Thống kê thuốc đã kê']),
]
CR, GAP, L = 250, 30, 30
W = L * 2 + len(CN) * CR + (len(CN) - 1) * GAP
H = 160 + 58 + max(len(c) for _, c in CN) * 68 + 40
o = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">', f'<rect width="{W}" height="{H}" fill="#fff"/>']
def txt(x, y, s, bold=False, size=15):
    dong = s.split('|')
    y0 = y - (len(dong) - 1) * 9.5
    for i, d in enumerate(dong):
        o.append(f'<text x="{x:.1f}" y="{y0 + i * 19:.1f}" text-anchor="middle" font-family="Arial" font-size="{size}" font-weight="{"bold" if bold else "normal"}">{escape(d)}</text>')
cx = W / 2
o.append(f'<rect x="{cx - 190:.1f}" y="24" width="380" height="58" fill="#fff" stroke="#000" stroke-width="1.6"/>')
txt(cx, 59, 'QUẢN LÝ PHÒNG KHÁM', True, 17)
o.append(f'<line x1="{cx:.1f}" y1="82" x2="{cx:.1f}" y2="122" stroke="#000" stroke-width="1.4"/>')
tam = [L + i * (CR + GAP) + CR / 2 for i in range(len(CN))]
o.append(f'<line x1="{tam[0]:.1f}" y1="122" x2="{tam[-1]:.1f}" y2="122" stroke="#000" stroke-width="1.4"/>')
for i, (ten, con) in enumerate(CN):
    x = L + i * (CR + GAP)
    o.append(f'<line x1="{tam[i]:.1f}" y1="122" x2="{tam[i]:.1f}" y2="160" stroke="#000" stroke-width="1.4"/>')
    o.append(f'<rect x="{x}" y="160" width="{CR}" height="58" fill="#fff" stroke="#000" stroke-width="1.6"/>')
    txt(tam[i], 194.2, ten, True)
    cuoi = 218 + 26 + (len(con) - 1) * 68 + 25
    o.append(f'<line x1="{x + 16}" y1="218" x2="{x + 16}" y2="{cuoi}" stroke="#000" stroke-width="1.4"/>')
    for k, c in enumerate(con):
        y = 244 + k * 68
        o.append(f'<line x1="{x + 16}" y1="{y + 25}" x2="{x + 34}" y2="{y + 25}" stroke="#000" stroke-width="1.4"/>')
        o.append(f'<rect x="{x + 34}" y="{y}" width="{CR - 34}" height="50" fill="#fff" stroke="#000" stroke-width="1.6"/>')
        txt(x + 34 + (CR - 34) / 2, y + 30.2, c)
o.append('</svg>')
open('01-bfd.svg', 'w', encoding='utf-8').write('\n'.join(o))
import subprocess
subprocess.run(['rsvg-convert', '-z', '2', '01-bfd.svg', '-o', '../ban-nang-cao-4-vai-tro/01-BFD-so-do-phan-ra-chuc-nang.png'], check=True)
