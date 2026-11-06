<?php
/*
 * Đặt lịch hộ (Nhân viên) — CN 2.1: bệnh nhân gọi điện hoặc đến quầy nhờ đặt lịch. Nhân viên tìm bệnh nhân theo
 * số điện thoại hoặc họ tên (đọc D2), hoặc thêm người mới chỉ với họ tên và số điện thoại, rồi chuyển sang
 * các màn chọn bác sĩ, chọn giờ, xác nhận dùng chung với bệnh nhân. Lịch sắp tới của bệnh nhân đổi, hủy được ngay tại đây.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('NV');

$tu_khoa = gon($_GET['q'] ?? '');
$so_tim = chi_so($tu_khoa);
$gia_tri = ['ho_ten' => '', 'sdt' => $so_tim];
$loi = [];

if (la_post()) {
    kiem_tra_csrf();
    $gia_tri = ['ho_ten' => gon($_POST['ho_ten'] ?? ''), 'sdt' => trim((string) ($_POST['sdt'] ?? ''))];
    $sdt = chi_so($gia_tri['sdt']);
    if ($gia_tri['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên';
    }
    if ($sdt === '') {
        $loi['sdt'] = 'Vui lòng nhập số điện thoại';
    } elseif (!sdt_hop_le($sdt)) {
        $loi['sdt'] = 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0';
    }
    if (!$loi) {
        $da_co = mot_dong('SELECT id, ho_ten FROM benh_nhan WHERE so_dien_thoai = ?', [$sdt]);
        if ($da_co) {
            dat_thong_diep('info', 'Số ' . e(dinh_dang_sdt($sdt)) . ' đã có hồ sơ của <strong>' . e($da_co['ho_ten']) . '</strong>, tiếp tục đặt lịch cho bệnh nhân này.');
            chuyen_huong('benh-nhan/chon-bac-si.php', ['bn' => $da_co['id']]);
        }
        // Hồ sơ chưa có mã; mã bệnh nhân được cấp khi nhân viên tiếp nhận lần đầu
        truy_van('INSERT INTO benh_nhan (ho_ten, so_dien_thoai) VALUES (?, ?)', [$gia_tri['ho_ten'], $sdt]);
        chuyen_huong('benh-nhan/chon-bac-si.php', ['bn' => (int) csdl()->lastInsertId()]);
    }
}

$ds_benh_nhan = $tu_khoa === '' ? [] : nhieu_dong(
    "SELECT * FROM benh_nhan WHERE ho_ten LIKE ? OR (? <> '' AND so_dien_thoai LIKE ?) ORDER BY ho_ten LIMIT 20",
    ["%$tu_khoa%", $so_tim, "%$so_tim%"]
);
$lich_sap_toi = [];
if ($ds_benh_nhan) {
    $ids = array_column($ds_benh_nhan, 'id');
    foreach (nhieu_dong(
        "SELECT lh.*, bs.ho_ten AS bac_si FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
         WHERE lh.benh_nhan_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ") AND lh.trang_thai = 'da_dat' AND TIMESTAMP(lh.ngay, lh.gio) > ?
         ORDER BY lh.ngay, lh.gio",
        [...$ids, bay_gio()]
    ) as $lh) {
        $lich_sap_toi[$lh['benh_nhan_id']][] = $lh;
    }
}

mo_trang('Đặt lịch hộ', 'nv', 'book');
?>
<main class="pg">
  <div class="pg-head">
    <h1>Đặt lịch hộ bệnh nhân</h1>
  </div>
  <?= hien_thong_diep() ?>

  <div class="split">
    <section class="card p22 stack g16 main">
      <form class="field" method="get" role="search">
        <label for="tim">Tìm bệnh nhân đã có hồ sơ</label>
        <div class="row" style="gap:10px;flex-wrap:nowrap">
          <input class="inp" id="tim" name="q" type="search" value="<?= e($tu_khoa) ?>" placeholder="Số điện thoại hoặc họ tên" autofocus>
          <button class="btn pri" type="submit">Tìm</button>
        </div>
      </form>

      <?php if ($tu_khoa === ''): ?>
        <p class="muted" style="font-size:15px">Hỏi số điện thoại của người gọi đến hoặc người đến quầy rồi tìm hồ sơ. Chưa có hồ sơ thì thêm bệnh nhân mới ở bên phải.</p>
      <?php elseif (!$ds_benh_nhan): ?>
        <div role="status" class="alert warn">Không tìm thấy bệnh nhân với “<?= e($tu_khoa) ?>”. Thêm bệnh nhân mới ở bên phải để đặt lịch.</div>
      <?php else: ?>
        <div class="tbl-box">
          <table style="min-width:640px">
            <thead><tr><th>Bệnh nhân</th><th>Lịch hẹn sắp tới</th><th class="t-right"></th></tr></thead>
            <tbody>
              <?php foreach ($ds_benh_nhan as $bn): ?>
                <tr>
                  <td>
                    <div class="stack" style="gap:2px">
                      <span class="strong"><?= e($bn['ho_ten']) ?></span>
                      <span class="muted" style="font-size:13px"><?= e(implode(' · ', array_filter([dinh_dang_sdt($bn['so_dien_thoai']), $bn['ma'] ?: 'chưa có mã', $bn['nam_sinh']]))) ?></span>
                    </div>
                  </td>
                  <td>
                    <?php foreach ($lich_sap_toi[$bn['id']] ?? [] as $lh): ?>
                      <div class="row" style="gap:8px;padding:3px 0">
                        <span style="font-size:14px"><?= e(gio_ngan($lh['gio']) . ' ' . ngay_ngan($lh['ngay'])) ?> · <?= e(ten_bac_si($lh['bac_si'])) ?></span>
                        <a class="btn sm" href="<?= e(url('benh-nhan/chon-bac-si.php', ['doi' => $lh['id'], 'bn' => $bn['id']])) ?>">Đổi</a>
                        <a class="btn sm red" href="<?= e(url('benh-nhan/huy-lich.php', ['id' => $lh['id'], 'bn' => $bn['id']])) ?>">Hủy</a>
                      </div>
                    <?php endforeach; ?>
                    <?php if (empty($lich_sap_toi[$bn['id']])): ?><span class="muted" style="font-size:14px">Không có</span><?php endif; ?>
                  </td>
                  <td class="t-right"><a class="btn sm pri" href="<?= e(url('benh-nhan/chon-bac-si.php', ['bn' => $bn['id']])) ?>">Đặt lịch</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <form class="card p22 stack g16 side" method="post" novalidate>
      <?= o_csrf() ?>
      <div class="stack g4">
        <h2 style="font-size:20px">Bệnh nhân mới</h2>
        <span class="muted" style="font-size:15px">Chỉ cần họ tên và số điện thoại; hồ sơ đầy đủ bổ sung khi tiếp nhận</span>
      </div>
      <?php foreach (['ho_ten' => ['Họ và tên', 'autocomplete="off"'], 'sdt' => ['Số điện thoại', 'inputmode="tel"']] as $khoa => [$nhan, $them]): ?>
        <div class="field">
          <label for="<?= $khoa ?>"><?= $nhan ?> <span class="req">*</span></label>
          <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= $khoa ?>" name="<?= $khoa ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
          <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
        </div>
      <?php endforeach; ?>
      <button class="btn pri" type="submit">Tiếp tục chọn bác sĩ, giờ khám</button>
    </form>
  </div>
</main>
<?php dong_trang(); ?>
