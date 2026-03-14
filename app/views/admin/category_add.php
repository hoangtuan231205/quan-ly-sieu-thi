<?php
/**
 * ADMIN - THÊM DANH MỤC MỚI
 * Standardized UI - Theme Woodland
 * Form submit via Fetch API  (/admin/api/category-save)
 */

$parents    = $parents    ?? [];
$csrf_token = $csrf_token ?? '';
?>
<?php include __DIR__ . '/layouts/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/admin-modern.css') ?>">

<style>
/* Toast */
#toast-container {
    position: fixed; bottom: 24px; right: 24px;
    z-index: 9999; display: flex; flex-direction: column; gap: 10px;
}
.toast {
    padding: 14px 20px; border-radius: 10px; font-size: 14px;
    font-weight: 500; color: white; min-width: 260px;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    animation: toast-in .25s ease;
}
@keyframes toast-in { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.toast.success { background: #22c55e; }
.toast.error   { background: #ef4444; }

/* Field error */
.field-error { color: #ef4444; font-size: 12px; margin-top: 4px; display: none; }
.form-control.is-invalid { border-color: #ef4444 !important; }

/* Loading state */
.btn-loading { opacity: .7; pointer-events: none; }
</style>

<div class="admin-modern">
    <div class="admin-modern-container">

        <!-- Breadcrumb -->
        <div class="admin-breadcrumb">
            <a href="<?= BASE_URL ?>/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <a href="<?= BASE_URL ?>/admin/categories">Danh mục</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span class="current">Thêm mới</span>
        </div>

        <!-- Page Header -->
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Thêm Danh Mục Mới</h1>
                <p class="admin-page-subtitle">Tạo danh mục sản phẩm mới cho siêu thị</p>
            </div>
            <div class="admin-header-actions">
                <a href="<?= BASE_URL ?>/admin/categories" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Thông tin danh mục</h3>
            </div>
            <div class="admin-card-body">
                <form id="categoryForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <!-- Tên danh mục -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--admin-text);">
                            Tên danh mục <span style="color:red;">*</span>
                        </label>
                        <input type="text" name="ten_danh_muc" id="tenDanhMuc" class="form-control"
                               placeholder="VD: Đồ uống, Bánh kẹo..."
                               style="width:100%;padding:12px 16px;border:1px solid var(--admin-border);border-radius:8px;font-size:14px;">
                        <div class="field-error" id="err-ten">Tên danh mục không được để trống</div>
                    </div>

                    <!-- Danh mục cha -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--admin-text);">
                            Danh mục cha
                        </label>
                        <select name="danh_muc_cha" id="danhMucCha" class="form-control"
                                style="width:100%;padding:12px 16px;border:1px solid var(--admin-border);border-radius:8px;font-size:14px;">
                            <option value="">-- Không có (Danh mục gốc) --</option>
                            <?php foreach ($parents as $parent): ?>
                                <option value="<?= (int)$parent['ID_danh_muc'] ?>">
                                    <?= htmlspecialchars($parent['Ten_danh_muc']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--admin-text);">
                            Mô tả
                        </label>
                        <textarea name="mo_ta" id="moTa" class="form-control" rows="4"
                                  placeholder="Nhập mô tả ngắn về danh mục..."
                                  style="width:100%;padding:12px 16px;border:1px solid var(--admin-border);border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                    </div>

                    <!-- Trạng thái -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block;font-weight:600;margin-bottom:8px;color:var(--admin-text);">
                            Trạng thái
                        </label>
                        <select name="trang_thai" id="trangThai" class="form-control"
                                style="width:100%;padding:12px 16px;border:1px solid var(--admin-border);border-radius:8px;font-size:14px;">
                            <option value="active" selected>Hoạt động</option>
                            <option value="inactive">Tạm ẩn</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--admin-border);">
                        <button type="submit" id="btnSave" class="btn-admin-primary">
                            <i class="fas fa-save" id="btnSaveIcon"></i>
                            <span id="btnSaveText">Lưu danh mục</span>
                        </button>
<a href="<?= BASE_URL ?>/admin/categories" class="btn-admin-secondary">
                            <span>Hủy</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Toast container -->
<div id="toast-container"></div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// ============================================================
//  TOAST
// ============================================================
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// ============================================================
//  VALIDATION
// ============================================================
function validateForm() {
    let valid = true;
    const name  = document.getElementById('tenDanhMuc');
    const err   = document.getElementById('err-ten');

    if (!name.value.trim()) {
        name.classList.add('is-invalid');
        err.style.display = 'block';
        valid = false;
    } else {
        name.classList.remove('is-invalid');
        err.style.display = 'none';
    }
    return valid;
}

// ============================================================
function resetForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('tenDanhMuc').classList.remove('is-invalid');
    document.getElementById('err-ten').style.display = 'none';
}

async function saveCategory(e) {
    e.preventDefault();
    if (!validateForm()) return;

    const btnSave     = document.getElementById('btnSave');
    const btnIcon     = document.getElementById('btnSaveIcon');
    const btnText     = document.getElementById('btnSaveText');

    // Loading state
    btnSave.classList.add('btn-loading');
    btnIcon.className = 'fas fa-spinner fa-spin';
    btnText.textContent = 'Đang lưu...';

    const formData = new FormData(document.getElementById('categoryForm'));

    try {
        const res  = await fetch(`${BASE_URL}/admin/api/category-save`, {
            method: 'POST',
            body:   formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        showToast(json.message, json.success ? 'success' : 'error');

        if (json.success) {
            // Redirect to list after short delay
            setTimeout(() => {
                window.location.href = `${BASE_URL}/admin/categories`;
            }, 1200);
        }
    } catch (err) {
        console.error(err);
        showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
    } finally {
        btnSave.classList.remove('btn-loading');
        btnIcon.className = 'fas fa-save';
        btnText.textContent = 'Lưu danh mục';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('categoryForm').addEventListener('submit', saveCategory);

    // Clear error on input
    document.getElementById('tenDanhMuc').addEventListener('input', function () {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            document.getElementById('err-ten').style.display = 'none';
        }
    });
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>