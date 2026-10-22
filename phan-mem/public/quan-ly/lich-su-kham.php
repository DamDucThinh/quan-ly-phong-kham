<?php
/*
 * [16] Hồ sơ bệnh án các lần trước — CN 3.2 Tra cứu lịch sử khám (đọc D5), đưa bác sĩ xem trước khi khám.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$lich_hen = mot_dong(
    'SELECT lh.*, bn.ma, bn.ho_ten, bn.gioi_tinh, bn.nam_sinh, bn.so_dien_thoai, bs.ho_ten AS bac_si
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.id = ?',
    [(int) ($_GET['id'] ?? 0)]
) ?? chuyen_huong('quan-ly/lich-hen-hom-nay.php');
if (in_array($lich_hen['trang_thai'], ['da_dat', 'da_huy'], true)) {
    dat_thong_diep('err', 'Bệnh nhân ' . e($lich_hen['ho_ten']) . ' chưa được tiếp nhận.');
    chuyen_huong('quan-ly/lich-hen-hom-nay.php');
}

$cac_lan = nhieu_dong(
    'SELECT ba.id, ba.ngay_kham, ba.chan_doan, bs.ho_ten AS bac_si, bs.chuyen_khoa
     FROM benh_an ba JOIN bac_si bs ON bs.id = ba.bac_si_id
     WHERE ba.benh_nhan_id = ? AND ba.lich_hen_id <> ? ORDER BY ba.ngay_kham DESC, ba.id DESC',
    [$lich_hen['benh_nhan_id'], $lich_hen['id']]
);
$dang_xem = null;
$thuoc = [];
if ($cac_lan) {
    $chon_id = (int) ($_GET['ba'] ?? $cac_lan[0]['id']);
    $dang_xem = mot_dong(
        'SELECT ba.*, bs.ho_ten AS bac_si, bs.chuyen_khoa FROM benh_an ba JOIN bac_si bs ON bs.id = ba.bac_si_id
         WHERE ba.id = ? AND ba.benh_nhan_id = ? AND ba.lich_hen_id <> ?',
        [$chon_id, $lich_hen['benh_nhan_id'], $lich_hen['id']]
    ) ?? chuyen_huong('quan-ly/lich-su-kham.php', ['id' => $lich_hen['id']]);
    $thuoc = nhieu_dong(
        'SELECT t.ten, t.ham_luong, t.don_vi, ct.so_luong, ct.cach_dung
         FROM don_thuoc dt JOIN chi_tiet_don_thuoc ct ON ct.don_thuoc_id = dt.id JOIN thuoc t ON t.id = ct.thuoc_id
         WHERE dt.benh_an_id = ? ORDER BY ct.id',
        [$dang_xem['id']]
    );
}
$da_kham = $lich_hen['trang_thai'] === 'da_kham';

mo_trang('Hồ sơ bệnh án ' . $lich_hen['ho_ten'], 'ql', 'today');
?>
<main class="pg">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-ly/lich-hen-hom-nay.php')) ?>">‹ Lịch hẹn hôm nay</a>
  </div>

  <section class="card row" style="padding:20px 24px;gap:16px 36px">
    <div class="stack g4" style="flex:1 1 260px">
      <span class="pill st-<?= e($lich_hen['trang_thai']) ?>" style="align-self:flex-start"><?= e(TRANG_THAI_LICH_HEN[$lich_hen['trang_thai']]) ?> · <?= e(ten_bac_si($lich_hen['bac_si'])) ?></span>
      <h1 style="margin-top:4px;font-size:26px"><?= e($lich_hen['ho_ten']) ?></h1>
      <span class="muted" style="font-size:15px"><?= e(implode(' · ', array_filter([$lich_hen['ma'], $lich_hen['gioi_tinh'], $lich_hen['nam_sinh'], dinh_dang_sdt($lich_hen['so_dien_thoai'])]))) ?></span>
    </div>
    <div class="stack g2" style="flex:1 1 240px">
      <span class="lb">Lý do khám hôm nay</span>
      <span style="font-size:16px;font-weight:500"><?= e($lich_hen['ly_do'] ?: '—') ?></span>
    </div>
    <a class="btn pri" href="<?= e(url('quan-ly/ket-qua-kham.php', ['id' => $lich_hen['id']])) ?>"><?= $da_kham ? 'Xem kết quả khám lần này' : 'Ghi kết quả khám lần này' ?></a>
  </section>

  <?php if (!$dang_xem): ?>
    <section class="card p24"><p class="muted" style="font-size:16px">Bệnh nhân khám lần đầu, chưa có lịch sử khám.</p></section>
  <?php else: ?>
    <div class="split">
      <section class="card p18 stack g8 side" style="flex:1 1 320px">
        <h2 class="side-title">Lịch sử khám (<?= count($cac_lan) ?> lần)</h2>
        <?php foreach ($cac_lan as $lan): $chon = $lan['id'] === $dang_xem['id']; ?>
          <a class="pick tall thin<?= $chon ? ' on' : '' ?>" style="min-height:76px" href="<?= e(url('quan-ly/lich-su-kham.php', ['id' => $lich_hen['id'], 'ba' => $lan['id']])) ?>"<?= $chon ? ' aria-current="true"' : '' ?>>
            <span class="s" style="font-weight:500"><?= e(ngay_vn($lan['ngay_kham'])) ?></span>
            <span class="t" style="font-size:16px"><?= e($lan['chan_doan']) ?></span>
            <span class="s"><?= e(ten_bac_si($lan['bac_si'])) ?> · <?= e($lan['chuyen_khoa']) ?></span>
          </a>
        <?php endforeach; ?>
      </section>
      <section class="card p24 stack g20 main">
        <div class="row between" style="gap:8px;padding-bottom:14px;border-bottom:1px solid #EBEFEE">
          <div class="stack g4">
            <span class="lb">Bệnh án ngày <?= e(ngay_vn($dang_xem['ngay_kham'])) ?></span>
            <strong style="font-size:20px;font-weight:600"><?= e($dang_xem['chan_doan']) ?></strong>
          </div>
          <span class="muted" style="font-size:15px"><?= e(ten_bac_si($dang_xem['bac_si'])) ?> · <?= e($dang_xem['chuyen_khoa']) ?></span>
        </div>
        <div class="vitals">
          <div class="vital"><span class="lb">Mạch</span><strong><?= e($dang_xem['mach'] ?: '—') ?></strong></div>
          <div class="vital"><span class="lb">Nhiệt độ</span><strong><?= e($dang_xem['nhiet_do'] ? $dang_xem['nhiet_do'] . '°C' : '—') ?></strong></div>
          <div class="vital"><span class="lb">Huyết áp</span><strong><?= e($dang_xem['huyet_ap'] ?: '—') ?></strong></div>
        </div>
        <div class="stack g4"><span class="lb">Kết quả khám</span><p style="font-size:16px;line-height:1.6"><?= nl2br(e($dang_xem['ket_qua'])) ?></p></div>
        <?php if ($dang_xem['loi_dan']): ?>
          <div class="stack g4"><span class="lb">Lời dặn</span><p style="font-size:16px;line-height:1.6"><?= nl2br(e($dang_xem['loi_dan'])) ?></p></div>
        <?php endif; ?>
        <div class="stack g8">
          <span class="lb">Đơn thuốc đã kê</span>
          <?php if ($thuoc): ?>
            <ul class="rx compact">
              <?php foreach ($thuoc as $t): ?>
                <li><span class="strong"><?= e(ten_thuoc($t)) ?></span><span class="muted"><?= e(so_luong_thuoc($t['so_luong'], $t['don_vi'])) ?><?= $t['cach_dung'] ? ' · ' . e($t['cach_dung']) : '' ?></span></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <span class="muted" style="font-size:15px">Không kê đơn.</span>
          <?php endif; ?>
        </div>
      </section>
    </div>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
