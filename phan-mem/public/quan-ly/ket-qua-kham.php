<?php
/*
 * [17] Ghi kết quả khám và kê đơn (gộp màn [18] Lập đơn thuốc theo quyết định đã chốt).
 *   CN 3.3 Cập nhật bệnh án: ghi kết quả khám, chẩn đoán của bác sĩ (ghi D5), gửi phiếu kết quả khám.
 *   CN 4.1 Lập đơn thuốc theo chỉ định của bác sĩ, lấy chẩn đoán từ D5.
 *   CN 4.2 Kiểm tra thuốc: thuốc phải có trong danh mục (D3), không trùng trong đơn, số lượng hợp lệ.
 *   CN 4.3 Lưu và in đơn thuốc (ghi D6), gửi đơn thuốc cho bệnh nhân.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$lich_hen = mot_dong(
    'SELECT lh.*, bn.ma, bn.ho_ten, bs.ho_ten AS bac_si
     FROM lich_hen lh JOIN benh_nhan bn ON bn.id = lh.benh_nhan_id JOIN bac_si bs ON bs.id = lh.bac_si_id
     WHERE lh.id = ?',
    [(int) ($_POST['id'] ?? $_GET['id'] ?? 0)]
) ?? chuyen_huong('quan-ly/lich-hen.php');
if (in_array($lich_hen['trang_thai'], ['da_dat', 'da_huy'], true)) {
    dat_thong_diep('err', 'Bệnh nhân ' . e($lich_hen['ho_ten']) . ' chưa được tiếp nhận nên chưa ghi kết quả khám được.');
    chuyen_huong('quan-ly/lich-hen.php');
}

$benh_an = mot_dong('SELECT * FROM benh_an WHERE lich_hen_id = ?', [$lich_hen['id']]);
$tien_su = tien_su((int) $lich_hen['benh_nhan_id'], (int) $lich_hen['id']);
$danh_muc = nhieu_dong('SELECT * FROM thuoc ORDER BY ten, ham_luong');
$thuoc_theo_id = array_column($danh_muc, null, 'id');

$gia_tri = ['mach' => '', 'nhiet_do' => '', 'huyet_ap' => '', 'can_nang' => '', 'ket_qua' => '', 'chan_doan' => '', 'loi_dan' => ''];
$co_ke_don = true;
$dong_thuoc = [];
$loi = [];
$loi_don = '';

if (la_post() && !$benh_an) {
    kiem_tra_csrf();
    foreach (array_keys($gia_tri) as $khoa) {
        $gia_tri[$khoa] = trim((string) ($_POST[$khoa] ?? ''));
    }
    $co_ke_don = ($_POST['co_ke_don'] ?? '1') === '1';
    foreach ((array) ($_POST['thuoc_id'] ?? []) as $i => $thuoc_id) {
        $dong_thuoc[] = [
            'thuoc_id' => (int) $thuoc_id,
            'so_luong' => trim((string) ($_POST['so_luong'][$i] ?? '')),
            'cach_dung' => mb_substr(gon($_POST['cach_dung'][$i] ?? ''), 0, 255),
            'loi' => '',
        ];
    }

    if ($gia_tri['ket_qua'] === '') {
        $loi['ket_qua'] = 'Vui lòng nhập kết quả khám';
    }
    if ($gia_tri['chan_doan'] === '') {
        $loi['chan_doan'] = 'Vui lòng nhập chẩn đoán';
    }
    if ($co_ke_don) {
        // CN 4.2 Kiểm tra thuốc
        $da_co = [];
        foreach ($dong_thuoc as &$dong) {
            if (!isset($thuoc_theo_id[$dong['thuoc_id']])) {
                $dong['loi'] = 'Không có trong danh mục';
            } elseif (isset($da_co[$dong['thuoc_id']])) {
                $dong['loi'] = 'Trùng thuốc trong đơn';
            } elseif (!preg_match('/^\d{1,3}$/', $dong['so_luong']) || (int) $dong['so_luong'] < 1) {
                $dong['loi'] = 'Số lượng chưa hợp lệ';
            }
            $da_co[$dong['thuoc_id']] = true;
        }
        unset($dong);
        if (!$dong_thuoc) {
            $loi_don = 'Chưa có thuốc nào trong đơn. Thêm thuốc, hoặc chọn "Không" nếu bác sĩ không chỉ định thuốc.';
        } elseif (array_filter(array_column($dong_thuoc, 'loi'))) {
            $loi_don = 'Đơn thuốc chưa hợp lệ: sửa hoặc xóa các thuốc được đánh dấu "Cần điều chỉnh".';
        }
    }

    if (!$loi && !$loi_don) {
        $pdo = csdl();
        $pdo->beginTransaction();
        $cap_nhat = truy_van("UPDATE lich_hen SET trang_thai = 'da_kham' WHERE id = ? AND trang_thai IN ('cho_kham', 'dang_kham')", [$lich_hen['id']]);
        if ($cap_nhat->rowCount() === 0) {
            $pdo->rollBack();
            dat_thong_diep('warn', 'Lần khám này đã được lưu trước đó.');
            chuyen_huong('quan-ly/ket-qua-kham.php', ['id' => $lich_hen['id']]);
        }

        // CN 3.3: lưu bệnh án (D5)
        truy_van(
            'INSERT INTO benh_an (lich_hen_id, benh_nhan_id, bac_si_id, ngay_kham, mach, nhiet_do, huyet_ap, can_nang, ket_qua, chan_doan, loi_dan, tao_luc)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $lich_hen['id'], $lich_hen['benh_nhan_id'], $lich_hen['bac_si_id'], $lich_hen['ngay'],
                mb_substr($gia_tri['mach'], 0, 10) ?: null, mb_substr($gia_tri['nhiet_do'], 0, 10) ?: null,
                mb_substr($gia_tri['huyet_ap'], 0, 15) ?: null, mb_substr($gia_tri['can_nang'], 0, 10) ?: null,
                $gia_tri['ket_qua'], mb_substr($gia_tri['chan_doan'], 0, 255), $gia_tri['loi_dan'] ?: null, bay_gio(),
            ]
        );
        $benh_an_id = (int) $pdo->lastInsertId();
        $ngay = ngay_vn($lich_hen['ngay']);
        gui_thong_bao(
            (int) $lich_hen['benh_nhan_id'],
            'ket_qua',
            "Phiếu kết quả khám ngày $ngay",
            mb_substr('Chẩn đoán: ' . $gia_tri['chan_doan'] . '.' . ($gia_tri['loi_dan'] ? ' Lời dặn: ' . gon($gia_tri['loi_dan']) : ''), 0, 500)
        );

        // CN 4.3: lưu đơn thuốc (D6)
        if ($co_ke_don) {
            truy_van('INSERT INTO don_thuoc (benh_an_id, tao_luc) VALUES (?, ?)', [$benh_an_id, bay_gio()]);
            $don_thuoc_id = (int) $pdo->lastInsertId();
            foreach ($dong_thuoc as $dong) {
                truy_van(
                    'INSERT INTO chi_tiet_don_thuoc (don_thuoc_id, thuoc_id, so_luong, cach_dung) VALUES (?, ?, ?, ?)',
                    [$don_thuoc_id, $dong['thuoc_id'], (int) $dong['so_luong'], $dong['cach_dung'] ?: null]
                );
            }
            $ten_cac_thuoc = implode(', ', array_map(fn($d) => ten_thuoc($thuoc_theo_id[$d['thuoc_id']]), $dong_thuoc));
            gui_thong_bao(
                (int) $lich_hen['benh_nhan_id'],
                'don_thuoc',
                "Đơn thuốc ngày $ngay",
                mb_substr('Đơn thuốc của ' . ten_bac_si($lich_hen['bac_si']) . ': ' . $ten_cac_thuoc . '. Vui lòng dùng theo hướng dẫn trên đơn.', 0, 500)
            );
        }
        $pdo->commit();

        dat_thong_diep('ok', '<strong>' . ($co_ke_don
            ? 'Đã lưu bệnh án và đơn thuốc. Phiếu kết quả khám và đơn thuốc đã gửi cho bệnh nhân.'
            : 'Đã lưu bệnh án. Phiếu kết quả khám đã gửi cho bệnh nhân.') . '</strong>');
        // "Lưu và in": quay lại màn này rồi tự mở hộp thoại in (không chuyển sang trang phiếu)
        chuyen_huong('quan-ly/ket-qua-kham.php', ['id' => $lich_hen['id'], 'in' => ($_POST['hanh_dong'] ?? '') === 'luu_in' ? 1 : null]);
    }
}

$thuoc_da_luu = $benh_an ? nhieu_dong(
    'SELECT t.ten, t.ham_luong, t.don_vi, ct.so_luong, ct.cach_dung
     FROM don_thuoc dt JOIN chi_tiet_don_thuoc ct ON ct.don_thuoc_id = dt.id JOIN thuoc t ON t.id = ct.thuoc_id
     WHERE dt.benh_an_id = ? ORDER BY ct.id',
    [$benh_an['id']]
) : [];
$co_canh_bao = (bool) array_filter(array_column($dong_thuoc, 'loi'));
$dong_tien_su = $tien_su ? 'tiền sử: ' . mb_strtolower(implode(', ', array_column($tien_su, 'chan_doan'))) : 'khám lần đầu';

function o_kham(string $khoa, string $nhan, array $gia_tri, array $loi, string $them = ''): void
{
    ?>
    <div class="field">
      <label for="<?= e($khoa) ?>"><?= $nhan ?></label>
      <input class="inp<?= isset($loi[$khoa]) ? ' bad' : '' ?>" id="<?= e($khoa) ?>" name="<?= e($khoa) ?>" value="<?= e($gia_tri[$khoa]) ?>" <?= $them ?>>
      <?php if (isset($loi[$khoa])): ?><span class="err"><?= e($loi[$khoa]) ?></span><?php endif; ?>
    </div>
    <?php
}

mo_trang('Ghi kết quả khám', 'ql', 'today');
?>
<main class="pg w1200" style="gap:22px">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-ly/lich-su-kham.php', ['id' => $lich_hen['id']])) ?>">‹ Hồ sơ bệnh án các lần trước</a>
    <h1>Ghi kết quả khám và kê đơn</h1>
    <p><?= e(implode(' · ', array_filter([$lich_hen['ho_ten'], $lich_hen['ma'], 'khám bởi ' . ten_bac_si($lich_hen['bac_si']), ngay_vn($lich_hen['ngay']), $dong_tien_su]))) ?></p>
  </div>

  <?= hien_thong_diep() ?>

  <?php if ($benh_an): ?>
    <section class="card p26 stack g18">
      <div class="row between">
        <h2>Bệnh án đã lưu</h2>
        <span class="pill st-da_kham"><span class="dotc"></span>Đã khám</span>
      </div>
      <div class="stack g4"><span class="lb">Chẩn đoán</span><strong style="font-size:18px;color:#0A5C53"><?= e($benh_an['chan_doan']) ?></strong></div>
      <div class="stack g4"><span class="lb">Kết quả khám</span><p style="font-size:16px;line-height:1.6"><?= nl2br(e($benh_an['ket_qua'])) ?></p></div>
      <div class="stack g8">
        <span class="lb">Đơn thuốc</span>
        <?php if ($thuoc_da_luu): ?>
          <ul class="rx compact">
            <?php foreach ($thuoc_da_luu as $t): ?>
              <li><span class="strong"><?= e(ten_thuoc($t)) ?></span><span class="muted"><?= e(so_luong_thuoc($t['so_luong'], $t['don_vi'])) ?><?= $t['cach_dung'] ? ' · ' . e($t['cach_dung']) : '' ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <span class="muted" style="font-size:15px">Không kê đơn cho lần khám này.</span>
        <?php endif; ?>
      </div>
      <div class="form-actions">
        <a class="btn" href="<?= e(url('in-phieu.php', ['id' => $benh_an['id']])) ?>" data-in><?= $thuoc_da_luu ? 'In phiếu kết quả và đơn thuốc' : 'In phiếu kết quả' ?></a>
        <a class="btn pri" href="<?= e(url('quan-ly/lich-hen.php')) ?>">Về danh sách lịch hẹn</a>
      </div>
      <?php if (isset($_GET['in'])): ?>
        <div hidden data-in-ngay="<?= e(url('in-phieu.php', ['id' => $benh_an['id'], 'phan' => $thuoc_da_luu ? 'don-thuoc' : 'ket-qua'])) ?>"></div>
      <?php endif; ?>
    </section>

  <?php else: ?>
    <?php if ($loi): ?>
      <div role="alert" class="alert err">Thiếu kết quả khám hoặc chẩn đoán. Vui lòng nhập đủ trước khi lưu.</div>
    <?php endif; ?>
    <form method="post" class="stack" style="gap:22px" id="form-kham" novalidate>
      <?= o_csrf() ?>
      <input type="hidden" name="id" value="<?= e($lich_hen['id']) ?>">

      <section class="card p26 stack g18">
        <h2>1. Kết quả khám, chẩn đoán</h2>
        <div class="form-grid w180">
          <?php
          o_kham('mach', 'Mạch (lần/phút)', $gia_tri, $loi, 'inputmode="numeric" maxlength="10"');
          o_kham('nhiet_do', 'Nhiệt độ (°C)', $gia_tri, $loi, 'inputmode="decimal" maxlength="10" placeholder="VD: 37,5"');
          o_kham('huyet_ap', 'Huyết áp (mmHg)', $gia_tri, $loi, 'maxlength="15" placeholder="VD: 120/80"');
          o_kham('can_nang', 'Cân nặng (kg)', $gia_tri, $loi, 'inputmode="decimal" maxlength="10"');
          ?>
        </div>
        <div class="field">
          <label for="ket_qua">Kết quả khám <span class="req">*</span></label>
          <textarea class="inp<?= isset($loi['ket_qua']) ? ' bad' : '' ?>" id="ket_qua" name="ket_qua" rows="3"><?= e($gia_tri['ket_qua']) ?></textarea>
          <?php if (isset($loi['ket_qua'])): ?><span class="err"><?= e($loi['ket_qua']) ?></span><?php endif; ?>
        </div>
        <?php o_kham('chan_doan', 'Chẩn đoán <span class="req">*</span>', $gia_tri, $loi, 'maxlength="255" placeholder="VD: Viêm phế quản cấp"'); ?>
        <div class="field">
          <label for="loi_dan">Lời dặn của bác sĩ</label>
          <textarea class="inp" id="loi_dan" name="loi_dan" rows="2"><?= e($gia_tri['loi_dan']) ?></textarea>
        </div>
      </section>

      <section class="card p26 stack g18">
        <div class="row between">
          <h2>2. Kê đơn thuốc</h2>
          <div class="row" role="radiogroup" aria-labelledby="hoi-ke-don">
            <span id="hoi-ke-don" style="font-size:15px;font-weight:500">Bác sĩ có chỉ định thuốc?</span>
            <div class="seg-box">
              <input type="radio" id="ke-co" name="co_ke_don" value="1"<?= $co_ke_don ? ' checked' : '' ?>><label for="ke-co">Có</label>
              <input type="radio" id="ke-khong" name="co_ke_don" value="0"<?= $co_ke_don ? '' : ' checked' ?>><label for="ke-khong">Không</label>
            </div>
          </div>
        </div>

        <div class="stack g18<?= $co_ke_don ? '' : ' hidden' ?>" id="phan-don">
          <div class="add-grid">
            <div class="field">
              <label for="them-thuoc">Thuốc (trong danh mục)</label>
              <select class="inp" id="them-thuoc">
                <option value="">— Chọn thuốc —</option>
                <?php foreach ($danh_muc as $t): ?>
                  <option value="<?= e($t['id']) ?>" data-don-vi="<?= e(mb_strtolower($t['don_vi'])) ?>"><?= e(ten_thuoc($t)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label for="them-sl">Số lượng</label>
              <input class="inp" id="them-sl" type="number" min="1" max="999" inputmode="numeric" placeholder="VD: 10">
            </div>
            <div class="field">
              <label for="them-cd">Cách dùng</label>
              <input class="inp" id="them-cd" maxlength="255" placeholder="VD: Ngày 2 lần, sau ăn">
            </div>
            <button class="btn" type="button" id="nut-them">+ Thêm</button>
          </div>
          <div role="alert" class="alert err hidden" id="loi-them"></div>
          <div class="tbl-box<?= $dong_thuoc ? '' : ' hidden' ?>" id="bang-thuoc">
            <table style="min-width:680px">
              <thead><tr><th>Thuốc</th><th>Số lượng</th><th>Cách dùng</th><th>Kiểm tra</th><th></th></tr></thead>
              <tbody id="ds-thuoc">
                <?php foreach ($dong_thuoc as $dong): $t = $thuoc_theo_id[$dong['thuoc_id']] ?? null; ?>
                  <tr data-id="<?= e($dong['thuoc_id']) ?>"<?= $dong['loi'] ? ' class="row-warn" data-loi="1"' : '' ?>>
                    <td class="strong"><?= e($t ? ten_thuoc($t) : 'Thuốc không còn trong danh mục') ?></td>
                    <td class="num"><?= e($dong['so_luong']) ?><?= $t ? ' ' . e(mb_strtolower($t['don_vi'])) : '' ?></td>
                    <td><?= e($dong['cach_dung']) ?></td>
                    <td><span class="pill <?= $dong['loi'] ? 'warn' : 'ok' ?>"><?= e($dong['loi'] ? 'Cần điều chỉnh: ' . mb_strtolower($dong['loi']) : 'Hợp lệ') ?></span></td>
                    <td class="t-right">
                      <input type="hidden" name="thuoc_id[]" value="<?= e($dong['thuoc_id']) ?>"><input type="hidden" name="so_luong[]" value="<?= e($dong['so_luong']) ?>"><input type="hidden" name="cach_dung[]" value="<?= e($dong['cach_dung']) ?>">
                      <button class="btn sm" type="button" data-xoa aria-label="<?= e('Xóa ' . ($t ? ten_thuoc($t) : 'thuốc')) ?>">Xóa</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p class="empty-box<?= $dong_thuoc ? ' hidden' : '' ?>" id="chua-co-thuoc">Chưa có thuốc nào trong đơn. Chọn thuốc, nhập số lượng rồi bấm “+ Thêm”.</p>
          <div role="alert" class="alert warn<?= $loi_don ? '' : ' hidden' ?>" id="canh-bao-don"><strong>Thuốc cần điều chỉnh:</strong> <span><?= e($loi_don) ?></span></div>
        </div>
        <p class="muted<?= $co_ke_don ? ' hidden' : '' ?>" id="khong-ke" style="font-size:15px">Không kê đơn cho lần khám này. Chỉ lưu bệnh án và gửi phiếu kết quả khám.</p>
      </section>

      <div class="form-actions">
        <button class="btn" type="submit" name="hanh_dong" value="luu_in" id="nut-luu-in"<?= $co_canh_bao ? ' disabled' : '' ?>><?= $co_ke_don ? 'Lưu và in đơn thuốc' : 'Lưu và in phiếu kết quả' ?></button>
        <button class="btn pri" type="submit" name="hanh_dong" value="luu" id="nut-luu"<?= $co_canh_bao ? ' disabled' : '' ?>><?= $co_ke_don ? 'Lưu bệnh án và đơn thuốc' : 'Lưu bệnh án' ?></button>
      </div>
    </form>
  <?php endif; ?>
</main>
<?php if (!$benh_an): ?>
<script>
(() => {
  const chon = (id) => document.getElementById(id);
  const bang = chon('ds-thuoc');

  const baoLoiThem = (noiDung) => { chon('loi-them').textContent = noiDung; chon('loi-them').classList.toggle('hidden', !noiDung); };

  // Còn thuốc bị đánh dấu "Cần điều chỉnh" thì chưa cho lưu (CN 4.2 không hợp lệ thì quay lại lập đơn)
  const capNhat = () => {
    const coKeDon = chon('ke-co').checked;
    const conLoi = coKeDon && bang.querySelector('[data-loi]') !== null;
    chon('phan-don').classList.toggle('hidden', !coKeDon);
    chon('khong-ke').classList.toggle('hidden', coKeDon);
    chon('chua-co-thuoc').classList.toggle('hidden', bang.rows.length > 0);
    chon('bang-thuoc').classList.toggle('hidden', bang.rows.length === 0);
    if (!conLoi) chon('canh-bao-don').classList.add('hidden');
    chon('nut-luu').disabled = conLoi;
    chon('nut-luu-in').disabled = conLoi;
    chon('nut-luu').textContent = coKeDon ? 'Lưu bệnh án và đơn thuốc' : 'Lưu bệnh án';
    chon('nut-luu-in').textContent = coKeDon ? 'Lưu và in đơn thuốc' : 'Lưu và in phiếu kết quả';
  };

  const o = (noiDung, lop) => { const td = document.createElement('td'); td.textContent = noiDung; if (lop) td.className = lop; return td; };
  const an = (ten, giaTri) => Object.assign(document.createElement('input'), { type: 'hidden', name: ten + '[]', value: giaTri });

  chon('nut-them').addEventListener('click', () => {
    const luaChon = chon('them-thuoc').selectedOptions[0];
    const id = chon('them-thuoc').value, soLuong = chon('them-sl').value.trim(), cachDung = chon('them-cd').value.trim();
    if (!id) return baoLoiThem('Vui lòng chọn thuốc trong danh mục.');
    if (!/^\d{1,3}$/.test(soLuong) || +soLuong < 1) return baoLoiThem('Số lượng phải là số nguyên lớn hơn 0.');
    if (bang.querySelector(`tr[data-id="${id}"]`)) return baoLoiThem(`${luaChon.textContent} đã có trong đơn. Xóa dòng cũ nếu muốn đổi số lượng hoặc cách dùng.`);
    baoLoiThem('');

    const tr = document.createElement('tr');
    tr.dataset.id = id;
    const kiemTra = o('');
    kiemTra.append(Object.assign(document.createElement('span'), { className: 'pill ok', textContent: 'Hợp lệ' }));
    const thaoTac = o('', 't-right');
    const nutXoa = Object.assign(document.createElement('button'), { type: 'button', className: 'btn sm', textContent: 'Xóa' });
    nutXoa.dataset.xoa = '';
    nutXoa.setAttribute('aria-label', 'Xóa ' + luaChon.textContent);
    thaoTac.append(an('thuoc_id', id), an('so_luong', soLuong), an('cach_dung', cachDung), nutXoa);
    tr.append(o(luaChon.textContent, 'strong'), o(`${soLuong} ${luaChon.dataset.donVi}`, 'num'), o(cachDung), kiemTra, thaoTac);
    bang.append(tr);

    chon('them-thuoc').value = '';
    chon('them-sl').value = '';
    chon('them-cd').value = '';
    capNhat();
  });

  bang.addEventListener('click', (suKien) => {
    const nut = suKien.target.closest('[data-xoa]');
    if (!nut) return;
    nut.closest('tr').remove();
    capNhat();
  });
  chon('ke-co').addEventListener('change', capNhat);
  chon('ke-khong').addEventListener('change', capNhat);
})();
</script>
<?php endif; ?>
<?php dong_trang(); ?>
