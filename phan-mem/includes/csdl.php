<?php
/*
 * Kết nối MySQL bằng PDO và các hàm truy vấn ngắn gọn.
 */

function csdl(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', CSDL_MAY_CHU, CSDL_CONG, CSDL_TEN);
        try {
            $pdo = new PDO($dsn, CSDL_NGUOI_DUNG, CSDL_MAT_KHAU, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $pdo->exec("SET time_zone = '+07:00'");
        } catch (PDOException $loi) {
            http_response_code(500);
            exit('Không kết nối được cơ sở dữ liệu. Hãy kiểm tra MySQL đã chạy và đã chạy lệnh "php database/cai-dat.php" (xem README.md). Chi tiết: '
                . htmlspecialchars($loi->getMessage()));
        }
    }
    return $pdo;
}

function truy_van(string $sql, array $tham_so = []): PDOStatement
{
    $lenh = csdl()->prepare($sql);
    $lenh->execute($tham_so);
    return $lenh;
}

function mot_dong(string $sql, array $tham_so = []): ?array
{
    $dong = truy_van($sql, $tham_so)->fetch();
    return $dong === false ? null : $dong;
}

function nhieu_dong(string $sql, array $tham_so = []): array
{
    return truy_van($sql, $tham_so)->fetchAll();
}

function mot_gia_tri(string $sql, array $tham_so = []): mixed
{
    return truy_van($sql, $tham_so)->fetchColumn();
}

/** Lỗi trùng khóa duy nhất của MySQL (mã 1062). */
function la_trung_khoa(PDOException $loi): bool
{
    return (int) ($loi->errorInfo[1] ?? 0) === 1062;
}
