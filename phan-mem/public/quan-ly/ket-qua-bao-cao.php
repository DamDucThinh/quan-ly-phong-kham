<?php
/*
 * [20] Kết quả thống kê — Báo cáo tình hình khám chữa bệnh.
 *   CN 5.1 Thống kê lượt khám (đọc D5), CN 5.2 Thống kê sử dụng thuốc (đọc D6).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$loai = ($_GET['loai'] ?? '') === 'thuoc' ? 'thuoc' : 'luot-kham';
$tu = (string) ($_GET['tu'] ?? '');
$den = (string) ($_GET['den'] ?? '');
if (!ngay_hop_le($tu) || !ngay_hop_le($den)) {
    chuyen_huong('quan-ly/bao-cao.php', ['loai' => $loai]);
}
$quay_lai = ['loai' => $loai, 'tu' => $tu, 'den' => $den];
if ($tu > $den) {
    chuyen_huong('quan-ly/bao-cao.php', $quay_lai + ['loi' => 'nguoc']);
}
$so_ngay = (int) (new DateTime($tu))->diff(new DateTime($den))->days + 1;
if ($so_ngay > 366) {
    chuyen_huong('quan-ly/bao-cao.php', $quay_lai + ['loi' => 'dai']);
}

if ($loai === 'luot-kham') {
    // CN 5.1: lượt khám mỗi ngày và theo bác sĩ
    $theo_ngay = array_column(
        nhieu_dong('SELECT ngay_kham, COUNT(*) AS n FROM benh_an WHERE ngay_kham BETWEEN ? AND ? GROUP BY ngay_kham', [$tu, $den]),
        'n',
        'ngay_kham'
    );
    $cot = [];
    for ($i = 0; $i < $so_ngay; $i++) {
        $ngay = date('Y-m-d', strtotime("$tu +$i day"));
        $cot[$ngay] = (int) ($theo_ngay[$ngay] ?? 0);
    }
    $tong = array_sum($cot);
    $co_du_lieu = $tong > 0;
} else {
    // CN 5.2: số lượng từng thuốc đã kê
    $ds_thuoc = nhieu_dong(
        'SELECT t.ten, t.ham_luong, t.don_vi, SUM(ct.so_luong) AS tong
         FROM chi_tiet_don_thuoc ct
         JOIN don_thuoc dt ON dt.id = ct.don_thuoc_id
         JOIN benh_an ba ON ba.id = dt.benh_an_id
         JOIN thuoc t ON t.id = ct.thuoc_id
         WHERE ba.ngay_kham BETWEEN ? AND ?
         GROUP BY t.id ORDER BY tong DESC, t.ten',
        [$tu, $den]
    );
    $co_du_lieu = $ds_thuoc !== [];
}

if (!$co_du_lieu && isset($_GET['tu_form'])) {
    // Nhánh "không có dữ liệu trong kỳ": quay lại [19]
    chuyen_huong('quan-ly/bao-cao.php', $quay_lai + ['loi' => 'trong']);
}

if ($co_du_lieu && $loai === 'luot-kham') {
    $trung_binh = $tong / $so_ngay;
    $ngay_dong = array_search(max($cot), $cot, true);
    $theo_bac_si = nhieu_dong(
        'SELECT bs.ho_ten, bs.chuyen_khoa, COUNT(ba.id) AS n
         FROM bac_si bs LEFT JOIN benh_an ba ON ba.bac_si_id = bs.id AND ba.ngay_kham BETWEEN ? AND ?
         GROUP BY bs.id ORDER BY n DESC, bs.id',
        [$tu, $den]
    );
    // Trục dọc chia 3 nấc tròn số, như bản thiết kế (45 / 30 / 15 / 0)
    $buoc = 1;
    foreach ([1, 2, 3, 5, 10, 15, 20, 25, 30, 40, 50, 75, 100, 150, 200, 250, 500, 1000] as $b) {
        $buoc = $b;
        if ($b * 3 >= max($cot)) {
            break;
        }
    }
    $tran = $buoc * 3;
    $nhan_cach = max(1, (int) ceil($so_ngay / 12));
    $cung_thang = substr($tu, 0, 7) === substr($den, 0, 7);
} elseif ($co_du_lieu) {
    $so_don = (int) mot_gia_tri(
        'SELECT COUNT(*) FROM don_thuoc dt JOIN benh_an ba ON ba.id = dt.benh_an_id WHERE ba.ngay_kham BETWEEN ? AND ?',
        [$tu, $den]
    );
    $lon_nhat = (int) $ds_thuoc[0]['tong'];
}

mo_trang('Báo cáo tình hình khám chữa bệnh', 'ql', 'report');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <h1>Báo cáo tình hình khám chữa bệnh</h1>
      <p>Từ <?= e(ngay_vn($tu)) ?> đến <?= e(ngay_vn($den)) ?></p>
    </div>
    <div class="row no-print" style="gap:10px">
      <a class="btn" href="<?= e(url('quan-ly/bao-cao.php', $quay_lai)) ?>">Xem báo cáo khác</a>
      <button class="btn pri" type="button" onclick="window.print()">In báo cáo</button>
    </div>
  </div>

  <nav class="seg-box no-print" aria-label="Loại báo cáo">
    <a class="seg<?= $loai === 'luot-kham' ? ' on' : '' ?>" href="<?= e(url('quan-ly/ket-qua-bao-cao.php', ['loai' => 'luot-kham', 'tu' => $tu, 'den' => $den])) ?>"<?= $loai === 'luot-kham' ? ' aria-current="page"' : '' ?>>Lượt khám (5.1)</a>
    <a class="seg<?= $loai === 'thuoc' ? ' on' : '' ?>" href="<?= e(url('quan-ly/ket-qua-bao-cao.php', ['loai' => 'thuoc', 'tu' => $tu, 'den' => $den])) ?>"<?= $loai === 'thuoc' ? ' aria-current="page"' : '' ?>>Sử dụng thuốc (5.2)</a>
  </nav>

  <?php if (!$co_du_lieu): ?>
    <section class="card p24"><p class="muted" style="font-size:16px">Không có dữ liệu trong khoảng đã chọn.</p></section>

  <?php elseif ($loai === 'luot-kham'): ?>
    <div class="stats w240">
      <div class="card stat"><span>Tổng lượt khám</span><strong><?= so($tong) ?></strong></div>
      <div class="card stat"><span>Trung bình mỗi ngày</span><strong><?= $trung_binh >= 10 ? so(round($trung_binh)) : number_format($trung_binh, 1, ',', '.') ?></strong></div>
      <div class="card stat"><span>Ngày đông nhất</span><strong><?= e(ngay_ngan($ngay_dong)) ?> · <?= so($cot[$ngay_dong]) ?></strong></div>
    </div>

    <section class="card p24 stack g16">
      <h2>Lượt khám theo ngày</h2>
      <div class="chart">
        <div class="chart-y" aria-hidden="true"><span><?= $tran ?></span><span><?= $buoc * 2 ?></span><span><?= $buoc ?></span><span>0</span></div>
        <div class="chart-main">
          <div class="chart-bars" role="img" aria-label="<?= e('Biểu đồ cột lượt khám theo ngày, từ ' . ngay_vn($tu) . ' đến ' . ngay_vn($den) . ', tổng ' . $tong . ' lượt') ?>">
            <?php foreach ($cot as $ngay => $n): ?>
              <div class="chart-col" title="<?= e(ngay_ngan($ngay) . ': ' . $n . ' lượt khám') ?>"><span style="height:<?= round($n / $tran * 240) ?>px"></span></div>
            <?php endforeach; ?>
          </div>
          <div class="chart-x" aria-hidden="true">
            <?php $i = 0; foreach (array_keys($cot) as $ngay): ?>
              <span><?= $i++ % $nhan_cach === 0 ? e($cung_thang ? date('d', strtotime($ngay)) : ngay_ngan($ngay)) : '' ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="card p24 stack g14">
      <h2>Theo bác sĩ</h2>
      <div class="tbl-box">
        <table style="min-width:560px">
          <thead><tr><th>Bác sĩ</th><th>Chuyên khoa</th><th class="t-right">Lượt khám</th><th class="t-right">Tỷ lệ</th></tr></thead>
          <tbody>
            <?php foreach ($theo_bac_si as $bs): ?>
              <tr>
                <td class="strong"><?= e(ten_bac_si($bs['ho_ten'])) ?></td>
                <td class="muted"><?= e($bs['chuyen_khoa']) ?></td>
                <td class="t-right num"><?= so($bs['n']) ?></td>
                <td class="t-right num"><?= round($bs['n'] / $tong * 100) ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  <?php else: ?>
    <div class="stats w240">
      <div class="card stat"><span>Đơn thuốc đã kê</span><strong><?= so($so_don) ?></strong></div>
      <div class="card stat"><span>Loại thuốc đã dùng</span><strong><?= count($ds_thuoc) ?></strong></div>
      <div class="card stat"><span>Dùng nhiều nhất</span><strong class="sm"><?= e($ds_thuoc[0]['ten']) ?></strong></div>
    </div>

    <section class="card p24 stack g16">
      <h2>Số lượng thuốc đã kê</h2>
      <ul class="hbars">
        <?php foreach ($ds_thuoc as $t): $nhan = so_luong_thuoc($t['tong'], $t['don_vi']); ?>
          <li title="<?= e(ten_thuoc($t) . ': ' . $nhan) ?>">
            <span style="font-weight:500"><?= e(ten_thuoc($t)) ?></span>
            <div class="hbar"><div style="width:<?= round($t['tong'] / $lon_nhat * 100) ?>%"></div></div>
            <span class="t-right num"><?= e($nhan) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
