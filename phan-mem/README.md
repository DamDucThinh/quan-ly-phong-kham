# Hệ thống quản lý phòng khám NichegHopital

Phần mềm cho phòng khám quy mô nhỏ, viết bằng PHP thuần và MySQL. Hai người dùng:

- **Quản lý phòng khám**: tiếp nhận bệnh nhân, ghi kết quả khám, kê đơn, quản lý bác sĩ, lịch làm việc, danh mục thuốc, xem báo cáo.
- **Bệnh nhân**: tự đăng ký bằng số điện thoại, đặt, đổi, hủy lịch hẹn, xem kết quả khám và đơn thuốc.

Giao diện làm theo bản thiết kế "Quản lý phòng khám — bản nâng cao", số màn hình [1]–[20] theo userflow.

## Cần có

- PHP 8.1 trở lên, có bật `pdo_mysql`.
- MySQL 8 (hoặc MariaDB 10.5 trở lên).

## Cài đặt

1. Sửa thông tin kết nối MySQL, hotline, địa chỉ phòng khám trong `includes/cau-hinh.php`.
2. Tạo CSDL và nạp dữ liệu mẫu, chọn một trong hai cách:
   - Dòng lệnh: `php database/cai-dat.php`
   - phpMyAdmin: import `database/schema.sql`, rồi import `database/du-lieu-mau.sql`.

   Cài lại sẽ xóa toàn bộ dữ liệu cũ. Ngày trong dữ liệu mẫu tính theo ngày cài, nên cài lại vào hôm demo thì "lịch hẹn hôm nay" mới có dữ liệu.

## Chạy

```
php -S localhost:8000 -t public
```

Mở http://localhost:8000.

Dùng XAMPP: chép cả thư mục `phan-mem` vào `htdocs`, mở http://localhost/phan-mem/public/.

## Tài khoản mẫu

| Vai trò | Tên đăng nhập | Mật khẩu |
|---|---|---|
| Quản lý phòng khám | `quanly01` | `matkhau` |
| Bệnh nhân (Hoàng Văn Nam) | `0905447812` | `matkhau` |
| Bệnh nhân (Châu Ngọc Mai, chưa có hồ sơ) | `0932118605` | `matkhau` |

## Màn hình

| Màn | File | Chức năng (BFD) |
|---|---|---|
| [1] Đăng nhập | `public/dang-nhap.php` | |
| Đăng ký bệnh nhân | `public/dang-ky.php` | bổ sung, thiết kế chưa có |
| [2] Trang chủ Bệnh nhân | `public/benh-nhan/trang-chu.php` | |
| [9] Chọn bác sĩ, ngày khám | `public/benh-nhan/chon-bac-si.php` | 2.1 |
| [10] Chọn khung giờ trống | `public/benh-nhan/chon-gio.php` | 2.2 |
| [11] Xác nhận lịch hẹn | `public/benh-nhan/xac-nhan.php` | 2.3 |
| [12] Lịch hẹn của tôi | `public/benh-nhan/lich-hen.php` | 2.4 |
| [13] Xác nhận hủy lịch hẹn | `public/benh-nhan/huy-lich.php` | 2.4 |
| Kết quả khám | `public/benh-nhan/ket-qua.php` | |
| [3] Trang chủ Quản lý | `public/quan-ly/trang-chu.php` | |
| [4] Danh sách bác sĩ | `public/quan-ly/bac-si.php` | 1.1 |
| [5] Thêm / sửa bác sĩ | `public/quan-ly/bac-si-form.php` | 1.1 |
| [6] Lịch làm việc bác sĩ | `public/quan-ly/lich-lam-viec.php` | 1.2 |
| [7] Danh mục thuốc | `public/quan-ly/thuoc.php` | 1.3 |
| [8] Thêm / sửa thuốc | `public/quan-ly/thuoc-form.php` | 1.3 |
| [14] Tiếp nhận bệnh nhân | `public/quan-ly/tiep-nhan.php` | 3.1 |
| [15] Danh sách lịch hẹn (từ hôm nay trở đi) | `public/quan-ly/lich-hen.php` | |
| [16] Hồ sơ bệnh án các lần trước | `public/quan-ly/lich-su-kham.php` | 3.2 |
| [17] Ghi kết quả khám và kê đơn | `public/quan-ly/ket-qua-kham.php` | 3.3, 4.1, 4.2, 4.3 |
| [19] Chọn loại báo cáo | `public/quan-ly/bao-cao.php` | 5 |
| [20] Kết quả thống kê | `public/quan-ly/ket-qua-bao-cao.php` | 5.1, 5.2 |
| Bản in phiếu kết quả, đơn thuốc | `public/in-phieu.php` | 3.3, 4.3 |

Màn [18] Lập đơn thuốc được gộp vào màn [17].

## Cơ sở dữ liệu

| Kho dữ liệu (DFD) | Bảng |
|---|---|
| D1 Hồ sơ bác sĩ | `bac_si`, `ca_lam_viec` |
| D2 Hồ sơ bệnh nhân | `benh_nhan`, `tai_khoan` |
| D3 Danh mục thuốc | `thuoc` |
| D4 Lịch hẹn | `lich_hen` |
| D5 Hồ sơ bệnh án | `benh_an` |
| D6 Đơn thuốc | `don_thuoc`, `chi_tiet_don_thuoc` |
| Thông báo cho bệnh nhân | `thong_bao` |

## Quy tắc nghiệp vụ chính

- Khung giờ khám dài 30 phút, mỗi khung nhận 1 bệnh nhân, cắt từ ca làm việc của bác sĩ. Bệnh nhân đặt được lịch trong 7 ngày tới, không đặt giờ đã qua. Hai giá trị này đổi được trong `includes/cau-hinh.php`.
- Khi bệnh nhân bấm "Đặt lịch", hệ thống kiểm tra lại khung giờ; CSDL có khóa chặn hai lịch cùng giữ một khung giờ của một bác sĩ.
- Trạng thái lịch hẹn: Chưa đến, Chờ khám (đã tiếp nhận), Đang khám (đã gọi vào khám), Đã khám (đã lưu bệnh án), Đã hủy.
- Bệnh nhân không có lịch hẹn hôm nay thì quản lý tạo bệnh nhân mới ở màn [14] và chọn bác sĩ khám ngay.
- Kiểm tra đơn thuốc: thuốc phải có trong danh mục, không trùng trong cùng đơn, số lượng là số nguyên lớn hơn 0.
- Thông báo cho bệnh nhân hiện trên trang chủ bệnh nhân, không gửi SMS hay email.
- Tải PDF và in dùng chức năng in của trình duyệt (chọn "Lưu thành PDF").
