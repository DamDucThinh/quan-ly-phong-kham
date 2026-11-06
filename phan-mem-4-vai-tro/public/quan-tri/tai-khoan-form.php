<?php
/*
 * [13] Thêm / sửa tài khoản (Quản trị) — CN 1.4: họ tên, tên đăng nhập, vai trò; tài khoản bác sĩ gắn với
 * một hồ sơ bác sĩ (D1). Mật khẩu bắt buộc khi tạo mới; khi sửa, để trống là giữ mật khẩu cũ (ghi D7).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

$nguoi_dung = yeu_cau_vai_tro('QT');
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$tai_khoan = $id ? (mot_dong("SELECT * FROM tai_khoan WHERE id = ? AND vai_tro <> 'BN'", [$id]) ?? chuyen_huong('quan-tri/tai-khoan.php')) : null;

$gia_tri = [
    'ho_ten' => $tai_khoan['ho_ten'] ?? '',
    'ten_dang_nhap' => $tai_khoan['ten_dang_nhap'] ?? '',
    'vai_tro' => $tai_khoan['vai_tro'] ?? '',
    'bac_si_id' => (string) ($tai_khoan['bac_si_id'] ?? ''),
];
$loi = [];
$vai_tro_nv = array_intersect_key(VAI_TRO, array_flip(['QT', 'NV', 'BS']));
// Bác sĩ chưa có tài khoản (và bác sĩ đang gắn với tài khoản này)
$bac_si_trong = nhieu_dong(
    'SELECT bs.id, bs.ho_ten, bs.chuyen_khoa FROM bac_si bs LEFT JOIN tai_khoan tk ON tk.bac_si_id = bs.id
     WHERE tk.id IS NULL OR tk.id = ? ORDER BY bs.id',
    [$id]
);
$lua_chon_bac_si = array_column(array_map(fn($b) => ['id' => $b['id'], 'ten' => ten_bac_si($b['ho_ten']) . ' · ' . $b['chuyen_khoa']], $bac_si_trong), 'ten', 'id');

if (la_post()) {
    kiem_tra_csrf();
    foreach (array_keys($gia_tri) as $khoa) {
        $gia_tri[$khoa] = gon($_POST[$khoa] ?? '');
    }
    $gia_tri['ten_dang_nhap'] = mb_strtolower($gia_tri['ten_dang_nhap']);
    $mat_khau = (string) ($_POST['mat_khau'] ?? '');
    if ($gia_tri['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên';
    }
    if (!preg_match('/^[a-z][a-z0-9._]{3,29}$/', $gia_tri['ten_dang_nhap'])) {
        $loi['ten_dang_nhap'] = 'Từ 4 đến 30 ký tự: chữ thường không dấu, số, dấu chấm, gạch dưới; bắt đầu bằng chữ';
    } elseif (mot_gia_tri('SELECT 1 FROM tai_khoan WHERE ten_dang_nhap = ? AND id <> ?', [$gia_tri['ten_dang_nhap'], $id])) {
        $loi['ten_dang_nhap'] = 'Tên đăng nhập này đã có người dùng';
    }
    if (!isset($vai_tro_nv[$gia_tri['vai_tro']])) {
        $loi['vai_tro'] = 'Vui lòng chọn vai trò';
    } elseif ($tai_khoan && $id === $nguoi_dung['id'] && $gia_tri['vai_tro'] !== 'QT') {
        $loi['vai_tro'] = 'Không thể đổi vai trò của tài khoản đang đăng nhập';
    }
    if ($gia_tri['vai_tro'] === 'BS' && !isset($lua_chon_bac_si[(int) $gia_tri['bac_si_id']])) {
        $loi['bac_si_id'] = 'Tài khoản bác sĩ phải gắn với một bác sĩ chưa có tài khoản';
    }
    if ((!$tai_khoan || $mat_khau !== '') && mb_strlen($mat_khau) < 6) {
        $loi['mat_khau'] = 'Mật khẩu cần ít nhất 6 ký tự';
    }

    if (!$loi) {
        $bac_si_id = $gia_tri['vai_tro'] === 'BS' ? (int) $gia_tri['bac_si_id'] : null;
        if ($tai_khoan) {
            truy_van('UPDATE tai_khoan SET ho_ten = ?, ten_dang_nhap = ?, vai_tro = ?, bac_si_id = ? WHERE id = ?',
                [$gia_tri['ho_ten'], $gia_tri['ten_dang_nhap'], $gia_tri['vai_tro'], $bac_si_id, $id]);
            if ($mat_khau !== '') {
                truy_van('UPDATE tai_khoan SET mat_khau = ? WHERE id = ?', [password_hash($mat_khau, PASSWORD_DEFAULT), $id]);
            }
        } else {
            truy_van('INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, ho_ten, bac_si_id, tao_luc) VALUES (?, ?, ?, ?, ?, ?)',
                [$gia_tri['ten_dang_nhap'], password_hash($mat_khau, PASSWORD_DEFAULT), $gia_tri['vai_tro'], $gia_tri['ho_ten'], $bac_si_id, bay_gio()]);
        }
        dat_thong_diep('ok', 'Đã lưu tài khoản <strong>' . e($gia_tri['ten_dang_nhap']) . '</strong> (' . e(VAI_TRO[$gia_tri['vai_tro']]) . ').');
        chuyen_huong('quan-tri/tai-khoan.php');
    }
}

mo_trang($tai_khoan ? 'Sửa tài khoản' : 'Thêm tài khoản', 'qt', 'accounts');
?>
<main class="pg w920">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-tri/tai-khoan.php')) ?>">‹ Tài khoản người dùng</a>
    <h1><?= $tai_khoan ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></h1>
  </div>
  <?php if ($loi): ?>
    <div role="alert" class="alert err">Thông tin chưa hợp lệ. Vui lòng sửa các ô được đánh dấu bên dưới.</div>
  <?php endif; ?>

  <form class="card p28 stack g20" method="post" novalidate>
    <?= o_csrf() ?>
    <?php if ($tai_khoan): ?><input type="hidden" name="id" value="<?= e($tai_khoan['id']) ?>"><?php endif; ?>
    <div class="form-grid">
      <?php foreach (['ho_ten' => ['Họ và tên', 'autocomplete="off"'], 'ten_dang_nhap' => ['Tên đăng nhập', 'autocomplete="off" placeholder="VD: nhanvien02"']] as $khoa => [$nhan, $them]): ?>
        <div class="field">
          <label for="<?= $khoa ?>"><?= $nhan ?> <span class="req">*</span></label>
          <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= $khoa ?>" name="<?= $khoa ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
          <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
        </div>
      <?php endforeach; ?>
      <div class="field">
        <label for="vai_tro">Vai trò <span class="req">*</span></label>
        <select class="inp<?= isset($loi['vai_tro']) ? ' bad' : '' ?>" id="vai_tro" name="vai_tro">
          <option value="">— Chọn —</option><?= cac_lua_chon($vai_tro_nv, $gia_tri['vai_tro'], true) ?>
        </select>
        <?php if (isset($loi['vai_tro'])): ?><span class="err"><?= e($loi['vai_tro']) ?></span><?php endif; ?>
      </div>
      <div class="field" id="o-bac-si">
        <label for="bac_si_id">Bác sĩ (với tài khoản bác sĩ)</label>
        <select class="inp<?= isset($loi['bac_si_id']) ? ' bad' : '' ?>" id="bac_si_id" name="bac_si_id">
          <option value="">— Chọn bác sĩ —</option><?= cac_lua_chon($lua_chon_bac_si, $gia_tri['bac_si_id'], true) ?>
        </select>
        <?php if (isset($loi['bac_si_id'])): ?><span class="err"><?= e($loi['bac_si_id']) ?></span><?php endif; ?>
      </div>
      <div class="field">
        <label for="mat_khau">Mật khẩu<?= $tai_khoan ? ' mới' : ' <span class="req">*</span>' ?></label>
        <input class="inp<?= isset($loi['mat_khau']) ? ' bad' : '' ?>" id="mat_khau" name="mat_khau" type="password" autocomplete="new-password"<?= $tai_khoan ? ' placeholder="Để trống nếu giữ mật khẩu cũ"' : '' ?>>
        <?php if (isset($loi['mat_khau'])): ?><span class="err"><?= e($loi['mat_khau']) ?></span><?php endif; ?>
      </div>
    </div>
    <span class="muted" style="font-size:14px"><span class="req">*</span> là thông tin bắt buộc. Bác sĩ cần có hồ sơ ở mục Bác sĩ trước khi tạo tài khoản.</span>
    <div class="form-actions">
      <a class="btn" href="<?= e(url('quan-tri/tai-khoan.php')) ?>">Hủy</a>
      <button class="btn pri" type="submit">Lưu tài khoản</button>
    </div>
  </form>
</main>
<script>
(() => {
  const vaiTro = document.getElementById('vai_tro'), oBacSi = document.getElementById('o-bac-si');
  const capNhat = () => oBacSi.classList.toggle('hidden', vaiTro.value !== 'BS');
  vaiTro.addEventListener('change', capNhat);
  capNhat();
})();
</script>
<?php dong_trang(); ?>
