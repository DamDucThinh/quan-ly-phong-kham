# Bộ vẽ tay sơ đồ DFD (cùng kiểu với ../nguon/03-DFD-muc-1-dinh.svg): tác nhân, tiến trình, kho, luồng thẳng/gấp khúc, nhãn nền trắng.
import math
from xml.sax.saxutils import escape

W, H = 2720, 1560
FONT = 'font-family="Arial"'
NUT = {}
CO = dict(nhan=15, tt=15, tn=17, kho=15, r=78)  # cỡ chữ: nhãn luồng, tiến trình, tác nhân, kho; bán kính tiến trình
ve_nut, ve_canh, ve_nhan = [], [], []


def tac_nhan(ma, x, y, ten, w=None):
    w = max(w or 0, len(ten) * CO['tn'] * 0.56 + 50)
    h = CO['tn'] * 3.3
    NUT[ma] = dict(loai='hcn', x=x, y=y, w=w, h=h)
    ve_nut.append(f'<rect x="{x - w / 2}" y="{y - h / 2}" width="{w}" height="{h}" fill="#fff" stroke="#000" stroke-width="2.2"/>')
    ve_nut.append(f'<text x="{x}" y="{y + CO["tn"] * 0.35:.1f}" text-anchor="middle" {FONT} font-size="{CO["tn"]}" font-weight="bold">{escape(ten)}</text>')


def tien_trinh(ma, x, y, so, dong, r=None):
    r = r or CO['r']; c = CO['tt']; dg = c * 1.27
    NUT[ma] = dict(loai='tron', x=x, y=y, r=r)
    ve_nut.append(f'<circle cx="{x}" cy="{y}" r="{r}" fill="#fff" stroke="#000" stroke-width="2"/>')
    cac = [(so, 'bold')] + [(d, 'normal') for d in dong]
    y0 = y - (len(cac) - 1) * dg / 2 + c * 0.35
    for i, (t, dam) in enumerate(cac):
        ve_nut.append(f'<text x="{x}" y="{y0 + i * dg:.1f}" text-anchor="middle" {FONT} font-size="{c}" font-weight="{dam}">{escape(t)}</text>')


def kho(ma, x, y, ten, w=None):
    w = max(w or 0, (len(ten) + 4) * CO['kho'] * 0.5 + 40)
    h = CO['kho'] * 2.93
    NUT[ma] = dict(loai='hcn', x=x, y=y, w=w, h=h)
    ve_nut.append(f'<rect x="{x - w / 2}" y="{y - h / 2}" width="{w}" height="{h}" fill="#fff"/>')
    for yy in (y - h / 2, y + h / 2):
        ve_nut.append(f'<line x1="{x - w / 2}" y1="{yy}" x2="{x + w / 2}" y2="{yy}" stroke="#000" stroke-width="2.2"/>')
    ve_nut.append(f'<text x="{x}" y="{y + CO["kho"] * 0.35:.1f}" text-anchor="middle" {FONT} font-size="{CO["kho"]}"><tspan font-weight="bold">{ma}</tspan>  {escape(ten)}</text>')


def ben_trong(n, px, py):
    if n['loai'] == 'tron':
        return (px - n['x']) ** 2 + (py - n['y']) ** 2 <= n['r'] ** 2
    return abs(px - n['x']) <= n['w'] / 2 and abs(py - n['y']) <= n['h'] / 2


def cat(n, ax, ay, bx, by):
    """Điểm đoạn (a→b) rời khỏi nút n (a nằm trong n)."""
    lo, hi = 0.0, 1.0
    for _ in range(40):
        m = (lo + hi) / 2
        if ben_trong(n, ax + (bx - ax) * m, ay + (by - ay) * m):
            lo = m
        else:
            hi = m
    return ax + (bx - ax) * hi, ay + (by - ay) * hi


def nhan(x, y, chu):
    dong = chu.split('|')
    c = CO['nhan']; dg = c * 1.27
    rong = max(len(d) for d in dong) * c * 0.487 + 8
    cao = len(dong) * dg + 4
    ve_nhan.append(f'<rect x="{x - rong / 2:.1f}" y="{y - cao / 2:.1f}" width="{rong:.1f}" height="{cao}" fill="#fff"/>')
    for i, d in enumerate(dong):
        ve_nhan.append(f'<text x="{x:.1f}" y="{y - cao / 2 + c * 1.13 + i * dg:.1f}" text-anchor="middle" {FONT} font-size="{c}">{escape(d)}</text>')


def duong(a, b, chu='', hai_chieu=False, lech=0, t=0.5, dx=0, dy=0, qua=(), o=None):
    """Luồng a→b; qua = các điểm gấp khúc; lech = dịch song song (cho cặp luồng hai chiều)."""
    na, nb = NUT[a], NUT[b]
    def tam(n, q):  # đi thẳng (ngang/dọc) vào nút từ điểm gấp q
        if not q:
            return n['x'], n['y']
        if ben_trong(n, n['x'], q[1]):
            return n['x'], q[1]
        return q[0], n['y']
    diem = [tam(na, qua[0] if qua else None)] + list(qua) + [tam(nb, qua[-1] if qua else None)]
    if lech and not qua:
        ux, uy = nb['x'] - na['x'], nb['y'] - na['y']
        d = math.hypot(ux, uy)
        ox, oy = -uy / d * lech, ux / d * lech
        diem = [(x + ox, y + oy) for x, y in diem]
    diem[0] = cat(na, *diem[0], *diem[1])
    diem[-1] = cat(nb, *diem[-1], *diem[-2])
    d = 'M ' + ' L '.join(f'{x:.1f} {y:.1f}' for x, y in diem)
    dau = ' marker-start="url(#mt)"' if hai_chieu else ''
    ve_canh.append(f'<path d="{d}" fill="none" stroke="#000" stroke-width="1.5" marker-end="url(#mt)"{dau}/>')
    if chu and o:
        nhan(o[0], o[1], chu)
    elif chu:
        # nhãn đặt trên đoạn dài nhất, tại tỉ lệ t
        i = max(range(len(diem) - 1), key=lambda k: math.dist(diem[k], diem[k + 1])) if qua else 0
        (x1, y1), (x2, y2) = diem[i], diem[i + 1]
        nhan(x1 + (x2 - x1) * t + dx, y1 + (y2 - y1) * t + dy, chu)




def bat_dau(rong, cao):
    global W, H
    W, H = rong, cao
    NUT.clear(); ve_nut.clear(); ve_canh.clear(); ve_nhan.clear()


def xuat(ten_file, tieu_de, thu_muc_png='../ban-nang-cao-nhieu-vai-tro', zoom=1.6):
    import subprocess
    svg = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">',
           '<defs><marker id="mt" viewBox="0 0 10 10" refX="9.5" refY="5" markerWidth="9" markerHeight="9" orient="auto-start-reverse" markerUnits="userSpaceOnUse"><path d="M0,0 L10,5 L0,10 z" fill="#000"/></marker></defs>',
           f'<rect width="{W}" height="{H}" fill="#fff"/>',
           f'<text x="{W / 2}" y="70" text-anchor="middle" {FONT} font-size="28" font-weight="bold">{escape(tieu_de)}</text>']
    svg += ve_canh + ve_nut + ve_nhan + ['</svg>']
    open(ten_file + '.svg', 'w', encoding='utf-8').write('\n'.join(svg) + '\n')
    subprocess.run(['rsvg-convert', '-z', str(zoom), ten_file + '.svg', '-o', f'{thu_muc_png}/{ten_file}.png'], check=True)
