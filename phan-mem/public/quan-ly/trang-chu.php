<?php
/*
 * [3] Trang chủ Quản lý phòng khám: số liệu lịch hẹn hôm nay và các lịch sắp đến giờ cần tiếp nhận.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('QL');
$dem = mot_dong(
    "SELECT SUM(trang_thai <> 'da_huy') AS tong, SUM(trang_thai = 'da_dat') AS chua_den,
            SUM(trang_thai = 'cho_kham') AS cho_kham, SUM(trang_thai = 'da_kham') AS da_kham
     FROM lich_hen WHERE ngay = ?",
    [hom_nay()]
);
$sap_den = nhieu_dong(
    "SELECT lh.id, lh.gio, lh.ly_do, bn.ho_ten, bs.ho_ten AS bac_si
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.ngay = ? AND lh.trang_thai = 'da_dat' ORDER BY lh.gio LIMIT 6",
    [hom_nay()]
);
$gio = (int) date('G');
$loi_chao = $gio < 11 ? 'Chào buổi sáng' : ($gio < 18 ? 'Chào buổi chiều' : 'Chào buổi tối');

mo_trang('Trang chủ', 'ql', 'home');
?>
<main class="pg">
  <div class="pg-head">
    <span class="eyebrow"><?= e(thu_ngay(hom_nay())) ?></span>
    <h1><?= e($loi_chao) ?>, <?= e($nguoi_dung['ho_ten']) ?></h1>
  </div>

  <div class="stats">
    <div class="card stat"><span>Lịch hẹn hôm nay</span><strong><?= (int) $dem['tong'] ?></strong></div>
    <div class="card stat"><span>Chưa đến</span><strong><?= (int) $dem['chua_den'] ?></strong></div>
    <div class="card stat"><span>Đang chờ khám</span><strong class="c-cam"><?= (int) $dem['cho_kham'] ?></strong></div>
    <div class="card stat"><span>Đã khám xong</span><strong class="c-xanh"><?= (int) $dem['da_kham'] ?></strong></div>
  </div>

  <section class="card p22 stack g14">
    <div class="row between">
      <h2>Sắp đến giờ hẹn</h2>
      <a href="<?= e(url('quan-ly/lich-hen-hom-nay.php')) ?>" style="font-size:15px;font-weight:600">Xem cả ngày</a>
    </div>
    <?php if ($sap_den): ?>
      <div class="tbl-box">
        <table style="min-width:640px">
          <thead><tr><th>Giờ</th><th>Bệnh nhân</th><th>Bác sĩ</th><th>Lý do khám</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($sap_den as $lh): ?>
              <tr>
                <td class="strong num"><?= e(gio_ngan($lh['gio'])) ?></td>
                <td style="font-weight:500"><?= e($lh['ho_ten']) ?></td>
                <td><?= e(ten_bac_si($lh['bac_si'])) ?></td>
                <td class="muted"><?= e($lh['ly_do']) ?></td>
                <td class="t-right"><a class="btn sm" href="<?= e(url('quan-ly/tiep-nhan.php', ['id' => $lh['id']])) ?>">Tiếp nhận</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="muted" style="font-size:15px">Không còn lịch hẹn nào chờ tiếp nhận hôm nay.</p>
    <?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
