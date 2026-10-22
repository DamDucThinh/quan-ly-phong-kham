<?php
/*
 * Cài đặt cơ sở dữ liệu: tạo bảng (schema.sql) rồi nạp dữ liệu mẫu (du-lieu-mau.sql).
 *
 *   php database/cai-dat.php            hỏi lại trước khi xóa CSDL cũ
 *   php database/cai-dat.php --dong-y   không hỏi lại
 *
 * Chạy lại sẽ XÓA toàn bộ dữ liệu đang có trong CSDL.
 */

require __DIR__ . '/../includes/cau-hinh.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chạy file này bằng dòng lệnh: php database/cai-dat.php');
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', CSDL_MAY_CHU, CSDL_CONG),
        CSDL_NGUOI_DUNG,
        CSDL_MAT_KHAU,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $loi) {
    fwrite(STDERR, 'Không kết nối được MySQL: ' . $loi->getMessage() . PHP_EOL);
    exit(1);
}

$da_co = $pdo->query('SHOW DATABASES LIKE ' . $pdo->quote(CSDL_TEN))->fetchColumn();
if ($da_co && !in_array('--dong-y', $argv, true)) {
    echo 'CSDL ' . CSDL_TEN . " đã có. Cài lại sẽ xóa toàn bộ dữ liệu trong đó. Gõ 'co' để tiếp tục: ";
    if (trim((string) fgets(STDIN)) !== 'co') {
        exit("Đã hủy, không thay đổi gì.\n");
    }
}

/** Tách file .sql thành từng câu lệnh: bỏ dòng chú thích, cắt ở dấu ; cuối dòng. */
function cac_cau_lenh(string $tep): array
{
    $sql = preg_replace('/\bphong_kham\b/', CSDL_TEN, (string) file_get_contents($tep));
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    return array_values(array_filter(array_map('trim', preg_split('/;\s*$/m', $sql)), fn($cau) => $cau !== ''));
}

foreach (['schema.sql' => 'Tạo bảng', 'du-lieu-mau.sql' => 'Nạp dữ liệu mẫu'] as $tep => $viec) {
    echo "$viec ($tep)... ";
    foreach (cac_cau_lenh(__DIR__ . '/' . $tep) as $cau) {
        try {
            $pdo->exec($cau);
        } catch (PDOException $loi) {
            fwrite(STDERR, "\nLỗi ở câu lệnh:\n" . mb_substr($cau, 0, 300) . "\n" . $loi->getMessage() . PHP_EOL);
            exit(1);
        }
    }
    echo "xong\n";
}

$dem = $pdo->query('SELECT
    (SELECT COUNT(*) FROM bac_si) AS bac_si,
    (SELECT COUNT(*) FROM thuoc) AS thuoc,
    (SELECT COUNT(*) FROM benh_nhan) AS benh_nhan,
    (SELECT COUNT(*) FROM lich_hen) AS lich_hen,
    (SELECT COUNT(*) FROM benh_an) AS benh_an,
    (SELECT COUNT(*) FROM don_thuoc) AS don_thuoc')->fetch(PDO::FETCH_ASSOC);

echo "\nĐã cài CSDL " . CSDL_TEN . ': ' . $dem['bac_si'] . ' bác sĩ, ' . $dem['thuoc'] . ' thuốc, '
    . $dem['benh_nhan'] . ' bệnh nhân, ' . $dem['lich_hen'] . ' lịch hẹn, '
    . $dem['benh_an'] . ' bệnh án, ' . $dem['don_thuoc'] . " đơn thuốc.\n";
echo "Đăng nhập: quanly01 / matkhau (Quản lý phòng khám), 0905447812 / matkhau (Bệnh nhân).\n";
