<?php
/*
 * [19] Chọn loại báo cáo, khoảng thời gian — gửi yêu cầu báo cáo (chức năng 5).
 * Khoảng thời gian sai hoặc không có dữ liệu thì màn [20] trả về đây kèm thông báo (tham số loi).
 */
require __DIR__ . '/../../includes/khoi-dong.php';

yeu_cau_vai_tro('QL');
const SO_LA_MA = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];

$loai = ($_GET['loai'] ?? '') === 'thuoc' ? 'thuoc' : 'luot-kham';
$tu = ngay_hop_le($_GET['tu'] ?? '') ? $_GET['tu'] : date('Y-m-01');
$den = ngay_hop_le($_GET['den'] ?? '') ? $_GET['den'] : hom_nay();
$loi = $_GET['loi'] ?? '';

$quy = intdiv((int) date('n') - 1, 3);           // quý hiện tại, đếm từ 0
$nam_quy_truoc = (int) date('Y') - ($quy === 0 ? 1 : 0);
$quy_truoc = $quy === 0 ? 4 : $quy;
$dau_quy_truoc = sprintf('%d-%02d-01', $nam_quy_truoc, ($quy_truoc - 1) * 3 + 1);
$khoang_nhanh = [
    ['Tháng này', date('Y-m-01'), hom_nay()],
    ['Tháng trước', date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
    ['Quý ' . SO_LA_MA[$quy_truoc] . '/' . $nam_quy_truoc, $dau_quy_truoc, date('Y-m-t', strtotime($dau_quy_truoc . ' +2 month'))],
];
$cac_loai = [
    'luot-kham' => ['Thống kê lượt khám', 'Số lượt khám theo ngày, theo bác sĩ (CN 5.1)'],
    'thuoc' => ['Thống kê sử dụng thuốc', 'Số lượng từng loại thuốc đã kê đơn (CN 5.2)'],
];

mo_trang('Báo cáo thống kê', 'ql', 'report');
?>
<main class="pg w960">
  <div class="pg-head">
    <h1>Báo cáo thống kê</h1>
  </div>

  <form class="card p28 stack" style="gap:26px" method="get" action="<?= e(url('quan-ly/ket-qua-bao-cao.php')) ?>" id="form-bao-cao">
    <input type="hidden" name="tu_form" value="1">
    <fieldset class="stack g12">
      <legend>1. Loại báo cáo</legend>
      <div class="pick-grid" style="grid-template-columns:repeat(auto-fit,minmax(min(280px,100%),1fr))">
        <?php foreach ($cac_loai as $ma => [$tieu_de, $mo_ta]): ?>
          <label class="type-pick">
            <input type="radio" name="loai" value="<?= e($ma) ?>"<?= $ma === $loai ? ' checked' : '' ?>>
            <span class="type-card"><span class="t"><?= e($tieu_de) ?></span><span class="s"><?= e($mo_ta) ?></span></span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset class="stack g14">
      <legend>2. Khoảng thời gian</legend>
      <div class="chips">
        <?php foreach ($khoang_nhanh as [$nhan, $tu_nhanh, $den_nhanh]): $dang_chon = $tu === $tu_nhanh && $den === $den_nhanh; ?>
          <a class="chip<?= $dang_chon ? ' on' : '' ?>" href="<?= e(url('quan-ly/bao-cao.php', ['loai' => $loai, 'tu' => $tu_nhanh, 'den' => $den_nhanh])) ?>"
             data-tu="<?= e($tu_nhanh) ?>" data-den="<?= e($den_nhanh) ?>"><?= e($nhan) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="form-grid">
        <div class="field"><label for="tu">Từ ngày</label><input class="inp<?= $loi === 'nguoc' ? ' bad' : '' ?>" id="tu" name="tu" type="date" value="<?= e($tu) ?>" max="<?= e(hom_nay()) ?>" required></div>
        <div class="field"><label for="den">Đến ngày</label><input class="inp<?= $loi === 'nguoc' ? ' bad' : '' ?>" id="den" name="den" type="date" value="<?= e($den) ?>" required></div>
      </div>
      <div role="alert" class="alert err<?= $loi === 'nguoc' ? '' : ' hidden' ?>" id="loi-nguoc">Ngày bắt đầu đang sau ngày kết thúc. Vui lòng chọn lại khoảng thời gian.</div>
      <?php if ($loi === 'dai'): ?>
        <div role="alert" class="alert err">Khoảng thời gian dài nhất là 366 ngày. Vui lòng chọn khoảng ngắn hơn.</div>
      <?php elseif ($loi === 'trong'): ?>
        <div role="status" class="alert warn">Không có dữ liệu trong khoảng đã chọn. Vui lòng chọn khoảng thời gian khác.</div>
      <?php endif; ?>
    </fieldset>

    <div class="row end">
      <button class="go" type="submit" id="nut-xem"<?= $loi === 'nguoc' ? ' disabled' : '' ?>>Xem báo cáo</button>
    </div>
  </form>
</main>
<script>
(() => {
  const tu = document.getElementById('tu'), den = document.getElementById('den');
  const kiemTra = () => {
    const nguoc = tu.value && den.value && tu.value > den.value;
    document.getElementById('loi-nguoc').classList.toggle('hidden', !nguoc);
    tu.classList.toggle('bad', nguoc);
    den.classList.toggle('bad', nguoc);
    document.getElementById('nut-xem').disabled = nguoc || !tu.value || !den.value;
  };
  tu.addEventListener('input', kiemTra);
  den.addEventListener('input', kiemTra);
  // Bấm khoảng nhanh thì điền ngày ngay, giữ loại báo cáo đang chọn
  document.querySelectorAll('.chip[data-tu]').forEach((chip) => chip.addEventListener('click', (suKien) => {
    suKien.preventDefault();
    tu.value = chip.dataset.tu;
    den.value = chip.dataset.den;
    document.querySelectorAll('.chip[data-tu]').forEach((c) => c.classList.toggle('on', c === chip));
    kiemTra();
  }));
})();
</script>
<?php dong_trang(); ?>
