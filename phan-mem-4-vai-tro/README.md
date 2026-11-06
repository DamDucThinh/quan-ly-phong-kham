# Hệ thống quản lý phòng khám NichegHopital (bản 4 vai trò)

Phần mềm cho phòng khám đa khoa nhỏ, viết bằng PHP thuần và MySQL. Phòng khám không có quầy thuốc: bác sĩ kê đơn, bệnh nhân mang đơn ra nhà thuốc bên ngoài mua. Bốn vai trò:

- **Bệnh nhân**: tự đăng ký bằng số điện thoại, đặt, đổi, hủy lịch hẹn, xem kết quả khám và đơn thuốc.
- **Nhân viên**: đặt, đổi, hủy lịch hộ bệnh nhân gọi điện hoặc đến quầy, tiếp nhận bệnh nhân (có hẹn và không hẹn trước), xem danh sách lịch hẹn.
- **Bác sĩ**: xem hàng chờ khám của mình, gọi bệnh nhân vào khám, xem lịch sử khám, ghi kết quả khám, kê đơn, in phiếu; xem lịch làm việc.
- **Quản trị**: quản lý bác sĩ, lịch làm việc, danh mục thuốc, tài khoản người dùng (tạo, sửa, khóa), xem báo cáo lượt khám và thuốc đã kê.

Phần mềm bản trước nằm ở thư mục `../phan-mem/`, dùng CSDL `phong_kham`. Bản này dùng CSDL riêng `phong_kham_4_vai_tro` nên hai bản chạy song song được.

## Cần có

- PHP 8.1 trở lên, có bật `pdo_mysql`.
- MySQL 8 (hoặc MariaDB 10.5 trở lên).

## Cài đặt

1. Sửa thông tin kết nối MySQL, hotline, địa chỉ phòng khám trong `includes/cau-hinh.php`.
2. Tạo CSDL và nạp dữ liệu mẫu, chọn một trong hai cách:
   - Dòng lệnh: `php database/cai-dat.php` (tạo CSDL theo tên `CSDL_TEN` trong `cau-hinh.php`).
   - phpMyAdmin: import `database/schema.sql`, rồi import `database/du-lieu-mau.sql`. Hai file này ghi tên CSDL là `phong_kham`; khi import tay, sửa các dòng `CREATE DATABASE` / `USE` thành `phong_kham_4_vai_tro` cho khớp `cau-hinh.php`.

   Cài lại sẽ xóa toàn bộ dữ liệu cũ. Ngày trong dữ liệu mẫu tính theo ngày cài, nên cài lại vào hôm demo thì "lịch hẹn hôm nay" mới có dữ liệu.

## Chạy

```
php -S localhost:8000 -t public
```

Mở http://localhost:8000.

Dùng XAMPP: chép cả thư mục `phan-mem-4-vai-tro` vào `htdocs`, mở http://localhost/phan-mem-4-vai-tro/public/.

## Tài khoản mẫu

Mật khẩu của mọi tài khoản mẫu là `matkhau`.

| Vai trò | Tên đăng nhập | Người dùng |
|---|---|---|
| Quản trị | `quantri01` | Nguyễn Thu Hà |
| Nhân viên | `nhanvien01` | Phan Thị Thảo |
| Bác sĩ | `bacsi01` … `bacsi05` | BS. Trần Minh Khoa, Lê Thị Hồng, Phạm Quốc Bảo, Võ Ngọc Anh, Đỗ Văn Hải |
| Bệnh nhân | `0905447812` | Hoàng Văn Nam |
| Bệnh nhân | `0932118605` | Châu Ngọc Mai (chưa có mã bệnh nhân) |

## Màn hình

Số màn hình theo userflow ở `../ban-nang-cao-4-vai-tro/11`–`15`.

| Số | Vai trò | Màn hình | File | Chức năng (BFD) |
|---|---|---|---|---|
| [1] | Chung | Đăng nhập | `public/dang-nhap.php` | |
| [2] | Bệnh nhân | Đăng ký | `public/dang-ky.php` | 1.4 |
| [3] | Bệnh nhân | Trang chủ | `public/benh-nhan/trang-chu.php` | |
| [4] | Nhân viên | Trang chủ | `public/nhan-vien/trang-chu.php` | |
| [5] | Bác sĩ | Hàng chờ khám | `public/bac-si/trang-chu.php` | 3.2 |
| [6] | Quản trị | Trang chủ (tình hình hôm nay) | `public/quan-tri/trang-chu.php` | |
| [7], [8] | Quản trị | Danh sách bác sĩ, thêm/sửa | `public/quan-tri/bac-si.php`, `bac-si-form.php` | 1.1 |
| [9] | Quản trị | Lịch làm việc bác sĩ | `public/quan-tri/lich-lam-viec.php` | 1.2 |
| [10], [11] | Quản trị | Danh mục thuốc, thêm/sửa | `public/quan-tri/thuoc.php`, `thuoc-form.php` | 1.3 |
| [12], [13] | Quản trị | Tài khoản người dùng, thêm/sửa | `public/quan-tri/tai-khoan.php`, `tai-khoan-form.php` | 1.4 |
| [14] | Nhân viên | Đặt lịch hộ (tìm, thêm bệnh nhân) | `public/nhan-vien/dat-lich.php` | 2.1 |
| [15] | Bệnh nhân, Nhân viên | Chọn bác sĩ, ngày khám | `public/benh-nhan/chon-bac-si.php` | 2.1 |
| [16] | Bệnh nhân, Nhân viên | Chọn khung giờ trống | `public/benh-nhan/chon-gio.php` | 2.2 |
| [17] | Bệnh nhân, Nhân viên | Xác nhận lịch hẹn | `public/benh-nhan/xac-nhan.php` | 2.3 |
| [18] | Bệnh nhân | Lịch hẹn của tôi | `public/benh-nhan/lich-hen.php` | 2.4 |
| [19] | Bệnh nhân, Nhân viên | Xác nhận hủy lịch hẹn | `public/benh-nhan/huy-lich.php` | 2.4 |
| [20] | Bệnh nhân | Kết quả khám, đơn thuốc | `public/benh-nhan/ket-qua.php` | 3.3, 4.3 |
| [21] | Nhân viên | Tiếp nhận bệnh nhân | `public/nhan-vien/tiep-nhan.php` | 3.1 |
| [22] | Nhân viên | Danh sách lịch hẹn | `public/nhan-vien/lich-hen.php` | 2.4, 3.1 |
| [23] | Bác sĩ | Hồ sơ bệnh án các lần trước | `public/bac-si/lich-su-kham.php` | 3.2 |
| [24] | Bác sĩ | Ghi kết quả khám và kê đơn | `public/bac-si/ket-qua-kham.php` | 3.3, 4.1–4.3 |
| [25] | Bác sĩ | Lịch làm việc của tôi | `public/bac-si/lich-lam-viec.php` | 1.2 |
| [26], [27] | Quản trị | Chọn loại báo cáo, kết quả | `public/quan-tri/bao-cao.php`, `ket-qua-bao-cao.php` | 5.1, 5.2 |
| – | Chung | Bản in phiếu kết quả, đơn thuốc | `public/in-phieu.php` | 3.3, 4.3 |

## Cơ sở dữ liệu

| Kho dữ liệu (DFD) | Bảng |
|---|---|
| D1 Hồ sơ bác sĩ | `bac_si`, `ca_lam_viec` |
| D2 Hồ sơ bệnh nhân | `benh_nhan` |
| D3 Danh mục thuốc | `thuoc` |
| D4 Lịch hẹn | `lich_hen` |
| D5 Hồ sơ bệnh án | `benh_an` |
| D6 Đơn thuốc | `don_thuoc`, `chi_tiet_don_thuoc` |
| D7 Tài khoản người dùng | `tai_khoan` (vai trò `QT`, `NV`, `BS`, `BN`) |
| Thông báo cho bệnh nhân | `thong_bao` |

## Quy tắc nghiệp vụ chính

- Khung giờ khám dài 30 phút, mỗi khung nhận 1 bệnh nhân, cắt từ ca làm việc của bác sĩ. Đặt được lịch trong 7 ngày tới, không đặt giờ đã qua. CSDL có khóa chặn hai lịch cùng giữ một khung giờ của một bác sĩ.
- Nhân viên đặt lịch hộ qua cùng quy trình chọn bác sĩ, chọn giờ, xác nhận như bệnh nhân; lịch được ghi là "Nhân viên đặt hộ".
- Trạng thái lịch hẹn: Chưa đến → Chờ khám (nhân viên tiếp nhận) → Đang khám (bác sĩ gọi vào khám) → Đã khám (bác sĩ lưu bệnh án); Đã hủy.
- Bác sĩ chỉ xem, khám bệnh nhân trong hàng chờ của mình.
- Kiểm tra đơn thuốc khi kê: thuốc có trong danh mục, không trùng trong đơn, số lượng nguyên lớn hơn 0. Bệnh án và đơn thuốc lưu trong một giao dịch. Phòng khám không quản lý tồn kho; đơn được in để bệnh nhân mua thuốc ở ngoài.
- Tài khoản nhân viên, bác sĩ, quản trị do quản trị tạo; tài khoản bị khóa không đăng nhập được.
- Thông báo cho bệnh nhân hiện trên trang chủ bệnh nhân, không gửi SMS hay email. Tải PDF và in dùng chức năng in của trình duyệt.
