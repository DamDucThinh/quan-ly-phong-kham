<?php
/*
 * [8] Thêm / sửa thông tin thuốc — CN 1.3: kiểm tra thông tin hợp lệ rồi lưu danh mục thuốc (ghi D3).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$thuoc = $id ? (mot_dong('SELECT * FROM thuoc WHERE id = ?', [$id]) ?? chuyen_huong('quan-ly/thuoc.php')) : null;

$gia_tri = [
    'ten' => $thuoc['ten'] ?? '',
    'ham_luong' => $thuoc['ham_luong'] ?? '',
    'don_vi' => $thuoc['don_vi'] ?? '',
    'nhom' => $thuoc['nhom'] ?? '',
    'lieu_toi_da' => $thuoc['lieu_toi_da'] ?? '',
    'luu_y' => $thuoc['luu_y'] ?? '',
];
$loi = [];

if (la_post()) {
    kiem_tra_csrf();
    foreach (array_keys($gia_tri) as $khoa) {
        $gia_tri[$khoa] = gon($_POST[$khoa] ?? '');
    }
    if ($gia_tri['ten'] === '') {
        $loi['ten'] = 'Vui lòng nhập tên thuốc';
    }
    if ($gia_tri['ham_luong'] === '') {
        $loi['ham_luong'] = 'Vui lòng nhập hàm lượng';
    }
    if (!in_array($gia_tri['don_vi'], DON_VI_THUOC, true)) {
        $loi['don_vi'] = 'Vui lòng chọn đơn vị tính';
    }
    if (!in_array($gia_tri['nhom'], NHOM_THUOC, true)) {
        $loi['nhom'] = 'Vui lòng chọn nhóm thuốc';
    }
    if (!isset($loi['ten']) && !isset($loi['ham_luong'])) {
        $trung = mot_gia_tri('SELECT ma FROM thuoc WHERE ten = ? AND ham_luong = ? AND id <> ?', [$gia_tri['ten'], $gia_tri['ham_luong'], $id]);
        if ($trung) {
            $loi['ten'] = "Thuốc này đã có trong danh mục (mã $trung)";
        }
    }

    if (!$loi) {
        $tham_so = [$gia_tri['ten'], $gia_tri['ham_luong'], $gia_tri['don_vi'], $gia_tri['nhom'], $gia_tri['lieu_toi_da'] ?: null, mb_substr($gia_tri['luu_y'], 0, 500) ?: null];
        if ($thuoc) {
            truy_van('UPDATE thuoc SET ten = ?, ham_luong = ?, don_vi = ?, nhom = ?, lieu_toi_da = ?, luu_y = ? WHERE id = ?', [...$tham_so, $thuoc['id']]);
        } else {
            // Mã thuốc sinh lúc lưu để hai người thêm cùng lúc không bị trùng mã
            truy_van('INSERT INTO thuoc (ma, ten, ham_luong, don_vi, nhom, lieu_toi_da, luu_y) VALUES (?, ?, ?, ?, ?, ?, ?)', [ma_thuoc_moi(), ...$tham_so]);
            $id = (int) csdl()->lastInsertId();
        }
        chuyen_huong('quan-ly/thuoc-form.php', ['id' => $id, 'da_luu' => 1]);
    }
}

function o_thuoc(string $khoa, string $nhan, array $gia_tri, array $loi, bool $bat_buoc, string $them = ''): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= e($nhan) ?><?= $bat_buoc ? ' <span class="req">*</span>' : '' ?></label>
      <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

function chon_thuoc(string $khoa, string $nhan, array $ds, array $gia_tri, array $loi): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= e($nhan) ?> <span class="req">*</span></label>
      <select class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>">
        <option value="">— Chọn —</option>
        <?= cac_lua_chon($ds, $gia_tri[$khoa]) ?>
      </select>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

mo_trang($thuoc ? 'Sửa thông tin thuốc' : 'Thêm thuốc mới', 'ql', 'drugs');
?>
<main class="pg w920">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-ly/thuoc.php')) ?>">‹ Danh mục thuốc</a>
    <h1><?= $thuoc ? 'Sửa thông tin thuốc' : 'Thêm thuốc mới' ?></h1>
  </div>

  <?php if (isset($_GET['da_luu']) && !la_post()): ?>
    <div role="status" class="alert ok bar">
      <?= bieu_tuong(ICON_DUNG, 24, 2.4) ?>
      <span class="txt" style="font-size:16px"><strong>Đã lưu vào danh mục thuốc.</strong> Thông tin này được dùng để kiểm tra thuốc khi kê đơn.</span>
      <a class="btn pri" href="<?= e(url('quan-ly/thuoc.php')) ?>">Về danh mục</a>
    </div>
  <?php endif; ?>
  <?php if ($loi): ?>
    <div role="alert" class="alert err">Thông tin chưa hợp lệ. Vui lòng sửa các ô được đánh dấu bên dưới.</div>
  <?php endif; ?>

  <form class="card p28 stack g20" method="post" novalidate>
    <?= o_csrf() ?>
    <?php if ($thuoc): ?><input type="hidden" name="id" value="<?= e($thuoc['id']) ?>"><?php endif; ?>
    <div class="form-grid">
      <div class="field">
        <label for="ma">Mã thuốc</label>
        <input class="inp" id="ma" value="<?= e($thuoc['ma'] ?? ma_thuoc_moi()) ?>" readonly>
      </div>
      <?php
      o_thuoc('ten', 'Tên thuốc', $gia_tri, $loi, true, 'placeholder="VD: Cetirizin"');
      o_thuoc('ham_luong', 'Hàm lượng', $gia_tri, $loi, true, 'placeholder="VD: 10 mg"');
      chon_thuoc('don_vi', 'Đơn vị tính', DON_VI_THUOC, $gia_tri, $loi);
      chon_thuoc('nhom', 'Nhóm thuốc', NHOM_THUOC, $gia_tri, $loi);
      o_thuoc('lieu_toi_da', 'Liều tối đa / ngày', $gia_tri, $loi, false, 'placeholder="VD: 1 viên"');
      ?>
    </div>
    <div class="field">
      <label for="luu_y">Lưu ý / chống chỉ định</label>
      <textarea class="inp" id="luu_y" name="luu_y" rows="3" maxlength="500"><?= e($gia_tri['luu_y']) ?></textarea>
    </div>
    <span class="muted" style="font-size:14px"><span class="req">*</span> là thông tin bắt buộc</span>
    <div class="form-actions">
      <a class="btn" href="<?= e(url('quan-ly/thuoc.php')) ?>">Hủy</a>
      <button class="btn pri" type="submit">Lưu thuốc</button>
    </div>
  </form>
</main>
<?php dong_trang(); ?>
