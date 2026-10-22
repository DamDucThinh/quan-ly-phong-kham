<?php
/*
 * Đăng ký tài khoản bệnh nhân bằng số điện thoại (bổ sung, thiết kế chưa có màn này).
 * Nếu phòng khám đã tạo hồ sơ cho số điện thoại này khi tiếp nhận, tài khoản được gắn vào hồ sơ đó.
 */
require __DIR__ . '/../includes/khoi-dong.php';

if ($nguoi_dung = nguoi_dung()) {
    chuyen_huong(trang_chu_cua($nguoi_dung['vai_tro']));
}

$gia_tri = ['ho_ten' => '', 'sdt' => ''];
$loi = [];

if (la_post()) {
    kiem_tra_csrf();
    $gia_tri['ho_ten'] = gon($_POST['ho_ten'] ?? '');
    $gia_tri['sdt'] = trim((string) ($_POST['sdt'] ?? ''));
    $mat_khau = (string) ($_POST['mat_khau'] ?? '');
    $sdt = chi_so($gia_tri['sdt']);

    if ($gia_tri['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên';
    }
    if ($sdt === '') {
        $loi['sdt'] = 'Vui lòng nhập số điện thoại';
    } elseif (!sdt_hop_le($sdt)) {
        $loi['sdt'] = 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0';
    } elseif (mot_gia_tri('SELECT 1 FROM tai_khoan WHERE ten_dang_nhap = ?', [$sdt])) {
        $loi['sdt'] = 'Số điện thoại này đã có tài khoản. Vui lòng đăng nhập.';
    }
    if (mb_strlen($mat_khau) < 6) {
        $loi['mat_khau'] = 'Mật khẩu cần ít nhất 6 ký tự';
    } elseif ($mat_khau !== (string) ($_POST['nhap_lai'] ?? '')) {
        $loi['nhap_lai'] = 'Mật khẩu nhập lại không khớp';
    }

    if (!$loi) {
        $pdo = csdl();
        try {
            $pdo->beginTransaction();
            $benh_nhan_id = mot_gia_tri('SELECT id FROM benh_nhan WHERE so_dien_thoai = ? FOR UPDATE', [$sdt]);
            if (!$benh_nhan_id) {
                truy_van('INSERT INTO benh_nhan (ho_ten, so_dien_thoai) VALUES (?, ?)', [$gia_tri['ho_ten'], $sdt]);
                $benh_nhan_id = (int) $pdo->lastInsertId();
            }
            truy_van(
                'INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro, benh_nhan_id) VALUES (?, ?, ?, ?)',
                [$sdt, password_hash($mat_khau, PASSWORD_DEFAULT), 'BN', $benh_nhan_id]
            );
            $pdo->commit();
        } catch (PDOException $loi_csdl) {
            $pdo->rollBack();
            if (!la_trung_khoa($loi_csdl)) {
                throw $loi_csdl;
            }
            $loi['sdt'] = 'Số điện thoại này đã có tài khoản. Vui lòng đăng nhập.';
        }
        if (!$loi) {
            dang_nhap(mot_dong('SELECT * FROM tai_khoan WHERE ten_dang_nhap = ?', [$sdt]));
            dat_thong_diep('ok', '<strong>Đăng ký thành công.</strong> Chào mừng bạn đến với ' . e(TEN_PHONG_KHAM) . '.');
            chuyen_huong('benh-nhan/trang-chu.php');
        }
    }
}

function o_nhap(string $ten, string $nhan, array $loi, string $gia_tri = '', string $kieu = 'text', string $them = ''): void
{
    ?>
    <div class="field">
      <label for="<?= e($ten) ?>"><?= e($nhan) ?> <span class="req">*</span></label>
      <input class="inp<?= isset($loi[$ten]) ? ' bad' : '' ?>" id="<?= e($ten) ?>" name="<?= e($ten) ?>" type="<?= e($kieu) ?>" value="<?= e($gia_tri) ?>" <?= $them ?>>
      <?php if (isset($loi[$ten])): ?><span class="err"><?= e($loi[$ten]) ?></span><?php endif; ?>
    </div>
    <?php
}

mo_trang('Đăng ký');
?>
<div class="auth">
  <?php phan_gioi_thieu(); ?>
  <section class="auth-main">
    <form class="auth-form" method="post" novalidate>
      <?= o_csrf() ?>
      <div class="stack g6">
        <h2>Đăng ký</h2>
        <p class="muted" style="font-size:15px">Dùng số điện thoại để đăng nhập và đặt lịch khám trực tuyến.</p>
      </div>
      <?php if ($loi): ?>
        <div class="alert err" role="alert">Thông tin chưa hợp lệ. Vui lòng sửa các ô được đánh dấu bên dưới.</div>
      <?php endif; ?>
      <div class="stack g16">
        <?php
        o_nhap('ho_ten', 'Họ và tên', $loi, $gia_tri['ho_ten'], 'text', 'autocomplete="name"');
        o_nhap('sdt', 'Số điện thoại', $loi, $gia_tri['sdt'], 'tel', 'inputmode="tel" autocomplete="tel" placeholder="10 chữ số, bắt đầu bằng 0"');
        o_nhap('mat_khau', 'Mật khẩu', $loi, '', 'password', 'autocomplete="new-password" placeholder="Ít nhất 6 ký tự"');
        o_nhap('nhap_lai', 'Nhập lại mật khẩu', $loi, '', 'password', 'autocomplete="new-password"');
        ?>
      </div>
      <button class="go" type="submit">Đăng ký</button>
      <p class="muted" style="text-align:center">Đã có tài khoản? <a href="<?= e(url('dang-nhap.php')) ?>">Đăng nhập</a></p>
    </form>
  </section>
</div>
<?php dong_trang(); ?>
