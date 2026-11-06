# Tổng kết phiên làm việc — Đồ án PPTK (22/10/2026)

File này để tiếp tục ở phiên sau: đọc hết trước khi làm gì thêm.

## 1. Dự án

**Đồ án PPTK, Đề tài 4: Hệ thống quản lý phòng khám.** Tên phòng khám: **NichegHopital** (giữ nguyên cách viết này, người dùng đã xác nhận).

Mô hình phòng khám **quy mô nhỏ, chỉ 2 người dùng phần mềm**: **Quản lý phòng khám** và **Bệnh nhân**. Bác sĩ không dùng phần mềm: bác sĩ khám và chỉ định thuốc, quản lý là người nhập vào hệ thống.

| Thư mục / file | Nội dung |
|---|---|
| `De-tai-4-BFD-DFD-quan-ly-phong-kham.docx` | Báo cáo đề tài |
| `ban-don-gian/` | BFD, DFD bản đơn giản (PNG + `nguon/`) |
| `ban-nang-cao/` | BFD, DFD (01–08) và **userflow (09–13)** bản nâng cao, dạng PNG |
| `nguon/` | File nguồn Graphviz `.gv` + `.svg` của bản nâng cao (gồm cả userflow 09–13) |
| `phan-mem/` | Phần mềm PHP + MySQL (xem mục 3) |
| `README.md` | Trang giới thiệu kho GitHub |

## 2. Userflow bản nâng cao (đã xong)

- 5 sơ đồ Graphviz, cùng kiểu với BFD/DFD (đen trắng, Arial, PNG 170 dpi): `09-userflow-tong-quan`, `10-…-luong-1-quan-ly-danh-muc`, `11-…-luong-2-dat-doi-huy-lich-hen`, `12-…-luong-3-tiep-nhan-kham-ke-don` (3 cột), `13-…-luong-4-bao-cao-thong-ke`.
- Đã làm lại cho 2 người dùng. Màn hình đánh số **[1]–[20]**: [1] đăng nhập, [2] trang chủ BN, [3] trang chủ QL, [4]–[8] danh mục, [9]–[13] đặt/đổi/hủy lịch, [14]–[18] tiếp nhận/khám/kê đơn, [19]–[20] báo cáo.
- Bổ sung ngoài BFD/DFD: bước đăng nhập, kiểm tra lại khung giờ khi xác nhận, nhánh báo lỗi nhập liệu, câu hỏi "Bác sĩ có chỉ định thuốc?".
- Xuất lại ảnh: `dot -Tpng -Gdpi=170 nguon/<file>.gv -o ban-nang-cao/<file>.png` (và `-Tsvg` vào `nguon/`).

## 3. Phần mềm `phan-mem/`

Code theo bản thiết kế (Design canvas): https://claude.ai/artifact/9KSXStuU1GrEmRe76V5dV2 — "Quản lý phòng khám — bản nâng cao", font Lexend, màu chính `#0E7C70`.

### Quyết định người dùng đã chốt

| Vấn đề | Đã chốt |
|---|---|
| Công nghệ | **PHP thuần + MySQL**, không framework, mỗi màn một file |
| Màn [17] và [18] | **Gộp kê đơn vào [17]**, bỏ màn [18] |
| Kiểm tra thuốc (CN 4.2) | **Chỉ**: có trong danh mục, không trùng trong đơn, số lượng nguyên > 0 (không cảnh báo chống chỉ định) |
| Tài khoản bệnh nhân | **Tự đăng ký bằng SĐT**; nếu SĐT đã có hồ sơ do phòng khám tạo thì gắn vào hồ sơ đó |
| Hotline, địa chỉ | Chưa có, để `[SỐ ĐIỆN THOẠI]`, `[ĐỊA CHỈ PHÒNG KHÁM]` trong `includes/cau-hinh.php` |
| Chú thích "Màn hình [n] · CN x.y" | **Đã xóa hẳn** khỏi giao diện |
| Mô tả xám dưới tiêu đề trang | **Đã xóa**; chỉ giữ dòng dữ liệu ở [17] (thông tin bệnh nhân) và [20] (khoảng ngày) |
| Danh sách lịch hẹn [15] | Tên **"Danh sách lịch hẹn"**, hiện **mọi lịch từ hôm nay trở đi**, gần nhất ở trên, chia nhóm theo ngày, **phân trang vừa màn hình** (số trang + mũi tên) |
| In / tải PDF phiếu | **In ngay trên trang** (khung ẩn), không chuyển sang trang phiếu |
| Thanh điều hướng | **Dính trên cùng** khi cuộn |

### Quy tắc nghiệp vụ đang chạy

- Khung giờ 30 phút, 1 bệnh nhân/khung, cắt từ ca làm việc [6]; đặt trước tối đa 7 ngày; không đặt giờ đã qua (`PHUT_MOI_KHUNG`, `SO_NGAY_DAT_TRUOC` trong `cau-hinh.php`).
- **Chống đặt trùng** (đã kiểm tra kỹ, hoạt động): (1) màn [10] gạch ô đã có người đặt; (2) bấm "Đặt lịch" kiểm tra lại; (3) CSDL có khóa `uq_khung_gio (bac_si_id, ngay, gio, giu_cho)`, cột sinh `giu_cho` = NULL khi lịch đã hủy hoặc khám vãng lai. Hủy lịch thì khung giờ được trả lại.
- Trạng thái lịch hẹn: `da_dat` (Chưa đến) → `cho_kham` (tiếp nhận) → `dang_kham` (gọi vào khám) → `da_kham` (lưu bệnh án); `da_huy`.
- [14]: bệnh nhân không có lịch hôm nay → "Tạo bệnh nhân mới" + chọn bác sĩ khám ngay (lượt vãng lai, không chiếm khung giờ). Khác userflow, làm theo thiết kế.
- Thông báo cho bệnh nhân chỉ hiện trong ứng dụng (trang chủ BN), không SMS/email. Quên mật khẩu → hướng dẫn gọi hotline.
- Bác sĩ, thuốc chỉ thêm/sửa, không xóa (theo thiết kế).

### Cấu trúc và cách chạy

```
phan-mem/
  includes/   cau-hinh.php, khoi-dong.php, csdl.php, tien-ich.php, xac-thuc.php, nghiep-vu.php, giao-dien.php
  database/   schema.sql, du-lieu-mau.sql, cai-dat.php
  public/     dang-nhap.php, dang-ky.php, in-phieu.php, assets/app.css, assets/app.js,
              benh-nhan/*.php, quan-ly/*.php
```

- Chạy: `cd phan-mem && php -S localhost:8000 -t public` → http://localhost:8000
- Tài khoản: `quanly01`/`matkhau` (QL, Nguyễn Thu Hà), `0905447812`/`matkhau` (BN Hoàng Văn Nam), `0932118605`/`matkhau` (BN Châu Ngọc Mai, chưa có hồ sơ).
- Bảng CSDL ↔ kho DFD: D1 `bac_si`, `ca_lam_viec` · D2 `benh_nhan`, `tai_khoan` · D3 `thuoc` · D4 `lich_hen` · D5 `benh_an` · D6 `don_thuoc`, `chi_tiet_don_thuoc` · thêm `thong_bao`.
- Máy: PHP 8.5 (có pdo_mysql), MySQL chạy bằng Homebrew, root không mật khẩu, CSDL `phong_kham`.
- Đặt tên trong code: hàm/biến tiếng Việt không dấu (`truy_van`, `mot_dong`, `chuyen_huong`, `kiem_tra_lich_trong`…), JS camelCase tiếng Việt (`inPhieu`, `chiaTrang`), comment tiếng Việt.

### ⚠ Cẩn thận

- **`php database/cai-dat.php` XÓA SẠCH CSDL rồi nạp lại dữ liệu mẫu.** CSDL hiện có dữ liệu người dùng tự thao tác (vd lịch #5095: Hoàng Văn Nam – BS. Đỗ Văn Hải 10:00 27/10/2026; lịch #111 ngày 26/10 đã hủy; lịch #103 hôm 22/10 đang "Chờ khám"). **Hỏi người dùng trước khi cài lại.**
- Kiểm thử có ghi dữ liệu (đặt lịch, tiếp nhận, lưu bệnh án) sẽ làm thay đổi dữ liệu thật trên CSDL đó. Nếu chỉ cần kiểm tra trên server người dùng đang chạy thì chỉ xem (GET), hoặc thử trong giao dịch MySQL rồi `ROLLBACK`.
- Script kiểm thử tự động của phiên này nằm trong thư mục tạm, **sẽ không còn ở phiên sau**. Cách đã dùng: curl đăng nhập + gọi từng màn (kiểm mã HTTP, không có Warning/Fatal), Playwright (Python, Chromium có sẵn) để chụp màn hình, kiểm phân trang và in.
- Người dùng có vẻ dùng **Safari trên macOS**: sau khi sửa CSS/JS nhắc bấm **Cmd+Shift+R**. Phần in PDF chưa được thử trực tiếp trên Safari.

## 4. GitHub

- Kho **công khai**: https://github.com/DamDucThinh/quan-ly-phong-kham (nhánh `main`, đăng nhập `gh` bằng tài khoản DamDucThinh).
- Commit: `9aa479d` (bản đầu toàn bộ đồ án), `6ff8527` (danh sách lịch hẹn + phân trang). File này chưa được đẩy lên.
- Git của thư mục dùng tên `DamDucThinh` và email ẩn danh `90485169+DamDucThinh@users.noreply.github.com` (đặt riêng cho kho, không đổi cấu hình git chung).
- Cập nhật: `git add -A && git commit -m "..." && git push`. Commit kết thúc bằng dòng `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
- GitHub chỉ lưu mã, không chạy PHP/MySQL; muốn chạy trực tuyến cần hosting PHP + MySQL.

## 5. Việc còn dở / có thể làm tiếp

1. **DFD chưa khớp mô hình 2 người dùng**: DFD 02–08 vẫn vẽ Bác sĩ là tác nhân ngoài. Đã nêu 2 hướng (giữ và giải thích, hoặc chuyển luồng của Bác sĩ sang Quản lý phòng khám); người dùng chưa chọn.
2. Điền hotline, địa chỉ thật vào `includes/cau-hinh.php`.
3. Danh sách lịch hẹn đang hiện cả lịch đã hủy (nhãn đỏ) — đã hỏi có muốn ẩn không, chưa có trả lời.
4. Chưa chèn ảnh userflow 09–13 vào file Word đề tài (đã đề nghị, chưa làm).
5. Hướng dẫn đưa phần mềm lên hosting PHP nếu cần link chạy trực tuyến.
6. Chưa có: đặt lại mật khẩu, xóa bác sĩ/thuốc, chặn một bệnh nhân đặt nhiều lịch trùng giờ với nhiều bác sĩ.

## 6. Cách làm việc người dùng muốn

- Trao đổi bằng tiếng Việt, ngắn gọn.
- **Chỗ nào chưa rõ thì hỏi, người dùng xác nhận xong mới làm tiếp** (người dùng dặn rõ khi bắt đầu code). Hay dùng câu hỏi có lựa chọn để người dùng chọn nhanh.
- Thao tác ra bên ngoài (đẩy GitHub…) chỉ làm khi người dùng yêu cầu.

## 7. Phiên 23/10/2026: báo cáo, slide, video

| Kết quả | Ghi chú |
|---|---|
| `03_BaoCaoDAHP2_NichegHopital.docx` | Báo cáo theo mẫu `03_MauBaoCaoDAHP2.docx` (mẫu giữ nguyên). ~63 trang: Mở đầu, 3 chương, Kết luận, Tài liệu tham khảo, Phụ lục; 41 hình, 18 bảng; mục lục, danh mục hình/bảng là trường Word (`updateFields` bật: Word hỏi cập nhật khi mở, bấm Yes để số trang khớp). Bìa, mã SV, lớp, GVHD để `[...]` cho người dùng tự điền; nhật ký chỉ ghi các mốc có bằng chứng (ngày tạo file, commit). |
| `Slide-thuyet-trinh-NichegHopital.pptx` | 16 slide, có ghi chú thuyết trình từng slide, phông Arial, màu #0E7C70 / #0B4F48 / điểm nhấn #F2B154. |
| `Video-gioi-thieu-NichegHopital.mp4` + `video-gioi-thieu/` | Video ~100 s dựng bằng HyperFrames, kể một ca khám của BN Hoàng Văn Nam. Sửa kịch bản trong `video-gioi-thieu/tao-canh.py` rồi chạy `python3 tao-canh.py`, `npx hyperframes check`, `npx hyperframes render`. Không nhạc nền (máy chưa có nguồn nhạc miễn phí), chỉ tiếng click/whoosh. |
| DFD 02–07 vẽ lại | Người dùng chọn "vẽ lại": mọi luồng của Bác sĩ chuyển sang Quản lý phòng khám. 02, 04–07 sửa `.gv`; 03 là SVG vẽ tay (không có `.gv`), sửa thẳng SVG rồi `rsvg-convert -z 1.6`. 05–07 đổi sang `rankdir=TB` cho dễ đọc khi in A4. 08 không có Bác sĩ nên giữ nguyên. File Word cũ `De-tai-4-...docx` vẫn chứa ảnh DFD cũ. |

- Ảnh chụp màn hình, video, kiểm thử đều chạy trên **bản sao CSDL `phong_kham_demo`** (đã xóa sau khi xong); CSDL `phong_kham` không bị thay đổi.
- 12 ca kiểm thử Playwright đều đạt (bảng 3.2 trong báo cáo).
- Chưa đẩy GitHub các thay đổi DFD và các file mới.

## 8. Phiên 30/10/2026: báo cáo theo sườn 4 chương

- `03_BaoCaoDAHP2_NichegHopital.docx` viết lại theo sườn người dùng đưa: Ch.1 Mô tả hệ thống và BFD (1.1 khảo sát, 1.2 BFD) · Ch.2 DFD (ngữ cảnh, mức 1, mức 2 cả 5 chức năng, từ điển dữ liệu) · Ch.3 ERD (thực thể/thuộc tính, ERD khái niệm, ma trận CRUD) · Ch.4 Thiết kế CSDL và giao diện (lược đồ quan hệ, 10 bảng, phân quyền, giao diện + userflow). ~66 trang.
- Người dùng chốt: **không** Mở đầu, Kết luận, Tài liệu tham khảo; **bỏ** phần lập trình và mã nguồn; **có Phụ lục** cho mục 1.1: A kế hoạch khảo sát, B câu hỏi phỏng vấn, C phiếu khảo sát bệnh nhân, D bảng tổng hợp (để trống, chưa có số liệu khảo sát thật).
- Thêm sơ đồ ERD khái niệm (Chen, Graphviz) và lược đồ quan hệ; hai sơ đồ chỉ nằm trong báo cáo, chưa lưu vào `nguon/`.

## 9. Phiên 06/11/2026: bản 5 vai trò, rồi chuyển sang bản 4 vai trò theo góp ý giảng viên

- Sáng: làm bản 5 vai trò (Bệnh nhân, Lễ tân, Bác sĩ, Dược sĩ, Quản lý phòng khám): sơ đồ ở `ban-nang-cao-nhieu-vai-tro/` + `nguon-nhieu-vai-tro/`, phần mềm ở `phan-mem/` (CSDL `phong_kham`). Báo cáo chưa kịp dựng cho bản này.
- Chiều: giảng viên vẽ lại DFD mức 0 với **4 tác nhân: Bệnh nhân, Nhân viên, Bác sĩ, Quản trị**, bỏ Dược sĩ và kho thuốc (bệnh nhân mua thuốc ở ngoài). Người dùng chốt:
  - Tên vai trò theo thầy (Nhân viên, Quản trị) ở mọi nơi; nhãn luồng mức 0 theo thầy, viết đủ chữ ("Quản lý dịch vụ" đổi thành "Quản lý lịch làm việc của bác sĩ").
  - Mọi chức năng giữ như cũ, chỉ bỏ cấp phát, nhập kho, tồn kho; đơn thuốc nằm trong "Kết quả khám chữa bệnh"; mức 0 không vẽ luồng thuốc. Quản trị có thêm "Quản lý thông tin bác sĩ", "Yêu cầu báo cáo", "Báo cáo thống kê".
  - Quản trị cập nhật danh mục thuốc. Chức năng 1 đổi tên thành **"Quản trị hệ thống"**. BFD giữ cấu trúc (5 chức năng, 16 chức năng con).
  - Lưu vào thư mục mới, không đè bản 5 vai trò.
- Kết quả:
  - `ban-nang-cao-4-vai-tro/` 01–15 (BFD, DFD 0/1/2, ERD, lược đồ CSDL, userflow; màn [1]–[27]); nguồn ở `nguon-4-vai-tro/` (mỗi sơ đồ một script `ve-*.py`).
  - `phan-mem-4-vai-tro/`: vai trò `QT`, `NV`, `BS`, `BN`; thư mục `quan-tri/`, `nhan-vien/`; bỏ trang Dược sĩ, bảng `nhap_kho`, cột tồn kho và cấp phát. CSDL riêng **`phong_kham_4_vai_tro`** (bản 5 vai trò vẫn chạy với `phong_kham`). Tài khoản: `quantri01`, `nhanvien01`, `bacsi01`–`05`, `0905447812` (mật khẩu `matkhau`). 15 ca kiểm thử Playwright đều đạt (`nguon-4-vai-tro/kiem-thu-quy-trinh.py`, kết quả ở `anh-giao-dien/kiem-thu.json`); CSDL đã cài lại sạch sau kiểm thử.
  - Báo cáo `03_BaoCaoDAHP2_NichegHopitalfinal.docx` dựng lại phần thân (71 trang) bằng `nguon-4-vai-tro/dung-bao-cao.py` (nội dung ở `noi-dung-bao-cao.py`), giữ nguyên bìa, nhận xét, lời cảm ơn, nhật ký; bản trước khi sửa ở `sao-luu/`. Chạy: `uv run --no-project --python 3.12 --with python-docx --with pymupdf --with pillow python dung-bao-cao.py` trong `nguon-4-vai-tro/` (đóng Word trước).
- Còn dở: slide và video vẫn theo bản cũ; nhật ký thực hiện chưa thêm dòng cho việc chuyển sang 4 vai trò; chưa đẩy GitHub.
