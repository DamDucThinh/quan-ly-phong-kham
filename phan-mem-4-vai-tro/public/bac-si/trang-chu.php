<?php
/*
 * Hàng chờ khám (Bác sĩ): bệnh nhân hôm nay của bác sĩ đang đăng nhập (đọc D4). Nhân viên tiếp nhận xong thì
 * bệnh nhân vào hàng chờ; bác sĩ gọi vào khám, xem lịch sử khám (CN 3.2), ghi kết quả và kê đơn (CN 3.3, 4.1–4.3).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BS');
$bac_si_id = $nguoi_dung['bac_si_id'];

if (la_post()) {
    kiem_tra_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $goi = truy_van(
        "UPDATE lich_hen SET trang_thai = 'dang_kham' WHERE id = ? AND bac_si_id = ? AND ngay = ? AND trang_thai = 'cho_kham'",
        [$id, $bac_si_id, hom_nay()]
    );
    if ($goi->rowCount() === 0) {
        dat_thong_diep('warn', 'Bệnh nhân này không còn trong hàng chờ.');
        chuyen_huong('bac-si/trang-chu.php');
    }
    chuyen_huong('bac-si/lich-su-kham.php', ['id' => $id]);
}

$bac_si = mot_dong('SELECT * FROM bac_si WHERE id = ?', [$bac_si_id]);
$thu_tu = "FIELD(lh.trang_thai, 'dang_kham', 'cho_kham', 'da_dat', 'da_kham')";
$ds = nhieu_dong(
    "SELECT lh.*, bn.ma, bn.ho_ten, bn.nam_sinh, bn.gioi_tinh,
            (SELECT COUNT(*) FROM benh_an ba WHERE ba.benh_nhan_id = lh.benh_nhan_id AND ba.lich_hen_id <> lh.id) AS so_lan_truoc,
            (SELECT ba.id FROM benh_an ba WHERE ba.lich_hen_id = lh.id) AS benh_an_id
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id
     WHERE lh.bac_si_id = ? AND lh.ngay = ? AND lh.trang_thai <> 'da_huy'
     ORDER BY $thu_tu, lh.gio, lh.id",
    [$bac_si_id, hom_nay()]
);
$dem = array_count_values(array_column($ds, 'trang_thai'));
$ca_hom_nay = nhieu_dong('SELECT gio_bat_dau, gio_ket_thuc FROM ca_lam_viec WHERE bac_si_id = ? AND thu = ? ORDER BY gio_bat_dau', [$bac_si_id, (int) date('N')]);

mo_trang('Hàng chờ khám', 'bs', 'home');
?>
<main class="pg">
  <div class="pg-head">
    <span class="eyebrow"><?= e(thu_ngay(hom_nay())) ?> · <?= e($bac_si['chuyen_khoa']) ?> · <?= e($bac_si['phong']) ?></span>
    <h1>Hàng chờ khám của <?= e(ten_bac_si($bac_si['ho_ten'])) ?></h1>
    <p>Ca hôm nay: <?= $ca_hom_nay ? e(implode(', ', array_map(fn($c) => gio_ngan($c['gio_bat_dau']) . '–' . gio_ngan($c['gio_ket_thuc']), $ca_hom_nay))) : 'không có ca làm việc' ?></p>
  </div>
  <?= hien_thong_diep() ?>

  <div class="stats">
    <div class="card stat"><span>Đang chờ khám</span><strong class="c-cam"><?= $dem['cho_kham'] ?? 0 ?></strong></div>
    <div class="card stat"><span>Đang khám</span><strong><?= $dem['dang_kham'] ?? 0 ?></strong></div>
    <div class="card stat"><span>Đã khám xong</span><strong class="c-xanh"><?= $dem['da_kham'] ?? 0 ?></strong></div>
    <div class="card stat"><span>Có hẹn, chưa đến</span><strong><?= $dem['da_dat'] ?? 0 ?></strong></div>
  </div>

  <section class="card p22 stack g14">
    <h2>Bệnh nhân hôm nay</h2>
    <?php if ($ds): ?>
      <div class="tbl-box">
        <table style="min-width:820px">
          <thead><tr><th>Giờ</th><th>Bệnh nhân</th><th>Lý do khám</th><th>Trạng thái</th><th class="t-right">Thao tác</th></tr></thead>
          <tbody>
            <?php foreach ($ds as $lh): ?>
              <tr<?= $lh['trang_thai'] === 'dang_kham' ? ' class="row-now"' : '' ?>>
                <td class="num">
                  <div class="stack" style="gap:2px"><span class="strong"><?= e(gio_ngan($lh['gio'])) ?></span>
                    <?php if ($lh['loai'] === 'vang_lai'): ?><span class="muted" style="font-size:12px">Không hẹn trước</span><?php endif; ?></div>
                </td>
                <td>
                  <div class="stack" style="gap:2px"><span class="strong"><?= e($lh['ho_ten']) ?></span>
                    <span class="muted" style="font-size:13px"><?= e(implode(' · ', array_filter([$lh['ma'], $lh['gioi_tinh'], $lh['nam_sinh'], $lh['so_lan_truoc'] ? $lh['so_lan_truoc'] . ' lần khám trước' : 'khám lần đầu']))) ?></span></div>
                </td>
                <td class="muted"><?= e($lh['ly_do']) ?></td>
                <td><?= nhan_trang_thai($lh['trang_thai']) ?></td>
                <td class="t-right">
                  <?php if ($lh['trang_thai'] === 'cho_kham'): ?>
                    <form method="post" style="display:inline">
                      <?= o_csrf() ?>
                      <input type="hidden" name="id" value="<?= e($lh['id']) ?>">
                      <button class="btn sm pri" type="submit">Gọi vào khám</button>
                    </form>
                  <?php elseif ($lh['trang_thai'] === 'dang_kham'): ?>
                    <a class="btn sm pri" href="<?= e(url($lh['so_lan_truoc'] > 0 ? 'bac-si/lich-su-kham.php' : 'bac-si/ket-qua-kham.php', ['id' => $lh['id']])) ?>">Tiếp tục khám</a>
                  <?php elseif ($lh['trang_thai'] === 'da_kham' && $lh['benh_an_id']): ?>
                    <a class="btn sm" href="<?= e(url('bac-si/ket-qua-kham.php', ['id' => $lh['id']])) ?>">Xem kết quả</a>
                    <a class="btn sm" href="<?= e(url('in-phieu.php', ['id' => $lh['benh_an_id']])) ?>" data-in>In phiếu</a>
                  <?php else: ?>
                    <span class="muted" style="font-size:14px">Chờ nhân viên tiếp nhận</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="empty-row">Hôm nay chưa có bệnh nhân nào.</p>
    <?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
