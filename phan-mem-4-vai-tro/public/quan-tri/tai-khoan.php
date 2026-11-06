<?php
/*
 * [12] Tài khoản người dùng (Quản trị) — CN 1.4: danh sách tài khoản quản trị, nhân viên, bác sĩ (đọc D7), khóa hoặc mở khóa.
 * Bệnh nhân tự đăng ký nên chỉ hiện số lượng.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('QT');

if (la_post()) {
    kiem_tra_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id === $nguoi_dung['id']) {
        dat_thong_diep('err', 'Không thể tự khóa tài khoản đang đăng nhập.');
    } else {
        truy_van("UPDATE tai_khoan SET hoat_dong = 1 - hoat_dong WHERE id = ? AND vai_tro <> 'BN'", [$id]);
        $tk = mot_dong('SELECT ho_ten, hoat_dong FROM tai_khoan WHERE id = ?', [$id]);
        if ($tk) {
            dat_thong_diep('ok', ($tk['hoat_dong'] ? 'Đã mở khóa' : 'Đã khóa') . ' tài khoản <strong>' . e($tk['ho_ten']) . '</strong>.');
        }
    }
    chuyen_huong('quan-tri/tai-khoan.php', ['vt' => $_POST['vt'] ?? null]);
}

$loc = isset(VAI_TRO[$_GET['vt'] ?? '']) && $_GET['vt'] !== 'BN' ? $_GET['vt'] : '';
$ds = nhieu_dong(
    "SELECT tk.*, bs.ho_ten AS bac_si, bs.chuyen_khoa FROM tai_khoan tk LEFT JOIN bac_si bs ON bs.id = tk.bac_si_id
     WHERE tk.vai_tro <> 'BN' AND (? = '' OR tk.vai_tro = ?)
     ORDER BY FIELD(tk.vai_tro, 'QT', 'NV', 'BS'), tk.ten_dang_nhap",
    [$loc, $loc]
);
$so_benh_nhan = (int) mot_gia_tri("SELECT COUNT(*) FROM tai_khoan WHERE vai_tro = 'BN'");

mo_trang('Tài khoản người dùng', 'qt', 'accounts');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <h1>Tài khoản người dùng</h1>
      <p><?= count($ds) ?> tài khoản do quản trị cấp<?= $loc ? '' : ' · ' . $so_benh_nhan . ' bệnh nhân tự đăng ký' ?></p>
    </div>
    <a class="btn pri" href="<?= e(url('quan-tri/tai-khoan-form.php')) ?>">+ Thêm tài khoản</a>
  </div>
  <?= hien_thong_diep() ?>

  <section class="card p22 stack g16">
    <div class="chips" role="group" aria-label="Lọc theo vai trò">
      <a class="chip<?= $loc === '' ? ' on' : '' ?>" href="<?= e(url('quan-tri/tai-khoan.php')) ?>">Tất cả</a>
      <?php foreach (['QT', 'NV', 'BS'] as $vt): ?>
        <a class="chip<?= $loc === $vt ? ' on' : '' ?>" href="<?= e(url('quan-tri/tai-khoan.php', ['vt' => $vt])) ?>"><?= e(VAI_TRO[$vt]) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="tbl-box">
      <table style="min-width:860px">
        <thead><tr><th>Họ tên</th><th>Tên đăng nhập</th><th>Vai trò</th><th>Bác sĩ liên kết</th><th>Trạng thái</th><th class="t-right">Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($ds as $tk): ?>
            <tr>
              <td class="strong"><?= e($tk['ho_ten']) ?></td>
              <td class="num"><?= e($tk['ten_dang_nhap']) ?></td>
              <td><?= e(VAI_TRO[$tk['vai_tro']]) ?></td>
              <td class="muted"><?= $tk['bac_si'] ? e(ten_bac_si($tk['bac_si']) . ' · ' . $tk['chuyen_khoa']) : '—' ?></td>
              <td><?= $tk['hoat_dong'] ? '<span class="pill ok">Hoạt động</span>' : '<span class="pill warn">Đã khóa</span>' ?></td>
              <td class="t-right">
                <a class="btn sm" href="<?= e(url('quan-tri/tai-khoan-form.php', ['id' => $tk['id']])) ?>">Sửa</a>
                <?php if ((int) $tk['id'] !== $nguoi_dung['id']): ?>
                  <form method="post" style="display:inline">
                    <?= o_csrf() ?>
                    <input type="hidden" name="id" value="<?= e($tk['id']) ?>">
                    <input type="hidden" name="vt" value="<?= e($loc) ?>">
                    <button class="btn sm<?= $tk['hoat_dong'] ? ' red' : '' ?>" type="submit"><?= $tk['hoat_dong'] ? 'Khóa' : 'Mở khóa' ?></button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<?php dong_trang(); ?>
