<?php
/*
 * Kết quả khám: phiếu kết quả khám và đơn thuốc phòng khám đã gửi cho bệnh nhân sau mỗi lần khám (đọc D5, D6).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$cac_lan_kham = nhieu_dong(
    'SELECT ba.id, ba.ngay_kham, ba.chan_doan, bs.ho_ten AS bac_si, bs.chuyen_khoa
     FROM benh_an ba JOIN bac_si bs ON bs.id = ba.bac_si_id
     WHERE ba.benh_nhan_id = ? ORDER BY ba.ngay_kham DESC, ba.id DESC',
    [$nguoi_dung['benh_nhan_id']]
);

$dang_xem = null;
$thuoc = [];
if ($cac_lan_kham) {
    $id = (int) ($_GET['id'] ?? $cac_lan_kham[0]['id']);
    $dang_xem = mot_dong(
        'SELECT ba.*, bs.ho_ten AS bac_si, bs.chuyen_khoa FROM benh_an ba JOIN bac_si bs ON bs.id = ba.bac_si_id
         WHERE ba.id = ? AND ba.benh_nhan_id = ?',
        [$id, $nguoi_dung['benh_nhan_id']]
    ) ?? chuyen_huong('benh-nhan/ket-qua.php');
    $thuoc = nhieu_dong(
        'SELECT t.ten, t.ham_luong, t.don_vi, ct.so_luong, ct.cach_dung
         FROM don_thuoc dt JOIN chi_tiet_don_thuoc ct ON ct.don_thuoc_id = dt.id JOIN thuoc t ON t.id = ct.thuoc_id
         WHERE dt.benh_an_id = ? ORDER BY ct.id',
        [$dang_xem['id']]
    );
}

mo_trang('Kết quả khám', 'bn', 'result');
?>
<main class="pg w1200" style="gap:24px">
  <div class="pg-head">
    <h1>Kết quả khám</h1>
  </div>

  <?php if (!$dang_xem): ?>
    <section class="card done-box" style="padding:48px;gap:14px">
      <h2 style="font-size:22px">Chưa có kết quả khám</h2>
      <p class="muted" style="font-size:16px">Sau mỗi lần khám, phiếu kết quả và đơn thuốc sẽ hiện ở đây.</p>
      <a class="btn pri" href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>">Đặt lịch khám</a>
    </section>
  <?php else: ?>
    <div class="split">
      <section class="card p18 stack g8 side" style="flex:1 1 280px">
        <h2 class="side-title">Các lần khám</h2>
        <?php foreach ($cac_lan_kham as $lan): $chon = $lan['id'] === $dang_xem['id']; ?>
          <a class="pick tall<?= $chon ? ' on' : '' ?>" href="<?= e(url('benh-nhan/ket-qua.php', ['id' => $lan['id']])) ?>"<?= $chon ? ' aria-current="true"' : '' ?>>
            <span class="s" style="font-weight:500"><?= e(ngay_vn($lan['ngay_kham'])) ?></span>
            <span class="t"><?= e($lan['chan_doan']) ?></span>
            <span class="s"><?= e(ten_bac_si($lan['bac_si'])) ?> · <?= e($lan['chuyen_khoa']) ?></span>
          </a>
        <?php endforeach; ?>
      </section>

      <div class="main stack g24" style="flex:2.6 1 600px">
        <section class="card p26 stack g18">
          <div class="row between" style="align-items:flex-start;padding-bottom:14px;border-bottom:1px solid #EBEFEE">
            <div class="stack g4">
              <span class="lb">Phiếu kết quả khám · <?= e(ngay_vn($dang_xem['ngay_kham'])) ?></span>
              <strong style="font-size:20px;font-weight:600"><?= e(ten_bac_si($dang_xem['bac_si'])) ?> · <?= e($dang_xem['chuyen_khoa']) ?></strong>
            </div>
            <a class="btn" href="<?= e(url('in-phieu.php', ['id' => $dang_xem['id'], 'phan' => 'ket-qua'])) ?>" data-in>Tải phiếu (PDF)</a>
          </div>
          <div class="vitals">
            <div class="vital"><span class="lb">Mạch</span><strong><?= e($dang_xem['mach'] ?: '—') ?></strong></div>
            <div class="vital"><span class="lb">Nhiệt độ</span><strong><?= e($dang_xem['nhiet_do'] ? $dang_xem['nhiet_do'] . '°C' : '—') ?></strong></div>
            <div class="vital"><span class="lb">Huyết áp</span><strong><?= e($dang_xem['huyet_ap'] ?: '—') ?></strong></div>
          </div>
          <div class="stack g4"><span class="lb">Chẩn đoán</span><strong style="font-size:18px;font-weight:600;color:#0A5C53"><?= e($dang_xem['chan_doan']) ?></strong></div>
          <div class="stack g4"><span class="lb">Kết quả khám</span><p style="font-size:16px;line-height:1.6"><?= nl2br(e($dang_xem['ket_qua'])) ?></p></div>
          <?php if ($dang_xem['loi_dan']): ?>
            <div class="stack g4"><span class="lb">Lời dặn của bác sĩ</span><p style="font-size:16px;line-height:1.6"><?= nl2br(e($dang_xem['loi_dan'])) ?></p></div>
          <?php endif; ?>
        </section>

        <section class="card p26 stack g16">
          <div class="row between">
            <h2 style="font-size:19px">Đơn thuốc</h2>
            <?php if ($thuoc): ?>
              <a class="btn" href="<?= e(url('in-phieu.php', ['id' => $dang_xem['id'], 'phan' => 'don-thuoc'])) ?>" data-in>Tải đơn thuốc (PDF)</a>
            <?php endif; ?>
          </div>
          <?php if ($thuoc): ?>
            <ul class="rx">
              <?php foreach ($thuoc as $t): ?>
                <li>
                  <div class="row between" style="gap:6px 12px">
                    <strong style="font-size:16px;font-weight:600"><?= e(ten_thuoc($t)) ?></strong>
                    <span class="num" style="font-size:15px;font-weight:500"><?= e(so_luong_thuoc($t['so_luong'], $t['don_vi'])) ?></span>
                  </div>
                  <?php if ($t['cach_dung']): ?><span class="muted" style="font-size:15px;line-height:1.5"><?= e($t['cach_dung']) ?></span><?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="muted" style="font-size:15px">Lần khám này bác sĩ không kê đơn thuốc.</p>
          <?php endif; ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
