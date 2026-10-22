<?php
/*
 * [11] Xem lại và xác nhận — CN 2.3 Xác nhận lịch hẹn.
 * Khi bấm "Đặt lịch", hệ thống kiểm tra lại khung giờ rồi mới ghi D4. Nếu khung giờ vừa có người đặt
 * thì hiện nhánh "chọn giờ khác". Khi đổi lịch, lịch cũ được thay bằng giờ mới.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('BN');
$benh_nhan_id = $nguoi_dung['benh_nhan_id'];

if (isset($_GET['xong'])) {
    // Trạng thái sau khi đặt thành công
    $lich_hen = mot_dong(
        'SELECT lh.*, bs.ho_ten AS bac_si, bs.phong FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
         WHERE lh.id = ? AND lh.benh_nhan_id = ?',
        [(int) $_GET['xong'], $benh_nhan_id]
    ) ?? chuyen_huong('benh-nhan/lich-hen.php');
    $trang_thai = 'xong';
} else {
    $doi = lich_dang_doi($benh_nhan_id);
    $nguon = la_post() ? $_POST : $_GET;
    $bac_si = mot_dong('SELECT * FROM bac_si WHERE id = ?', [(int) ($nguon['bs'] ?? 0)]);
    $ngay = (string) ($nguon['ngay'] ?? '');
    $gio = (string) ($nguon['gio'] ?? '');
    if (!$bac_si || !ngay_hop_le($ngay) || !preg_match('/^\d{2}:\d{2}$/', $gio)) {
        chuyen_huong('benh-nhan/chon-bac-si.php', ['doi' => $doi['id'] ?? null]);
    }
    $benh_nhan = mot_dong('SELECT * FROM benh_nhan WHERE id = ?', [$benh_nhan_id]);
    $ly_do = la_post() ? mb_substr(gon($_POST['ly_do'] ?? ''), 0, 255) : ($doi['ly_do'] ?? '');
    $trang_thai = 'xac_nhan';

    if (la_post()) {
        kiem_tra_csrf();
        if (!khung_gio_con_trong((int) $bac_si['id'], $ngay, $gio)) {
            $trang_thai = 'het_cho';
        } else {
            $noi_dung = 'Lịch khám ' . $gio . ' ' . ten_thu($ngay) . ' ' . ngay_vn($ngay) . ' với ' . ten_bac_si($bac_si['ho_ten']) . ', ' . $bac_si['phong'] . '.';
            try {
                if ($doi) {
                    truy_van(
                        "UPDATE lich_hen SET bac_si_id = ?, ngay = ?, gio = ?, ly_do = ? WHERE id = ? AND trang_thai = 'da_dat'",
                        [$bac_si['id'], $ngay, $gio, $ly_do ?: null, $doi['id']]
                    );
                    $lich_hen_id = (int) $doi['id'];
                    gui_thong_bao($benh_nhan_id, 'doi_lich', 'Đổi lịch thành công', $noi_dung);
                } else {
                    truy_van(
                        'INSERT INTO lich_hen (benh_nhan_id, bac_si_id, ngay, gio, ly_do, tao_luc) VALUES (?, ?, ?, ?, ?, ?)',
                        [$benh_nhan_id, $bac_si['id'], $ngay, $gio, $ly_do ?: null, bay_gio()]
                    );
                    $lich_hen_id = (int) csdl()->lastInsertId();
                    gui_thong_bao($benh_nhan_id, 'dat_lich', 'Đặt lịch thành công', $noi_dung);
                }
                chuyen_huong('benh-nhan/xac-nhan.php', ['xong' => $lich_hen_id, 'da_doi' => $doi ? 1 : null]);
            } catch (PDOException $loi) {
                // Khóa uq_khung_gio báo trùng: người khác vừa giữ khung giờ này
                if (!la_trung_khoa($loi)) {
                    throw $loi;
                }
                $trang_thai = 'het_cho';
            }
        }
    }
}

mo_trang('Xác nhận lịch hẹn', 'bn', 'book');
?>
<main class="pg w900">
  <div class="pg-head">
    <h1>Xác nhận</h1>
  </div>

  <?php if ($trang_thai === 'xac_nhan'): ?>
    <form method="post" class="stack g24">
      <?= o_csrf() ?>
      <input type="hidden" name="bs" value="<?= e($bac_si['id']) ?>">
      <input type="hidden" name="ngay" value="<?= e($ngay) ?>">
      <input type="hidden" name="gio" value="<?= e($gio) ?>">
      <?php if ($doi): ?><input type="hidden" name="doi" value="<?= e($doi['id']) ?>"><?php endif; ?>
      <section class="card p28 stack" style="padding-top:26px">
        <h2 style="margin:0 0 10px;font-size:20px">Thông tin lịch hẹn</h2>
        <div class="drow"><span class="muted">Bệnh nhân</span><strong><?= e($benh_nhan['ho_ten']) ?> · <?= e(dinh_dang_sdt($benh_nhan['so_dien_thoai'])) ?></strong></div>
        <div class="drow"><span class="muted">Bác sĩ</span><strong><?= e(ten_bac_si($bac_si['ho_ten'])) ?> · <?= e($bac_si['chuyen_khoa']) ?></strong></div>
        <div class="drow"><span class="muted">Thời gian</span><strong><?= e(thoi_diem($ngay, $gio)) ?></strong></div>
        <div class="drow"><span class="muted">Địa điểm</span><strong><?= e($bac_si['phong']) ?> · <?= e(DIA_CHI) ?></strong></div>
        <?php if ($doi): ?>
          <div class="drow"><span class="muted">Lịch cũ</span><span><?= e(thoi_diem($doi['ngay'], $doi['gio'])) ?> · <?= e(ten_bac_si($doi['bac_si'])) ?> (sẽ được thay)</span></div>
        <?php endif; ?>
        <div class="stack g8" style="padding-top:16px;border-top:1px solid #EBEFEE">
          <label for="ly-do" style="font-size:16px;font-weight:500">Lý do khám</label>
          <textarea class="inp" id="ly-do" name="ly_do" rows="3" maxlength="255" placeholder="VD: Ho, sốt nhẹ 3 ngày"><?= e($ly_do) ?></textarea>
        </div>
      </section>
      <div class="actions">
        <a class="back" href="<?= e(url('benh-nhan/chon-gio.php', ['bs' => $bac_si['id'], 'ngay' => $ngay, 'doi' => $doi['id'] ?? null])) ?>">Không, chọn lại giờ</a>
        <button class="go" type="submit"><?= $doi ? 'Xác nhận đổi lịch' : 'Đặt lịch' ?></button>
      </div>
    </form>

  <?php elseif ($trang_thai === 'het_cho'): ?>
    <section role="alert" class="card taken-box">
      <h2>Khung giờ <?= e($gio) ?> vừa có người đặt</h2>
      <p>Trong lúc bạn xem lại, khung giờ này đã được bệnh nhân khác đặt. Vui lòng chọn giờ khác — các thông tin khác được giữ nguyên.</p>
      <a class="go" href="<?= e(url('benh-nhan/chon-gio.php', ['bs' => $bac_si['id'], 'ngay' => $ngay, 'doi' => $doi['id'] ?? null])) ?>">Chọn giờ khác</a>
    </section>

  <?php else: ?>
    <section role="status" class="card done-box">
      <span class="done-icon"><?= bieu_tuong(ICON_DUNG, 42, 2.4) ?></span>
      <h2><?= isset($_GET['da_doi']) ? 'Đổi lịch thành công' : 'Đặt lịch thành công' ?></h2>
      <p><?= e(thoi_diem($lich_hen['ngay'], $lich_hen['gio'])) ?> với <?= e(ten_bac_si($lich_hen['bac_si'])) ?>, <?= e($lich_hen['phong']) ?>.
        Thông báo lịch hẹn đã được gửi đến bạn. Vui lòng đến trước 10 phút.</p>
      <div class="actions center" style="margin-top:8px">
        <a class="back" href="<?= e(url('benh-nhan/trang-chu.php')) ?>">Về trang chủ</a>
        <a class="go" href="<?= e(url('benh-nhan/lich-hen.php')) ?>">Xem lịch hẹn của tôi</a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php dong_trang(); ?>
