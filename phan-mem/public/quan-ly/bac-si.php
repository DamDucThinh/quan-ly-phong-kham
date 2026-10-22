<?php
/*
 * [4] Danh sách bác sĩ — CN 1.1 (đọc D1). Bác sĩ trong danh sách hiện để bệnh nhân chọn khi đặt lịch.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$tu_khoa = gon($_GET['q'] ?? '');
$ds_bac_si = nhieu_dong(
    'SELECT bs.*, (SELECT COUNT(*) FROM ca_lam_viec c WHERE c.bac_si_id = bs.id) AS so_ca
     FROM bac_si bs
     WHERE ? = \'\' OR bs.ho_ten LIKE ? OR bs.chuyen_khoa LIKE ?
     ORDER BY bs.id',
    [$tu_khoa, "%$tu_khoa%", "%$tu_khoa%"]
);

mo_trang('Danh sách bác sĩ', 'ql', 'doctors');
?>
<main class="pg">
  <div class="head-row">
    <div class="pg-head">
      <h1>Danh sách bác sĩ</h1>
    </div>
    <a class="btn pri" href="<?= e(url('quan-ly/bac-si-form.php')) ?>">+ Thêm bác sĩ</a>
  </div>

  <section class="card p22 stack g16">
    <form class="row between" method="get" role="search">
      <div class="row" style="gap:10px">
        <label class="sr" for="tim">Tìm bác sĩ</label>
        <input class="inp" id="tim" name="q" type="search" value="<?= e($tu_khoa) ?>" placeholder="Tìm theo tên hoặc chuyên khoa" style="width:320px;max-width:100%">
        <button class="btn" type="submit">Tìm</button>
      </div>
      <span class="muted" style="font-size:14px"><?= count($ds_bac_si) ?> bác sĩ</span>
    </form>
    <div class="tbl-box">
      <table style="min-width:860px">
        <thead><tr><th>Bác sĩ</th><th>Chuyên khoa</th><th>Phòng</th><th>Số điện thoại</th><th>Số ca / tuần</th><th class="t-right">Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($ds_bac_si as $bs): ?>
            <tr>
              <td><div class="row" style="gap:12px;flex-wrap:nowrap"><span class="avatar md"><?= e(chu_cai_dau($bs['ho_ten'])) ?></span><span class="strong"><?= e(ten_bac_si($bs['ho_ten'])) ?></span></div></td>
              <td><?= e($bs['chuyen_khoa']) ?></td>
              <td><?= e($bs['phong']) ?></td>
              <td class="num"><?= e(dinh_dang_sdt($bs['so_dien_thoai'])) ?></td>
              <td class="num"><?= (int) $bs['so_ca'] ?></td>
              <td class="t-right">
                <div class="cell-actions">
                  <a class="btn sm" href="<?= e(url('quan-ly/lich-lam-viec.php', ['bs' => $bs['id']])) ?>">Lịch làm việc</a>
                  <a class="btn sm" href="<?= e(url('quan-ly/bac-si-form.php', ['id' => $bs['id']])) ?>">Sửa</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!$ds_bac_si): ?><p class="empty-row">Không tìm thấy bác sĩ phù hợp.</p><?php endif; ?>
  </section>
</main>
<?php dong_trang(); ?>
