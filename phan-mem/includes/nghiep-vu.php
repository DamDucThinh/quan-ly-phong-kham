<?php
/*
 * Quy tắc nghiệp vụ dùng ở nhiều màn: danh mục cố định, khung giờ khám (CN 2.2),
 * mã tự sinh, tiền sử bệnh, thông báo gửi bệnh nhân.
 */

const CHUYEN_KHOA = ['Nội tổng quát', 'Nhi khoa', 'Tai Mũi Họng', 'Da liễu', 'Răng Hàm Mặt'];
const PHONG = ['Phòng 01', 'Phòng 02', 'Phòng 03', 'Phòng 04', 'Phòng 05', 'Phòng 06'];
const NHOM_THUOC = ['Giảm đau, hạ sốt', 'Giảm đau, chống viêm', 'Kháng sinh', 'Kháng histamin', 'Tiêu hóa', 'Long đờm', 'Bù nước, điện giải'];
const DON_VI_THUOC = ['Viên', 'Gói', 'Chai', 'Ống', 'Tuýp'];
const LY_DO_HUY = ['Bận việc đột xuất', 'Đã khỏi bệnh', 'Muốn khám ở nơi khác', 'Lý do khác'];
const TRANG_THAI_LICH_HEN = [
    'da_dat' => 'Chưa đến',
    'cho_kham' => 'Chờ khám',
    'dang_kham' => 'Đang khám',
    'da_kham' => 'Đã khám',
    'da_huy' => 'Đã hủy',
];

/** Mốc giờ chọn khi nhập ca làm việc: 06:00 đến 21:00, cách nhau 30 phút. */
function cac_moc_gio(): array
{
    $ds = [];
    for ($phut = 6 * 60; $phut <= 21 * 60; $phut += 30) {
        $ds[] = sprintf('%02d:%02d', intdiv($phut, 60), $phut % 60);
    }
    return $ds;
}

/** Các ngày bệnh nhân đặt được lịch: hôm nay và các ngày kế tiếp. */
function ngay_dat_duoc(): array
{
    $ds = [];
    for ($i = 0; $i < SO_NGAY_DAT_TRUOC; $i++) {
        $ds[] = date('Y-m-d', strtotime(hom_nay() . " +$i day"));
    }
    return $ds;
}

/** Khung giờ khám của bác sĩ trong một ngày, cắt từ các ca làm việc (D1). */
function khung_gio_trong_ngay(int $bac_si_id, string $ngay): array
{
    $ca = nhieu_dong(
        'SELECT gio_bat_dau, gio_ket_thuc FROM ca_lam_viec WHERE bac_si_id = ? AND thu = ? ORDER BY gio_bat_dau',
        [$bac_si_id, (int) date('N', strtotime($ngay))]
    );
    $khung = [];
    foreach ($ca as $c) {
        $ket_thuc = strtotime("$ngay {$c['gio_ket_thuc']}");
        for ($t = strtotime("$ngay {$c['gio_bat_dau']}"); $t + PHUT_MOI_KHUNG * 60 <= $ket_thuc; $t += PHUT_MOI_KHUNG * 60) {
            $khung[] = date('H:i', $t);
        }
    }
    return array_values(array_unique($khung));
}

/** CN 2.2 Kiểm tra lịch trống: từng khung giờ kèm trạng thái còn trống hay không (đọc D1 và D4). */
function kiem_tra_lich_trong(int $bac_si_id, string $ngay): array
{
    $da_dat = array_map('gio_ngan', array_column(
        nhieu_dong('SELECT gio FROM lich_hen WHERE bac_si_id = ? AND ngay = ? AND giu_cho = 1', [$bac_si_id, $ngay]),
        'gio'
    ));
    $ket_qua = [];
    foreach (khung_gio_trong_ngay($bac_si_id, $ngay) as $gio) {
        $da_qua = strtotime("$ngay $gio") <= time();
        $bi_dat = in_array($gio, $da_dat, true);
        $ket_qua[] = [
            'gio' => $gio,
            'trong' => !$da_qua && !$bi_dat,
            'ly_do' => $bi_dat ? 'đã có người đặt' : ($da_qua ? 'đã qua giờ' : ''),
        ];
    }
    return $ket_qua;
}

function khung_gio_con_trong(int $bac_si_id, string $ngay, string $gio): bool
{
    if (!in_array($ngay, ngay_dat_duoc(), true)) {
        return false;
    }
    foreach (kiem_tra_lich_trong($bac_si_id, $ngay) as $khung) {
        if ($khung['gio'] === $gio) {
            return $khung['trong'];
        }
    }
    return false;
}

/** Lịch hẹn bệnh nhân còn đổi hoặc hủy được: chưa tiếp nhận, chưa hủy, chưa tới giờ. */
function lich_hen_doi_duoc(int $lich_hen_id, int $benh_nhan_id): ?array
{
    return mot_dong(
        "SELECT lh.*, bs.ho_ten AS bac_si, bs.chuyen_khoa, bs.phong
         FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
         WHERE lh.id = ? AND lh.benh_nhan_id = ? AND lh.trang_thai = 'da_dat' AND TIMESTAMP(lh.ngay, lh.gio) > ?",
        [$lich_hen_id, $benh_nhan_id, bay_gio()]
    );
}

/**
 * Lịch hẹn đang được đổi (tham số doi trên các màn [9]–[11]). Lịch không còn đổi được thì quay về [12].
 */
function lich_dang_doi(int $benh_nhan_id): ?array
{
    $id = (int) ($_POST['doi'] ?? $_GET['doi'] ?? 0);
    if ($id === 0) {
        return null;
    }
    $lich_hen = lich_hen_doi_duoc($id, $benh_nhan_id);
    if (!$lich_hen) {
        dat_thong_diep('err', 'Lịch hẹn này không còn đổi được (đã tới giờ khám, đã hủy hoặc đã được tiếp nhận).');
        chuyen_huong('benh-nhan/lich-hen.php');
    }
    return $lich_hen;
}

function ma_benh_nhan_moi(): string
{
    $so = (int) mot_gia_tri("SELECT COALESCE(MAX(CAST(SUBSTRING(ma, 4) AS UNSIGNED)), 0) FROM benh_nhan WHERE ma LIKE 'BN-%'");
    return sprintf('BN-%05d', $so + 1);
}

function ma_thuoc_moi(): string
{
    $so = (int) mot_gia_tri("SELECT COALESCE(MAX(CAST(SUBSTRING(ma, 3) AS UNSIGNED)), 0) FROM thuoc WHERE ma LIKE 'TH%'");
    return sprintf('TH%03d', $so + 1);
}

/** Tiền sử: chẩn đoán ở các lần khám trước (D5), mới nhất trước. */
function tien_su(int $benh_nhan_id, int $tru_lich_hen_id = 0): array
{
    return nhieu_dong(
        'SELECT chan_doan, MAX(ngay_kham) AS ngay FROM benh_an
         WHERE benh_nhan_id = ? AND lich_hen_id <> ? GROUP BY chan_doan ORDER BY ngay DESC',
        [$benh_nhan_id, $tru_lich_hen_id]
    );
}

function gui_thong_bao(int $benh_nhan_id, string $loai, string $tieu_de, string $noi_dung): void
{
    truy_van(
        'INSERT INTO thong_bao (benh_nhan_id, loai, tieu_de, noi_dung, tao_luc) VALUES (?, ?, ?, ?, ?)',
        [$benh_nhan_id, $loai, $tieu_de, $noi_dung, bay_gio()]
    );
}

/** Paracetamol 500 mg */
function ten_thuoc(array $thuoc): string
{
    return $thuoc['ten'] . ' ' . $thuoc['ham_luong'];
}

/** 14 viên */
function so_luong_thuoc(int|string $so_luong, string $don_vi): string
{
    return so((int) $so_luong) . ' ' . mb_strtolower($don_vi);
}
