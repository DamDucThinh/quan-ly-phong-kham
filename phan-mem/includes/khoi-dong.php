<?php
/*
 * Mọi trang đều nạp file này đầu tiên.
 */

require __DIR__ . '/cau-hinh.php';

date_default_timezone_set(MUI_GIO);
mb_internal_encoding('UTF-8');

require __DIR__ . '/csdl.php';
require __DIR__ . '/tien-ich.php';
require __DIR__ . '/xac-thuc.php';
require __DIR__ . '/nghiep-vu.php';
require __DIR__ . '/giao-dien.php';

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
