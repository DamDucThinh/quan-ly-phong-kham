/*
 * In phiếu kết quả khám, đơn thuốc ngay trên trang đang xem: nạp bản in vào một khung ẩn rồi mở hộp thoại in.
 * Đóng hộp thoại in thì người dùng vẫn ở nguyên trang, không bị chuyển sang trang phiếu.
 *   <a href="in-phieu.php?..." data-in>      bấm thì in
 *   <div hidden data-in-ngay="in-phieu.php?..."> tự in khi trang vừa mở (sau "Lưu và in đơn thuốc")
 */
(() => {
  const inPhieu = (duongDan) => {
    document.getElementById('khung-in')?.remove();
    const khung = document.createElement('iframe');
    khung.id = 'khung-in';
    khung.title = 'Bản in';
    khung.setAttribute('aria-hidden', 'true');
    khung.tabIndex = -1;
    // Đặt ngoài màn hình nhưng vẫn có kích thước thật: Safari in khung ẩn hoặc kích thước 0 sẽ ra trang trắng
    khung.style.cssText = 'position:fixed;left:-10000px;top:0;width:820px;height:1160px;border:0';
    khung.addEventListener('load', () => {
      const cuaSo = khung.contentWindow;
      const choFont = cuaSo.document.fonts ? cuaSo.document.fonts.ready : Promise.resolve();
      choFont.then(() => {
        cuaSo.focus();
        cuaSo.print();
      });
    });
    khung.src = duongDan;
    document.body.append(khung);
  };

  document.addEventListener('click', (suKien) => {
    const lienKet = suKien.target.closest('a[data-in]');
    // Giữ Cmd/Ctrl khi bấm thì vẫn mở trang phiếu ở tab mới như liên kết thường
    if (!lienKet || suKien.metaKey || suKien.ctrlKey) {
      return;
    }
    suKien.preventDefault();
    inPhieu(lienKet.href);
  });

  const tuDong = document.querySelector('[data-in-ngay]');
  if (tuDong) {
    inPhieu(tuDong.dataset.inNgay);
    // Bỏ ?in=1 khỏi địa chỉ trang để bấm tải lại không in thêm lần nữa
    const diaChi = new URL(window.location.href);
    diaChi.searchParams.delete('in');
    window.history.replaceState(null, '', diaChi);
  }
})();
