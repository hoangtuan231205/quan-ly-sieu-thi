/**
 * =============================================================================
 * products.js  –  Quản lý Sản phẩm (API-driven)
 * =============================================================================
 *
 * Tất cả thao tác CRUD đều dùng fetch() gọi REST API:
 *   GET    /admin/api/products           → load danh sách
 *   GET    /admin/api/products/{id}      → lấy chi tiết để edit
 *   POST   /admin/api/products           → thêm mới
 *   POST   /admin/api/products/{id}      → cập nhật
 *   DELETE /admin/api/products/{id}      → xóa
 *
 * Cấu hình được inject từ PHP qua window.ADMIN_CONFIG
 */

// ─── Trạng thái cục bộ ────────────────────────────────────────────────────────
const state = {
    editingId:   null,      // null = đang thêm mới, number = đang sửa
    deletingId:  null,
    currentPage: 1,
    lastMeta:    {}
};

// ─── Shorthand ────────────────────────────────────────────────────────────────
const cfg  = () => window.ADMIN_CONFIG;
const $    = (id) => document.getElementById(id);

// =============================================================================
// KHỞI TẠO
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
    loadProducts(1);

    // Tìm kiếm khi nhấn Enter
    $('filterSearch')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') loadProducts(1);
    });

    // Đóng modal khi click ra ngoài
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.classList.add('hidden');
        }
    });
});

// =============================================================================
// LOAD DANH SÁCH  –  GET /admin/api/products
// =============================================================================

async function loadProducts(page = 1) {
    state.currentPage = page;

    const search   = $('filterSearch')?.value.trim()    ?? '';
    const category = $('filterCategory')?.value         ?? '';
    const status   = $('filterStatus')?.value           ?? '';

    const params = new URLSearchParams({
        page, per_page: 20,
        ...(search   && { search }),
        ...(category && { category }),
        ...(status   && { status })
    });

    const tbody = $('productTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" style="text-align:center; padding:60px 20px;">
                <div class="spinner" style="margin:0 auto;"></div>
                <p style="margin-top:12px; color:var(--admin-text-muted);">Đang tải...</p>
            </td>
        </tr>`;

    try {
        const res  = await fetch(`${cfg().apiBase}?${params}`);
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'Lỗi tải dữ liệu');

        state.lastMeta = json.meta ?? {};
        renderTable(json.data);
        renderPagination(json.meta);
        $('totalCount').textContent = (json.meta?.total ?? 0).toLocaleString('vi-VN');

    } catch (err) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align:center; padding:40px; color:#dc2626;">
                    <i class="fas fa-exclamation-circle" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                    ${escHtml(err.message)}
                </td>
            </tr>`;
    }
}

function clearFilters() {
    if ($('filterSearch'))   $('filterSearch').value   = '';
    if ($('filterCategory')) $('filterCategory').value = '';
    if ($('filterStatus'))   $('filterStatus').value   = '';
    loadProducts(1);
}

// =============================================================================
// RENDER BẢNG
// =============================================================================

function renderTable(products) {
    const tbody = $('productTableBody');

    if (!products?.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align:center; padding:60px 20px;">
                    <i class="fas fa-box-open" style="font-size:48px; color:var(--admin-text-light);
                       margin-bottom:16px; display:block;"></i>
                    <p style="color:var(--admin-text-muted);">Không có sản phẩm nào</p>
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = products.map(p => {
        const stock  = getStockStatus(p.So_luong_ton ?? 0);
        const status = p.Trang_thai === 'active'
            ? '<span class="status-badge success">Đang bán</span>'
            : '<span class="status-badge danger">Ngừng bán</span>';
        const img    = p.Hinh_anh
            ? `<img src="${cfg().imgBase}${escHtml(p.Hinh_anh)}" alt="${escHtml(p.Ten)}"
                    style="width:72px; height:72px; object-fit:cover; border-radius:10px;"
                    onerror="this.src='${cfg().imgPlaceholder}'">`
            : `<div style="width:72px; height:72px; background:#f3f4f6; border-radius:10px;
                           display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-image" style="color:#9ca3af;"></i></div>`;

        return `
        <tr>
            <td style="text-align:center;">${img}</td>
            <td>
                <div style="font-weight:600; color:var(--admin-text);">${escHtml(p.Ten)}</div>
                <small style="color:var(--admin-text-muted);">${escHtml(p.Ma_hien_thi ?? '')}</small>
            </td>
            <td>${escHtml(p.Ten_danh_muc ?? '')}</td>
            <td style="text-align:right; font-weight:600;">
                ${Number(p.Gia_tien ?? 0).toLocaleString('vi-VN')} ₫
            </td>
            <td style="text-align:center;">
                <span class="${stock.class}">${escHtml(p.So_luong_ton ?? 0)} ${stock.text}</span>
            </td>
            <td>${status}</td>
            <td style="text-align:center;">
                <div style="display:flex; gap:6px; justify-content:center;">
                    <button class="btn-icon btn-edit" title="Sửa"
                            onclick="openEditModal(${p.ID_sp})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-delete" title="Xóa"
                            onclick="openDeleteModal(${p.ID_sp}, '${escJs(p.Ten)}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function getStockStatus(qty) {
    if (qty <= 0)  return { class: 'status-badge danger',  text: '– Hết hàng' };
    if (qty < 30)  return { class: 'status-badge warning', text: '– Sắp hết' };
    return            { class: 'status-badge success', text: '' };
}

// =============================================================================
// PAGINATION
// =============================================================================

function renderPagination(meta) {
    if (!meta) return;

    $('paginationInfo').textContent =
        `Hiển thị ${meta.from}–${meta.to} trong ${(meta.total ?? 0).toLocaleString('vi-VN')} sản phẩm`;

    const btns    = $('paginationButtons');
    btns.innerHTML = '';

    const addBtn = (label, page, disabled = false, active = false) => {
        const btn = document.createElement('button');
        btn.innerHTML   = label;
        btn.className   = active ? 'btn-admin-primary' : 'btn-admin-secondary';
        btn.disabled    = disabled;
        btn.style.padding = '6px 12px';
        btn.onclick     = () => loadProducts(page);
        btns.appendChild(btn);
    };

    addBtn('‹', meta.current_page - 1, meta.current_page <= 1);

    // Hiển thị tối đa 5 trang gần trang hiện tại
    const start = Math.max(1, meta.current_page - 2);
    const end   = Math.min(meta.last_page, start + 4);

    for (let p = start; p <= end; p++) {
        addBtn(p, p, false, p === meta.current_page);
    }

    addBtn('›', meta.current_page + 1, meta.current_page >= meta.last_page);
}

// =============================================================================
// MODAL THÊM MỚI
// =============================================================================

function openAddModal() {
    state.editingId = null;

    resetForm();
    $('modalTitle').textContent = 'Thêm sản phẩm mới';
    $('modalHeader').style.background = '';
    $('productModal').classList.remove('hidden');
}

// =============================================================================
// MODAL SỬA  –  GET /admin/api/products/{id}
// =============================================================================

async function openEditModal(id) {
    $('productModal').classList.remove('hidden');
    $('modalTitle').textContent = 'Đang tải...';

    try {
        const res  = await fetch(`${cfg().apiBase}/${id}`);
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'Không tìm thấy sản phẩm');

        const p = json.data;
        state.editingId = p.ID_sp;

        $('modalTitle').textContent     = 'Chỉnh sửa sản phẩm';
        $('modalHeader').style.background = 'var(--admin-primary)';

        $('formName').value        = p.Ten           ?? '';
        $('formCategory').value    = p.ID_danh_muc   ?? '';
        $('formSku').value         = p.Ma_hien_thi   ?? '';
        $('formUnit').value        = p.Don_vi_tinh   ?? '';
        $('formPrice').value       = p.Gia_tien      ?? '';
        $('formCost').value        = p.Gia_nhap      ?? '';
        $('formOrigin').value      = p.Xuat_xu       ?? '';
        $('formIngredients').value = p.Thanh_phan    ?? '';
        $('formDesc').value        = p.Mo_ta_sp      ?? '';
        $('formStatus').value      = p.Trang_thai    ?? 'active';
        $('charCount').textContent = ($('formDesc').value || '').length;

        $('formImagePreview').src = p.Hinh_anh
            ? `${cfg().imgBase}${p.Hinh_anh}`
            : cfg().imgPlaceholder;

    } catch (err) {
        $('productModal').classList.add('hidden');
        showNotification('❌ ' + err.message, 'error');
    }
}

function closeModal() {
    $('productModal').classList.add('hidden');
}

// =============================================================================
// SUBMIT (Thêm mới / Cập nhật)
// POST /admin/api/products
// POST /admin/api/products/{id}
// =============================================================================

async function submitProduct() {
    const name     = $('formName').value.trim();
    const category = $('formCategory').value;
    const price    = $('formPrice').value;
    const unit     = $('formUnit').value.trim();

    if (!name)     { alert('Vui lòng nhập tên sản phẩm'); return; }
    if (!category) { alert('Vui lòng chọn danh mục');     return; }
    if (!price || parseFloat(price) < 0) { alert('Vui lòng nhập giá bán hợp lệ'); return; }
    if (!unit)     { alert('Vui lòng nhập đơn vị tính');  return; }

    const formData = new FormData();
    formData.append('csrf_token',  cfg().csrfToken);
    formData.append('ten',         name);
    formData.append('danh_muc_id', category);
    formData.append('gia_tien',    price);
    formData.append('gia_nhap',    $('formCost').value        || 0);
    formData.append('don_vi',      unit);
    formData.append('ma_hien_thi', $('formSku').value.trim()  || '');
    formData.append('xuat_xu',     $('formOrigin').value.trim()      || '');
    formData.append('thanh_phan',  $('formIngredients').value.trim() || '');
    formData.append('mo_ta',       $('formDesc').value.trim()  || '');
    formData.append('trang_thai',  $('formStatus').value       || 'active');

    const imageFile = $('formImage').files[0];
    if (imageFile) formData.append('hinh_anh', imageFile);

    const url    = state.editingId
        ? `${cfg().apiBase}/${state.editingId}`
        : cfg().apiBase;

    const saveBtn = document.querySelector('#productModal .btn-admin-primary:last-of-type');
    const origText = saveBtn.innerHTML;
    saveBtn.disabled  = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

    try {
        const res  = await fetch(url, { method: 'POST', body: formData });
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'Lưu thất bại');

        closeModal();
        showNotification(json.message || 'Lưu thành công', 'success');
        loadProducts(state.currentPage);

    } catch (err) {
        showNotification('❌ ' + err.message, 'error');
    } finally {
        saveBtn.disabled  = false;
        saveBtn.innerHTML = origText;
    }
}

// =============================================================================
// XÓA  –  DELETE /admin/api/products/{id}
// =============================================================================

function openDeleteModal(id, name) {
    state.deletingId = id;
    $('deleteProductName').textContent = name;
    $('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    $('deleteModal').classList.add('hidden');
    state.deletingId = null;
}

async function confirmDelete() {
    const id = state.deletingId;
    if (!id) return;

    const btn       = document.querySelector('#deleteModal .btn-admin-primary');
    const origText  = btn.innerHTML;
    btn.disabled    = true;
    btn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';

    try {
        const res  = await fetch(`${cfg().apiBase}/${id}`, {
            method:  'DELETE',
            headers: {
                'X-CSRF-TOKEN': cfg().csrfToken,
                'Content-Type': 'application/json'
            }
        });
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'Xóa thất bại');

        closeDeleteModal();
        showNotification(json.message || 'Xóa thành công', 'success');
        loadProducts(state.currentPage);

    } catch (err) {
        showNotification('❌ ' + err.message, 'error');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = origText;
    }
}

// =============================================================================
// HELPERS – UI
// =============================================================================

function resetForm() {
    $('formName').value        = '';
    $('formCategory').value    = '';
    $('formSku').value         = '';
    $('formUnit').value        = '';
    $('formPrice').value       = '';
    $('formCost').value        = '';
    $('formOrigin').value      = '';
    $('formIngredients').value = '';
    $('formDesc').value        = '';
    $('formStatus').value      = 'active';
    $('charCount').textContent = '0';
    $('formImage').value       = '';
    $('formImagePreview').src  = cfg().imgPlaceholder;
    if ($('modalHeader')) $('modalHeader').style.background = '';
}

function previewModalImage(input) {
    const file = input.files[0];
    if (!file) return;

    const valid = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!valid.includes(file.type)) {
        alert('Chỉ chấp nhận ảnh JPEG, PNG, GIF, WEBP');
        input.value = '';
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        alert('Kích thước ảnh tối đa 5MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => { $('formImagePreview').src = e.target.result; };
    reader.readAsDataURL(file);
}

function updateCharCount(textarea) {
    const max     = 2000;
    const current = textarea.value.length;

    $('charCount').textContent = current;

    if (current > max) {
        textarea.value = textarea.value.substring(0, max);
        $('charCount').classList.add('text-red-500');
    } else {
        $('charCount').classList.remove('text-red-500');
    }
}

function showNotification(message, type = 'success') {
    const notif   = $('notification');
    const msgEl   = $('notification-message');
    if (!notif || !msgEl) return;

    notif.style.background = type === 'error' ? '#dc2626' : '#1f2937';
    msgEl.textContent      = message;
    notif.classList.remove('hidden');

    clearTimeout(notif._timer);
    notif._timer = setTimeout(() => notif.classList.add('hidden'), 3500);
}

// Escape HTML để tránh XSS trong innerHTML
function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Escape cho giá trị bên trong JS string (onclick="...")
function escJs(str) {
    return String(str ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}