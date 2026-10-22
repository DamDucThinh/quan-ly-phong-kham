<?php
/*
 * [12] Lịch hẹn của tôi — CN 2.4 Đổi, hủy lịch hẹn (đọc D4). Chỉ hiện lịch chưa khám, chưa tới giờ.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$ds_lich_hen = nhieu_dong(
    "SELECT lh.*, bs.ho_ten AS bac_si, bs.chuyen_khoa, bs.phong
     FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.benh_nhan_id = ? AND lh.trang_thai = 'da_dat' AND TIMESTAMP(lh.ngay, lh.gio) > ?
     ORDER BY lh.ngay, lh.gio",
    [$nguoi_dung['benh_nhan_id'], bay_gio()]
);

mo_trang('Lịch hẹn của tôi', 'bn', 'home');
?>
<main class="pg w1000" style="gap:24px">
  <div class="head-row">
    <div class="pg-head">
      <a class="back-link" href="<?= e(url('benh-nhan/trang-chu.php')) ?>">‹ Trang chủ</a>
      <h1>Lịch hẹn của tôi</h1>
    </div>
    <a class="btn pri" href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>">+ Đặt lịch mới</a>
  </div>

  <?= hien_thong_diep() ?>

  <?php foreach ($ds_lich_hen as $lh): ?>
    <article class="card p24 row" style="gap:20px 28px">
      <?= o_ngay($lh['ngay'], true) ?>
      <div class="stack" style="flex:1 1 280px;gap:5px">
        <div class="row" style="gap:10px">
          <strong style="font-size:22px;font-weight:600"><?= e(gio_ngan($lh['gio'])) ?></strong>
          <span class="pill ok">Đã đặt</span>
        </div>
        <span style="font-size:16px"><?= e(ten_bac_si($lh['bac_si'])) ?> · <?= e($lh['chuyen_khoa']) ?> · <?= e($lh['phong']) ?></span>
        <?php if ($lh['ly_do']): ?><span class="muted" style="font-size:15px">Lý do: <?= e($lh['ly_do']) ?></span><?php endif; ?>
      </div>
      <div class="row stack-btns" style="gap:10px">
        <a class="btn" href="<?= e(url('benh-nhan/chon-bac-si.php', ['doi' => $lh['id']])) ?>">Đổi lịch</a>
        <a class="btn red" href="<?= e(url('benh-nhan/huy-lich.php', ['id' => $lh['id']])) ?>">Hủy lịch</a>
      </div>
    </article>
  <?php endforeach; ?>

  <?php if (!$ds_lich_hen): ?>
    <section class="card done-box" style="padding:48px;gap:14px">
      <h2 style="font-size:22px">Bạn chưa có lịch hẹn nào</h2>
      <p class="muted" style="font-size:16px">Không có lịch hẹn chưa khám để đổi hoặc hủy.</p>
      <a class="btn pri" href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>">Đặt lịch khám</a>
    </section>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
