# Kiểm thử trọn quy trình phần mềm bản 4 vai trò và chụp màn hình cho báo cáo.
# Chạy: (cd ../phan-mem-4-vai-tro && php database/cai-dat.php --dong-y && php -S 127.0.0.1:8813 -t public) rồi python3 kiem-thu-quy-trinh.py <thư mục ảnh>
# Kiểm thử ghi dữ liệu vào CSDL phong_kham_4_vai_tro: chạy xong nên cài lại CSDL.
from playwright.sync_api import sync_playwright
import datetime, json, re, subprocess, sys

G = 'http://127.0.0.1:8813/'
RA = sys.argv[1] + '/'
kq = []


def ghi(ma, ten, dat, chung):
    kq.append([ma, ten, 'Đạt' if dat else 'KHÔNG ĐẠT', chung])
    print(ma, 'PASS' if dat else 'FAIL', ten, '|', chung)


def sql(q):
    return subprocess.run(['mysql', '-uroot', 'phong_kham_4_vai_tro', '-N', '-e', q], capture_output=True, text=True).stdout.strip()


def chup(p, ten, url=None, full=False):
    if url:
        p.goto(G + url)
    p.wait_for_load_state('networkidle'); p.wait_for_timeout(300)
    p.screenshot(path=RA + ten + '.png', full_page=full)


def dn(p, tk, anh=None):
    p.goto(G + 'dang-xuat.php'); p.goto(G + 'dang-nhap.php'); p.fill('#tk', tk); p.fill('#mk', 'matkhau')
    if anh:
        chup(p, anh)
    p.click('form.auth-form button[type=submit]'); p.wait_for_load_state('networkidle')


def chu(p):
    return p.inner_text('body')


def chon_ngay_con_cho(p, bo_qua=0):
    """Xem lần lượt ngày đang chọn và các ngày khác, dừng ở ngày thứ (bo_qua + 1) còn khung giờ trống."""
    cac_ngay = p.eval_on_selector_all('a.day', 'ds => ds.map(a => a.href)')
    for lien_ket in [None] + cac_ngay:
        if lien_ket:
            p.goto(lien_ket); p.wait_for_load_state('networkidle')
        if p.locator('a.slot').count():
            if bo_qua == 0:
                return
            bo_qua -= 1
    raise SystemExit('Không còn ngày nào có khung giờ trống')


with sync_playwright() as pw:
    b = pw.chromium.launch()
    ctx = b.new_context(viewport={'width': 1440, 'height': 810}, device_scale_factor=4 / 3, locale='vi-VN')
    p = ctx.new_page()
    chup(p, 'n02-dang-ky', 'dang-ky.php')

    # ---- Nhân viên
    dn(p, 'nhanvien01', 'n01-dang-nhap')
    chup(p, 'n10-nv-trang-chu')
    chup(p, 'n11-nv-dat-lich', 'nhan-vien/dat-lich.php?q=0905')
    chup(p, 'n12-nv-chon-bac-si', 'benh-nhan/chon-bac-si.php?bn=1&bs=3')
    chon_ngay_con_cho(p); chup(p, 'n13-nv-chon-gio')
    p.locator('a.slot').first.click(); p.fill('#ly-do', 'Đau họng, nghẹt mũi 3 ngày (gọi điện đặt)'); chup(p, 'n14-nv-xac-nhan')
    p.click('form button[type=submit]'); chup(p, 'n15-nv-dat-xong')
    moi = re.search(r'xong=(\d+)', p.url).group(1)
    ghi('KT01', 'Nhân viên đặt lịch hộ bệnh nhân gọi điện', sql(f"SELECT CONCAT(dat_boi,'/',trang_thai) FROM lich_hen WHERE id={moi}") == 'nhan_vien/da_dat',
        f'lịch ghi nhận đặt bởi nhân viên, trạng thái Chưa đến')
    p.goto(G + 'nhan-vien/dat-lich.php'); p.fill('#ho_ten', 'Ngô Thị Thử'); p.fill('#sdt', '0911000111')
    p.click('form.side button[type=submit]'); p.wait_for_load_state('networkidle')
    ghi('KT02', 'Nhân viên thêm bệnh nhân mới đến quầy rồi đặt lịch', 'chon-bac-si.php?bn=' in p.url and 'Ngô Thị Thử' in chu(p),
        'tạo hồ sơ chỉ với họ tên và SĐT, chuyển sang chọn bác sĩ')
    lh = '103'  # lịch hôm nay 09:00 của bệnh nhân Hoàng Văn Nam với BS. Trần Minh Khoa (dữ liệu mẫu)
    chup(p, 'n16-nv-tiep-nhan', f'nhan-vien/tiep-nhan.php?id={lh}')
    p.click('form.main button[type=submit]'); p.wait_for_load_state('networkidle')
    ghi('KT03', 'Nhân viên tiếp nhận bệnh nhân có lịch hẹn hôm nay', sql(f"SELECT trang_thai FROM lich_hen WHERE id={lh}") == 'cho_kham',
        'lịch chuyển sang Chờ khám, vào hàng chờ bác sĩ')
    chup(p, 'n17-nv-lich-hen', 'nhan-vien/lich-hen.php')

    # ---- Bác sĩ
    dn(p, 'bacsi03'); p.goto(G + f'bac-si/ket-qua-kham.php?id={lh}'); p.wait_for_load_state('networkidle')
    ghi('KT04', 'Bác sĩ khác mở hồ sơ bệnh nhân không thuộc hàng chờ của mình', 'bac-si/trang-chu.php' in p.url,
        'bị chuyển về hàng chờ của mình')
    dn(p, 'bacsi01'); chup(p, 'n20-bs-hang-cho')
    p.locator(f'form:has(input[name=id][value="{lh}"]) button').click(); p.wait_for_load_state('networkidle'); chup(p, 'n21-bs-lich-su')
    ghi('KT05', 'Bác sĩ gọi bệnh nhân vào khám', sql(f"SELECT trang_thai FROM lich_hen WHERE id={lh}") == 'dang_kham',
        'trạng thái Đang khám, mở hồ sơ các lần khám trước')
    p.goto(G + f'bac-si/ket-qua-kham.php?id={lh}')
    p.fill('#mach', '82'); p.fill('#nhiet_do', '37,8'); p.fill('#huyet_ap', '120/80'); p.fill('#can_nang', '68')
    p.fill('#ket_qua', 'Họng đỏ, amidan sưng nhẹ, không xuất tiết.'); p.fill('#chan_doan', 'Viêm họng cấp')
    p.fill('#loi_dan', 'Uống nhiều nước ấm, súc họng nước muối.')
    chup(p, 'n22-bs-ghi-ket-qua')

    def them(v, sl, cd):
        p.select_option('#them-thuoc', value=v); p.fill('#them-sl', sl); p.fill('#them-cd', cd); p.click('#nut-them'); p.wait_for_timeout(200)

    them('2', '14', 'Ngày 2 lần, mỗi lần 1 viên, sau ăn'); them('1', '6', 'Uống khi sốt trên 38,5°C')
    them('2', '10', 'Ngày 2 lần')
    ghi('KT06', 'Kê một thuốc hai lần trong cùng đơn', 'đã có trong đơn' in chu(p) and p.locator('#ds-thuoc tr').count() == 2,
        'báo thuốc đã có trong đơn, không thêm dòng thứ hai')
    p.evaluate("(() => { const c = document.querySelector('#phan-don').parentElement; window.scrollTo(0, c.getBoundingClientRect().top + window.scrollY - 130); })()")
    p.fill('#them-sl', ''); p.fill('#them-cd', ''); p.select_option('#them-thuoc', value='')
    p.evaluate("(() => { const x = document.getElementById('loi-them'); x.textContent = ''; x.classList.add('hidden'); })()")
    chup(p, 'n23-bs-ke-don')
    p.click('#nut-luu'); p.wait_for_load_state('networkidle'); chup(p, 'n24-bs-luu-xong')
    so_thuoc = sql(f"SELECT COUNT(*) FROM chi_tiet_don_thuoc ct JOIN don_thuoc d ON d.id = ct.don_thuoc_id JOIN benh_an b ON b.id = d.benh_an_id WHERE b.lich_hen_id={lh}")
    ghi('KT07', 'Bác sĩ lưu bệnh án và đơn thuốc', so_thuoc == '2' and sql(f"SELECT trang_thai FROM lich_hen WHERE id={lh}") == 'da_kham',
        'lịch Đã khám, đơn 2 thuốc được lưu để in cho bệnh nhân')
    ba = sql(f"SELECT id FROM benh_an WHERE lich_hen_id={lh}")
    chup(p, 'n25-bs-lich-lam-viec', 'bac-si/lich-lam-viec.php')
    chup(p, 'n26-phieu-in', f'in-phieu.php?id={ba}', True)
    p.goto(G + 'quan-tri/thuoc.php'); p.wait_for_load_state('networkidle')
    ghi('KT08', 'Bác sĩ mở trang danh mục thuốc của quản trị', 'bac-si/trang-chu.php' in p.url, 'bị chuyển về hàng chờ khám')

    # ---- Quản trị
    dn(p, 'quantri01'); chup(p, 'n40-qt-trang-chu')
    chup(p, 'n46-qt-bac-si', 'quan-tri/bac-si.php')
    chup(p, 'n47-qt-bac-si-form', 'quan-tri/bac-si-form.php?id=1')
    chup(p, 'n48-qt-lich-lam-viec', 'quan-tri/lich-lam-viec.php?bs=1')
    chup(p, 'n35-qt-thuoc', 'quan-tri/thuoc.php')
    p.goto(G + 'quan-tri/thuoc-form.php'); p.fill('#ten', 'Cetirizin'); p.fill('#ham_luong', '10 mg')
    p.select_option('#don_vi', 'Viên'); p.select_option('#nhom', 'Kháng histamin'); p.fill('#lieu_toi_da', '1 viên')
    p.fill('#luu_y', 'Có thể gây buồn ngủ, thận trọng khi lái xe.')
    chup(p, 'n36-qt-thuoc-form'); p.click('form button[type=submit]'); p.wait_for_load_state('networkidle')
    ghi('KT09', 'Quản trị thêm thuốc mới vào danh mục', sql("SELECT ma FROM thuoc WHERE ten='Cetirizin'") == 'TH009',
        'thuốc được cấp mã TH009, bác sĩ chọn được khi kê đơn')
    chup(p, 'n41-qt-tai-khoan', 'quan-tri/tai-khoan.php')
    p.goto(G + 'quan-tri/tai-khoan-form.php'); p.fill('#ho_ten', 'Trần Thị Mai'); p.fill('#ten_dang_nhap', 'nhanvien02')
    p.select_option('#vai_tro', 'NV'); p.fill('#mat_khau', 'matkhau')
    chup(p, 'n42-qt-tai-khoan-form'); p.click('form button[type=submit]'); p.wait_for_load_state('networkidle')
    tk = sql("SELECT id FROM tai_khoan WHERE ten_dang_nhap='nhanvien02'")
    p.goto(G + 'quan-tri/tai-khoan.php'); p.locator(f'form:has(input[name=id][value="{tk}"]) button').click(); p.wait_for_load_state('networkidle')
    p2 = ctx.new_page(); p2.goto(G + 'dang-xuat.php'); p2.goto(G + 'dang-nhap.php'); p2.fill('#tk', 'nhanvien02'); p2.fill('#mk', 'matkhau')
    p2.click('form.auth-form button[type=submit]'); p2.wait_for_load_state('networkidle')
    ghi('KT10', 'Quản trị tạo tài khoản nhân viên rồi khóa; tài khoản bị khóa đăng nhập', 'đã bị khóa' in chu(p2),
        'báo “Tài khoản đã bị khóa”, không vào được hệ thống')
    p2.close()
    dn(p, 'quantri01')
    hom = datetime.date.today()
    chup(p, 'n43-qt-bao-cao', 'quan-tri/bao-cao.php?loai=thuoc')
    p.goto(G + f'quan-tri/ket-qua-bao-cao.php?loai=thuoc&tu={hom:%Y-%m-01}&den={hom.isoformat()}'); chup(p, 'n49-qt-bao-cao-thuoc')
    ghi('KT11', 'Báo cáo thuốc đã kê tháng này', 'Số lượng thuốc đã kê' in chu(p), 'liệt kê số lượng từng thuốc đã kê và số đơn trong kỳ')
    p.goto(G + 'quan-tri/ket-qua-bao-cao.php?loai=luot-kham&tu=2026-10-01&den=2026-10-31'); chup(p, 'n45-qt-bao-cao-luot-kham')
    ghi('KT12', 'Báo cáo lượt khám tháng 10/2026', 'Lượt khám theo ngày' in chu(p), 'biểu đồ lượt khám theo ngày, bảng theo bác sĩ')
    p.goto(G + 'quan-tri/ket-qua-bao-cao.php?loai=luot-kham&tu=2026-10-31&den=2026-10-01&tu_form=1'); p.wait_for_load_state('networkidle')
    ghi('KT13', 'Chọn ngày bắt đầu sau ngày kết thúc', 'loi=nguoc' in p.url, 'quay lại màn chọn báo cáo, báo chọn lại khoảng thời gian')
    p.goto(G + 'nhan-vien/trang-chu.php'); p.wait_for_load_state('networkidle')
    ghi('KT14', 'Quản trị mở trang của nhân viên', 'quan-tri/trang-chu.php' in p.url, 'bị chuyển về trang chủ quản trị')

    # ---- Bệnh nhân
    dn(p, '0905447812'); chup(p, 'n03-bn-trang-chu')
    p.goto(G + 'benh-nhan/ket-qua.php'); chup(p, 'n50-bn-ket-qua')
    ghi('KT15', 'Bệnh nhân xem kết quả khám và đơn thuốc', 'Mua thuốc theo đơn tại nhà thuốc' in chu(p) and 'Viêm họng cấp' in chu(p),
        'thấy chẩn đoán, đơn thuốc và lời nhắc mua thuốc theo đơn ở nhà thuốc')
    chup(p, 'n04-bn-chon-bac-si', 'benh-nhan/chon-bac-si.php?bs=3')
    chon_ngay_con_cho(p, 1); chup(p, 'n05-bn-chon-gio')
    p.locator('a.slot').first.click(); p.fill('#ly-do', 'Nghẹt mũi kéo dài'); chup(p, 'n06-bn-xac-nhan')
    chup(p, 'n07-bn-lich-hen', 'benh-nhan/lich-hen.php')
    huy = sql("SELECT id FROM lich_hen WHERE benh_nhan_id=1 AND trang_thai='da_dat' AND TIMESTAMP(ngay,gio) > NOW() ORDER BY ngay, gio LIMIT 1")
    chup(p, 'n08-bn-huy-lich', f'benh-nhan/huy-lich.php?id={huy}')
    b.close()

json.dump(kq, open(RA + 'kiem-thu.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
