-- ============================================================================
-- Dữ liệu mẫu để chạy thử, lấy theo bản thiết kế.
-- Ngày tháng tính theo ngày cài đặt (CURDATE()), nên lịch hẹn "hôm nay" luôn có dữ liệu.
-- Tài khoản: quanly01 / matkhau (Quản lý phòng khám), 0905447812 / matkhau (Bệnh nhân Hoàng Văn Nam).
-- ============================================================================

USE phong_kham;
SET time_zone = '+07:00';

-- Hai ngày hẹn sắp tới của bệnh nhân mẫu, tránh Chủ nhật (bác sĩ không có ca)
SET @hen_1 = CURDATE() + INTERVAL IF(DAYOFWEEK(CURDATE() + INTERVAL 3 DAY) = 1, 4, 3) DAY;
SET @hen_2 = CURDATE() + INTERVAL IF(DAYOFWEEK(CURDATE() + INTERVAL 12 DAY) = 1, 13, 12) DAY;

-- D1 Bác sĩ
INSERT INTO bac_si (id, ho_ten, chuyen_khoa, phong, so_dien_thoai) VALUES
(1, 'Trần Minh Khoa', 'Nội tổng quát', 'Phòng 01', '0903128456'),
(2, 'Lê Thị Hồng',    'Nhi khoa',      'Phòng 02', '0912334870'),
(3, 'Phạm Quốc Bảo',  'Tai Mũi Họng',  'Phòng 03', '0938551209'),
(4, 'Võ Ngọc Anh',    'Da liễu',       'Phòng 04', '0977602115'),
(5, 'Đỗ Văn Hải',     'Răng Hàm Mặt',  'Phòng 05', '0988210643');

-- D1 Ca làm việc trong tuần (1 = Thứ Hai ... 7 = Chủ nhật)
INSERT INTO ca_lam_viec (bac_si_id, thu, gio_bat_dau, gio_ket_thuc) VALUES
(1, 1, '07:30', '11:30'), (1, 2, '07:30', '11:30'), (1, 3, '07:30', '11:30'), (1, 4, '07:30', '11:30'),
(1, 5, '07:30', '11:30'), (1, 6, '07:30', '11:30'), (1, 1, '13:30', '17:00'), (1, 3, '13:30', '17:00'),
(1, 4, '13:30', '17:00'), (1, 5, '13:30', '17:00'),
(2, 1, '07:30', '11:30'), (2, 2, '07:30', '11:30'), (2, 3, '07:30', '11:30'), (2, 4, '07:30', '11:30'),
(2, 5, '07:30', '11:30'), (2, 2, '13:30', '17:00'), (2, 4, '13:30', '17:00'),
(3, 2, '07:30', '11:30'), (3, 4, '07:30', '11:30'), (3, 1, '13:30', '17:00'), (3, 2, '13:30', '17:00'),
(3, 3, '13:30', '17:00'), (3, 4, '13:30', '17:00'), (3, 5, '13:30', '17:00'), (3, 6, '13:30', '17:00'),
(4, 1, '07:30', '11:30'), (4, 3, '07:30', '11:30'), (4, 5, '07:30', '11:30'), (4, 6, '07:30', '11:30'),
(4, 1, '13:30', '17:00'), (4, 3, '13:30', '17:00'), (4, 5, '13:30', '17:00'),
(5, 2, '07:30', '11:30'), (5, 4, '07:30', '11:30'), (5, 6, '07:30', '11:30'), (5, 1, '13:30', '17:00'),
(5, 3, '13:30', '17:00'), (5, 5, '13:30', '17:00');

-- D2 Bệnh nhân (ma = NULL: đã đăng ký trực tuyến, chưa được tiếp nhận lần nào)
INSERT INTO benh_nhan (id, ma, ho_ten, so_dien_thoai, nam_sinh, gioi_tinh, dia_chi) VALUES
(1,  'BN-02541', 'Hoàng Văn Nam',   '0905447812', 1990, 'Nam', '45 Lê Lợi, Hải Châu, Đà Nẵng'),
(2,  NULL,       'Châu Ngọc Mai',   '0932118605', NULL, NULL,  NULL),
(3,  'BN-02102', 'Đặng Quốc Việt',  '0965337290', 1983, 'Nam', '12 Nguyễn Văn Linh, Thanh Khê, Đà Nẵng'),
(4,  NULL,       'Hà Thanh Vy',     '0911207448', NULL, NULL,  NULL),
(5,  'BN-01876', 'Nguyễn Thị Lan',  '0901223118', 1975, 'Nữ',  '88 Trần Phú, Hải Châu, Đà Nẵng'),
(6,  'BN-02390', 'Phạm Gia Huy',    '0934556721', 2019, 'Nam', NULL),
(7,  'BN-02233', 'Trương Văn Tâm',  '0912880431', 1968, 'Nam', NULL),
(8,  'BN-02011', 'Mai Thị Thu',     '0947612803', 1995, 'Nữ',  NULL),
(9,  'BN-01502', 'Lý Văn Hùng',     '0905113274', 1972, 'Nam', NULL),
(10, 'BN-01518', 'Trần Thị Bích',   '0914220387', 1988, 'Nữ',  NULL),
(11, 'BN-01533', 'Ngô Minh Tuấn',   '0935441962', 1999, 'Nam', NULL),
(12, 'BN-01547', 'Phan Thị Hạnh',   '0978330415', 1964, 'Nữ',  NULL),
(13, 'BN-01561', 'Vũ Đức Long',     '0906558123', 1980, 'Nam', NULL),
(14, 'BN-01579', 'Bùi Thị Ngọc',    '0913667240', 1992, 'Nữ',  NULL),
(15, 'BN-01584', 'Đinh Quang Huy',  '0932775018', 2001, 'Nam', NULL),
(16, 'BN-01596', 'Lâm Thị Kim',     '0989114736', 1958, 'Nữ',  NULL),
(17, 'BN-01603', 'Huỳnh Văn Phúc',  '0945882301', 1977, 'Nam', NULL),
(18, 'BN-01617', 'Tô Thị Yến',      '0916390548', 1985, 'Nữ',  NULL);

-- Tài khoản. Mật khẩu của cả ba tài khoản là: matkhau
INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, ho_ten, benh_nhan_id) VALUES
('quanly01',   '$2y$12$/RME9.xXZXJluaYxakYex.jn0UlFEDj4ZLPjEchu7JrT6KAf88smu', 'QL', 'Nguyễn Thu Hà', NULL),
('0905447812', '$2y$12$/RME9.xXZXJluaYxakYex.jn0UlFEDj4ZLPjEchu7JrT6KAf88smu', 'BN', NULL, 1),
('0932118605', '$2y$12$/RME9.xXZXJluaYxakYex.jn0UlFEDj4ZLPjEchu7JrT6KAf88smu', 'BN', NULL, 2);

-- D3 Danh mục thuốc
INSERT INTO thuoc (id, ma, ten, ham_luong, don_vi, nhom, lieu_toi_da, luu_y) VALUES
(1, 'TH001', 'Paracetamol',   '500 mg', 'Viên', 'Giảm đau, hạ sốt',     NULL,     'Không quá 4 g/ngày; thận trọng khi suy gan'),
(2, 'TH002', 'Amoxicillin',   '500 mg', 'Viên', 'Kháng sinh',           NULL,     'Chống chỉ định khi dị ứng penicillin'),
(3, 'TH003', 'Loratadin',     '10 mg',  'Viên', 'Kháng histamin',       '1 viên', 'Tối đa 1 viên/ngày'),
(4, 'TH004', 'Omeprazol',     '20 mg',  'Viên', 'Tiêu hóa',             NULL,     'Uống trước ăn sáng 30 phút'),
(5, 'TH005', 'Ibuprofen',     '400 mg', 'Viên', 'Giảm đau, chống viêm', NULL,     'Thận trọng khi viêm, loét dạ dày'),
(6, 'TH006', 'Oresol',        '4,1 g',  'Gói',  'Bù nước, điện giải',   NULL,     'Pha đúng 200 ml nước'),
(7, 'TH007', 'Cefuroxim',     '250 mg', 'Viên', 'Kháng sinh',           NULL,     'Thận trọng khi dị ứng cephalosporin'),
(8, 'TH008', 'Acetylcystein', '200 mg', 'Gói',  'Long đờm',             NULL,     'Không dùng cho trẻ dưới 2 tuổi');

-- D4 Lịch hẹn hôm nay, lịch hẹn sắp tới và các lần khám trước của bệnh nhân mẫu
INSERT INTO lich_hen (id, benh_nhan_id, bac_si_id, ngay, gio, ly_do, trang_thai, ly_do_huy) VALUES
(101, 5,  1, CURDATE(), '08:00', 'Khám tổng quát',              'da_kham',   NULL),
(102, 6,  2, CURDATE(), '08:30', 'Sốt, ho',                     'da_kham',   NULL),
(103, 1,  1, CURDATE(), '09:00', 'Ho kéo dài, sốt nhẹ 3 ngày',  'da_dat',    NULL),
(104, 7,  3, CURDATE(), '09:00', 'Đau họng',                    'dang_kham', NULL),
(105, 2,  1, CURDATE(), '09:30', 'Mệt mỏi, chóng mặt',          'cho_kham',  NULL),
(106, 3,  1, CURDATE(), '10:00', 'Đau dạ dày',                  'da_dat',    NULL),
(107, 8,  5, CURDATE(), '10:30', 'Đau răng',                    'da_huy',    'Bận việc đột xuất'),
(108, 4,  4, CURDATE(), '10:30', 'Mụn trứng cá',                'da_dat',    NULL),
(109, 9,  1, CURDATE(), '14:00', 'Đau đầu, mất ngủ',            'da_dat',    NULL),
(110, 10, 3, CURDATE(), '14:30', 'Ù tai',                       'da_dat',    NULL),
(111, 1,  1, @hen_1,                   '10:00', 'Đau thượng vị, ợ chua', 'da_dat',  NULL),
(112, 1,  3, @hen_2,                   '14:00', 'Nghẹt mũi kéo dài',     'da_dat',  NULL),
(113, 1,  3, CURDATE() - INTERVAL 130 DAY, '09:30', 'Đau họng, sốt nhẹ',     'da_kham', NULL),
(114, 1,  1, CURDATE() - INTERVAL 234 DAY, '08:30', 'Đau bụng sau ăn',       'da_kham', NULL),
(115, 1,  3, CURDATE() - INTERVAL 338 DAY, '15:00', 'Hắt hơi, chảy mũi',     'da_kham', NULL),
(116, 7,  3, CURDATE() - INTERVAL 90 DAY,  '15:30', 'Đau rát họng',          'da_kham', NULL);

-- D5 Bệnh án của các lần khám trên
INSERT INTO benh_an (id, lich_hen_id, benh_nhan_id, bac_si_id, ngay_kham, mach, nhiet_do, huyet_ap, can_nang, ket_qua, chan_doan, loi_dan, tao_luc) VALUES
(1, 113, 1, 3, CURDATE() - INTERVAL 130 DAY, '80', '37,2', '118/76', '67',
    'Họng đỏ, amidan sưng nhẹ, không có mủ.', 'Viêm họng cấp',
    'Súc họng nước muối ấm ngày 3 lần. Tránh đồ lạnh.', TIMESTAMP(CURDATE() - INTERVAL 130 DAY, '09:50:00')),
(2, 114, 1, 1, CURDATE() - INTERVAL 234 DAY, '76', '36,8', '122/80', '68',
    'Đau vùng thượng vị, ợ chua sau ăn.', 'Viêm dạ dày',
    'Ăn đúng giờ, hạn chế đồ cay, rượu bia, cà phê.', TIMESTAMP(CURDATE() - INTERVAL 234 DAY, '08:50:00')),
(3, 115, 1, 3, CURDATE() - INTERVAL 338 DAY, '78', '36,9', '120/78', '68',
    'Niêm mạc mũi phù nề, chảy dịch trong.', 'Viêm mũi dị ứng',
    'Tránh bụi, giữ ấm mũi họng.', TIMESTAMP(CURDATE() - INTERVAL 338 DAY, '15:20:00')),
(4, 101, 5, 1, CURDATE(), '82', '36,7', '150/95', '61',
    'Huyết áp cao hơn bình thường, không có triệu chứng khác.', 'Tăng huyết áp độ 1',
    'Ăn nhạt, tập thể dục đều, đo huyết áp hằng ngày. Tái khám sau 2 tuần.', TIMESTAMP(CURDATE(), '08:20:00')),
(5, 102, 6, 2, CURDATE(), '110', '38,5', NULL, '18',
    'Sốt 38,5°C, họng đỏ nhẹ, phổi không ran.', 'Sốt siêu vi',
    'Cho bé uống nhiều nước, lau mát khi sốt. Tái khám nếu sốt quá 3 ngày.', TIMESTAMP(CURDATE(), '08:50:00')),
(6, 116, 7, 3, CURDATE() - INTERVAL 90 DAY, '84', '37,4', '130/85', '72',
    'Họng đỏ, amidan sưng, có ít mủ.', 'Viêm amidan cấp',
    'Súc họng nước muối, ăn đồ mềm.', TIMESTAMP(CURDATE() - INTERVAL 90 DAY, '15:50:00'));

-- D6 Đơn thuốc
INSERT INTO don_thuoc (id, benh_an_id, tao_luc)
SELECT id, id, tao_luc FROM benh_an WHERE id IN (1, 2, 3, 5, 6);

INSERT INTO chi_tiet_don_thuoc (don_thuoc_id, thuoc_id, so_luong, cach_dung) VALUES
(1, 7, 10, 'Ngày 2 lần, mỗi lần 1 viên, sau ăn'),
(1, 1, 6,  'Uống khi sốt trên 38,5°C, cách nhau ít nhất 6 giờ'),
(2, 4, 28, 'Mỗi sáng 1 viên, trước ăn 30 phút'),
(3, 3, 10, 'Mỗi tối 1 viên'),
(5, 1, 6,  'Uống khi sốt trên 38,5°C, cách nhau ít nhất 6 giờ'),
(5, 6, 4,  'Pha 1 gói với 200 ml nước, uống trong ngày'),
(6, 2, 14, 'Ngày 2 lần, mỗi lần 1 viên, sau ăn');

-- Thông báo của bệnh nhân Hoàng Văn Nam và Châu Ngọc Mai
INSERT INTO thong_bao (benh_nhan_id, loai, tieu_de, noi_dung, tao_luc) VALUES
(1, 'ket_qua',
    CONCAT('Phiếu kết quả khám ngày ', DATE_FORMAT(CURDATE() - INTERVAL 130 DAY, '%d/%m/%Y')),
    'Chẩn đoán: Viêm họng cấp. Lời dặn: Súc họng nước muối ấm ngày 3 lần. Tránh đồ lạnh.',
    TIMESTAMP(CURDATE() - INTERVAL 130 DAY, '09:50:00')),
(1, 'don_thuoc',
    CONCAT('Đơn thuốc ngày ', DATE_FORMAT(CURDATE() - INTERVAL 130 DAY, '%d/%m/%Y')),
    'Đơn thuốc của BS. Phạm Quốc Bảo: Cefuroxim 250 mg, Paracetamol 500 mg. Vui lòng dùng theo hướng dẫn trên đơn.',
    TIMESTAMP(CURDATE() - INTERVAL 130 DAY, '09:52:00')),
(1, 'dat_lich', 'Đặt lịch thành công',
    CONCAT('Lịch khám 09:00 ', ELT(WEEKDAY(CURDATE()) + 1, 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'),
           ' ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'), ' với BS. Trần Minh Khoa, Phòng 01.'),
    TIMESTAMP(CURDATE() - INTERVAL 2 DAY, '20:14:00')),
(1, 'dat_lich', 'Đặt lịch thành công',
    CONCAT('Lịch khám 10:00 ', ELT(WEEKDAY(@hen_1) + 1, 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'),
           ' ', DATE_FORMAT(@hen_1, '%d/%m/%Y'), ' với BS. Trần Minh Khoa, Phòng 01.'),
    TIMESTAMP(CURDATE() - INTERVAL 1 DAY, '09:50:00')),
(1, 'dat_lich', 'Đặt lịch thành công',
    CONCAT('Lịch khám 14:00 ', ELT(WEEKDAY(@hen_2) + 1, 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'),
           ' ', DATE_FORMAT(@hen_2, '%d/%m/%Y'), ' với BS. Phạm Quốc Bảo, Phòng 03.'),
    TIMESTAMP(CURDATE() - INTERVAL 1 DAY, '21:05:00')),
(2, 'dat_lich', 'Đặt lịch thành công',
    CONCAT('Lịch khám 09:30 ', ELT(WEEKDAY(CURDATE()) + 1, 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'),
           ' ', DATE_FORMAT(CURDATE(), '%d/%m/%Y'), ' với BS. Trần Minh Khoa, Phòng 01.'),
    TIMESTAMP(CURDATE() - INTERVAL 1 DAY, '19:30:00'));

-- ----------------------------------------------------------------------------
-- Lịch sử khám 120 ngày gần nhất để màn báo cáo [19]–[20] có số liệu.
-- Mỗi ngày (trừ Chủ nhật) có 12 đến 30 lượt khám, chia đều cho 5 bác sĩ,
-- dùng 10 bệnh nhân mẫu id 9–18. Khoảng 80% lượt khám có đơn thuốc.
-- ----------------------------------------------------------------------------
CREATE TEMPORARY TABLE so (n INT PRIMARY KEY);
INSERT INTO so (n)
SELECT a.d + 10 * b.d + 100 * c.d + 1000 * e.d
FROM (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
CROSS JOIN (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
CROSS JOIN (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
CROSS JOIN (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) e;

ALTER TABLE lich_hen AUTO_INCREMENT = 1000;
INSERT INTO lich_hen (benh_nhan_id, bac_si_id, ngay, gio, ly_do, trang_thai, tao_luc)
SELECT 9 + n MOD 10,
       1 + (n MOD 40) MOD 5,
       CURDATE() - INTERVAL (1 + n DIV 40) DAY,
       ADDTIME('07:30:00', SEC_TO_TIME(((n MOD 40) DIV 5) * 1800)),
       'Khám bệnh',
       'da_kham',
       TIMESTAMP(CURDATE() - INTERVAL (3 + n DIV 40) DAY, '20:00:00')
FROM so
WHERE n < 120 * 40
  AND (n MOD 40) < 12 + ((n DIV 40) * 7) MOD 19
  AND DAYOFWEEK(CURDATE() - INTERVAL (1 + n DIV 40) DAY) <> 1;

ALTER TABLE benh_an AUTO_INCREMENT = 1000;
INSERT INTO benh_an (lich_hen_id, benh_nhan_id, bac_si_id, ngay_kham, mach, nhiet_do, huyet_ap, ket_qua, chan_doan, loi_dan, tao_luc)
SELECT id, benh_nhan_id, bac_si_id, ngay,
       CAST(72 + id MOD 18 AS CHAR),
       CONCAT('36,', 5 + id MOD 5),
       CONCAT(110 + id MOD 25, '/', 70 + id MOD 15),
       ELT((bac_si_id - 1) * 2 + id MOD 2 + 1,
           'Đau vùng thượng vị, ợ chua sau ăn.', 'Ho có đờm, phổi thô hai bên.',
           'Sốt, mệt mỏi, họng đỏ nhẹ.', 'Họng đỏ, amidan sưng nhẹ.',
           'Họng đỏ, amidan sưng nhẹ.', 'Niêm mạc mũi phù nề, chảy dịch trong.',
           'Da khô, mẩn đỏ, ngứa từng đợt.', 'Mụn viêm vùng má và trán.',
           'Răng hàm có lỗ sâu, ê buốt khi ăn lạnh.', 'Nướu sưng đỏ, chảy máu khi chải răng.'),
       ELT((bac_si_id - 1) * 2 + id MOD 2 + 1,
           'Viêm dạ dày', 'Viêm phế quản cấp',
           'Sốt siêu vi', 'Viêm họng cấp',
           'Viêm họng cấp', 'Viêm mũi dị ứng',
           'Viêm da cơ địa', 'Mụn trứng cá',
           'Sâu răng', 'Viêm nướu'),
       'Tái khám nếu triệu chứng không giảm.',
       TIMESTAMP(ngay, ADDTIME(gio, '00:15:00'))
FROM lich_hen
WHERE id >= 1000;

ALTER TABLE don_thuoc AUTO_INCREMENT = 1000;
INSERT INTO don_thuoc (benh_an_id, tao_luc)
SELECT id, tao_luc FROM benh_an WHERE id >= 1000 AND id MOD 5 <> 0;

INSERT INTO chi_tiet_don_thuoc (don_thuoc_id, thuoc_id, so_luong, cach_dung)
SELECT d.id, 1 + (d.id + s.n * 3) MOD 8, 6 + (d.id MOD 3) * 4, 'Theo hướng dẫn của bác sĩ'
FROM don_thuoc d
JOIN so s ON s.n < 1 + d.id MOD 3
WHERE d.id >= 1000;

DROP TEMPORARY TABLE so;
