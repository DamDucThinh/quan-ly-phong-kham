<?php
/*
 * Đăng nhập, đăng xuất và phân quyền. Bốn vai trò: QT (Quản trị), NV (Nhân viên), BS (Bác sĩ) và BN (Bệnh nhân).
 */

const VAI_TRO = [
    'QT' => 'Quản trị',
    'NV' => 'Nhân viên',
    'BS' => 'Bác sĩ',
    'BN' => 'Bệnh nhân',
];

function nguoi_dung(): ?array
{
    return $_SESSION['nguoi_dung'] ?? null;
}

function dang_nhap(array $tai_khoan): void
{
    $ho_ten = $tai_khoan['ho_ten'];
    if ($tai_khoan['vai_tro'] === 'BN') {
        $ho_ten = mot_gia_tri('SELECT ho_ten FROM benh_nhan WHERE id = ?', [$tai_khoan['benh_nhan_id']]);
    }
    session_regenerate_id(true);
    $_SESSION['nguoi_dung'] = [
        'id' => (int) $tai_khoan['id'],
        'vai_tro' => $tai_khoan['vai_tro'],
        'ho_ten' => (string) $ho_ten,
        'benh_nhan_id' => $tai_khoan['benh_nhan_id'] === null ? null : (int) $tai_khoan['benh_nhan_id'],
        'bac_si_id' => $tai_khoan['bac_si_id'] === null ? null : (int) $tai_khoan['bac_si_id'],
    ];
}

function dang_xuat(): void
{
    $_SESSION = [];
    session_destroy();
}

function trang_chu_cua(string $vai_tro): string
{
    return [
        'QT' => 'quan-tri/trang-chu.php',
        'NV' => 'nhan-vien/trang-chu.php',
        'BS' => 'bac-si/trang-chu.php',
    ][$vai_tro] ?? 'benh-nhan/trang-chu.php';
}

/** Chặn trang khi chưa đăng nhập hoặc sai vai trò. Trả về người dùng hiện tại. */
function yeu_cau_vai_tro(string ...$vai_tro): array
{
    $nguoi_dung = nguoi_dung();
    if (!$nguoi_dung) {
        chuyen_huong('dang-nhap.php');
    }
    if (!in_array($nguoi_dung['vai_tro'], $vai_tro, true)) {
        chuyen_huong(trang_chu_cua($nguoi_dung['vai_tro']));
    }
    return $nguoi_dung;
}
