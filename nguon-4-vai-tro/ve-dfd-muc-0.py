# DFD mức 0 theo sơ đồ thầy vẽ: 4 tác nhân ở 4 góc, tiến trình 0 ở giữa, nhãn chạy dọc theo luồng.
# Chạy (trong nguon-4-vai-tro/): python3 ve-dfd-muc-0.py . ../ban-nang-cao-4-vai-tro
import math, subprocess, sys
sys.path.insert(0, sys.argv[1])
import so_do
from so_do import tac_nhan, tien_trinh, escape, FONT

THU_MUC_PNG = sys.argv[2] if len(sys.argv) > 2 else '.'
so_do.CO.update(nhan=24, tt=25, tn=26)
W, H = 1900, 1360
so_do.bat_dau(W, H)
C = (950, 720)
R = 170
tien_trinh('HT', *C, '0', ['HỆ THỐNG', 'QUẢN LÝ', 'PHÒNG KHÁM'], r=R)

GOC = {'BN': (290, 240, 'Bệnh nhân'), 'BS': (1610, 240, 'Bác sĩ'),
       'NV': (290, 1200, 'Nhân viên'), 'QT': (1610, 1200, 'Quản trị')}
for ma, (x, y, ten) in GOC.items():
    tac_nhan(ma, x, y, ten, 350)

# Luồng của mỗi tác nhân, xếp từ đường nằm cao nhất trên màn hình xuống. 'vao' = tác nhân gửi vào hệ thống.
LUONG = {
    'BN': [('vao', 'Đăng ký / đăng nhập'), ('vao', 'Đăng ký khám chữa bệnh'),
           ('ra', 'Lịch khám chữa bệnh'), ('ra', 'Kết quả khám chữa bệnh')],
    'BS': [('vao', 'Tra cứu lịch khám chữa bệnh'), ('ra', 'Thông tin lịch khám chữa bệnh'),
           ('vao', 'Kết quả khám chữa bệnh')],
    'NV': [('vao', 'Tra cứu, tiếp nhận bệnh nhân'), ('vao', 'Sắp xếp lịch khám chữa bệnh'),
           ('ra', 'Danh sách lịch khám chữa bệnh')],
    'QT': [('vao', 'Quản lý tài khoản người dùng'), ('vao', 'Quản lý lịch làm việc của bác sĩ'),
           ('vao', 'Quản lý thông tin bác sĩ'), ('vao', 'Yêu cầu báo cáo'), ('ra', 'Báo cáo thống kê')],
}
BUOC = 104     # khoảng cách các điểm xuất phát trên cạnh tác nhân
BUOC_RIENG = {'QT': 80}
THU = 0.86     # độ chụm lại khi vào vòng tròn
GAP = 10       # nhãn cách đường
T_NHAN = 0.44  # vị trí nhãn tính từ tác nhân

for ma, ds in LUONG.items():
    ax, ay, _ = GOC[ma]
    n = so_do.NUT[ma]
    ux, uy = C[0] - ax, C[1] - ay
    d = math.hypot(ux, uy); ux, uy = ux / d, uy / d
    nx, ny = -uy, ux
    yb = ay + (n['h'] / 2 if ay < C[1] else -n['h'] / 2)   # hàng trên ra từ cạnh dưới, hàng dưới ra từ cạnh trên
    k = len(ds)
    cac_x = sorted([(i - (k - 1) / 2) * BUOC_RIENG.get(ma, BUOC) for i in range(k)], key=lambda off: off * nx * ny)
    for (chieu, chu), off in zip(ds, cac_x):
        bx, by = ax + off, yb
        q = ((bx - ax) * nx + (by - ay) * ny) * THU
        s = math.sqrt(R * R - q * q)
        cx, cy = C[0] - ux * s + nx * q, C[1] - uy * s + ny * q
        a, b = ((bx, by), (cx, cy)) if chieu == 'vao' else ((cx, cy), (bx, by))
        so_do.ve_canh.append(f'<path d="M {a[0]:.1f} {a[1]:.1f} L {b[0]:.1f} {b[1]:.1f}" fill="none" stroke="#000" stroke-width="2" marker-end="url(#mt)"/>')
        goc = math.degrees(math.atan2(cy - by, cx - bx))
        goc = goc - 180 if goc > 90 else goc + 180 if goc < -90 else goc
        mx, my = bx + (cx - bx) * T_NHAN, by + (cy - by) * T_NHAN
        so_do.ve_nhan.append(f'<text transform="translate({mx:.1f},{my:.1f}) rotate({goc:.2f})" y="{-GAP}" text-anchor="middle" {FONT} font-size="{so_do.CO["nhan"]}" stroke="#fff" stroke-width="6" stroke-linejoin="round" paint-order="stroke">{escape(chu)}</text>')

ten = '02-DFD-muc-0-khung-canh'
svg = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">',
       '<defs><marker id="mt" viewBox="0 0 10 10" refX="9.5" refY="5" markerWidth="16" markerHeight="16" orient="auto-start-reverse" markerUnits="userSpaceOnUse"><path d="M0,0 L10,5 L0,10 z" fill="#000"/></marker></defs>',
       f'<rect width="{W}" height="{H}" fill="#fff"/>',
       f'<text x="{W / 2}" y="76" text-anchor="middle" {FONT} font-size="32" font-weight="bold">DFD MỨC KHUNG CẢNH (MỨC 0)</text>']
svg += so_do.ve_canh + so_do.ve_nut + so_do.ve_nhan + ['</svg>']
open(f'{ten}.svg', 'w', encoding='utf-8').write('\n'.join(svg) + '\n')
subprocess.run(['rsvg-convert', '-z', '1.45', f'{ten}.svg', '-o', f'{THU_MUC_PNG}/{ten}.png'], check=True)
