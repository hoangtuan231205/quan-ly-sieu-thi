// =====================================================
// STATE
// =====================================================
let addSelected = null;
let addLines = [];

let editSelected = null;
let editLines = [];
let editLineIndex = null; // index dòng đang sửa (null = add mới)


// =====================================================
// UTIL
// =====================================================
function money(n) {
  return (Number(n) || 0).toLocaleString('vi-VN') + ' đ';
}
function show(el) { el.style.display = 'flex'; }
function hide(el) { el.style.display = 'none'; }

/**
 * Toast notification - hiện thông báo góc phải màn hình
 * @param {string} message - Nội dung thông báo
 * @param {'success'|'error'|'warning'} type - Loại thông báo
 * @param {number} duration - Thời gian hiển thị (ms)
 */
function showWhToast(message, type = 'success', duration = 3000) {
  // Tạo container nếu chưa có
  let container = document.getElementById('wh-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'wh-toast-container';
    Object.assign(container.style, {
      position: 'fixed', top: '20px', right: '20px',
      zIndex: '99999', display: 'flex', flexDirection: 'column', gap: '10px'
    });
    document.body.appendChild(container);
  }

  const colors = {
    success: { bg: '#d1fae5', border: '#6ee7b7', text: '#065f46', icon: '✅' },
    error:   { bg: '#fee2e2', border: '#fca5a5', text: '#7f1d1d', icon: '❌' },
    warning: { bg: '#fef3c7', border: '#fcd34d', text: '#78350f', icon: '⚠️' }
  };
  const c = colors[type] || colors.success;

  const toast = document.createElement('div');
  Object.assign(toast.style, {
    background: c.bg, border: `1px solid ${c.border}`, color: c.text,
    padding: '12px 18px', borderRadius: '10px', fontSize: '14px',
    fontWeight: '500', boxShadow: '0 4px 16px rgba(0,0,0,0.12)',
    display: 'flex', alignItems: 'center', gap: '10px',
    minWidth: '260px', maxWidth: '360px',
    animation: 'whToastIn 0.3s ease', transition: 'opacity 0.4s ease'
  });
  toast.innerHTML = `<span>${c.icon}</span><span>${message}</span>`;
  container.appendChild(toast);

  // Inject keyframes nếu chưa có
  if (!document.getElementById('wh-toast-style')) {
    const style = document.createElement('style');
    style.id = 'wh-toast-style';
    style.textContent = `@keyframes whToastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
    document.head.appendChild(style);
  }

  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 400);
  }, duration);
}

function debounce(fn, delay = 250) {
  let t = null;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), delay);
  };
}

async function getJSON(url) {
  const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  return r.json();
}

async function postJSON(url, data) {
  const fd = new FormData();
  Object.keys(data).forEach(k => fd.append(k, data[k]));
  const r = await fetch(url, {
    method: 'POST',
    body: fd,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  });
  return r.json();
}


// =====================================================
// MODAL OPEN/CLOSE
// =====================================================
function openAdd() {
  addLines = [];
  addSelected = null;

  document.getElementById('wh-add-date').value = '';
  document.getElementById('wh-add-note').value = '';
  document.getElementById('wh-add-q').value = '';

  clearAddPick();
  renderAddLines();
  show(document.getElementById('wh-add-modal'));
}

function closeAdd() {
  hide(document.getElementById('wh-add-modal'));
}

async function openEdit(id) {
  show(document.getElementById('wh-edit-modal'));
  await loadEdit(id);
}

function closeEdit() {
  hide(document.getElementById('wh-edit-modal'));
}

// click nền modal để đóng
document.addEventListener('click', (e) => {
  const add = document.getElementById('wh-add-modal');
  const edit = document.getElementById('wh-edit-modal');
  if (e.target === add) closeAdd();
  if (e.target === edit) closeEdit();
});


// =====================================================
// SEARCH PRODUCT (CHUNG)
// =====================================================
function bindSearch(inputId, boxId, callback) {
  const search = debounce(async () => {
    const q = document.getElementById(inputId).value.trim();
    const box = document.getElementById(boxId);

    // Cho phép query từ 1 ký tự (để hỗ trợ tiếng Việt như "cá", "sữa")
    if (q.length < 1) { box.style.display = 'none'; box.innerHTML = ''; return; }

    const json = await getJSON(`${WH_BASE}/warehouse/search-product?q=${encodeURIComponent(q)}`);
    if (!json.success || !json.products || !json.products.length) {
      box.style.display = 'none';
      box.innerHTML = '';
      return;
    }

    box.innerHTML = json.products.map(p => `
      <div class="wh-suggest-item"
        data-id="${p.id}"
        data-ma="${p.ma}"
        data-ten="${p.ten}"
        data-dvt="${p.dvt}"
        data-gia="${p.gia}">
        <div class="wh-s1">${p.ten}</div>
        <div class="wh-s2">${p.ma} • ${p.dvt} • ${money(p.gia)}</div>
      </div>
    `).join('');
    box.style.display = 'block';
  });

  document.addEventListener('input', e => {
    if (e.target && e.target.id === inputId) search();
  });

  document.addEventListener('click', e => {
    const it = e.target.closest && e.target.closest(`#${boxId} .wh-suggest-item`);
    if (!it) return;

    callback({
      ID_sp: Number(it.dataset.id),
      Ma: it.dataset.ma,
      Ten_sp: it.dataset.ten,
      Don_vi_tinh: it.dataset.dvt,
      Gia_hien_tai: Number(it.dataset.gia || 0)
    });

    const box = document.getElementById(boxId);
    box.style.display = 'none';
  });
}


// =====================================================
// ADD PHIẾU
// =====================================================
function clearAddPick() {
  document.getElementById('wh-add-ma').value = '';
  document.getElementById('wh-add-dvt').value = '';
  document.getElementById('wh-add-qty').value = 1;
  document.getElementById('wh-add-price').value = 0;
  document.getElementById('wh-add-expiry').value = '';
  addSelected = null;
}

bindSearch('wh-add-q', 'wh-add-suggest', p => {
  addSelected = p;
  document.getElementById('wh-add-q').value = p.Ten_sp; // Điền tên sản phẩm vào ô tìm kiếm
  document.getElementById('wh-add-ma').value = p.Ma;
  document.getElementById('wh-add-dvt').value = p.Don_vi_tinh;
  document.getElementById('wh-add-price').value = p.Gia_hien_tai;
});

async function addLine() {
  const qty = Math.max(1, parseInt(document.getElementById('wh-add-qty').value || '1', 10));
  const price = Math.max(0, parseFloat(document.getElementById('wh-add-price').value || '0'));
  const supplierId = document.getElementById('wh-add-supplier').value;
  const supplierText = document.getElementById('wh-add-supplier').selectedOptions[0]?.text || '';
  const categoryId = document.getElementById('wh-add-category').value;
  const categoryText = document.getElementById('wh-add-category').selectedOptions[0]?.text || '';
  const expiryDate = document.getElementById('wh-add-expiry').value;

  // Validation: Kiểm tra nhà cung cấp và danh mục
  if (!supplierId) return alert('Vui lòng chọn nhà cung cấp');
  if (!categoryId) return alert('Vui lòng chọn danh mục');
  if (!expiryDate) return alert('Vui lòng chọn hạn sử dụng');

  // =====================================================================
  // SENIOR UPGRADE: Nếu chưa chọn sản phẩm từ gợi ý → tự động tạo mới
  // =====================================================================
  if (!addSelected) {
    const tenSpNhap = document.getElementById('wh-add-q').value.trim();

    if (!tenSpNhap) {
      return alert('Vui lòng nhập tên sản phẩm');
    }

    // Gọi API tạo sản phẩm mới
    try {
      const dvt = document.getElementById('wh-add-dvt').value || 'Cái';

      const result = await postJSON(`${WH_BASE}/warehouse/create-product-quick`, {
        csrf_token: WH_CSRF || '',
        ten_sp: tenSpNhap,
        id_danh_muc: categoryId,
        don_vi_tinh: dvt,
        gia_ban: price
      });

      if (!result.success) {
        return alert(result.message || 'Không thể tạo sản phẩm mới');
      }

      // Tạo thành công → Set addSelected với SP vừa tạo
      addSelected = {
        ID_sp: result.product.id,
        Ma: result.product.ma,
        Ten_sp: result.product.ten,
        Don_vi_tinh: result.product.dvt,
        Gia_hien_tai: result.product.gia
      };

      // Cập nhật UI
      document.getElementById('wh-add-ma').value = addSelected.Ma;
      document.getElementById('wh-add-dvt').value = addSelected.Don_vi_tinh;

    } catch (err) {
      console.error('Error creating product:', err);
      return alert('Lỗi khi tạo sản phẩm mới');
    }
  }

  // Thêm vào danh sách
  addLines.push({
    ID_sp: addSelected.ID_sp,
    Ten_sp: addSelected.Ten_sp,
    Don_vi_tinh: addSelected.Don_vi_tinh,
    Gia_hien_tai: addSelected.Gia_hien_tai,
    So_luong: qty,
    Don_gia_nhap: price,
    ID_ncc: supplierId,
    Ten_ncc: supplierText,
    Danh_muc: categoryId,
    Ten_danh_muc: categoryText,
    Han_su_dung: expiryDate
  });

  clearAddPick();
  document.getElementById('wh-add-q').value = '';
  document.getElementById('wh-add-supplier').value = '';
  document.getElementById('wh-add-category').value = '';
  renderAddLines();
}

function renderAddLines() {
  const tb = document.getElementById('wh-add-lines');
  const total = document.getElementById('wh-add-total');

  if (!addLines.length) {
    tb.innerHTML = `<tr><td colspan="9" class="wh-empty">Chưa có sản phẩm</td></tr>`;
    total.textContent = money(0);
    return;
  }

  let sum = 0;
  tb.innerHTML = addLines.map((x, i) => {
    const t = (Number(x.So_luong) || 0) * (Number(x.Don_gia_nhap) || 0);
    sum += t;

    return `
      <tr>
        <td>${x.Ten_sp}</td>
        <td>${x.Don_vi_tinh}</td>
        <td>${x.Ten_ncc || '-'}</td>
        <td>${x.Ten_danh_muc || '-'}</td>
        <td>${x.So_luong}</td>
        <td>${money(x.Don_gia_nhap)}</td>
        <td>${x.Han_su_dung || '-'}</td>
        <td class="wh-bold">${money(t)}</td>
        <td><button type="button" class="wh-icon wh-danger"
            onclick="addLines.splice(${i},1);renderAddLines()">🗑</button></td>
      </tr>`;
  }).join('');

  total.textContent = money(sum);
}


async function submitAdd() {
  const ngay = document.getElementById('wh-add-date').value;
  const note = (document.getElementById('wh-add-note').value || '').trim();

  if (!ngay) return showWhToast('Vui lòng chọn ngày nhập', 'warning');
  if (!addLines.length) return showWhToast('Chưa có sản phẩm trong danh sách', 'warning');

  const btn = document.querySelector('#wh-add-modal .wh-btn-primary');
  if (btn) { btn.disabled = true; btn.textContent = '⏳ Đang lưu...'; }

  try {
    const json = await postJSON(`${WH_BASE}/warehouse/import-create`, {
      csrf_token: WH_CSRF || '',
      ngay_nhap: ngay,
      ghi_chu: note,
      items: JSON.stringify(addLines)
    });

    if (!json.success) {
      showWhToast(json.message || 'Tạo phiếu thất bại', 'error');
      if (btn) { btn.disabled = false; btn.textContent = '💾 Lưu Phiếu'; }
      return;
    }
    closeAdd();
    showWhToast('Tạo phiếu nhập thành công!', 'success');
    setTimeout(() => location.reload(), 1200);
  } catch (e) {
    showWhToast('Lỗi kết nối, vui lòng thử lại', 'error');
    if (btn) { btn.disabled = false; btn.textContent = '💾 Lưu Phiếu'; }
  }
}


// =====================================================
// EDIT PHIẾU
// =====================================================
function clearEditPick() {
  document.getElementById('wh-edit-add-ma').value = '';
  document.getElementById('wh-edit-add-dvt').value = '';
  document.getElementById('wh-edit-add-gia').value = '';
  document.getElementById('wh-edit-add-qty').value = 1;
  document.getElementById('wh-edit-add-price').value = 0;
  document.getElementById('wh-edit-q').value = '';
  editSelected = null;
}

bindSearch('wh-edit-q', 'wh-edit-suggest', p => {
  editSelected = p;

  document.getElementById('wh-edit-add-ma').value = p.Ma;
  document.getElementById('wh-edit-add-dvt').value = p.Don_vi_tinh;
  document.getElementById('wh-edit-add-gia').value = money(p.Gia_hien_tai);
  document.getElementById('wh-edit-add-price').value = p.Gia_hien_tai;

  // khi chọn sản phẩm mới từ search => coi như đang add mới
  editLineIndex = null;
});

async function loadEdit(id) {
  const json = await getJSON(`${WH_BASE}/warehouse/import-detail?id=${id}`);
  if (!json.success) return alert(json.message || 'Không tải được phiếu');

  const imp = json.import;

  document.getElementById('wh-edit-id').value = imp.ID_phieu_nhap;
  document.getElementById('wh-edit-code').textContent = '#' + (imp.Ma_hien_thi || '');
  document.getElementById('wh-edit-ma').value = imp.Ma_hien_thi || '';
  document.getElementById('wh-edit-date').value = (imp.Ngay_nhap || '').slice(0, 10);
  document.getElementById('wh-edit-user').value = imp.Nguoi_tao_ten || '';
  document.getElementById('wh-edit-note').value = imp.Ghi_chu || '';

  editLines = (json.items || []).map(x => ({
    ID_sp: Number(x.ID_sp),
    Ten_sp: x.Ten_sp,
    Don_vi_tinh: x.Don_vi_tinh || 'SP',
    Gia_hien_tai: Number(x.Gia_hien_tai || 0),
    So_luong: Number(x.So_luong || 1),
    Don_gia_nhap: Number(x.Don_gia_nhap || 0),
    Ngay_het_han: x.Ngay_het_han || ''
  }));

  editLineIndex = null;
  clearEditPick();
  renderEditLines();
}


function editAddLine() {
  const qty = Math.max(1, parseInt(document.getElementById('wh-edit-add-qty').value || '1', 10));
  const price = Math.max(0, parseFloat(document.getElementById('wh-edit-add-price').value || '0'));

  // nếu đang sửa dòng => chỉ update số lượng/đơn giá (và giá hiện tại nếu có)
  if (editLineIndex !== null) {
    const x = editLines[editLineIndex];
    if (!x) { editLineIndex = null; return; }

    x.So_luong = qty;
    x.Don_gia_nhap = price;

    // nếu có editSelected (chọn từ search) thì update cả giá hiện tại cho đúng
    if (editSelected && Number(editSelected.ID_sp) === Number(x.ID_sp)) {
      x.Gia_hien_tai = Number(editSelected.Gia_hien_tai || x.Gia_hien_tai || 0);
    }

    editLineIndex = null;
    clearEditPick();
    renderEditLines();
    return;
  }

  // add mới
  if (!editSelected) return alert('Chưa chọn sản phẩm');

  editLines.push({
    ID_sp: editSelected.ID_sp,
    Ten_sp: editSelected.Ten_sp,
    Don_vi_tinh: editSelected.Don_vi_tinh,
    Gia_hien_tai: editSelected.Gia_hien_tai,
    So_luong: qty,
    Don_gia_nhap: price
  });

  clearEditPick();
  renderEditLines();
}

// ✅ NÚT ✏️: đổ dữ liệu lên form để sửa
function pickEditLine(i) {
  const x = editLines[i];
  if (!x) return;

  editLineIndex = i;
  editSelected = { ID_sp: x.ID_sp, Ten_sp: x.Ten_sp, Don_vi_tinh: x.Don_vi_tinh, Gia_hien_tai: x.Gia_hien_tai };


  document.getElementById('wh-edit-add-ma').value = x.ID_sp;
  document.getElementById('wh-edit-add-dvt').value = x.Don_vi_tinh;
  document.getElementById('wh-edit-add-gia').value = money(x.Gia_hien_tai || 0);
  document.getElementById('wh-edit-add-qty').value = x.So_luong;
  document.getElementById('wh-edit-add-price').value = x.Don_gia_nhap;

  document.getElementById('wh-edit-add-qty').focus();
}

function renderEditLines() {
  const tb = document.getElementById('wh-edit-lines');
  const total = document.getElementById('wh-edit-total');

  if (!editLines.length) {
    tb.innerHTML = `<tr><td colspan="8" class="wh-empty">Chưa có sản phẩm</td></tr>`;
    total.textContent = money(0);
    return;
  }

  let sum = 0;
  tb.innerHTML = editLines.map((x, i) => {
    const t = (Number(x.So_luong) || 0) * (Number(x.Don_gia_nhap) || 0);
    sum += t;

    return `
      <tr>
        <td>${x.Ten_sp}</td>
        <td>${x.Don_vi_tinh}</td>
        <td>${money(x.Gia_hien_tai || 0)}</td>
        <td>${x.So_luong}</td>
        <td>${money(x.Don_gia_nhap)}</td>
        <td>${x.Ngay_het_han || '-'}</td>
        <td class="wh-bold">${money(t)}</td>
        <td>
          <button type="button" class="wh-icon wh-edit" title="Sửa" onclick="pickEditLine(${i})">✏️</button>
          <button type="button" class="wh-icon wh-danger" title="Xóa"
              onclick="if(editLineIndex===${i}) editLineIndex=null; editLines.splice(${i},1); renderEditLines()">🗑</button>
        </td>
      </tr>`;
  }).join('');

  total.textContent = money(sum);
}

// =====================================================
// SUBMIT EDIT
// =====================================================
async function submitEdit() {
  const id = document.getElementById('wh-edit-id').value;
  const ngay = document.getElementById('wh-edit-date').value;
  const note = (document.getElementById('wh-edit-note').value || '').trim();

  if (!id) return showWhToast('Thiếu ID phiếu', 'error');
  if (!ngay) return showWhToast('Vui lòng chọn ngày nhập', 'warning');
  if (!editLines.length) return showWhToast('Chưa có sản phẩm trong danh sách', 'warning');

  const btn = document.querySelector('#wh-edit-modal .wh-btn-primary');
  if (btn) { btn.disabled = true; btn.textContent = '⏳ Đang lưu...'; }

  try {
    const json = await postJSON(`${WH_BASE}/warehouse/import-update`, {
      csrf_token: WH_CSRF || '',
      id_phieu_nhap: id,
      ngay_nhap: ngay,
      ghi_chu: note,
      items: JSON.stringify(editLines)
    });

    if (!json.success) {
      showWhToast(json.message || 'Cập nhật thất bại', 'error');
      if (btn) { btn.disabled = false; btn.textContent = '💾 Lưu Thay Đổi'; }
      return;
    }
    closeEdit();
    showWhToast('Cập nhật phiếu nhập thành công!', 'success');
    setTimeout(() => location.reload(), 1200);
  } catch (e) {
    showWhToast('Lỗi kết nối, vui lòng thử lại', 'error');
    if (btn) { btn.disabled = false; btn.textContent = '💾 Lưu Thay Đổi'; }
  }
}


// =====================================================
// DELETE IMPORT
// =====================================================
function deleteImport(id) {
  // Dùng confirm nhẹ hơn alert
  if (!confirm('Bạn có chắc muốn xóa phiếu nhập này?\nHành động này không thể hoàn tác.')) return;

  fetch(`${WH_BASE}/warehouse/import-delete`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'id=' + encodeURIComponent(id) + '&csrf_token=' + encodeURIComponent(WH_CSRF || '')
  })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        showWhToast('Đã xóa phiếu nhập thành công', 'success');
        setTimeout(() => location.reload(), 1200);
      } else {
        showWhToast(res.message || 'Xóa thất bại', 'error');
      }
    })
    .catch(() => showWhToast('Lỗi kết nối, vui lòng thử lại', 'error'));
}
document.addEventListener('DOMContentLoaded', () => {
  const resetBtn = document.getElementById('wh-reset-search');
  const form = document.getElementById('wh-search-form');

  if (!resetBtn || !form) return;

  resetBtn.addEventListener('click', () => {
    // reset input UI
    form.reset();

    // xoá query trên URL, quay về dashboard sạch
    window.location.href = form.action;
  });
});
