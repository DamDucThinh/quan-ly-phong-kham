<?php
/*
 * [1] Đăng nhập. Quản lý phòng khám dùng tên đăng nhập, bệnh nhân dùng số điện thoại.
 */
require __DIR__ . '/../includes/khoi-dong.php';

if ($nguoi_dung = nguoi_dung()) {
    chuyen_huong(trang_chu_cua($nguoi_dung['vai_tro']));
}

$ten_dang_nhap = '';
$sai = false;

if (la_post()) {
    kiem_tra_csrf();
    $ten_dang_nhap = trim((string) ($_POST['ten_dang_nhap'] ?? ''));
    // Số điện thoại người dùng hay gõ kèm khoảng trắng hoặc dấu chấm: bỏ đi trước khi tìm
    $khoa = preg_match('/^[\d\s.\-]+$/', $ten_dang_nhap) ? chi_so($ten_dang_nhap) : mb_strtolower($ten_dang_nhap);
    $tai_khoan = mot_dong('SELECT * FROM tai_khoan WHERE ten_dang_nhap = ?', [$khoa]);
    if ($tai_khoan && password_verify((string) ($_POST['mat_khau'] ?? ''), $tai_khoan['mat_khau'])) {
        dang_nhap($tai_khoan);
        chuyen_huong(trang_chu_cua($tai_khoan['vai_tro']));
    }
    $sai = true;
}

mo_trang('Đăng nhập');
?>
<div class="auth">
  <?php phan_gioi_thieu(); ?>
  <section class="auth-main">
    <form class="auth-form" method="post" novalidate>
      <?= o_csrf() ?>
      <h2>Đăng nhập</h2>
      <?= hien_thong_diep() ?>
      <div class="stack g16">
        <div class="field">
          <label for="tk">Tên đăng nhập</label>
          <input class="inp<?= $sai ? ' bad' : '' ?>" id="tk" name="ten_dang_nhap" value="<?= e($ten_dang_nhap) ?>" autocomplete="username" placeholder="Bệnh nhân nhập số điện thoại" required>
        </div>
        <div class="field">
          <label for="mk">Mật khẩu</label>
          <input class="inp<?= $sai ? ' bad' : '' ?>" id="mk" name="mat_khau" type="password" autocomplete="current-password" required>
        </div>
        <?php if ($sai): ?>
          <div class="alert err icon" role="alert"><?= bieu_tuong(ICON_CANH_BAO, 20, 2.2) ?>Sai tên đăng nhập hoặc mật khẩu. Vui lòng nhập lại.</div>
        <?php endif; ?>
        <?php if (isset($_GET['quen'])): ?>
          <div class="alert info" role="status">Vui lòng gọi hotline <strong><?= e(HOTLINE) ?></strong> để phòng khám cấp lại mật khẩu.</div>
        <?php endif; ?>
        <a href="<?= e(url('dang-nhap.php', ['quen' => 1])) ?>" style="align-self:flex-end;font-size:15px">Quên mật khẩu?</a>
      </div>
      <button class="go" type="submit">Đăng nhập</button>
      <p class="muted" style="text-align:center">Bệnh nhân chưa có tài khoản? <a href="<?= e(url('dang-ky.php')) ?>">Đăng ký bằng số điện thoại</a></p>
    </form>
  </section>
</div>
<?php dong_trang(); ?>
