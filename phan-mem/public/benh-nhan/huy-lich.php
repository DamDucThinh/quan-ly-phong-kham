<?php
/*
 * [13] Xác nhận hủy lịch hẹn — CN 2.4: cập nhật trạng thái lịch hẹn thành "đã hủy" (ghi D4),
 * khung giờ được trả lại cho bệnh nhân khác.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$benh_nhan_id = $nguoi_dung['benh_nhan_id'];

if (isset($_GET['xong'])) {
    $lich_hen = mot_dong(
        "SELECT lh.*, bs.ho_ten AS bac_si FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
         WHERE lh.id = ? AND lh.benh_nhan_id = ? AND lh.trang_thai = 'da_huy'",
        [(int) $_GET['xong'], $benh_nhan_id]
    ) ?? chuyen_huong('benh-nhan/lich-hen.php');
    $da_huy = true;
} else {
    $lich_hen = lich_hen_doi_duoc((int) ($_POST['id'] ?? $_GET['id'] ?? 0), $benh_nhan_id);
    if (!$lich_hen) {
        dat_thong_diep('err', 'Lịch hẹn này không còn hủy được (đã tới giờ khám, đã hủy hoặc đã được tiếp nhận).');
        chuyen_huong('benh-nhan/lich-hen.php');
    }
    $da_huy = false;

    if (la_post()) {
        kiem_tra_csrf();
        $ly_do_huy = in_array($_POST['ly_do_huy'] ?? '', LY_DO_HUY, true) ? $_POST['ly_do_huy'] : null;
        truy_van(
            "UPDATE lich_hen SET trang_thai = 'da_huy', ly_do_huy = ? WHERE id = ? AND trang_thai = 'da_dat'",
            [$ly_do_huy, $lich_hen['id']]
        );
        gui_thong_bao(
            $benh_nhan_id,
            'huy_lich',
            'Đã hủy lịch hẹn',
            'Lịch khám ' . thoi_diem($lich_hen['ngay'], $lich_hen['gio']) . ' với ' . ten_bac_si($lich_hen['bac_si']) . ' đã được hủy.'
        );
        chuyen_huong('benh-nhan/huy-lich.php', ['xong' => $lich_hen['id']]);
    }
}

mo_trang('Hủy lịch hẹn', 'bn', 'home');
?>
<main class="pg w760" style="gap:24px">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('benh-nhan/lich-hen.php')) ?>">‹ Lịch hẹn của tôi</a>
    <h1>Hủy lịch hẹn</h1>
  </div>

  <?php if (!$da_huy): ?>
    <form method="post" class="stack g24">
      <?= o_csrf() ?>
      <input type="hidden" name="id" value="<?= e($lich_hen['id']) ?>">
      <section class="card p28 stack">
        <p style="margin:0 0 12px;font-size:18px;font-weight:600">Bạn có chắc muốn hủy lịch hẹn này?</p>
        <div class="drow"><span class="muted">Bác sĩ</span><strong><?= e(ten_bac_si($lich_hen['bac_si'])) ?> · <?= e($lich_hen['chuyen_khoa']) ?></strong></div>
        <div class="drow"><span class="muted">Thời gian</span><strong><?= e(thoi_diem($lich_hen['ngay'], $lich_hen['gio'])) ?></strong></div>
        <div class="drow"><span class="muted">Lý do khám</span><span><?= e($lich_hen['ly_do'] ?: '—') ?></span></div>
        <div class="stack g8" style="padding-top:16px;border-top:1px solid #EBEFEE">
          <label for="ly-do-huy" style="font-size:16px;font-weight:500">Lý do hủy (không bắt buộc)</label>
          <select class="inp" id="ly-do-huy" name="ly_do_huy">
            <option value="">— Không chọn —</option>
            <?= cac_lua_chon(LY_DO_HUY, null) ?>
          </select>
        </div>
        <p class="muted" style="margin:16px 0 0;font-size:15px;line-height:1.5">Khung giờ sẽ được trả lại cho bệnh nhân khác. Nếu chỉ muốn đổi giờ, hãy chọn “Đổi lịch” thay vì hủy.</p>
      </section>
      <div class="actions">
        <a class="btn lg" href="<?= e(url('benh-nhan/lich-hen.php')) ?>">Không, giữ lịch hẹn</a>
        <button class="btn lg danger" type="submit">Có, hủy lịch hẹn</button>
      </div>
    </form>
  <?php else: ?>
    <section role="status" class="card done-box">
      <h2>Đã hủy lịch hẹn</h2>
      <p>Lịch <?= e(thoi_diem($lich_hen['ngay'], $lich_hen['gio'])) ?> với <?= e(ten_bac_si($lich_hen['bac_si'])) ?> đã được hủy. Thông báo hủy lịch đã được gửi đến bạn.</p>
      <div class="actions center" style="margin-top:8px">
        <a class="btn lg" href="<?= e(url('benh-nhan/lich-hen.php')) ?>">Lịch hẹn của tôi</a>
        <a class="btn lg pri" href="<?= e(url('benh-nhan/chon-bac-si.php')) ?>">Đặt lịch mới</a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
