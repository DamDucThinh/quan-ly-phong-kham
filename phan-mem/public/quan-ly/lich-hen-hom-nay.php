<?php
/*
 * [15] Danh sách lịch hẹn hôm nay: lịch bệnh nhân đã đặt (từ CN 2.3) và lượt khám vãng lai.
 * Chưa đến: tiếp nhận. Chờ khám: gọi vào khám. Đang khám: mở hồ sơ khám.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');

if (la_post()) {
    kiem_tra_csrf();
    if (($_POST['hanh_dong'] ?? '') === 'goi') {
        truy_van(
            "UPDATE lich_hen SET trang_thai = 'dang_kham' WHERE id = ? AND ngay = ? AND trang_thai = 'cho_kham'",
            [(int) ($_POST['id'] ?? 0), hom_nay()]
        );
    }
    chuyen_huong('quan-ly/lich-hen-hom-nay.php', ['bs' => (int) ($_POST['loc'] ?? 0) ?: null]);
}

$loc = (int) ($_GET['bs'] ?? 0);
$ds_bac_si = nhieu_dong('SELECT id, ho_ten FROM bac_si ORDER BY id');
$ds = nhieu_dong(
    "SELECT lh.*, bn.ho_ten, bn.so_dien_thoai, bs.ho_ten AS bac_si,
            (SELECT COUNT(*) FROM benh_an ba WHERE ba.benh_nhan_id = lh.benh_nhan_id AND ba.lich_hen_id <> lh.id) AS so_lan_truoc,
            (SELECT ba.id FROM benh_an ba WHERE ba.lich_hen_id = lh.id) AS benh_an_id
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.ngay = ? AND (? = 0 OR lh.bac_si_id = ?)
     ORDER BY lh.gio, lh.id",
    [hom_nay(), $loc, $loc]
);

mo_trang('Lịch hẹn hôm nay', 'ql', 'today');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <span class="eyebrow"><?= e(thu_ngay(hom_nay())) ?></span>
      <h1>Lịch hẹn hôm nay</h1>
    </div>
    <a class="btn pri" href="<?= e(url('quan-ly/tiep-nhan.php')) ?>">Tiếp nhận bệnh nhân</a>
  </div>
  <?= hien_thong_diep() ?>

  <section class="card p22 stack g16">
    <div class="chips" role="group" aria-label="Lọc theo bác sĩ">
      <a class="chip<?= $loc === 0 ? ' on' : '' ?>" href="<?= e(url('quan-ly/lich-hen-hom-nay.php')) ?>"<?= $loc === 0 ? ' aria-current="true"' : '' ?>>Tất cả bác sĩ</a>
      <?php foreach ($ds_bac_si as $bs): ?>
        <a class="chip<?= $loc === $bs['id'] ? ' on' : '' ?>" href="<?= e(url('quan-ly/lich-hen-hom-nay.php', ['bs' => $bs['id']])) ?>"<?= $loc === $bs['id'] ? ' aria-current="true"' : '' ?>><?= e(ten_bac_si($bs['ho_ten'])) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="tbl-box">
      <table style="min-width:900px">
        <thead><tr><th>Giờ</th><th>Bệnh nhân</th><th>Bác sĩ</th><th>Lý do khám</th><th>Trạng thái</th><th class="t-right">Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($ds as $lh): ?>
            <tr<?= $lh['trang_thai'] === 'dang_kham' ? ' class="row-now"' : '' ?>>
              <td class="num">
                <div class="stack" style="gap:2px"><span class="strong"><?= e(gio_ngan($lh['gio'])) ?></span>
                  <?php if ($lh['loai'] === 'vang_lai'): ?><span class="muted" style="font-size:12px">Không hẹn trước</span><?php endif; ?></div>
              </td>
              <td><div class="stack" style="gap:2px"><span class="strong"><?= e($lh['ho_ten']) ?></span><span class="muted" style="font-size:13px"><?= e(dinh_dang_sdt($lh['so_dien_thoai'])) ?></span></div></td>
              <td><?= e(ten_bac_si($lh['bac_si'])) ?></td>
              <td class="muted"><?= e($lh['ly_do']) ?></td>
              <td><?= nhan_trang_thai($lh['trang_thai']) ?></td>
              <td class="t-right">
                <?php if ($lh['trang_thai'] === 'da_dat'): ?>
                  <a class="btn" href="<?= e(url('quan-ly/tiep-nhan.php', ['id' => $lh['id']])) ?>">Tiếp nhận</a>
                <?php elseif ($lh['trang_thai'] === 'cho_kham'): ?>
                  <form method="post" style="display:inline">
                    <?= o_csrf() ?>
                    <input type="hidden" name="hanh_dong" value="goi">
                    <input type="hidden" name="id" value="<?= e($lh['id']) ?>">
                    <input type="hidden" name="loc" value="<?= $loc ?>">
                    <button class="btn pri" type="submit">Gọi vào khám</button>
                  </form>
                <?php elseif ($lh['trang_thai'] === 'dang_kham'): ?>
                  <a class="btn pri" href="<?= e(url($lh['so_lan_truoc'] > 0 ? 'quan-ly/lich-su-kham.php' : 'quan-ly/ket-qua-kham.php', ['id' => $lh['id']])) ?>">Mở hồ sơ khám</a>
                <?php elseif ($lh['trang_thai'] === 'da_kham' && $lh['benh_an_id']): ?>
                  <a class="btn sm" href="<?= e(url('in-phieu.php', ['id' => $lh['benh_an_id']])) ?>" data-in>In phiếu</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!$ds): ?><p class="empty-row">Không có lịch hẹn nào hôm nay<?= $loc ? ' của bác sĩ này' : '' ?>.</p><?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
