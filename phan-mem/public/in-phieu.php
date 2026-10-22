<?php
/*
 * Bản in phiếu kết quả khám và đơn thuốc (CN 3.3, CN 4.3). Người dùng chọn "Lưu thành PDF" khi in để có file PDF.
 * Các trang khác nạp trang này vào khung ẩn để in (assets/app.js), người dùng không bị chuyển sang đây.
 * Tham số: id = mã bệnh án, phan = ket-qua | don-thuoc | ca-hai.
 * Quản lý xem được mọi phiếu; bệnh nhân chỉ xem được phiếu của mình.
 */
require __DIR__ . '/../includes/khoi-dong.php';

$nguoi_dung = nguoi_dung() ?? chuyen_huong('dang-nhap.php');

$benh_an = mot_dong(
    'SELECT ba.*, bn.ma, bn.ho_ten AS benh_nhan, bn.nam_sinh, bn.gioi_tinh, bn.dia_chi, bn.so_dien_thoai,
            bs.ho_ten AS bac_si, bs.chuyen_khoa, bs.phong
     FROM benh_an ba
     JOIN benh_nhan bn ON bn.id = ba.benh_nhan_id
     JOIN bac_si bs ON bs.id = ba.bac_si_id
     WHERE ba.id = ?',
    [(int) ($_GET['id'] ?? 0)]
);
if (!$benh_an || ($nguoi_dung['vai_tro'] === 'BN' && (int) $benh_an['benh_nhan_id'] !== $nguoi_dung['benh_nhan_id'])) {
    http_response_code(404);
    exit('Không tìm thấy phiếu.');
}

$thuoc = nhieu_dong(
    'SELECT t.ten, t.ham_luong, t.don_vi, ct.so_luong, ct.cach_dung
     FROM don_thuoc dt JOIN chi_tiet_don_thuoc ct ON ct.don_thuoc_id = dt.id JOIN thuoc t ON t.id = ct.thuoc_id
     WHERE dt.benh_an_id = ? ORDER BY ct.id',
    [$benh_an['id']]
);
$phan = $_GET['phan'] ?? 'ca-hai';
$in_ket_qua = $phan !== 'don-thuoc';
$in_don = $phan !== 'ket-qua' && $thuoc;
$quay_lai = $nguoi_dung['vai_tro'] === 'QL'
    ? url('quan-ly/lich-hen-hom-nay.php')
    : url('benh-nhan/ket-qua.php', ['id' => $benh_an['id']]);

function dau_phieu(array $benh_an, string $tieu_de): void
{
    ?>
    <div class="phieu-head">
      <div class="stack g4">
        <strong style="font-size:18px"><?= e(TEN_PHONG_KHAM) ?></strong>
        <span class="muted"><?= e(DIA_CHI) ?></span>
        <span class="muted">Hotline: <?= e(HOTLINE) ?></span>
      </div>
      <div class="stack g4" style="text-align:right">
        <span class="muted">Ngày khám</span>
        <strong><?= e(ngay_vn($benh_an['ngay_kham'])) ?></strong>
      </div>
    </div>
    <h1><?= e($tieu_de) ?></h1>
    <div class="info">
      <span>Họ và tên: <strong><?= e($benh_an['benh_nhan']) ?></strong></span>
      <span>Mã bệnh nhân: <strong><?= e($benh_an['ma'] ?? '—') ?></strong></span>
      <span>Năm sinh: <?= e($benh_an['nam_sinh'] ?? '—') ?> · Giới tính: <?= e($benh_an['gioi_tinh'] ?? '—') ?></span>
      <span>Điện thoại: <?= e(dinh_dang_sdt($benh_an['so_dien_thoai'])) ?></span>
      <span>Địa chỉ: <?= e($benh_an['dia_chi'] ?: '—') ?></span>
      <span>Bác sĩ: <?= e(ten_bac_si($benh_an['bac_si'])) ?> · <?= e($benh_an['chuyen_khoa']) ?></span>
    </div>
    <?php
}

function chu_ky(array $benh_an): void
{
    ?>
    <div class="sign"><div><span>Bác sĩ khám bệnh</span><strong><?= e(ten_bac_si($benh_an['bac_si'])) ?></strong></div></div>
    <?php
}

mo_trang('Phiếu khám ' . ngay_vn($benh_an['ngay_kham']));
?>
<div class="phieu-wrap">
  <div class="row between no-print">
    <a class="back-link" href="<?= e($quay_lai) ?>">‹ Quay lại</a>
    <button class="btn pri" type="button" onclick="window.print()">In / Lưu thành PDF</button>
  </div>

  <?php if ($in_ket_qua): ?>
    <section class="phieu">
      <?php dau_phieu($benh_an, 'PHIẾU KẾT QUẢ KHÁM'); ?>
      <div class="vitals">
        <div class="vital"><span class="lb">Mạch (lần/phút)</span><strong><?= e($benh_an['mach'] ?: '—') ?></strong></div>
        <div class="vital"><span class="lb">Nhiệt độ (°C)</span><strong><?= e($benh_an['nhiet_do'] ?: '—') ?></strong></div>
        <div class="vital"><span class="lb">Huyết áp (mmHg)</span><strong><?= e($benh_an['huyet_ap'] ?: '—') ?></strong></div>
        <div class="vital"><span class="lb">Cân nặng (kg)</span><strong><?= e($benh_an['can_nang'] ?: '—') ?></strong></div>
      </div>
      <div class="stack g4"><span class="lb">Kết quả khám</span><p style="line-height:1.6"><?= nl2br(e($benh_an['ket_qua'])) ?></p></div>
      <div class="stack g4"><span class="lb">Chẩn đoán</span><strong style="font-size:18px"><?= e($benh_an['chan_doan']) ?></strong></div>
      <?php if ($benh_an['loi_dan']): ?>
        <div class="stack g4"><span class="lb">Lời dặn của bác sĩ</span><p style="line-height:1.6"><?= nl2br(e($benh_an['loi_dan'])) ?></p></div>
      <?php endif; ?>
      <?php chu_ky($benh_an); ?>
    </section>
  <?php endif; ?>

  <?php if ($in_don): ?>
    <section class="phieu">
      <?php dau_phieu($benh_an, 'ĐƠN THUỐC'); ?>
      <div class="stack g4"><span class="lb">Chẩn đoán</span><strong><?= e($benh_an['chan_doan']) ?></strong></div>
      <table>
        <thead><tr><th>STT</th><th>Thuốc</th><th class="t-right">Số lượng</th><th>Cách dùng</th></tr></thead>
        <tbody>
          <?php foreach ($thuoc as $i => $t): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td class="strong"><?= e(ten_thuoc($t)) ?></td>
              <td class="t-right num"><?= e(so_luong_thuoc($t['so_luong'], $t['don_vi'])) ?></td>
              <td><?= e($t['cach_dung']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if ($benh_an['loi_dan']): ?>
        <p><span class="lb">Lời dặn:</span> <?= e($benh_an['loi_dan']) ?></p>
      <?php endif; ?>
      <?php chu_ky($benh_an); ?>
    </section>
  <?php endif; ?>

  <?php if (!$in_ket_qua && !$in_don): ?>
    <div class="alert info">Lần khám này không kê đơn thuốc.</div>
  <?php endif; ?>
</div>
<?php dong_trang(); ?>
