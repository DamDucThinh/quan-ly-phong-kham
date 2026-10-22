<?php
/*
 * Bố cục dùng chung: phần đầu trang, thanh điều hướng của Quản lý phòng khám và Bệnh nhân,
 * phần cuối trang, cùng vài mẩu giao diện lặp lại nhiều nơi.
 */

const ICON_NHA = 'M4 10.5L12 4l8 6.5V20h-5v-6h-6v6H4z';
const ICON_LICH = 'M5 5h14a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 19 20H5a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 5 5zM8 3v4M16 3v4M3.5 10h17';
const ICON_DAT_LICH = ICON_LICH . 'M12 12.5v5M9.5 15h5';
const ICON_PHIEU = 'M7 3.5h7l4 4v13H6.5v-17zM9.5 12h5M9.5 15.5h5';
const ICON_BUT = 'M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4zM13.5 6.5l4 4';
const ICON_DUNG = 'M5 12.5l4.5 4.5L19 7.5';
const ICON_CANH_BAO = 'M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0-18zM12 7.5v5.5M12 16.5v.01';

function bieu_tuong(string $duong_ve, int $co = 20, float $net = 2): string
{
    return '<svg width="' . $co . '" height="' . $co . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . $net
        . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . $duong_ve . '"></path></svg>';
}

function logo(string $mau = '#FFFFFF', int $co = 22): string
{
    return '<svg width="' . $co . '" height="' . $co . '" viewBox="0 0 24 24" fill="none" aria-hidden="true">'
        . '<path d="M5.5 19V5l13 14V5" stroke="' . $mau . '" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"></path>'
        . '<circle cx="18.5" cy="4.6" r="2.4" fill="#F2B154"></circle></svg>';
}

function hien_thong_diep(): string
{
    $thong_diep = lay_thong_diep();
    if (!$thong_diep) {
        return '';
    }
    $vai_tro = $thong_diep['loai'] === 'err' ? 'alert' : 'status';
    return '<div class="alert ' . e($thong_diep['loai']) . '" role="' . $vai_tro . '">' . $thong_diep['html'] . '</div>';
}

/** Ô lịch nhỏ: THỨ SÁU / 23 / Tháng 10 */
function o_ngay(string $ngay, bool $lon = false): string
{
    return '<div class="datebox' . ($lon ? ' lg' : '') . '">'
        . '<span class="wd">' . e(mb_strtoupper(ten_thu($ngay))) . '</span>'
        . '<strong>' . date('d', strtotime($ngay)) . '</strong>'
        . '<span>Tháng ' . (int) date('m', strtotime($ngay)) . '</span></div>';
}

function nhan_trang_thai(string $trang_thai): string
{
    return '<span class="pill st-' . e($trang_thai) . '"><span class="dotc"></span>' . e(TRANG_THAI_LICH_HEN[$trang_thai] ?? $trang_thai) . '</span>';
}

/** In danh sách <option>, đánh dấu giá trị đang chọn. */
function cac_lua_chon(array $ds, ?string $dang_chon, bool $dung_khoa = false): string
{
    $html = '';
    foreach ($ds as $khoa => $nhan) {
        $gia_tri = $dung_khoa ? (string) $khoa : (string) $nhan;
        $html .= '<option value="' . e($gia_tri) . '"' . ($gia_tri === (string) $dang_chon ? ' selected' : '') . '>' . e($nhan) . '</option>';
    }
    return $html;
}

function mo_trang(string $tieu_de, string $khu = '', string $muc = ''): void
{
    ?><!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tieu_de) ?> · <?= e(TEN_PHONG_KHAM) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&amp;display=swap">
<link rel="stylesheet" href="<?= e(url('assets/app.css')) ?>">
</head>
<body class="<?= e($khu) ?>">
<?php
    if ($khu === 'ql') {
        thanh_dieu_huong_ql($muc);
    } elseif ($khu === 'bn') {
        thanh_dieu_huong_bn($muc);
    }
}

function dong_trang(string $script = ''): void
{
    echo '<script src="', e(url('assets/app.js')), '"></script>', $script, "\n</body>\n</html>\n";
}

function o_nguoi_dung(): string
{
    $nguoi_dung = nguoi_dung();
    return '<div class="user"><span class="avatar">' . e(chu_cai_dau($nguoi_dung['ho_ten'])) . '</span>'
        . '<span class="user-txt"><span class="uname">' . e($nguoi_dung['ho_ten']) . '</span>'
        . '<a href="' . e(url('dang-xuat.php')) . '">Đăng xuất</a></span></div>';
}

function thanh_dieu_huong_ql(string $muc): void
{
    $cac_muc = [
        'home' => ['Trang chủ', 'quan-ly/trang-chu.php'],
        'reception' => ['Tiếp nhận', 'quan-ly/tiep-nhan.php'],
        'today' => ['Lịch hẹn', 'quan-ly/lich-hen.php'],
        'doctors' => ['Bác sĩ', 'quan-ly/bac-si.php'],
        'drugs' => ['Thuốc', 'quan-ly/thuoc.php'],
        'report' => ['Báo cáo', 'quan-ly/bao-cao.php'],
    ];
    ?>
<header class="topbar no-print">
  <div class="topbar-in">
    <a class="brand" href="<?= e(url('quan-ly/trang-chu.php')) ?>"><span class="brand-mark"><?= logo() ?></span><strong><?= e(TEN_PHONG_KHAM) ?></strong></a>
    <nav class="nav" aria-label="Chức năng quản lý">
      <?php foreach ($cac_muc as $ma => [$nhan, $trang]): ?>
        <a href="<?= e(url($trang)) ?>"<?= $ma === $muc ? ' aria-current="page"' : '' ?>><?= e($nhan) ?></a>
      <?php endforeach; ?>
    </nav>
    <?= o_nguoi_dung() ?>
  </div>
</header>
<?php
}

function thanh_dieu_huong_bn(string $muc): void
{
    $cac_muc = [
        'home' => ['Trang chủ', 'Trang chủ', 'benh-nhan/trang-chu.php', ICON_NHA],
        'book' => ['Đặt lịch khám', 'Đặt lịch', 'benh-nhan/chon-bac-si.php', ICON_DAT_LICH],
        'result' => ['Kết quả khám', 'Kết quả', 'benh-nhan/ket-qua.php', ICON_PHIEU],
    ];
    ?>
<header class="topbar no-print">
  <div class="topbar-in">
    <a class="brand" href="<?= e(url('benh-nhan/trang-chu.php')) ?>"><span class="brand-mark"><?= logo() ?></span><strong><?= e(TEN_PHONG_KHAM) ?></strong></a>
    <nav class="nav bn-links" aria-label="Điều hướng bệnh nhân">
      <?php foreach ($cac_muc as $ma => [$nhan, , $trang]): ?>
        <a href="<?= e(url($trang)) ?>"<?= $ma === $muc ? ' aria-current="page"' : '' ?>><?= e($nhan) ?></a>
      <?php endforeach; ?>
    </nav>
    <?= o_nguoi_dung() ?>
  </div>
</header>
<nav class="bn-tabbar no-print" aria-label="Điều hướng bệnh nhân (di động)">
  <?php foreach ($cac_muc as $ma => [, $nhan_ngan, $trang, $hinh]): ?>
    <a href="<?= e(url($trang)) ?>"<?= $ma === $muc ? ' aria-current="page"' : '' ?>><?= bieu_tuong($hinh, 24) ?><span><?= e($nhan_ngan) ?></span></a>
  <?php endforeach; ?>
</nav>
<?php
}

/** Nửa trái của màn đăng nhập và đăng ký. */
function phan_gioi_thieu(): void
{
    ?>
  <section class="auth-intro">
    <div class="brand on-dark"><span class="brand-mark white"><?= logo('#0E7C70', 28) ?></span><strong><?= e(TEN_PHONG_KHAM) ?></strong></div>
    <div class="stack g20" style="max-width:520px">
      <h1>Hệ thống quản lý phòng khám</h1>
      <p>Bệnh nhân đặt, đổi, hủy lịch hẹn trực tuyến. Quản lý phòng khám tiếp nhận bệnh nhân, ghi kết quả khám, kê đơn, quản lý danh mục và xem báo cáo.</p>
    </div>
    <div class="auth-meta"><span>Hotline: <?= e(HOTLINE) ?></span><span><?= e(DIA_CHI) ?></span></div>
  </section>
<?php
}
