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

/*
 * Phân trang bảng vừa khít màn hình: <tbody data-phan-trang="id-thanh-chuyen-trang">.
 * Mỗi trang chứa vừa đủ số dòng để cả trang không phải cuộn; số dòng tính lại khi đổi cỡ cửa sổ.
 * Dòng .nhom-ngay là tiêu đề nhóm; trang bắt đầu giữa một nhóm thì lặp lại tiêu đề nhóm đó kèm "(tiếp)".
 * Trang hiện tại ghi vào tham số ?trang= để tải lại trang vẫn ở đúng chỗ.
 */
(() => {
  const bang = document.querySelector('tbody[data-phan-trang]');
  if (!bang) {
    return;
  }
  const thanh = document.getElementById(bang.dataset.phanTrang);
  const tatCa = [...bang.rows];
  const dongDuLieu = tatCa.filter((tr) => !tr.classList.contains('nhom-ngay'));
  if (!thanh || dongDuLieu.length === 0) {
    return;
  }

  // Mỗi dòng dữ liệu thuộc tiêu đề nhóm đứng gần nhất phía trên nó
  const nhomCua = new Map();
  let nhomHienTai = null;
  tatCa.forEach((tr) => {
    if (tr.classList.contains('nhom-ngay')) {
      nhomHienTai = tr;
    } else {
      nhomCua.set(tr, nhomHienTai);
    }
  });

  const dongTiep = document.createElement('tr');
  dongTiep.className = 'nhom-ngay';
  dongTiep.hidden = true;
  const oTiep = document.createElement('th');
  oTiep.colSpan = bang.closest('table').tHead.rows[0].cells.length;
  dongTiep.append(oTiep);
  bang.prepend(dongTiep);

  const anHet = () => {
    tatCa.forEach((tr) => { tr.hidden = true; });
    dongTiep.hidden = true;
  };

  // Hiện dòng dữ liệu thứ k cùng tiêu đề nhóm cần thiết; trả về các dòng vừa hiện
  const hienDong = (k, dauTrang) => {
    const tr = dongDuLieu[k];
    const nhom = nhomCua.get(tr);
    const daHien = [];
    if (nhom) {
      if (k === 0 || nhomCua.get(dongDuLieu[k - 1]) !== nhom) {
        nhom.hidden = false;
        daHien.push(nhom);
      } else if (dauTrang) {
        oTiep.innerHTML = nhom.cells[0].innerHTML;
        oTiep.append(' (tiếp)');
        dongTiep.hidden = false;
        daHien.push(dongTiep);
      }
    }
    tr.hidden = false;
    daHien.push(tr);
    return daHien;
  };

  const vuaManHinh = () => document.documentElement.scrollHeight <= window.innerHeight;

  const nut = (noiDung, nhan, trang, dangChon, tat) => {
    const b = document.createElement('button');
    b.type = 'button';
    b.innerHTML = noiDung;
    b.setAttribute('aria-label', nhan);
    b.disabled = tat;
    if (dangChon) {
      b.setAttribute('aria-current', 'page');
    }
    b.addEventListener('click', () => hienTrang(trang));
    return b;
  };
  const MUI_TEN_TRAI = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"></path></svg>';
  const MUI_TEN_PHAI = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"></path></svg>';

  // Các số trang cần hiện: trang đầu, trang cuối, 2 trang quanh trang hiện tại; chỗ bị lược thay bằng "…"
  const cacSo = (hienTai, tong) => {
    const giu = new Set([0, tong - 1, hienTai - 1, hienTai, hienTai + 1]);
    const kq = [];
    for (let i = 0; i < tong; i++) {
      if (giu.has(i)) {
        kq.push(i);
      } else if (kq[kq.length - 1] !== '…') {
        kq.push('…');
      }
    }
    return kq;
  };

  const veThanh = (hienTai, tong) => {
    thanh.replaceChildren();
    const tomTat = document.createElement('span');
    tomTat.className = 'tom-tat';
    tomTat.textContent = `Trang ${hienTai + 1}/${tong} · ${dongDuLieu.length} lịch hẹn`;
    const so = document.createElement('div');
    so.className = 'so-trang';
    so.append(nut(MUI_TEN_TRAI, 'Trang trước', hienTai - 1, false, hienTai === 0));
    cacSo(hienTai, tong).forEach((i) => {
      if (i === '…') {
        const chamLung = document.createElement('span');
        chamLung.className = 'ba-cham';
        chamLung.textContent = '…';
        so.append(chamLung);
      } else {
        so.append(nut(String(i + 1), `Trang ${i + 1}`, i, i === hienTai, false));
      }
    });
    so.append(nut(MUI_TEN_PHAI, 'Trang sau', hienTai + 1, false, hienTai === tong - 1));
    thanh.append(tomTat, so);
  };

  let dauMoiTrang = [0];
  let trangDangXem = 0;

  // Chia trang: thêm từng dòng cho tới khi trang vượt chiều cao cửa sổ
  const chiaTrang = () => {
    thanh.hidden = false;
    veThanh(0, 9); // vẽ tạm thanh chuyển trang để tính cả chiều cao của nó
    dauMoiTrang = [];
    let k = 0;
    while (k < dongDuLieu.length) {
      anHet();
      dauMoiTrang.push(k);
      let dauTrang = true;
      while (k < dongDuLieu.length) {
        const daHien = hienDong(k, dauTrang);
        if (!dauTrang && !vuaManHinh()) {
          daHien.forEach((tr) => { tr.hidden = true; });
          break;
        }
        dauTrang = false;
        k++;
      }
    }
  };

  const hienTrang = (trang) => {
    const tong = dauMoiTrang.length;
    trangDangXem = Math.max(0, Math.min(trang, tong - 1));
    anHet();
    const cuoi = trangDangXem + 1 < tong ? dauMoiTrang[trangDangXem + 1] : dongDuLieu.length;
    for (let k = dauMoiTrang[trangDangXem]; k < cuoi; k++) {
      hienDong(k, k === dauMoiTrang[trangDangXem]);
    }
    thanh.hidden = tong < 2;
    if (tong > 1) {
      veThanh(trangDangXem, tong);
    }
    const diaChi = new URL(window.location.href);
    if (trangDangXem > 0) {
      diaChi.searchParams.set('trang', trangDangXem + 1);
    } else {
      diaChi.searchParams.delete('trang');
    }
    window.history.replaceState(null, '', diaChi);
    document.querySelectorAll('input[name="trang"]').forEach((o) => { o.value = trangDangXem + 1; });
  };

  chiaTrang();
  hienTrang((parseInt(new URLSearchParams(window.location.search).get('trang'), 10) || 1) - 1);

  // Đổi cỡ cửa sổ: chia lại trang, giữ dòng đầu của trang đang xem
  let hen;
  window.addEventListener('resize', () => {
    clearTimeout(hen);
    hen = setTimeout(() => {
      const dongDau = dauMoiTrang[trangDangXem];
      chiaTrang();
      let trangMoi = 0;
      dauMoiTrang.forEach((dau, i) => { if (dau <= dongDau) { trangMoi = i; } });
      hienTrang(trangMoi);
    }, 150);
  });
})();
