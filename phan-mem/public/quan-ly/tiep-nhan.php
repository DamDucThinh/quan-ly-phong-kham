<?php
/*
 * [14] Tiếp nhận bệnh nhân — CN 3.1: tìm lịch hẹn trong ngày (đọc D4), tạo mới hoặc cập nhật hồ sơ bệnh nhân (D2),
 * tạo phiếu tiếp nhận chuyển sang khám. Bệnh nhân không có lịch hẹn hôm nay thì "Tạo bệnh nhân mới"
 * và chọn bác sĩ khám ngay (khám vãng lai, như bản thiết kế).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');

$tu_khoa = gon($_GET['q'] ?? '');
$so_tim = chi_so($tu_khoa);
$ds_cho = nhieu_dong(
    "SELECT lh.id, lh.gio, bn.ho_ten, bn.so_dien_thoai, bs.ho_ten AS bac_si
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.ngay = ? AND lh.trang_thai = 'da_dat'
       AND (? = '' OR bn.ho_ten LIKE ? OR (? <> '' AND bn.so_dien_thoai LIKE ?))
     ORDER BY lh.gio, lh.id",
    [hom_nay(), $tu_khoa, "%$tu_khoa%", $so_tim, "%$so_tim%"]
);

// Chế độ "hen": tiếp nhận theo lịch hẹn; chế độ "moi": bệnh nhân không có lịch hẹn hôm nay
$che_do = isset($_GET['moi']) || ($_POST['che_do'] ?? '') === 'moi' ? 'moi' : 'hen';
$lich_hen = null;
$canh_bao = '';
if ($che_do === 'hen') {
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? ($ds_cho[0]['id'] ?? 0));
    if ($id) {
        $lich_hen = mot_dong(
            "SELECT lh.*, bs.ho_ten AS bac_si FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
             WHERE lh.id = ? AND lh.ngay = ? AND lh.trang_thai = 'da_dat'",
            [$id, hom_nay()]
        );
        if (!$lich_hen && isset($_GET['id'])) {
            $canh_bao = 'Lịch hẹn này đã được tiếp nhận hoặc không phải lịch hẹn hôm nay.';
        }
    }
    if (!$lich_hen && $ds_cho && !la_post()) {
        $lich_hen = mot_dong(
            'SELECT lh.*, bs.ho_ten AS bac_si FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id WHERE lh.id = ?',
            [$ds_cho[0]['id']]
        );
    }
    if (!$lich_hen) {
        if (la_post()) {
            dat_thong_diep('err', 'Lịch hẹn này vừa được tiếp nhận ở nơi khác.');
            chuyen_huong('quan-ly/tiep-nhan.php');
        }
        $che_do = 'moi';
    }
}
$benh_nhan = $lich_hen ? mot_dong('SELECT * FROM benh_nhan WHERE id = ?', [$lich_hen['benh_nhan_id']]) : null;

// Bác sĩ có ca làm việc hôm nay; nếu hôm nay không ai có ca thì cho chọn tất cả
$bac_si_hom_nay = nhieu_dong(
    'SELECT DISTINCT bs.id, bs.ho_ten FROM bac_si bs JOIN ca_lam_viec c ON c.bac_si_id = bs.id WHERE c.thu = ? ORDER BY bs.id',
    [(int) date('N')]
) ?: nhieu_dong('SELECT id, ho_ten FROM bac_si ORDER BY id');
$lua_chon_bac_si = array_column(array_map(fn($b) => ['id' => $b['id'], 'ten' => ten_bac_si($b['ho_ten'])], $bac_si_hom_nay), 'ten', 'id');

$gia_tri = [
    'ho_ten' => $benh_nhan['ho_ten'] ?? '',
    'sdt' => $benh_nhan ? dinh_dang_sdt($benh_nhan['so_dien_thoai']) : chi_so((string) ($_GET['sdt'] ?? '')),
    'nam_sinh' => (string) ($benh_nhan['nam_sinh'] ?? ''),
    'gioi_tinh' => $benh_nhan['gioi_tinh'] ?? '',
    'dia_chi' => $benh_nhan['dia_chi'] ?? '',
    'ly_do' => $lich_hen['ly_do'] ?? '',
    'bac_si_id' => '',
];
$loi = [];

if (la_post()) {
    kiem_tra_csrf();
    foreach (array_keys($gia_tri) as $khoa) {
        $gia_tri[$khoa] = gon($_POST[$khoa] ?? '');
    }
    $sdt = chi_so($gia_tri['sdt']);
    if ($gia_tri['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên';
    }
    if ($sdt === '') {
        $loi['sdt'] = 'Vui lòng nhập số điện thoại';
    } elseif (!sdt_hop_le($sdt)) {
        $loi['sdt'] = 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0';
    }
    if (!preg_match('/^(19|20)\d{2}$/', $gia_tri['nam_sinh']) || (int) $gia_tri['nam_sinh'] > (int) date('Y')) {
        $loi['nam_sinh'] = 'Năm sinh gồm 4 chữ số, VD: 1990';
    }
    if (!in_array($gia_tri['gioi_tinh'], ['Nam', 'Nữ'], true)) {
        $loi['gioi_tinh'] = 'Vui lòng chọn giới tính';
    }
    if ($che_do === 'moi' && !isset($lua_chon_bac_si[(int) $gia_tri['bac_si_id']])) {
        $loi['bac_si_id'] = 'Vui lòng chọn bác sĩ khám';
    }

    if (!$loi) {
        $pdo = csdl();
        $pdo->beginTransaction();
        try {
            $ket_qua = $che_do === 'hen'
                ? tiep_nhan_theo_lich_hen((int) $lich_hen['id'], $gia_tri, $sdt)
                : tiep_nhan_vang_lai($gia_tri, $sdt);
            if (is_string($ket_qua)) {
                $pdo->rollBack();
                $loi['chung'] = $ket_qua;
            } else {
                $pdo->commit();
                dat_thong_diep('ok bar', '<span class="txt"><strong>' . ($ket_qua['tao_moi'] ? 'Đã tạo hồ sơ bệnh nhân mới' : 'Đã cập nhật hồ sơ bệnh nhân')
                    . ' ' . e($ket_qua['ma']) . '.</strong> Phiếu tiếp nhận đã chuyển sang khám — bệnh nhân vào hàng chờ của '
                    . e(ten_bac_si($ket_qua['bac_si'])) . '.</span><a class="btn pri" href="' . e(url('quan-ly/lich-hen.php')) . '">Mở danh sách khám</a>');
                chuyen_huong('quan-ly/tiep-nhan.php');
            }
        } catch (PDOException $loi_csdl) {
            $pdo->rollBack();
            if (!la_trung_khoa($loi_csdl)) {
                throw $loi_csdl;
            }
            $loi['chung'] = 'Có người vừa lưu cùng lúc. Vui lòng bấm lưu lại.';
        }
    }
}

/** Ghi hồ sơ bệnh nhân (D2): cấp mã mới nếu bệnh nhân chưa có hồ sơ. */
function luu_ho_so(array $benh_nhan, array $gia_tri, string $sdt): array
{
    $ma = $benh_nhan['ma'] ?: ma_benh_nhan_moi();
    truy_van(
        'UPDATE benh_nhan SET ma = ?, ho_ten = ?, so_dien_thoai = ?, nam_sinh = ?, gioi_tinh = ?, dia_chi = ? WHERE id = ?',
        [$ma, $gia_tri['ho_ten'], $sdt, (int) $gia_tri['nam_sinh'], $gia_tri['gioi_tinh'], $gia_tri['dia_chi'] ?: null, $benh_nhan['id']]
    );
    return ['ma' => $ma, 'tao_moi' => !$benh_nhan['ma']];
}

/** Tiếp nhận bệnh nhân có lịch hẹn hôm nay. Trả về chuỗi lỗi nếu không tiếp nhận được. */
function tiep_nhan_theo_lich_hen(int $lich_hen_id, array $gia_tri, string $sdt): array|string
{
    $lich_hen = mot_dong(
        "SELECT lh.*, bs.ho_ten AS bac_si FROM lich_hen lh JOIN bac_si bs ON bs.id = lh.bac_si_id
         WHERE lh.id = ? AND lh.trang_thai = 'da_dat' FOR UPDATE",
        [$lich_hen_id]
    );
    if (!$lich_hen) {
        return 'Lịch hẹn này vừa được tiếp nhận ở nơi khác.';
    }
    $benh_nhan = mot_dong('SELECT * FROM benh_nhan WHERE id = ? FOR UPDATE', [$lich_hen['benh_nhan_id']]);
    $nguoi_khac = mot_dong('SELECT ma, ho_ten FROM benh_nhan WHERE so_dien_thoai = ? AND id <> ?', [$sdt, $benh_nhan['id']]);
    if ($nguoi_khac) {
        return 'Số điện thoại ' . dinh_dang_sdt($sdt) . ' đã thuộc bệnh nhân khác (' . ($nguoi_khac['ma'] ?: $nguoi_khac['ho_ten']) . ').';
    }
    $ho_so = luu_ho_so($benh_nhan, $gia_tri, $sdt);
    truy_van("UPDATE lich_hen SET trang_thai = 'cho_kham', ly_do = ? WHERE id = ?", [mb_substr($gia_tri['ly_do'], 0, 255) ?: null, $lich_hen['id']]);
    return $ho_so + ['bac_si' => $lich_hen['bac_si']];
}

/** Tiếp nhận bệnh nhân không có lịch hẹn hôm nay: tạo hoặc cập nhật hồ sơ, tạo lượt khám vãng lai. */
function tiep_nhan_vang_lai(array $gia_tri, string $sdt): array|string
{
    $benh_nhan = mot_dong('SELECT * FROM benh_nhan WHERE so_dien_thoai = ? FOR UPDATE', [$sdt]);
    if ($benh_nhan) {
        $hen = mot_dong("SELECT gio FROM lich_hen WHERE benh_nhan_id = ? AND ngay = ? AND trang_thai = 'da_dat'", [$benh_nhan['id'], hom_nay()]);
        if ($hen) {
            return 'Bệnh nhân ' . $benh_nhan['ho_ten'] . ' có lịch hẹn hôm nay lúc ' . gio_ngan($hen['gio'])
                . '. Hãy chọn lịch hẹn đó ở danh sách bên trái để tiếp nhận.';
        }
        $ho_so = luu_ho_so($benh_nhan, $gia_tri, $sdt);
        $benh_nhan_id = (int) $benh_nhan['id'];
    } else {
        $ho_so = ['ma' => ma_benh_nhan_moi(), 'tao_moi' => true];
        truy_van(
            'INSERT INTO benh_nhan (ma, ho_ten, so_dien_thoai, nam_sinh, gioi_tinh, dia_chi) VALUES (?, ?, ?, ?, ?, ?)',
            [$ho_so['ma'], $gia_tri['ho_ten'], $sdt, (int) $gia_tri['nam_sinh'], $gia_tri['gioi_tinh'], $gia_tri['dia_chi'] ?: null]
        );
        $benh_nhan_id = (int) csdl()->lastInsertId();
    }
    truy_van(
        "INSERT INTO lich_hen (benh_nhan_id, bac_si_id, ngay, gio, loai, ly_do, trang_thai, tao_luc) VALUES (?, ?, ?, ?, 'vang_lai', ?, 'cho_kham', ?)",
        [$benh_nhan_id, (int) $gia_tri['bac_si_id'], hom_nay(), date('H:i'), mb_substr($gia_tri['ly_do'], 0, 255) ?: null, bay_gio()]
    );
    return $ho_so + ['bac_si' => mot_gia_tri('SELECT ho_ten FROM bac_si WHERE id = ?', [(int) $gia_tri['bac_si_id']])];
}

function o_tiep_nhan(string $khoa, string $nhan, array $gia_tri, array $loi, bool $bat_buoc, string $them = ''): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= e($nhan) ?><?= $bat_buoc ? ' <span class="req">*</span>' : '' ?></label>
      <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

$co_ho_so = $che_do === 'hen' && !empty($benh_nhan['ma']);

mo_trang('Tiếp nhận bệnh nhân', 'ql', 'reception');
?>
<main class="pg">
  <div class="pg-head">
    <h1>Tiếp nhận bệnh nhân</h1>
  </div>
  <?= hien_thong_diep() ?>
  <?php if ($canh_bao): ?><div class="alert warn"><?= e($canh_bao) ?></div><?php endif; ?>

  <div class="split">
    <section class="card p20 stack g14 side" style="flex:1 1 360px">
      <a class="btn dashed<?= $che_do === 'moi' ? ' on' : '' ?>" href="<?= e(url('quan-ly/tiep-nhan.php', ['moi' => 1])) ?>">+ Tạo bệnh nhân mới</a>
      <form class="field" method="get" role="search">
        <label for="tim">Tìm lịch hẹn hôm nay</label>
        <input class="inp" id="tim" name="q" type="search" value="<?= e($tu_khoa) ?>" placeholder="Số điện thoại hoặc họ tên">
      </form>
      <span class="muted" style="font-size:14px">Lịch hẹn hôm nay chưa tiếp nhận (<?= count($ds_cho) ?>)</span>
      <div class="pick-list">
        <?php foreach ($ds_cho as $p): $chon = $che_do === 'hen' && $lich_hen && $p['id'] === $lich_hen['id']; ?>
          <a class="pick thin<?= $chon ? ' on' : '' ?>" style="min-height:68px;padding:10px 14px" href="<?= e(url('quan-ly/tiep-nhan.php', ['id' => $p['id'], 'q' => $tu_khoa])) ?>"<?= $chon ? ' aria-current="true"' : '' ?>>
            <span class="time"><?= e(gio_ngan($p['gio'])) ?></span>
            <span class="pick-col" style="gap:2px">
              <span class="t"><?= e($p['ho_ten']) ?></span>
              <span class="s"><?= e(dinh_dang_sdt($p['so_dien_thoai'])) ?> · <?= e(ten_bac_si($p['bac_si'])) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if ($tu_khoa !== '' && !$ds_cho): ?>
        <div role="status" class="alert warn stack g8" style="padding:14px 16px">
          <strong style="font-size:15px">Không có lịch hẹn hôm nay</strong>
          <span style="font-size:14px;line-height:1.5">Không tìm thấy lịch hẹn với thông tin này. Nếu bệnh nhân đến lần đầu, hãy tạo hồ sơ bệnh nhân mới.</span>
          <a class="btn sm" style="align-self:flex-start" href="<?= e(url('quan-ly/tiep-nhan.php', ['moi' => 1, 'sdt' => $so_tim])) ?>">Tạo bệnh nhân mới</a>
        </div>
      <?php elseif (!$ds_cho): ?>
        <p class="muted" style="font-size:14px">Không còn lịch hẹn nào chờ tiếp nhận.</p>
      <?php endif; ?>
    </section>

    <form class="card p24 stack g18 main" style="flex:2 1 600px" method="post" novalidate>
      <?= o_csrf() ?>
      <input type="hidden" name="che_do" value="<?= e($che_do) ?>">
      <?php if ($lich_hen): ?><input type="hidden" name="id" value="<?= e($lich_hen['id']) ?>"><?php endif; ?>
      <div class="row between">
        <div class="stack g4">
          <h2 style="font-size:20px"><?= $che_do === 'hen' ? 'Phiếu tiếp nhận' : 'Tạo bệnh nhân mới' ?></h2>
          <span class="muted" style="font-size:15px">
            <?= $che_do === 'hen'
                ? e('Lịch hẹn ' . gio_ngan($lich_hen['gio']) . ' hôm nay · ' . ten_bac_si($lich_hen['bac_si']))
                : 'Bệnh nhân không có lịch hẹn hôm nay · chọn bác sĩ khám' ?>
          </span>
        </div>
        <span class="pill <?= $co_ho_so ? 'ok' : 'info' ?>"><?= $co_ho_so ? e('Đã có hồ sơ ' . $benh_nhan['ma']) : 'Chưa có hồ sơ · sẽ tạo hồ sơ mới' ?></span>
      </div>
      <?php if (isset($loi['chung'])): ?>
        <div role="alert" class="alert err"><?= e($loi['chung']) ?></div>
      <?php endif; ?>
      <div class="form-grid">
        <?php
        o_tiep_nhan('ho_ten', 'Họ và tên', $gia_tri, $loi, true, 'autocomplete="off"');
        o_tiep_nhan('sdt', 'Số điện thoại', $gia_tri, $loi, true, 'inputmode="tel"');
        o_tiep_nhan('nam_sinh', 'Năm sinh', $gia_tri, $loi, true, 'inputmode="numeric" maxlength="4" placeholder="VD: 1990"');
        ?>
        <div class="field">
          <label for="gioi_tinh">Giới tính <span class="req">*</span></label>
          <select class="inp<?= isset($loi['gioi_tinh']) ? ' bad' : '' ?>" id="gioi_tinh" name="gioi_tinh">
            <option value="">— Chọn —</option><?= cac_lua_chon(['Nam', 'Nữ'], $gia_tri['gioi_tinh']) ?>
          </select>
          <?php if (isset($loi['gioi_tinh'])): ?><span class="err"><?= e($loi['gioi_tinh']) ?></span><?php endif; ?>
        </div>
      </div>
      <?php if ($che_do === 'moi'): ?>
        <div class="field">
          <label for="bac_si_id">Bác sĩ khám <span class="req">*</span></label>
          <select class="inp<?= isset($loi['bac_si_id']) ? ' bad' : '' ?>" id="bac_si_id" name="bac_si_id">
            <option value="">— Chọn bác sĩ đang làm việc —</option><?= cac_lua_chon($lua_chon_bac_si, $gia_tri['bac_si_id'], true) ?>
          </select>
          <?php if (isset($loi['bac_si_id'])): ?><span class="err"><?= e($loi['bac_si_id']) ?></span><?php endif; ?>
        </div>
      <?php endif; ?>
      <?php
      o_tiep_nhan('dia_chi', 'Địa chỉ', $gia_tri, $loi, false);
      o_tiep_nhan('ly_do', 'Lý do khám (bệnh nhân khai)', $gia_tri, $loi, false, 'maxlength="255"');
      ?>
      <div class="form-actions">
        <button class="btn pri" type="submit"><?= $co_ho_so ? 'Cập nhật hồ sơ và tạo phiếu tiếp nhận' : 'Tạo hồ sơ bệnh nhân và phiếu tiếp nhận' ?></button>
      </div>
    </form>
  </div>
</main>
<?php dong_trang(); ?>
