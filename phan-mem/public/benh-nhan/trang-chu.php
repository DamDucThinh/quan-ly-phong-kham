<?php
/*
 * [2] Trang chủ Bệnh nhân: lối vào đặt lịch, đổi/hủy lịch, lịch hẹn sắp tới và thông báo từ phòng khám.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$benh_nhan_id = $nguoi_dung['benh_nhan_id'];

$sap_toi = mot_dong(
    "SELECT lh.ngay, lh.gio, bs.ho_ten AS bac_si, bs.chuyen_khoa, bs.phong
     FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.benh_nhan_id = ? AND lh.trang_thai = 'da_dat' AND TIMESTAMP(lh.ngay, lh.gio) > ?
     ORDER BY lh.ngay, lh.gio LIMIT 1",
    [$benh_nhan_id, bay_gio()]
);
$thong_bao = nhieu_dong('SELECT * FROM thong_bao WHERE benh_nhan_id = ? ORDER BY tao_luc DESC, id DESC LIMIT 8', [$benh_nhan_id]);
$mau_cham = ['ket_qua' => 'cam', 'don_thuoc' => 'cam', 'dat_lich' => 'xanh', 'doi_lich' => 'xanh', 'huy_lich' => 'xam'];

mo_trang('Trang chủ', 'bn', 'home');
?>
<main class="pg w1200">
  <?= hien_thong_diep() ?>
  <section class="hero">
    <div class="hero-txt">
      <span class="hi">Xin chào, <?= e($nguoi_dung['ho_ten']) ?></span>
      <h1>Bạn muốn làm gì hôm nay?</h1>
      <div class="row" style="margin-top:8px">
        <a class="cta light" href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>">Đặt lịch</a>
        <a class="cta ghost" href="<?= e(url('benh-nhan/lich-hen.php')) ?>">Đổi hoặc hủy lịch hẹn</a>
      </div>
    </div>
    <div class="next-card">
      <div class="row between" style="gap:8px">
        <span class="muted" style="font-size:14px;font-weight:500">Lịch hẹn sắp tới</span>
        <?php if ($sap_toi): ?><span class="pill ok">Đã đặt</span><?php endif; ?>
      </div>
      <?php if ($sap_toi): ?>
        <div class="row" style="gap:16px;flex-wrap:nowrap">
          <?= o_ngay($sap_toi['ngay']) ?>
          <div class="stack" style="gap:3px">
            <strong style="font-size:22px;font-weight:600"><?= e(gio_ngan($sap_toi['gio'])) ?></strong>
            <span style="font-size:15px"><?= e(ten_bac_si($sap_toi['bac_si'])) ?></span>
            <span class="muted" style="font-size:14px"><?= e($sap_toi['chuyen_khoa']) ?> · <?= e($sap_toi['phong']) ?></span>
          </div>
        </div>
        <a href="<?= e(url('benh-nhan/lich-hen.php')) ?>" style="font-size:15px;font-weight:600">Xem tất cả lịch hẹn</a>
      <?php else: ?>
        <p class="muted" style="font-size:15px">Bạn chưa có lịch hẹn nào sắp tới.</p>
        <a href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>" style="font-size:15px;font-weight:600">Đặt lịch khám ngay</a>
      <?php endif; ?>
    </div>
  </section>

  <section class="card p26 stack g6">
    <div class="row between" style="margin-bottom:10px">
      <h2 style="font-size:20px">Thông báo từ phòng khám</h2>
      <a href="<?= e(url('benh-nhan/ket-qua.php')) ?>" style="font-size:15px;font-weight:600;min-height:44px;display:inline-flex;align-items:center">Xem kết quả khám và đơn thuốc</a>
    </div>
    <?php foreach ($thong_bao as $tb): ?>
      <article class="note">
        <span class="dot <?= e($mau_cham[$tb['loai']] ?? '') ?>"></span>
        <div class="stack g4 grow">
          <div class="row between" style="gap:8px">
            <strong style="font-size:16px;font-weight:600"><?= e($tb['tieu_de']) ?></strong>
            <span class="muted" style="font-size:13px"><?= e(date('d/m · H:i', strtotime($tb['tao_luc']))) ?></span>
          </div>
          <span style="font-size:15px;color:#3E4B48;line-height:1.5"><?= e($tb['noi_dung']) ?></span>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (!$thong_bao): ?>
      <p class="muted note" style="font-size:15px">Chưa có thông báo nào.</p>
    <?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
