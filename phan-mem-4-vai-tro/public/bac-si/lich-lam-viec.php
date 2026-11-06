<?php
/*
 * Lịch làm việc của tôi (Bác sĩ): các ca trong tuần do quản trị khai báo (đọc D1) và số lịch hẹn 7 ngày tới (đọc D4).
 * Bác sĩ chỉ xem; muốn đổi ca thì báo quản trị.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BS');
$bac_si_id = $nguoi_dung['bac_si_id'];
$bac_si = mot_dong('SELECT * FROM bac_si WHERE id = ?', [$bac_si_id]);
$ca_theo_thu = [];
foreach (nhieu_dong('SELECT * FROM ca_lam_viec WHERE bac_si_id = ? ORDER BY thu, gio_bat_dau', [$bac_si_id]) as $ca) {
    $ca_theo_thu[(int) $ca['thu']][] = gio_ngan($ca['gio_bat_dau']) . '–' . gio_ngan($ca['gio_ket_thuc']);
}
$so_hen = array_column(nhieu_dong(
    "SELECT ngay, COUNT(*) AS so FROM lich_hen WHERE bac_si_id = ? AND ngay BETWEEN ? AND ? AND trang_thai <> 'da_huy' GROUP BY ngay",
    [$bac_si_id, hom_nay(), date('Y-m-d', strtotime(hom_nay() . ' +' . (SO_NGAY_DAT_TRUOC - 1) . ' day'))]
), 'so', 'ngay');

mo_trang('Lịch làm việc', 'bs', 'schedule');
?>
<main class="pg w1000">
  <div class="pg-head">
    <h1>Lịch làm việc của tôi</h1>
    <p><?= e(ten_bac_si($bac_si['ho_ten'])) ?> · <?= e($bac_si['chuyen_khoa']) ?> · <?= e($bac_si['phong']) ?>. Muốn đổi ca, vui lòng báo quản trị.</p>
  </div>

  <section class="card p22 stack g14">
    <h2>Các ca trong tuần</h2>
    <div class="tbl-box">
      <table style="min-width:520px">
        <thead><tr><th>Thứ</th><th>Ca làm việc</th></tr></thead>
        <tbody>
          <?php foreach (THU_TRONG_TUAN as $thu => $ten): ?>
            <tr>
              <td class="strong"><?= e($ten) ?></td>
              <td<?= isset($ca_theo_thu[$thu]) ? '' : ' class="muted"' ?>><?= e(isset($ca_theo_thu[$thu]) ? implode(', ', $ca_theo_thu[$thu]) : 'Nghỉ') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="card p22 stack g14">
    <h2>Lịch hẹn <?= SO_NGAY_DAT_TRUOC ?> ngày tới</h2>
    <div class="days">
      <?php foreach (ngay_dat_duoc() as $ngay): ?>
        <span class="day<?= isset($so_hen[$ngay]) ? '' : ' off' ?>"><span class="w"><?= e(ten_thu($ngay)) ?></span><span class="d"><?= e(ngay_ngan($ngay)) ?></span><span class="f"><?= (int) ($so_hen[$ngay] ?? 0) ?> lịch hẹn</span></span>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php dong_trang(); ?>
