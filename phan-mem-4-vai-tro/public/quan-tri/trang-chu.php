<?php
/*
 * Trang chủ Quản trị: tình hình hôm nay của cả phòng khám (lịch hẹn theo bác sĩ, đơn thuốc đã kê,
 * thuốc được kê nhiều, tài khoản đang hoạt động) để điều hành; việc chi tiết do nhân viên, bác sĩ làm.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('QT');
$dem = mot_dong(
    "SELECT SUM(trang_thai <> 'da_huy') AS tong, SUM(trang_thai = 'da_kham') AS da_kham,
            SUM(trang_thai IN ('cho_kham', 'dang_kham')) AS dang_cho, SUM(dat_boi = 'nhan_vien' AND trang_thai <> 'da_huy') AS nhan_vien_dat
     FROM lich_hen WHERE ngay = ?",
    [hom_nay()]
);
$don_hom_nay = (int) mot_gia_tri('SELECT COUNT(*) FROM don_thuoc WHERE DATE(tao_luc) = ?', [hom_nay()]);
// Thuốc được kê nhiều nhất 30 ngày qua (đọc D6, D3)
$ke_nhieu = nhieu_dong(
    'SELECT t.ten, t.ham_luong, t.don_vi, COUNT(DISTINCT dt.id) AS so_don, SUM(ct.so_luong) AS so_luong
     FROM chi_tiet_don_thuoc ct JOIN don_thuoc dt ON dt.id = ct.don_thuoc_id JOIN thuoc t ON t.id = ct.thuoc_id
     WHERE dt.tao_luc >= ? GROUP BY t.id ORDER BY so_don DESC, t.ten LIMIT 5',
    [date('Y-m-d', strtotime(hom_nay() . ' -29 day'))]
);
$theo_bac_si = nhieu_dong(
    "SELECT bs.ho_ten, bs.chuyen_khoa, bs.phong,
            SUM(lh.trang_thai IS NOT NULL AND lh.trang_thai <> 'da_huy') AS tong,
            SUM(lh.trang_thai IN ('cho_kham', 'dang_kham')) AS dang_cho, SUM(lh.trang_thai = 'da_kham') AS da_kham
     FROM bac_si bs LEFT JOIN lich_hen lh ON lh.bac_si_id = bs.id AND lh.ngay = ?
     GROUP BY bs.id ORDER BY bs.id",
    [hom_nay()]
);
$nhan_vien = array_column(nhieu_dong("SELECT vai_tro, COUNT(*) AS n FROM tai_khoan WHERE vai_tro <> 'BN' AND hoat_dong = 1 GROUP BY vai_tro"), 'n', 'vai_tro');
$gio = (int) date('G');
$loi_chao = $gio < 11 ? 'Chào buổi sáng' : ($gio < 18 ? 'Chào buổi chiều' : 'Chào buổi tối');

mo_trang('Trang chủ', 'qt', 'home');
?>
<main class="pg">
  <div class="pg-head">
    <span class="eyebrow"><?= e(thu_ngay(hom_nay())) ?></span>
    <h1><?= e($loi_chao) ?>, <?= e($nguoi_dung['ho_ten']) ?></h1>
  </div>

  <div class="stats">
    <div class="card stat"><span>Lịch hẹn hôm nay</span><strong><?= (int) $dem['tong'] ?></strong></div>
    <div class="card stat"><span>Đã khám xong</span><strong class="c-xanh"><?= (int) $dem['da_kham'] ?></strong></div>
    <div class="card stat"><span>Đang chờ, đang khám</span><strong class="c-cam"><?= (int) $dem['dang_cho'] ?></strong></div>
    <div class="card stat"><span>Đơn thuốc đã kê hôm nay</span><strong><?= $don_hom_nay ?></strong></div>
  </div>

  <section class="card p22 stack g14">
    <div class="row between">
      <h2>Hôm nay theo bác sĩ</h2>
      <a href="<?= e(url('quan-tri/bac-si.php')) ?>" style="font-size:15px;font-weight:600">Bác sĩ và lịch làm việc</a>
    </div>
    <div class="tbl-box">
      <table style="min-width:640px">
        <thead><tr><th>Bác sĩ</th><th>Chuyên khoa</th><th class="t-right">Lịch hẹn</th><th class="t-right">Đang chờ, đang khám</th><th class="t-right">Đã khám</th></tr></thead>
        <tbody>
          <?php foreach ($theo_bac_si as $bs): ?>
            <tr>
              <td class="strong"><?= e(ten_bac_si($bs['ho_ten'])) ?></td>
              <td class="muted"><?= e($bs['chuyen_khoa']) ?> · <?= e($bs['phong']) ?></td>
              <td class="t-right num"><?= (int) $bs['tong'] ?></td>
              <td class="t-right num"><?= (int) $bs['dang_cho'] ?></td>
              <td class="t-right num"><?= (int) $bs['da_kham'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <span class="muted" style="font-size:14px">Trong đó <?= (int) $dem['nhan_vien_dat'] ?> lịch do nhân viên đặt hộ.</span>
  </section>

  <div class="split">
    <section class="card p22 stack g12 main">
      <h2>Thuốc được kê nhiều nhất 30 ngày qua</h2>
      <?php foreach ($ke_nhieu as $t): ?>
        <div class="row between"><span class="strong"><?= e(ten_thuoc($t)) ?></span><span class="muted"><?= so((int) $t['so_don']) ?> đơn · <?= e(so_luong_thuoc($t['so_luong'], $t['don_vi'])) ?></span></div>
      <?php endforeach; ?>
      <?php if (!$ke_nhieu): ?><p class="muted" style="font-size:15px">Chưa có đơn thuốc nào trong 30 ngày qua.</p><?php endif; ?>
      <a href="<?= e(url('quan-tri/bao-cao.php', ['loai' => 'thuoc'])) ?>" style="font-size:15px;font-weight:600">Báo cáo thuốc đã kê</a>
    </section>
    <section class="card p22 stack g12 side">
      <h2>Tài khoản đang hoạt động</h2>
      <?php foreach (['NV', 'BS', 'QT'] as $vt): ?>
        <div class="row between"><span><?= e(VAI_TRO[$vt]) ?></span><strong><?= (int) ($nhan_vien[$vt] ?? 0) ?></strong></div>
      <?php endforeach; ?>
      <a href="<?= e(url('quan-tri/tai-khoan.php')) ?>" style="font-size:15px;font-weight:600">Quản lý tài khoản</a>
    </section>
  </div>
</main>
<?php dong_trang(); ?>
