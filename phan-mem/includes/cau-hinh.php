<?php
/*
 * Cấu hình hệ thống. Sửa các giá trị ở đây khi cài lên máy khác.
 */

// Kết nối MySQL
const CSDL_MAY_CHU = '127.0.0.1';
const CSDL_CONG = 3306;
const CSDL_TEN = 'phong_kham';
const CSDL_NGUOI_DUNG = 'root';
const CSDL_MAT_KHAU = '';

// Thông tin phòng khám, hiện ở trang đăng nhập và trên phiếu in
const TEN_PHONG_KHAM = 'NichegHopital';
const HOTLINE = '[SỐ ĐIỆN THOẠI]';
const DIA_CHI = '[ĐỊA CHỈ PHÒNG KHÁM]';

const MUI_GIO = 'Asia/Ho_Chi_Minh';
const PHUT_MOI_KHUNG = 30;      // độ dài một khung giờ khám; mỗi khung nhận 1 bệnh nhân
const SO_NGAY_DAT_TRUOC = 7;    // bệnh nhân đặt được lịch trong 7 ngày, tính cả hôm nay
