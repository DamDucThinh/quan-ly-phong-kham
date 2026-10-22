-- ============================================================================
-- Cơ sở dữ liệu hệ thống quản lý phòng khám NichegHopital
-- Các bảng ứng với kho dữ liệu trong DFD:
--   D1 Hồ sơ bác sĩ  : bac_si, ca_lam_viec
--   D2 Hồ sơ bệnh nhân: benh_nhan (tai_khoan dùng để đăng nhập)
--   D3 Danh mục thuốc : thuoc
--   D4 Lịch hẹn       : lich_hen
--   D5 Hồ sơ bệnh án  : benh_an
--   D6 Đơn thuốc      : don_thuoc, chi_tiet_don_thuoc
-- Chạy file này trước, sau đó chạy du-lieu-mau.sql.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS phong_kham CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phong_kham;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS thong_bao, chi_tiet_don_thuoc, don_thuoc, benh_an, lich_hen,
                     ca_lam_viec, tai_khoan, thuoc, benh_nhan, bac_si;
SET FOREIGN_KEY_CHECKS = 1;

-- D1 Hồ sơ bác sĩ
CREATE TABLE bac_si (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ho_ten        VARCHAR(100) NOT NULL,
  chuyen_khoa   VARCHAR(50)  NOT NULL,
  phong         VARCHAR(20)  NOT NULL,
  so_dien_thoai CHAR(10)     NOT NULL,
  email         VARCHAR(100) NULL,
  trinh_do      VARCHAR(100) NULL
) ENGINE=InnoDB;

-- D1 Lịch làm việc: mỗi dòng là một ca trong tuần của một bác sĩ
CREATE TABLE ca_lam_viec (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bac_si_id    INT UNSIGNED NOT NULL,
  thu          TINYINT UNSIGNED NOT NULL COMMENT '1 = Thứ Hai ... 7 = Chủ nhật',
  gio_bat_dau  TIME NOT NULL,
  gio_ket_thuc TIME NOT NULL,
  INDEX idx_ca_thu (bac_si_id, thu),
  CONSTRAINT fk_ca_bac_si FOREIGN KEY (bac_si_id) REFERENCES bac_si (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- D2 Hồ sơ bệnh nhân. ma = NULL: bệnh nhân mới đăng ký trực tuyến, chưa được tiếp nhận lần nào.
CREATE TABLE benh_nhan (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma            VARCHAR(10)  NULL UNIQUE,
  ho_ten        VARCHAR(100) NOT NULL,
  so_dien_thoai CHAR(10)     NOT NULL UNIQUE,
  nam_sinh      SMALLINT UNSIGNED NULL,
  gioi_tinh     ENUM('Nam', 'Nữ') NULL,
  dia_chi       VARCHAR(255) NULL,
  tao_luc       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tài khoản đăng nhập. Bệnh nhân đăng nhập bằng số điện thoại.
CREATE TABLE tai_khoan (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_dang_nhap VARCHAR(50)  NOT NULL UNIQUE,
  mat_khau      VARCHAR(255) NOT NULL COMMENT 'đã mã hóa bằng password_hash',
  vai_tro       ENUM('QL', 'BN') NOT NULL COMMENT 'QL: Quản lý phòng khám, BN: Bệnh nhân',
  ho_ten        VARCHAR(100) NULL COMMENT 'chỉ dùng cho tài khoản quản lý; bệnh nhân lấy tên từ benh_nhan',
  benh_nhan_id  INT UNSIGNED NULL UNIQUE,
  CONSTRAINT fk_tk_benh_nhan FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan (id)
) ENGINE=InnoDB;

-- D3 Danh mục thuốc
CREATE TABLE thuoc (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma          VARCHAR(10)  NOT NULL UNIQUE,
  ten         VARCHAR(100) NOT NULL,
  ham_luong   VARCHAR(30)  NOT NULL,
  don_vi      VARCHAR(20)  NOT NULL,
  nhom        VARCHAR(50)  NOT NULL,
  lieu_toi_da VARCHAR(50)  NULL,
  luu_y       VARCHAR(500) NULL
) ENGINE=InnoDB;

-- D4 Lịch hẹn
CREATE TABLE lich_hen (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  benh_nhan_id INT UNSIGNED NOT NULL,
  bac_si_id    INT UNSIGNED NOT NULL,
  ngay         DATE NOT NULL,
  gio          TIME NOT NULL,
  loai         ENUM('hen_truoc', 'vang_lai') NOT NULL DEFAULT 'hen_truoc' COMMENT 'vang_lai: đến khám không hẹn trước',
  ly_do        VARCHAR(255) NULL,
  trang_thai   ENUM('da_dat', 'cho_kham', 'dang_kham', 'da_kham', 'da_huy') NOT NULL DEFAULT 'da_dat',
  ly_do_huy    VARCHAR(100) NULL,
  tao_luc      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  -- = 1 khi lịch đang giữ khung giờ; NULL khi đã hủy hoặc khám vãng lai.
  -- Khóa uq_khung_gio chặn hai lịch cùng giữ một khung giờ của một bác sĩ
  -- (nhánh "khung giờ vừa có người đặt" của CN 2.3).
  giu_cho      TINYINT AS (IF(trang_thai = 'da_huy' OR loai = 'vang_lai', NULL, 1)) STORED,
  UNIQUE KEY uq_khung_gio (bac_si_id, ngay, gio, giu_cho),
  INDEX idx_lh_ngay (ngay, trang_thai),
  INDEX idx_lh_benh_nhan (benh_nhan_id),
  CONSTRAINT fk_lh_benh_nhan FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan (id),
  CONSTRAINT fk_lh_bac_si FOREIGN KEY (bac_si_id) REFERENCES bac_si (id)
) ENGINE=InnoDB;

-- D5 Hồ sơ bệnh án: mỗi lần khám một bệnh án
CREATE TABLE benh_an (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lich_hen_id  INT UNSIGNED NOT NULL UNIQUE,
  benh_nhan_id INT UNSIGNED NOT NULL,
  bac_si_id    INT UNSIGNED NOT NULL,
  ngay_kham    DATE NOT NULL,
  mach         VARCHAR(10) NULL,
  nhiet_do     VARCHAR(10) NULL,
  huyet_ap     VARCHAR(15) NULL,
  can_nang     VARCHAR(10) NULL,
  ket_qua      TEXT NOT NULL,
  chan_doan    VARCHAR(255) NOT NULL,
  loi_dan      TEXT NULL,
  tao_luc      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ba_benh_nhan (benh_nhan_id),
  INDEX idx_ba_ngay (ngay_kham),
  CONSTRAINT fk_ba_lich_hen FOREIGN KEY (lich_hen_id) REFERENCES lich_hen (id),
  CONSTRAINT fk_ba_benh_nhan FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan (id),
  CONSTRAINT fk_ba_bac_si FOREIGN KEY (bac_si_id) REFERENCES bac_si (id)
) ENGINE=InnoDB;

-- D6 Đơn thuốc: tối đa một đơn cho mỗi bệnh án
CREATE TABLE don_thuoc (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  benh_an_id INT UNSIGNED NOT NULL UNIQUE,
  tao_luc    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_dt_benh_an FOREIGN KEY (benh_an_id) REFERENCES benh_an (id)
) ENGINE=InnoDB;

-- D6 Thuốc trong đơn. Khóa uq_thuoc_trong_don chặn một thuốc xuất hiện hai lần trong cùng đơn.
CREATE TABLE chi_tiet_don_thuoc (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  don_thuoc_id INT UNSIGNED NOT NULL,
  thuoc_id     INT UNSIGNED NOT NULL,
  so_luong     SMALLINT UNSIGNED NOT NULL,
  cach_dung    VARCHAR(255) NULL,
  UNIQUE KEY uq_thuoc_trong_don (don_thuoc_id, thuoc_id),
  CONSTRAINT fk_ct_don FOREIGN KEY (don_thuoc_id) REFERENCES don_thuoc (id) ON DELETE CASCADE,
  CONSTRAINT fk_ct_thuoc FOREIGN KEY (thuoc_id) REFERENCES thuoc (id)
) ENGINE=InnoDB;

-- Thông báo gửi bệnh nhân, hiện ở trang chủ bệnh nhân
CREATE TABLE thong_bao (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  benh_nhan_id INT UNSIGNED NOT NULL,
  loai         ENUM('dat_lich', 'doi_lich', 'huy_lich', 'ket_qua', 'don_thuoc') NOT NULL,
  tieu_de      VARCHAR(150) NOT NULL,
  noi_dung     VARCHAR(500) NOT NULL,
  tao_luc      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tb_benh_nhan (benh_nhan_id, tao_luc),
  CONSTRAINT fk_tb_benh_nhan FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan (id)
) ENGINE=InnoDB;
