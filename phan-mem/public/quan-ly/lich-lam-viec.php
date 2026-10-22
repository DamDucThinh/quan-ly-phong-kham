<?php
/*
 * [6] Lịch làm việc bác sĩ — CN 1.2: thêm / sửa ca, kiểm tra ca hợp lệ rồi lưu lịch làm việc (ghi D1).
 * Lịch này được dùng khi kiểm tra lịch trống lúc bệnh nhân đặt lịch (CN 2.2).
 * Ca được thêm / xóa ngay trên trang, bấm "Lưu lịch làm việc" mới ghi vào CSDL.
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
$ds_bac_si = nhieu_dong(
    'SELECT bs.*, (SELECT COUNT(*) FROM ca_lam_viec c WHERE c.bac_si_id = bs.id) AS so_ca FROM bac_si bs ORDER BY bs.id'
);
if (!$ds_bac_si) {
    dat_thong_diep('warn', 'Chưa có bác sĩ nào. Hãy thêm bác sĩ trước khi nhập lịch làm việc.');
    chuyen_huong('quan-ly/bac-si.php');
}
$bac_si_id = (int) ($_POST['bs'] ?? $_GET['bs'] ?? $ds_bac_si[0]['id']);
$bac_si = null;
foreach ($ds_bac_si as $bs) {
    if ((int) $bs['id'] === $bac_si_id) {
        $bac_si = $bs;
    }
}
$bac_si ??= $ds_bac_si[0];

$moc_gio = cac_moc_gio();
$loi = '';
$ds_ca = array_map(
    fn($c) => ['thu' => (int) $c['thu'], 'tu' => gio_ngan($c['gio_bat_dau']), 'den' => gio_ngan($c['gio_ket_thuc'])],
    nhieu_dong('SELECT * FROM ca_lam_viec WHERE bac_si_id = ? ORDER BY thu, gio_bat_dau', [$bac_si['id']])
);

if (la_post()) {
    kiem_tra_csrf();
    $ds_ca = [];
    foreach ((array) ($_POST['thu'] ?? []) as $i => $thu) {
        $ds_ca[] = ['thu' => (int) $thu, 'tu' => (string) ($_POST['tu'][$i] ?? ''), 'den' => (string) ($_POST['den'][$i] ?? '')];
    }
    usort($ds_ca, fn($a, $b) => [$a['thu'], $a['tu']] <=> [$b['thu'], $b['tu']]);

    // Kiểm tra lại ở máy chủ: đủ thông tin, giờ kết thúc sau giờ bắt đầu, không trùng giờ trong cùng một thứ
    foreach ($ds_ca as $i => $ca) {
        if (!isset(THU_TRONG_TUAN[$ca['thu']]) || !in_array($ca['tu'], $moc_gio, true) || !in_array($ca['den'], $moc_gio, true)) {
            $loi = 'Thiếu thông tin: vui lòng chọn đủ thứ, giờ bắt đầu và giờ kết thúc.';
        } elseif ($ca['den'] <= $ca['tu']) {
            $loi = 'Giờ kết thúc phải sau giờ bắt đầu (' . THU_TRONG_TUAN[$ca['thu']] . ' ' . $ca['tu'] . ' – ' . $ca['den'] . ').';
        } else {
            foreach (array_slice($ds_ca, 0, $i) as $truoc) {
                if ($truoc['thu'] === $ca['thu'] && $ca['tu'] < $truoc['den'] && $truoc['tu'] < $ca['den']) {
                    $loi = 'Ca ' . THU_TRONG_TUAN[$ca['thu']] . ' ' . $ca['tu'] . ' – ' . $ca['den'] . ' bị trùng giờ với ca '
                        . $truoc['tu'] . ' – ' . $truoc['den'] . '. Vui lòng chọn giờ khác.';
                }
            }
        }
        if ($loi) {
            break;
        }
    }

    if (!$loi) {
        $pdo = csdl();
        $pdo->beginTransaction();
        truy_van('DELETE FROM ca_lam_viec WHERE bac_si_id = ?', [$bac_si['id']]);
        foreach ($ds_ca as $ca) {
            truy_van('INSERT INTO ca_lam_viec (bac_si_id, thu, gio_bat_dau, gio_ket_thuc) VALUES (?, ?, ?, ?)', [$bac_si['id'], $ca['thu'], $ca['tu'], $ca['den']]);
        }
        $pdo->commit();

        // Lịch hẹn đã đặt nhưng không còn nằm trong ca nào: báo để phòng khám liên hệ bệnh nhân đổi lịch
        $ngoai_ca = 0;
        $lich_sap_toi = nhieu_dong(
            "SELECT ngay, gio FROM lich_hen WHERE bac_si_id = ? AND trang_thai = 'da_dat' AND loai = 'hen_truoc' AND TIMESTAMP(ngay, gio) > ?",
            [$bac_si['id'], bay_gio()]
        );
        foreach ($lich_sap_toi as $lh) {
            if (!in_array(gio_ngan($lh['gio']), khung_gio_trong_ngay((int) $bac_si['id'], $lh['ngay']), true)) {
                $ngoai_ca++;
            }
        }
        $html = '<strong>Đã lưu lịch làm việc.</strong> Hệ thống dùng lịch này khi kiểm tra lịch trống (CN 2.2).';
        if ($ngoai_ca) {
            $html .= ' Lưu ý: còn ' . $ngoai_ca . ' lịch hẹn đã đặt nằm ngoài ca mới, cần liên hệ bệnh nhân để đổi lịch.';
        }
        dat_thong_diep($ngoai_ca ? 'warn' : 'ok', $html);
        chuyen_huong('quan-ly/lich-lam-viec.php', ['bs' => $bac_si['id']]);
    }
}

mo_trang('Lịch làm việc bác sĩ', 'ql', 'doctors');
?>
<main class="pg">
  <div class="pg-head">
    <a class="back-link" href="<?= e(url('quan-ly/bac-si.php')) ?>">‹ Danh sách bác sĩ</a>
    <h1>Lịch làm việc bác sĩ</h1>
  </div>

  <div class="split">
    <aside class="card p16 stack g6 side" style="flex:1 1 280px">
      <h2 class="side-title xs" style="font-size:17px">Chọn bác sĩ</h2>
      <?php foreach ($ds_bac_si as $bs): $chon = $bs['id'] === $bac_si['id']; ?>
        <a class="pick thin<?= $chon ? ' on' : '' ?>" href="<?= e(url('quan-ly/lich-lam-viec.php', ['bs' => $bs['id']])) ?>"<?= $chon ? ' aria-current="true"' : '' ?>>
          <span class="pick-col" style="gap:2px">
            <span class="t"><?= e(ten_bac_si($bs['ho_ten'])) ?></span>
            <span class="s"><?= e($bs['chuyen_khoa']) ?> · <?= (int) $bs['so_ca'] ?> ca</span>
          </span>
        </a>
      <?php endforeach; ?>
    </aside>

    <div class="main stack g24" style="flex:3 1 640px">
      <section class="card p22 stack g14">
        <h2 class="sm">Thêm ca làm việc cho <?= e(ten_bac_si($bac_si['ho_ten'])) ?></h2>
        <div class="add-grid ca">
          <div class="field">
            <label for="them-thu">Thứ</label>
            <select class="inp" id="them-thu"><option value="">— Chọn —</option><?= cac_lua_chon(THU_TRONG_TUAN, null, true) ?></select>
          </div>
          <div class="field">
            <label for="them-tu">Từ giờ</label>
            <select class="inp" id="them-tu"><option value="">— Chọn —</option><?= cac_lua_chon($moc_gio, null) ?></select>
          </div>
          <div class="field">
            <label for="them-den">Đến giờ</label>
            <select class="inp" id="them-den"><option value="">— Chọn —</option><?= cac_lua_chon($moc_gio, null) ?></select>
          </div>
          <button class="btn" type="button" id="them-ca">+ Thêm ca</button>
        </div>
        <div role="alert" class="alert err<?= $loi ? '' : ' hidden' ?>" id="loi-ca"><?= e($loi) ?></div>
      </section>

      <form class="card p22 stack g14" method="post" id="form-ca">
        <?= o_csrf() ?>
        <input type="hidden" name="bs" value="<?= e($bac_si['id']) ?>">
        <div class="row between" style="gap:10px">
          <h2 class="sm">Các ca trong tuần</h2>
          <span class="muted" style="font-size:14px"><span id="so-ca"><?= count($ds_ca) ?></span> ca</span>
        </div>
        <div class="tbl-box<?= $ds_ca ? '' : ' hidden' ?>" id="bang-ca">
          <table style="min-width:520px">
            <thead><tr><th>Thứ</th><th>Thời gian</th><th>Buổi</th><th class="t-right">Thao tác</th></tr></thead>
            <tbody id="ds-ca">
              <?php foreach ($ds_ca as $ca): ?>
                <tr data-thu="<?= $ca['thu'] ?>" data-tu="<?= e($ca['tu']) ?>" data-den="<?= e($ca['den']) ?>">
                  <td class="strong"><?= e(THU_TRONG_TUAN[$ca['thu']] ?? '?') ?></td>
                  <td class="num"><?= e($ca['tu']) ?> – <?= e($ca['den']) ?></td>
                  <td class="muted"><?= $ca['tu'] < '12:00' ? 'Sáng' : 'Chiều' ?></td>
                  <td class="t-right">
                    <input type="hidden" name="thu[]" value="<?= $ca['thu'] ?>"><input type="hidden" name="tu[]" value="<?= e($ca['tu']) ?>"><input type="hidden" name="den[]" value="<?= e($ca['den']) ?>">
                    <button class="btn sm" type="button" data-xoa aria-label="<?= e('Xóa ca ' . (THU_TRONG_TUAN[$ca['thu']] ?? '') . ' ' . $ca['tu']) ?>">Xóa</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p class="empty-box<?= $ds_ca ? ' hidden' : '' ?>" id="chua-co-ca">Bác sĩ chưa có ca làm việc nào.</p>
        <?= hien_thong_diep() ?>
        <div class="row end">
          <span class="muted hidden" id="chua-luu" style="font-size:14px">Có thay đổi chưa lưu</span>
          <button class="btn pri" type="submit">Lưu lịch làm việc</button>
        </div>
      </form>
    </div>
  </div>
</main>
<script>
(() => {
  const THU = <?= json_encode(THU_TRONG_TUAN, JSON_UNESCAPED_UNICODE) ?>;
  const bang = document.getElementById('ds-ca');
  const hopLoi = document.getElementById('loi-ca');
  const chon = (id) => document.getElementById(id);

  const baoLoi = (noiDung) => { hopLoi.textContent = noiDung; hopLoi.classList.toggle('hidden', !noiDung); };
  const cacCa = () => [...bang.querySelectorAll('tr')].map((tr) => ({ tr, thu: +tr.dataset.thu, tu: tr.dataset.tu, den: tr.dataset.den }));
  const daThayDoi = () => {
    chon('so-ca').textContent = bang.rows.length;
    chon('chua-co-ca').classList.toggle('hidden', bang.rows.length > 0);
    chon('bang-ca').classList.toggle('hidden', bang.rows.length === 0);
    chon('chua-luu').classList.remove('hidden');
  };
  const o = (noiDung, lop) => { const td = document.createElement('td'); td.textContent = noiDung; if (lop) td.className = lop; return td; };
  const an = (ten, giaTri) => Object.assign(document.createElement('input'), { type: 'hidden', name: ten + '[]', value: giaTri });

  chon('them-ca').addEventListener('click', () => {
    const thu = +chon('them-thu').value, tu = chon('them-tu').value, den = chon('them-den').value;
    if (!thu || !tu || !den) return baoLoi('Thiếu thông tin: vui lòng chọn đủ thứ, giờ bắt đầu và giờ kết thúc.');
    if (den <= tu) return baoLoi('Giờ kết thúc phải sau giờ bắt đầu.');
    const trung = cacCa().find((c) => c.thu === thu && tu < c.den && c.tu < den);
    if (trung) return baoLoi(`Ca bị trùng giờ với ca ${THU[trung.thu]} ${trung.tu} – ${trung.den}. Vui lòng chọn giờ khác.`);
    baoLoi('');

    const tr = document.createElement('tr');
    Object.assign(tr.dataset, { thu, tu, den });
    const thaoTac = o('', 't-right');
    const nutXoa = Object.assign(document.createElement('button'), { type: 'button', className: 'btn sm', textContent: 'Xóa' });
    nutXoa.dataset.xoa = '';
    nutXoa.setAttribute('aria-label', `Xóa ca ${THU[thu]} ${tu}`);
    thaoTac.append(an('thu', thu), an('tu', tu), an('den', den), nutXoa);
    tr.append(o(THU[thu], 'strong'), o(`${tu} – ${den}`, 'num'), o(tu < '12:00' ? 'Sáng' : 'Chiều', 'muted'), thaoTac);

    const sau = cacCa().find((c) => c.thu > thu || (c.thu === thu && c.tu > tu));
    bang.insertBefore(tr, sau ? sau.tr : null);
    ['them-thu', 'them-tu', 'them-den'].forEach((id) => { chon(id).value = ''; });
    daThayDoi();
  });

  bang.addEventListener('click', (suKien) => {
    const nut = suKien.target.closest('[data-xoa]');
    if (!nut) return;
    nut.closest('tr').remove();
    daThayDoi();
  });
})();
</script>
<?php dong_trang(); ?>
