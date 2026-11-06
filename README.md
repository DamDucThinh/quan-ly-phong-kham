# Hệ thống quản lý phòng khám NichegHopital

Đồ án PPTK, Đề tài 4: Hệ thống quản lý phòng khám. Kho gồm tài liệu phân tích (BFD, DFD, ERD, userflow), báo cáo và phần mềm viết bằng PHP + MySQL cho phòng khám quy mô nhỏ, với bốn vai trò: Bệnh nhân, Nhân viên, Bác sĩ và Quản trị. Phòng khám không có quầy thuốc: bác sĩ kê đơn, bệnh nhân mua thuốc theo đơn ở nhà thuốc bên ngoài.

## Nội dung

| Thư mục / file | Nội dung |
|---|---|
| `03_BaoCaoDAHP2_NichegHopitalfinal.docx` | Báo cáo tiểu luận (bản 4 vai trò) |
| `ban-nang-cao-4-vai-tro/` | Sơ đồ bản 4 vai trò: BFD, DFD mức 0 (theo sơ đồ giảng viên vẽ), mức 1, mức 2, ERD, lược đồ CSDL, userflow (ảnh PNG 01–15) |
| `nguon-4-vai-tro/` | File nguồn sơ đồ (`ve-*.py`, `.gv`, `.svg`), ảnh giao diện, công cụ dựng báo cáo và kịch bản kiểm thử |
| `phan-mem-4-vai-tro/` | Phần mềm bản 4 vai trò (xem [phan-mem-4-vai-tro/README.md](phan-mem-4-vai-tro/README.md)) |
| `De-tai-4-BFD-DFD-quan-ly-phong-kham.docx` | Báo cáo đề tài bản đầu |
| `ban-don-gian/` | BFD và DFD bản đơn giản (ảnh PNG, file nguồn trong `ban-don-gian/nguon/`) |
| `ban-nang-cao/`, `nguon/` | BFD, DFD và userflow bản trước (mô hình 2 người dùng) và file nguồn |
| `phan-mem/` | Phần mềm bản trước, giữ để tham khảo |
| `tong-ket-phien-lam-viec.md` | Ghi chú các phiên làm việc |

### Sơ đồ bản 4 vai trò

- `01` BFD: 5 chức năng (quản trị hệ thống, quản lý lịch hẹn, quản lý hồ sơ bệnh án, kê đơn thuốc, báo cáo thống kê), 16 chức năng con.
- `02`–`08` DFD: mức ngữ cảnh, mức 1 và mức 2 cho từng chức năng.
- `09`–`10` ERD và lược đồ CSDL quan hệ.
- `11`–`15` Userflow: tổng quan và 4 luồng (quản trị hệ thống; đặt, đổi, hủy lịch hẹn; tiếp nhận, khám, kê đơn; báo cáo thống kê), màn hình [1]–[27].

## Phần mềm

Bệnh nhân tự đặt, đổi, hủy lịch hẹn và xem kết quả khám, đơn thuốc; nhân viên đặt lịch hộ và tiếp nhận; bác sĩ khám, ghi kết quả, kê đơn; quản trị quản lý bác sĩ, lịch làm việc, danh mục thuốc, tài khoản và xem báo cáo.

Chạy thử (cần PHP 8.1+ và MySQL 8):

```
cd phan-mem-4-vai-tro
php database/cai-dat.php
php -S localhost:8000 -t public
```

Mở http://localhost:8000. Mật khẩu mọi tài khoản mẫu là `matkhau`: `quantri01` (Quản trị), `nhanvien01` (Nhân viên), `bacsi01`–`bacsi05` (Bác sĩ), `0905447812` (Bệnh nhân).

Hướng dẫn cài bằng XAMPP, danh sách màn hình và quy tắc nghiệp vụ: xem [phan-mem-4-vai-tro/README.md](phan-mem-4-vai-tro/README.md).
