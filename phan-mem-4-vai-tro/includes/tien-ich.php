<?php
/*
 * Hàm dùng chung: thoát HTML, đường dẫn, chuyển trang, chống giả mạo form, thông điệp một lần, định dạng.
 */

const THU_TRONG_TUAN = [1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy', 7 => 'Chủ nhật'];

function e(mixed $gia_tri): string
{
    return htmlspecialchars((string) ($gia_tri ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Đường dẫn web tới thư mục public, để chạy được cả bằng `php -S` lẫn trong htdocs của XAMPP. */
function duong_dan_goc(): string
{
    static $goc = null;
    if ($goc === null) {
        $thu_muc_public = str_replace('\\', '/', (string) realpath(__DIR__ . '/../public'));
        $goc_web = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        $goc = '';
        if ($goc_web !== '' && str_starts_with($thu_muc_public . '/', $goc_web . '/')) {
            $phan = trim(substr($thu_muc_public, strlen($goc_web)), '/');
            $goc = $phan === '' ? '' : '/' . implode('/', array_map('rawurlencode', explode('/', $phan)));
        }
    }
    return $goc;
}

function url(string $trang, array $tham_so = []): string
{
    $tham_so = array_filter($tham_so, fn($v) => $v !== null && $v !== '');
    $chuoi = $tham_so ? '?' . http_build_query($tham_so, '', '&', PHP_QUERY_RFC3986) : '';
    return duong_dan_goc() . '/' . ltrim($trang, '/') . $chuoi;
}

function chuyen_huong(string $trang, array $tham_so = []): never
{
    header('Location: ' . url($trang, $tham_so));
    exit;
}

function la_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function ma_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function o_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(ma_csrf()) . '">';
}

function kiem_tra_csrf(): void
{
    if (!hash_equals(ma_csrf(), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Phiên làm việc đã hết hạn. Vui lòng quay lại và tải lại trang.');
    }
}

/** Thông điệp hiện một lần ở trang kế tiếp. $html phải được thoát sẵn bằng e(). Loại: ok, err, warn, info. */
function dat_thong_diep(string $loai, string $html): void
{
    $_SESSION['thong_diep'] = ['loai' => $loai, 'html' => $html];
}

function lay_thong_diep(): ?array
{
    $thong_diep = $_SESSION['thong_diep'] ?? null;
    unset($_SESSION['thong_diep']);
    return $thong_diep;
}

function chi_so(string $chuoi): string
{
    return preg_replace('/\D+/', '', $chuoi);
}

function sdt_hop_le(string $sdt): bool
{
    return (bool) preg_match('/^0\d{9}$/', chi_so($sdt));
}

/** 0905447812 thành 0905 447 812 */
function dinh_dang_sdt(?string $sdt): string
{
    $so = chi_so((string) $sdt);
    return strlen($so) === 10 ? substr($so, 0, 4) . ' ' . substr($so, 4, 3) . ' ' . substr($so, 7) : (string) $sdt;
}

/** Chữ cái đầu của hai từ cuối: Trần Minh Khoa thành MK */
function chu_cai_dau(string $ho_ten): string
{
    $tu = array_slice(preg_split('/\s+/u', trim($ho_ten)) ?: [], -2);
    return mb_strtoupper(implode('', array_map(fn($t) => mb_substr($t, 0, 1), $tu)));
}

function ten_bac_si(string $ho_ten): string
{
    return 'BS. ' . $ho_ten;
}

function hom_nay(): string
{
    return date('Y-m-d');
}

function bay_gio(): string
{
    return date('Y-m-d H:i:s');
}

function ngay_hop_le(?string $ngay): bool
{
    $d = DateTime::createFromFormat('!Y-m-d', (string) $ngay);
    return $d !== false && $d->format('Y-m-d') === $ngay;
}

function ten_thu(string $ngay): string
{
    return THU_TRONG_TUAN[(int) date('N', strtotime($ngay))];
}

function ngay_vn(string $ngay): string
{
    return date('d/m/Y', strtotime($ngay));
}

function ngay_ngan(string $ngay): string
{
    return date('d/m', strtotime($ngay));
}

function gio_ngan(string $gio): string
{
    return substr($gio, 0, 5);
}

/** Thứ Sáu, 23/10/2026 */
function thu_ngay(string $ngay): string
{
    return ten_thu($ngay) . ', ' . ngay_vn($ngay);
}

/** 10:00, Thứ Sáu 23/10/2026 */
function thoi_diem(string $ngay, string $gio): string
{
    return gio_ngan($gio) . ', ' . ten_thu($ngay) . ' ' . ngay_vn($ngay);
}

function so(int|float $n): string
{
    return number_format($n, 0, ',', '.');
}

/** Chuẩn hóa chuỗi người dùng gõ: bỏ khoảng trắng thừa. */
function gon(?string $chuoi): string
{
    return trim(preg_replace('/\s+/u', ' ', (string) $chuoi));
}
