<?php
/*
 * [9] Chọn bác sĩ, ngày khám — CN 2.1 Tiếp nhận yêu cầu đặt lịch.
 * Mỗi ngày hiện số khung giờ còn trống (CN 2.2). Khi đổi lịch, tham số doi là mã lịch hẹn cũ.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$doi = lich_dang_doi($nguoi_dung['benh_nhan_id']);
$ma_doi = $doi['id'] ?? null;

$ds_bac_si = nhieu_dong('SELECT * FROM bac_si ORDER BY id');
$bac_si_id = (int) ($_GET['bs'] ?? ($doi['bac_si_id'] ?? 0));
$bac_si = null;
foreach ($ds_bac_si as $bs) {
    if ((int) $bs['id'] === $bac_si_id) {
        $bac_si = $bs;
    }
}
$bac_si ??= $ds_bac_si[0] ?? null;

$cac_ngay = [];
$ngay_het = null;
if ($bac_si) {
    foreach (ngay_dat_duoc() as $ngay) {
        $khung = kiem_tra_lich_trong((int) $bac_si['id'], $ngay);
        $muc = [
            'ngay' => $ngay,
            'con' => count(array_filter($khung, fn($k) => $k['trong'])),
            'lam_viec' => $khung !== [],
        ];
        $cac_ngay[] = $muc;
        if (($_GET['het'] ?? '') === $ngay && $muc['con'] === 0) {
            $ngay_het = $muc;
        }
    }
}

mo_trang($doi ? 'Đổi lịch hẹn' : 'Đặt lịch khám', 'bn', 'book');
?>
<main class="pg w1200">
  <div class="pg-head">
    <h1><?= $doi ? 'Đổi lịch hẹn' : 'Đặt lịch khám' ?></h1>
  </div>

  <?php if ($doi): ?>
    <div class="alert info">Đang đổi lịch <strong><?= e(thoi_diem($doi['ngay'], $doi['gio'])) ?></strong> với <?= e(ten_bac_si($doi['bac_si'])) ?>.
      Lịch cũ được giữ nguyên cho tới khi bạn xác nhận lịch mới. <a href="<?= e(url('benh-nhan/lich-hen.php')) ?>">Thôi, không đổi</a></div>
  <?php endif; ?>

  <?php if (!$bac_si): ?>
    <div class="alert warn">Phòng khám chưa có bác sĩ nào nhận lịch hẹn. Vui lòng gọi hotline <?= e(HOTLINE) ?>.</div>
  <?php else: ?>
    <section class="card p28 stack g18">
      <h2 style="font-size:20px">Chọn bác sĩ</h2>
      <div class="pick-grid" role="list" aria-label="Bác sĩ">
        <?php foreach ($ds_bac_si as $bs): $dang_chon = $bs['id'] === $bac_si['id']; ?>
          <a role="listitem" class="pick<?= $dang_chon ? ' on' : '' ?>" href="<?= e(url('benh-nhan/chon-bac-si.php', ['bs' => $bs['id'], 'doi' => $ma_doi])) ?>"<?= $dang_chon ? ' aria-current="true"' : '' ?>>
            <span class="avatar lg"><?= e(chu_cai_dau($bs['ho_ten'])) ?></span>
            <span class="pick-col">
              <span class="t"><?= e(ten_bac_si($bs['ho_ten'])) ?></span>
              <span class="s"><?= e($bs['chuyen_khoa']) ?> · <?= e($bs['phong']) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card p28 stack g18" id="chon-ngay">
      <div class="stack g4">
        <h2 style="font-size:20px">Chọn ngày khám</h2>
        <span class="muted" style="font-size:15px">Lịch của <?= e(ten_bac_si($bac_si['ho_ten'])) ?> trong <?= SO_NGAY_DAT_TRUOC ?> ngày tới</span>
      </div>
      <div class="days">
        <?php foreach ($cac_ngay as $d): $thu = ten_thu($d['ngay']); $ngay = ngay_ngan($d['ngay']); ?>
          <?php if ($d['con'] > 0): ?>
            <a class="day" href="<?= e(url('benh-nhan/chon-gio.php', ['bs' => $bac_si['id'], 'ngay' => $d['ngay'], 'doi' => $ma_doi])) ?>"
               aria-label="<?= e("$thu $ngay, còn {$d['con']} giờ trống") ?>">
              <span class="w"><?= e($thu) ?></span><span class="d"><?= e($ngay) ?></span><span class="f">Còn <?= $d['con'] ?> giờ</span>
            </a>
          <?php else: ?>
            <a class="day off<?= $ngay_het && $ngay_het['ngay'] === $d['ngay'] ? ' sel' : '' ?>"
               href="<?= e(url('benh-nhan/chon-bac-si.php', ['bs' => $bac_si['id'], 'het' => $d['ngay'], 'doi' => $ma_doi])) ?>#chon-ngay"
               aria-label="<?= e("$thu $ngay, " . ($d['lam_viec'] ? 'hết chỗ' : 'bác sĩ nghỉ')) ?>">
              <span class="w"><?= e($thu) ?></span><span class="d"><?= e($ngay) ?></span><span class="f"><?= $d['lam_viec'] ? 'Hết chỗ' : 'Nghỉ' ?></span>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if ($ngay_het): ?>
        <div class="alert warn stack g6" role="alert">
          <strong><?= e(ten_bac_si($bac_si['ho_ten'])) ?>
            <?= $ngay_het['lam_viec'] ? 'đã hết khung giờ trống vào' : 'không làm việc vào' ?>
            <?= e(ten_thu($ngay_het['ngay']) . ' ' . ngay_ngan($ngay_het['ngay'])) ?>.</strong>
          <span>Gợi ý: chọn ngày khác còn giờ trống, hoặc chọn bác sĩ khác cùng chuyên khoa.</span>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
