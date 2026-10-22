<?php
/*
 * [10] Chọn khung giờ trống — hệ thống đã kiểm tra lịch trống (CN 2.2: đọc lịch làm việc D1, lịch đã đặt D4).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$doi = lich_dang_doi($nguoi_dung['benh_nhan_id']);
$ma_doi = $doi['id'] ?? null;

$bac_si = mot_dong('SELECT * FROM bac_si WHERE id = ?', [(int) ($_GET['bs'] ?? 0)]);
$ngay = (string) ($_GET['ngay'] ?? '');
if (!$bac_si || !in_array($ngay, ngay_dat_duoc(), true)) {
    chuyen_huong('benh-nhan/chon-bac-si.php', ['doi' => $ma_doi]);
}

$khung = kiem_tra_lich_trong((int) $bac_si['id'], $ngay);
if (!array_filter($khung, fn($k) => $k['trong'])) {
    // Nhánh "không còn khung giờ trống": quay lại [9] với cảnh báo hết chỗ
    chuyen_huong('benh-nhan/chon-bac-si.php', ['bs' => $bac_si['id'], 'het' => $ngay, 'doi' => $ma_doi]);
}

mo_trang('Chọn giờ khám', 'bn', 'book');
?>
<main class="pg w1200">
  <div class="pg-head">
    <h1><?= $doi ? 'Đổi lịch hẹn' : 'Đặt lịch khám' ?></h1>
  </div>

  <a class="summary" href="<?= e(url('benh-nhan/chon-bac-si.php', ['bs' => $bac_si['id'], 'doi' => $ma_doi])) ?>"
     aria-label="<?= e(ten_bac_si($bac_si['ho_ten']) . ', ' . $bac_si['chuyen_khoa'] . ', ' . $bac_si['phong'] . ', ngày khám ' . thu_ngay($ngay) . '. Bấm để sửa') ?>">
    <span class="row grow" style="gap:14px;flex:1 1 260px;flex-wrap:nowrap">
      <span class="avatar xl"><?= e(chu_cai_dau($bac_si['ho_ten'])) ?></span>
      <span class="stack g2" style="min-width:0">
        <strong style="font-size:17px;font-weight:600"><?= e(ten_bac_si($bac_si['ho_ten'])) ?></strong>
        <span class="muted" style="font-size:14px"><?= e($bac_si['chuyen_khoa']) ?> · <?= e($bac_si['phong']) ?></span>
      </span>
    </span>
    <span class="row" style="margin-left:auto;flex-wrap:nowrap">
      <span class="date-chip"><?= str_replace('currentColor', '#0E7C70', bieu_tuong(ICON_LICH, 18)) ?><?= e(thu_ngay($ngay)) ?></span>
      <span class="edit"><?= bieu_tuong(ICON_BUT) ?></span>
    </span>
  </a>

  <section class="card p28 stack g16">
    <div class="stack g4">
      <h2 style="font-size:18px">Chọn giờ khám</h2>
      <span class="muted" style="font-size:15px">Bấm vào giờ còn trống để sang bước xác nhận</span>
    </div>
    <div class="slots" role="list" aria-label="Giờ khám">
      <?php foreach ($khung as $k): ?>
        <?php if ($k['trong']): ?>
          <a role="listitem" class="slot" href="<?= e(url('benh-nhan/xac-nhan.php', ['bs' => $bac_si['id'], 'ngay' => $ngay, 'gio' => $k['gio'], 'doi' => $ma_doi])) ?>"
             aria-label="<?= e($k['gio'] . ', bấm để chọn giờ này') ?>"><?= e($k['gio']) ?></a>
        <?php else: ?>
          <span role="listitem" class="slot busy" aria-label="<?= e($k['gio'] . ', ' . $k['ly_do']) ?>"><?= e($k['gio']) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php dong_trang(); ?>
