<?php
/*
 * [5] Thêm / sửa thông tin bác sĩ — CN 1.1: kiểm tra thông tin hợp lệ rồi lưu hồ sơ bác sĩ (ghi D1).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$bac_si = $id ? (mot_dong('SELECT * FROM bac_si WHERE id = ?', [$id]) ?? chuyen_huong('quan-ly/bac-si.php')) : null;

$gia_tri = [
    'ho_ten' => $bac_si['ho_ten'] ?? '',
    'chuyen_khoa' => $bac_si['chuyen_khoa'] ?? '',
    'so_dien_thoai' => $bac_si ? dinh_dang_sdt($bac_si['so_dien_thoai']) : '',
    'phong' => $bac_si['phong'] ?? '',
    'email' => $bac_si['email'] ?? '',
    'trinh_do' => $bac_si['trinh_do'] ?? '',
];
$loi = [];

if (la_post()) {
    kiem_tra_csrf();
    foreach (array_keys($gia_tri) as $khoa) {
        $gia_tri[$khoa] = gon($_POST[$khoa] ?? '');
    }
    // Người dùng hay gõ kèm "BS." trước tên: bỏ đi vì hệ thống tự thêm khi hiển thị
    $gia_tri['ho_ten'] = trim(preg_replace('/^(BS\.?|Bác sĩ)\s*/iu', '', $gia_tri['ho_ten']));
    $sdt = chi_so($gia_tri['so_dien_thoai']);

    if ($gia_tri['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên';
    }
    if (!in_array($gia_tri['chuyen_khoa'], CHUYEN_KHOA, true)) {
        $loi['chuyen_khoa'] = 'Vui lòng chọn chuyên khoa';
    }
    if ($sdt === '') {
        $loi['so_dien_thoai'] = 'Vui lòng nhập số điện thoại';
    } elseif (!sdt_hop_le($sdt)) {
        $loi['so_dien_thoai'] = 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0';
    }
    if (!in_array($gia_tri['phong'], PHONG, true)) {
        $loi['phong'] = 'Vui lòng chọn phòng khám';
    }
    if ($gia_tri['email'] !== '' && !filter_var($gia_tri['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email chưa đúng định dạng';
    }

    if (!$loi) {
        $tham_so = [$gia_tri['ho_ten'], $gia_tri['chuyen_khoa'], $gia_tri['phong'], $sdt, $gia_tri['email'] ?: null, $gia_tri['trinh_do'] ?: null];
        if ($bac_si) {
            truy_van('UPDATE bac_si SET ho_ten = ?, chuyen_khoa = ?, phong = ?, so_dien_thoai = ?, email = ?, trinh_do = ? WHERE id = ?', [...$tham_so, $bac_si['id']]);
        } else {
            truy_van('INSERT INTO bac_si (ho_ten, chuyen_khoa, phong, so_dien_thoai, email, trinh_do) VALUES (?, ?, ?, ?, ?, ?)', $tham_so);
            $id = (int) csdl()->lastInsertId();
        }
        chuyen_huong('quan-ly/bac-si-form.php', ['id' => $id, 'da_luu' => 1]);
    }
}

function o_bac_si(string $khoa, string $nhan, array $gia_tri, array $loi, bool $bat_buoc, string $them = ''): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= e($nhan) ?><?= $bat_buoc ? ' <span class="req">*</span>' : '' ?></label>
      <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

function chon_bac_si(string $khoa, string $nhan, array $ds, string $trong, array $gia_tri, array $loi): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= e($nhan) ?> <span class="req">*</span></label>
      <select class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>">
        <option value=""><?= e($trong) ?></option>
        <?= cac_lua_chon($ds, $gia_tri[$khoa]) ?>
      </select>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

mo_trang($bac_si ? 'Sửa thông tin bác sĩ' : 'Thêm bác sĩ mới', 'ql', 'doctors');
?>
<main class="pg w920">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-ly/bac-si.php')) ?>">‹ Danh sách bác sĩ</a>
    <h1><?= $bac_si ? 'Sửa thông tin bác sĩ' : 'Thêm bác sĩ mới' ?></h1>
  </div>

  <?php if (isset($_GET['da_luu']) && !la_post()): ?>
    <div role="status" class="alert ok bar">
      <?= bieu_tuong(ICON_DUNG, 24, 2.4) ?>
      <span class="txt" style="font-size:16px"><strong>Đã lưu hồ sơ bác sĩ.</strong> Bác sĩ sẽ hiện ra để bệnh nhân chọn khi đặt lịch.</span>
      <a class="btn" href="<?= e(url('quan-ly/lich-lam-viec.php', ['bs' => $id])) ?>">Thêm lịch làm việc</a>
      <a class="btn pri" href="<?= e(url('quan-ly/bac-si.php')) ?>">Về danh sách</a>
    </div>
  <?php endif; ?>
  <?php if ($loi): ?>
    <div role="alert" class="alert err">Thông tin chưa hợp lệ. Vui lòng sửa các ô được đánh dấu bên dưới.</div>
  <?php endif; ?>

  <form class="card p28 stack g20" method="post" novalidate>
    <?= o_csrf() ?>
    <?php if ($bac_si): ?><input type="hidden" name="id" value="<?= e($bac_si['id']) ?>"><?php endif; ?>
    <div class="form-grid w300">
      <?php
      o_bac_si('ho_ten', 'Họ và tên', $gia_tri, $loi, true, 'placeholder="VD: Nguyễn Hữu Phước"');
      chon_bac_si('chuyen_khoa', 'Chuyên khoa', CHUYEN_KHOA, '— Chọn chuyên khoa —', $gia_tri, $loi);
      o_bac_si('so_dien_thoai', 'Số điện thoại', $gia_tri, $loi, true, 'inputmode="tel" placeholder="10 chữ số, bắt đầu bằng 0"');
      chon_bac_si('phong', 'Phòng khám', PHONG, '— Chọn phòng —', $gia_tri, $loi);
      o_bac_si('email', 'Email', $gia_tri, $loi, false, 'type="email"');
      o_bac_si('trinh_do', 'Trình độ chuyên môn', $gia_tri, $loi, false, 'placeholder="VD: Bác sĩ CKI"');
      ?>
    </div>
    <span class="muted" style="font-size:14px"><span class="req">*</span> là thông tin bắt buộc</span>
    <div class="form-actions" style="padding-top:4px">
      <a class="btn" href="<?= e(url('quan-ly/bac-si.php')) ?>">Hủy</a>
      <button class="btn pri" type="submit">Lưu hồ sơ bác sĩ</button>
    </div>
  </form>
</main>
<?php dong_trang(); ?>
