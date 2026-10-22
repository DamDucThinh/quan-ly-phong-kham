<?php
/*
 * [7] Danh mục thuốc — CN 1.3 (đọc D3). Thuốc trong danh mục là thuốc được phép kê đơn.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$tu_khoa = gon($_GET['q'] ?? '');
$nhom = in_array($_GET['nhom'] ?? '', NHOM_THUOC, true) ? $_GET['nhom'] : '';
$ds_thuoc = nhieu_dong(
    "SELECT * FROM thuoc
     WHERE (? = '' OR ma LIKE ? OR ten LIKE ?) AND (? = '' OR nhom = ?)
     ORDER BY ma",
    [$tu_khoa, "%$tu_khoa%", "%$tu_khoa%", $nhom, $nhom]
);
$cac_nhom = array_column(nhieu_dong('SELECT DISTINCT nhom FROM thuoc ORDER BY nhom'), 'nhom');

mo_trang('Danh mục thuốc', 'ql', 'drugs');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <h1>Danh mục thuốc</h1>
    </div>
    <a class="btn pri" href="<?= e(url('quan-ly/thuoc-form.php')) ?>">+ Thêm thuốc</a>
  </div>

  <section class="card p22 stack g16">
    <form class="row between" method="get" role="search">
      <div class="row" style="gap:10px">
        <label class="sr" for="tim">Tìm thuốc</label>
        <input class="inp" id="tim" name="q" type="search" value="<?= e($tu_khoa) ?>" placeholder="Tìm theo tên hoặc mã thuốc" style="width:300px;max-width:100%">
        <label class="sr" for="nhom">Nhóm thuốc</label>
        <select class="inp" id="nhom" name="nhom" style="width:auto" onchange="this.form.submit()">
          <option value="">Tất cả nhóm</option>
          <?= cac_lua_chon($cac_nhom, $nhom) ?>
        </select>
        <button class="btn" type="submit">Tìm</button>
      </div>
      <span class="muted" style="font-size:14px"><?= count($ds_thuoc) ?> thuốc</span>
    </form>
    <div class="tbl-box">
      <table style="min-width:900px">
        <thead><tr><th>Mã</th><th>Tên thuốc</th><th>Hàm lượng</th><th>Đơn vị</th><th>Nhóm</th><th>Lưu ý / chống chỉ định</th><th class="t-right">Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($ds_thuoc as $t): ?>
            <tr>
              <td class="muted num"><?= e($t['ma']) ?></td>
              <td class="strong"><?= e($t['ten']) ?></td>
              <td><?= e($t['ham_luong']) ?></td>
              <td><?= e($t['don_vi']) ?></td>
              <td><?= e($t['nhom']) ?></td>
              <td class="muted" style="font-size:14px"><?= e($t['luu_y']) ?></td>
              <td class="t-right"><a class="btn sm" href="<?= e(url('quan-ly/thuoc-form.php', ['id' => $t['id']])) ?>">Sửa</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!$ds_thuoc): ?><p class="empty-row">Không tìm thấy thuốc phù hợp.</p><?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
