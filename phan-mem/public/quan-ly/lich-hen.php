<?php
/*
 * [15] Danh sách lịch hẹn: mọi lịch hẹn từ hôm nay trở đi (bệnh nhân đặt qua CN 2.3 và lượt khám vãng lai),
 * xếp theo thời gian, lịch gần nhất ở trên, chia nhóm theo ngày.
 * Lịch hôm nay: Chưa đến thì tiếp nhận, Chờ khám thì gọi vào khám, Đang khám thì mở hồ sơ khám.
 * Lịch các ngày sau chỉ để xem.
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
    chuyen_huong('quan-ly/lich-hen.php', ['bs' => (int) ($_POST['loc'] ?? 0) ?: null, 'trang' => (int) ($_POST['trang'] ?? 0) > 1 ? (int) $_POST['trang'] : null]);
}

$loc = (int) ($_GET['bs'] ?? 0);
$hom_nay = hom_nay();
$ds_bac_si = nhieu_dong('SELECT id, ho_ten FROM bac_si ORDER BY id');
$ds = nhieu_dong(
    "SELECT lh.*, bn.ho_ten, bn.so_dien_thoai, bs.ho_ten AS bac_si,
            (SELECT COUNT(*) FROM benh_an ba WHERE ba.benh_nhan_id = lh.benh_nhan_id AND ba.lich_hen_id <> lh.id) AS so_lan_truoc,
            (SELECT ba.id FROM benh_an ba WHERE ba.lich_hen_id = lh.id) AS benh_an_id
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.ngay >= ? AND (? = 0 OR lh.bac_si_id = ?)
     ORDER BY lh.ngay, lh.gio, lh.id",
    [$hom_nay, $loc, $loc]
);
$so_lich_moi_ngay = array_count_values(array_column($ds, 'ngay'));

/** Hôm nay · Thứ Năm, 22/10/2026 */
function nhan_ngay(string $ngay): string
{
    if ($ngay === hom_nay()) {
        return 'Hôm nay · ' . thu_ngay($ngay);
    }
    if ($ngay === date('Y-m-d', strtotime(hom_nay() . ' +1 day'))) {
        return 'Ngày mai · ' . thu_ngay($ngay);
    }
    return thu_ngay($ngay);
}

mo_trang('Danh sách lịch hẹn', 'ql', 'today');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <h1>Danh sách lịch hẹn</h1>
    </div>
    <a class="btn pri" href="<?= e(url('quan-ly/tiep-nhan.php')) ?>">Tiếp nhận bệnh nhân</a>
  </div>
  <?= hien_thong_diep() ?>

  <section class="card p22 stack g16">
    <div class="chips" role="group" aria-label="Lọc theo bác sĩ">
      <a class="chip<?= $loc === 0 ? ' on' : '' ?>" href="<?= e(url('quan-ly/lich-hen.php')) ?>"<?= $loc === 0 ? ' aria-current="true"' : '' ?>>Tất cả bác sĩ</a>
      <?php foreach ($ds_bac_si as $bs): ?>
        <a class="chip<?= $loc === $bs['id'] ? ' on' : '' ?>" href="<?= e(url('quan-ly/lich-hen.php', ['bs' => $bs['id']])) ?>"<?= $loc === $bs['id'] ? ' aria-current="true"' : '' ?>><?= e(ten_bac_si($bs['ho_ten'])) ?></a>
      <?php endforeach; ?>
    </div>
    <?php if ($ds): ?>
      <div class="tbl-box">
        <table class="bang-gon" style="min-width:900px">
          <thead><tr><th>Giờ</th><th>Bệnh nhân</th><th>Bác sĩ</th><th>Lý do khám</th><th>Trạng thái</th><th class="t-right">Thao tác</th></tr></thead>
          <tbody data-phan-trang="phan-trang">
            <?php $ngay_dang_in = null; foreach ($ds as $lh): $la_hom_nay = $lh['ngay'] === $hom_nay; ?>
              <?php if ($lh['ngay'] !== $ngay_dang_in): $ngay_dang_in = $lh['ngay']; ?>
                <tr class="nhom-ngay"><th colspan="6" scope="colgroup"><?= e(nhan_ngay($lh['ngay'])) ?> <span>· <?= $so_lich_moi_ngay[$lh['ngay']] ?> lịch hẹn</span></th></tr>
              <?php endif; ?>
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
                  <?php if (!$la_hom_nay): ?>
                  <?php elseif ($lh['trang_thai'] === 'da_dat'): ?>
                    <a class="btn sm" href="<?= e(url('quan-ly/tiep-nhan.php', ['id' => $lh['id']])) ?>">Tiếp nhận</a>
                  <?php elseif ($lh['trang_thai'] === 'cho_kham'): ?>
                    <form method="post" style="display:inline">
                      <?= o_csrf() ?>
                      <input type="hidden" name="hanh_dong" value="goi">
                      <input type="hidden" name="id" value="<?= e($lh['id']) ?>">
                      <input type="hidden" name="loc" value="<?= $loc ?>">
                      <input type="hidden" name="trang" value="<?= (int) ($_GET['trang'] ?? 1) ?>">
                      <button class="btn sm pri" type="submit">Gọi vào khám</button>
                    </form>
                  <?php elseif ($lh['trang_thai'] === 'dang_kham'): ?>
                    <a class="btn sm pri" href="<?= e(url($lh['so_lan_truoc'] > 0 ? 'quan-ly/lich-su-kham.php' : 'quan-ly/ket-qua-kham.php', ['id' => $lh['id']])) ?>">Mở hồ sơ khám</a>
                  <?php elseif ($lh['trang_thai'] === 'da_kham' && $lh['benh_an_id']): ?>
                    <a class="btn sm" href="<?= e(url('in-phieu.php', ['id' => $lh['benh_an_id']])) ?>" data-in>In phiếu</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <nav class="phan-trang" id="phan-trang" aria-label="Chuyển trang danh sách lịch hẹn" hidden></nav>
    <?php else: ?>
      <p class="empty-row">Chưa có lịch hẹn nào từ hôm nay trở đi<?= $loc ? ' của bác sĩ này' : '' ?>.</p>
    <?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
