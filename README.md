# Hệ thống quản lý phòng khám NichegHopital

Đồ án PPTK, Đề tài 4: Hệ thống quản lý phòng khám. Kho gồm tài liệu phân tích (BFD, DFD, userflow) và phần mềm viết bằng PHP + MySQL cho phòng khám quy mô nhỏ, với hai người dùng là Quản lý phòng khám và Bệnh nhân.

## Nội dung

| Thư mục / file | Nội dung |
|---|---|
| `De-tai-4-BFD-DFD-quan-ly-phong-kham.docx` | Báo cáo đề tài |
| `ban-don-gian/` | BFD và DFD bản đơn giản (ảnh PNG, file nguồn trong `ban-don-gian/nguon/`) |
| `ban-nang-cao/` | BFD, DFD và userflow bản nâng cao (ảnh PNG 01–13) |
| `nguon/` | File nguồn Graphviz (`.gv`) và `.svg` của bản nâng cao |
| `phan-mem/` | Phần mềm quản lý phòng khám |

### Sơ đồ bản nâng cao

- `01` BFD: sơ đồ phân rã chức năng (5 chức năng, 16 chức năng con).
- `02`–`08` DFD: mức khung cảnh, mức đỉnh và mức dưới đỉnh cho từng chức năng.
- `09`–`13` Userflow: tổng quan và 4 luồng (quản lý danh mục; đặt, đổi, hủy lịch hẹn; tiếp nhận, khám, kê đơn; báo cáo thống kê).

## Phần mềm

20 màn hình theo userflow: bệnh nhân đăng ký bằng số điện thoại, đặt, đổi, hủy lịch hẹn và xem kết quả khám; quản lý phòng khám tiếp nhận bệnh nhân, ghi kết quả khám, kê đơn, quản lý bác sĩ, lịch làm việc, danh mục thuốc và xem báo cáo.

Chạy thử (cần PHP 8.1+ và MySQL 8):

```
cd phan-mem
php database/cai-dat.php
php -S localhost:8000 -t public
```

Mở http://localhost:8000 và đăng nhập bằng `quanly01` / `matkhau` (Quản lý phòng khám) hoặc `0905447812` / `matkhau` (Bệnh nhân).

Hướng dẫn cài bằng XAMPP, danh sách màn hình và quy tắc nghiệp vụ: xem [phan-mem/README.md](phan-mem/README.md).
