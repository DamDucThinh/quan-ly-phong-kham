<?php
require __DIR__ . '/../includes/khoi-dong.php';

$nguoi_dung = nguoi_dung();
chuyen_huong($nguoi_dung ? trang_chu_cua($nguoi_dung['vai_tro']) : 'dang-nhap.php');
