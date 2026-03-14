<?php
/**
 * ADMIN - QUẢN LÝ DANH MỤC
 * Standardized UI - Theme Woodland
 * Data loaded via Fetch API  (/admin/api/categories)
 */

$csrf_token = $csrf_token ?? '';

// Icon mapping based on category name patterns
function getCategoryIcon($name) {
    $name  = mb_strtolower($name);
    $icons = [
        'đồ uống'   => ['fa-glass-whiskey', 'orange'],
        'nước'      => ['fa-glass-whiskey', 'orange'],
        'sữa'       => ['fa-glass-whiskey', 'blue'],
        'thực phẩm' => ['fa-apple-alt',     'green'],
        'rau'       => ['fa-leaf',           'green'],
        'trái cây'  => ['fa-apple-alt',      'green'],
        'thịt'      => ['fa-drumstick-bite', 'red'],
        'cá'        => ['fa-fish',           'blue'],
        'bánh'      => ['fa-cookie',         'yellow'],
        'kẹo'       => ['fa-candy-cane',     'pink'],
        'gia dụng'  => ['fa-home',           'rose'],
        'hóa phẩm'  => ['fa-soap',           'purple'],
        'chăm sóc'  => ['fa-heart',          'pink'],
        'gia vị'    => ['fa-pepper-hot',     'red'],
        'đông lạnh' => ['fa-snowflake',      'cyan'],
        'đóng gói'  => ['fa-box',            'amber'],
    ];
    foreach ($icons as $pattern => $config) {
        if (strpos($name, $pattern) !== false) return $config;
    }
    return ['fa-folder', 'blue'];
}

// Precompute icon map as JSON for JS
$iconMapJson = json_encode([
    'đồ uống'   => ['fa-glass-whiskey', 'orange'],
    'nước'      => ['fa-glass-whiskey', 'orange'],
    'sữa'       => ['fa-glass-whiskey', 'blue'],
    'thực phẩm' => ['fa-apple-alt',     'green'],
    'rau'       => ['fa-leaf',          'green'],
    'trái cây'  => ['fa-apple-alt',     'green'],
    'thịt'      => ['fa-drumstick-bite','red'],
    'cá'        => ['fa-fish',          'blue'],
    'bánh'      => ['fa-cookie',        'yellow'],
    'kẹo'       => ['fa-candy-cane',    'pink'],
    'gia dụng'  => ['fa-home',          'rose'],
    'hóa phẩm'  => ['fa-soap',         'purple'],
    'chăm sóc'  => ['fa-heart',         'pink'],
    'gia vị'    => ['fa-pepper-hot',    'red'],
    'đông lạnh' => ['fa-snowflake',     'cyan'],
    'đóng gói'  => ['fa-box',           'amber'],
], JSON_UNESCAPED_UNICODE);
?>
<?php include __DIR__ . '/layouts/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/admin-modern.css') ?>">

<style>
.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 24px;
}
@media (max-width: 1200px) { .category-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px)  { .category-grid { grid-template-columns: 1fr; } }

.category-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 24px;
    border: 1px solid var(--admin-border);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.category-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
    border-color: var(--admin-primary);
}
.category-card.inactive { opacity: 0.8; background: #fafafa; }

.category-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 16px;
}
.category-icon.orange { background: rgba(249,115,22,.1); color: #f97316; }
.category-icon.green  { background: rgba(34,197,94,.1);  color: #22c55e; }
.category-icon.blue   { background: rgba(59,130,246,.1); color: #3b82f6; }
.category-icon.purple { background: rgba(139,92,246,.1); color: #8b5cf6; }
.category-icon.rose   { background: rgba(244,63,94,.1);  color: #f43f5e; }
.category-icon.yellow { background: rgba(234,179,8,.1);  color: #eab308; }
.category-icon.pink   { background: rgba(236,72,153,.1); color: #ec4899; }
.category-icon.red    { background: rgba(239,68,68,.1);  color: #ef4444; }
.category-icon.cyan   { background: rgba(6,182,212,.1);  color: #06b6d4; }
.category-icon.amber  { background: rgba(245,158,11,.1); color: #f59e0b; }

.category-card-add {
    background: #f8fafc;
    border: 2px dashed var(--admin-border);
    border-radius: var(--border-radius);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; min-height: 250px;
    cursor: pointer; transition: all 0.2s ease;
    text-decoration: none; color: var(--admin-text-light);
}
.category-card-add:hover {
    border-color: var(--admin-primary);
    background: rgba(123,192,67,.05);
    color: var(--admin-primary);
}

/* Skeleton loader */
.skeleton {
    animation: skeleton-pulse 1.4s ease-in-out infinite;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    border-radius: 6px;
}
@keyframes skeleton-pulse {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skeleton-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 24px;
    border: 1px solid var(--admin-border);
    height: 220px;
}

/* Toast notification */
#toast-container {
    position: fixed; bottom: 24px; right: 24px;
    z-index: 9999; display: flex; flex-direction: column; gap: 10px;
}
.toast {
    padding: 14px 20px; border-radius: 10px; font-size: 14px;
    font-weight: 500; color: white; min-width: 260px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    animation: toast-in .25s ease;
}
@keyframes toast-in { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.toast.success { background: #22c55e; }
.toast.error   { background: #ef4444; }

/* Spin animation for reset button */
@keyframes spin { to { transform: rotate(360deg); } }
.spinning { animation: spin .5s linear; }
</style>

<div class="admin-modern">
    <div class="admin-modern-container">

        <!-- Breadcrumb -->
        <div class="admin-breadcrumb">
            <a href="<?= BASE_URL ?>/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span class="current">Quản lý danh mục</span>
        </div>

        <?php include __DIR__ . '/components/management_tabs.php'; ?>

        <!-- Page Header -->
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Danh Mục Sản Phẩm</h1>
                <p class="admin-page-subtitle">Quản lý và tổ chức danh mục hàng hóa của siêu thị.</p>
            </div>
            <div class="admin-header-actions">
                <a href="<?= BASE_URL ?>/admin/category-add" class="btn-admin-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm danh mục</span>
                </a>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="admin-card" style="margin-bottom:24px;">
            <div class="admin-card-body">
                <div class="admin-filter-bar" style="justify-content:space-between; align-items:center;">
                    <div class="form-group" style="flex:1; max-width:400px; margin:0;">
                        <div style="position:relative;">
                            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--admin-text-light);"></i>
                            <input type="text" id="searchInput" class="form-control"
                                   placeholder="Tìm kiếm danh mục..."
                                   style="padding-left:42px;">
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <!-- Reset Button -->
                        <button id="btnReset" class="btn-admin-secondary" title="Đặt lại bộ lọc và tải lại">
                            <i class="fas fa-redo-alt" id="resetIcon"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats bar -->
        <div id="statsBar" style="font-size:13px; color:var(--admin-text-muted); margin-bottom:16px; min-height:20px;"></div>

        <!-- Card Grid -->
        <div class="category-grid" id="categoryGrid">
            <!-- Skeleton cards on initial load -->
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="skeleton-card">
                <div class="skeleton" style="width:48px;height:48px;border-radius:12px;margin-bottom:16px;"></div>
                <div class="skeleton" style="width:60%;height:20px;margin-bottom:12px;"></div>
                <div class="skeleton" style="width:90%;height:14px;margin-bottom:6px;"></div>
                <div class="skeleton" style="width:75%;height:14px;margin-bottom:20px;"></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--admin-border);padding-top:16px;">
                    <div class="skeleton" style="width:60px;height:26px;border-radius:6px;"></div>
                    <div style="display:flex;gap:8px;">
                        <div class="skeleton" style="width:32px;height:32px;border-radius:6px;"></div>
                        <div class="skeleton" style="width:32px;height:32px;border-radius:6px;"></div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Pagination -->
        <div id="paginationWrap"></div>

    </div>
</div>

<!-- Toast container -->
<div id="toast-container"></div>

<script>
// ============================================================
//  CONFIG
// ============================================================
const BASE_URL   = '<?= BASE_URL ?>';
const CSRF_TOKEN = '<?= Session::getCsrfToken() ?>';
const ICON_MAP   = <?= $iconMapJson ?>;

// ============================================================
//  STATE
// ============================================================
let currentPage    = 1;
let currentKeyword = '';
let debounceTimer  = null;

// ============================================================
//  ICON HELPER
// ============================================================
function getCategoryIcon(name) {
    const lower = name.toLowerCase();
    for (const [pattern, config] of Object.entries(ICON_MAP)) {
        if (lower.includes(pattern)) return config;
    }
    return ['fa-folder', 'blue'];
}

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
//  RENDER FUNCTIONS
// ============================================================
function renderSkeletons(count = 6) {
    const grid = document.getElementById('categoryGrid');
    grid.innerHTML = Array.from({ length: count }, () => `
        <div class="skeleton-card">
            <div class="skeleton" style="width:48px;height:48px;border-radius:12px;margin-bottom:16px;"></div>
            <div class="skeleton" style="width:60%;height:20px;margin-bottom:12px;"></div>
            <div class="skeleton" style="width:90%;height:14px;margin-bottom:6px;"></div>
            <div class="skeleton" style="width:75%;height:14px;margin-bottom:20px;"></div>
            <div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:16px;">
                <div class="skeleton" style="width:60px;height:26px;border-radius:6px;"></div>
                <div style="display:flex;gap:8px;">
                    <div class="skeleton" style="width:32px;height:32px;border-radius:6px;"></div>
                    <div class="skeleton" style="width:32px;height:32px;border-radius:6px;"></div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderCategories(categories) {
    const grid = document.getElementById('categoryGrid');
    let html = '';

    if (categories.length === 0) {
        html = `
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--admin-text-muted);">
                <i class="fas fa-folder-open" style="font-size:48px;margin-bottom:16px;opacity:.4;display:block;"></i>
                <p style="font-size:16px;font-weight:600;">Không tìm thấy danh mục nào</p>
                <p style="font-size:13px;">Thử tìm kiếm khác hoặc thêm danh mục mới</p>
            </div>`;
    } else {
        categories.forEach(cat => {
            const [iconClass, iconColor] = getCategoryIcon(cat.Ten_danh_muc);
            const isActive   = (cat.Trang_thai ?? 'active') === 'active';
            const count      = cat.So_san_pham ?? 0;
            const desc       = cat.Mo_ta ? escHtml(cat.Mo_ta) : 'Chưa có mô tả';
            const statusBadge = isActive
                ? `<span class="status-badge success">Hoạt động</span>`
                : `<span class="status-badge normal">Tạm ẩn</span>`;

            html += `
                <div class="category-card ${isActive ? '' : 'inactive'}" data-id="${cat.ID_danh_muc}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div class="category-icon ${iconColor}">
                            <i class="fas ${iconClass}"></i>
                        </div>
                        ${statusBadge}
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:var(--text-dark);margin-bottom:8px;">
                        ${escHtml(cat.Ten_danh_muc)}
                    </h3>
                    <p style="font-size:14px;color:var(--admin-text-muted);margin-bottom:20px;flex-grow:1;line-height:1.5;">
                        ${desc}
                    </p>
                    <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--admin-border);padding-top:16px;margin-top:auto;">
                        <span style="background:#eff6ff;color:#3b82f6;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                            ${Number(count).toLocaleString('vi-VN')} SP
                        </span>
                        <div style="display:flex;gap:8px;">
                            <button onclick="deleteCategory(${cat.ID_danh_muc})" class="btn-icon"
                                    style="color:var(--admin-danger);" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
        });
    }

    // Add "add new" card
    html += `
        <a href="${BASE_URL}/admin/category-add" class="category-card-add">
            <div style="width:64px;height:64px;border-radius:50%;background:white;display:flex;align-items:center;
                        justify-content:center;font-size:24px;margin-bottom:16px;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <i class="fas fa-plus"></i>
            </div>
            <h4 style="font-weight:700;margin-bottom:4px;">Tạo danh mục mới</h4>
            <p style="font-size:13px;color:var(--admin-text-muted);">Thêm danh mục sản phẩm vào kho</p>
        </a>`;

    grid.innerHTML = html;
}

function renderPagination(pagination) {
    const wrap = document.getElementById('paginationWrap');
    if (!pagination || pagination.last_page <= 1) { wrap.innerHTML = ''; return; }

    const { current_page, last_page, total, from, to } = pagination;
    const prevDisabled = current_page <= 1;
    const nextDisabled = current_page >= last_page;

    wrap.innerHTML = `
        <div class="admin-card">
            <div class="admin-card-footer" style="border-top:none;display:flex;justify-content:space-between;align-items:center;">
                <div style="font-size:13px;color:var(--admin-text-muted);">
                    Hiển thị <strong>${from}–${to}</strong> / ${total} danh mục
                </div>
                <div class="pagination">
                    <button class="page-link ${prevDisabled ? 'disabled' : ''}"
                            onclick="loadCategories(${current_page - 1})" ${prevDisabled ? 'disabled' : ''}>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span style="padding:0 12px;font-size:13px;align-self:center;">
                        Trang ${current_page} / ${last_page}
                    </span>
                    <button class="page-link ${nextDisabled ? 'disabled' : ''}"
                            onclick="loadCategories(${current_page + 1})" ${nextDisabled ? 'disabled' : ''}>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>`;
}

function renderStats(pagination) {
    const bar = document.getElementById('statsBar');
    if (!pagination) { bar.textContent = ''; return; }
    const keyword = currentKeyword ? ` — tìm kiếm: "<strong>${escHtml(currentKeyword)}</strong>"` : '';
    bar.innerHTML = `Tổng cộng <strong>${pagination.total}</strong> danh mục${keyword}`;
}

// ============================================================
//  FETCH CATEGORIES
// ============================================================
async function loadCategories(page = 1, showSkeleton = false) {
    currentPage = page;
    if (showSkeleton) renderSkeletons();

    const params = new URLSearchParams({
        page:    page,
        keyword: currentKeyword,
    });

    try {
        const res  = await fetch(`${BASE_URL}/admin/api/categories?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        if (!json.success) {
            showToast('Không thể tải danh mục', 'error');
            document.getElementById('categoryGrid').innerHTML = '';
            return;
        }

        renderCategories(json.data);
        renderPagination(json.pagination);
        renderStats(json.pagination);
    } catch (err) {
        console.error(err);
        showToast('Lỗi kết nối, vui lòng thử lại', 'error');
        document.getElementById('categoryGrid').innerHTML = '';
    }
}

// ============================================================
//  DELETE
// ============================================================
async function deleteCategory(id) {
    if (!confirm('Xóa danh mục này?\n\nLưu ý: Không thể xóa danh mục có sản phẩm hoặc danh mục con.')) return;

    const formData = new FormData();
    formData.append('category_id', id);
    formData.append('csrf_token',  CSRF_TOKEN);

    try {
        const res  = await fetch(`${BASE_URL}/admin/category-delete`, {
            method: 'POST',
            body:   formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();

        showToast(json.message, json.success ? 'success' : 'error');
        if (json.success) loadCategories(currentPage);
    } catch (err) {
        console.error(err);
        showToast('Có lỗi xảy ra', 'error');
    }
}

// ============================================================
//  RESET
// ============================================================
function resetFilters() {
    const icon  = document.getElementById('resetIcon');
    const input = document.getElementById('searchInput');

    // Spin icon
    icon.classList.add('spinning');
    setTimeout(() => icon.classList.remove('spinning'), 500);

    input.value    = '';
    currentKeyword = '';
    loadCategories(1, true);
}

// ============================================================
//  HTML ESCAPE
// ============================================================
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ============================================================
//  INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Initial load
    loadCategories(1, false);

    // Search with debounce
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentKeyword = e.target.value.trim();
            loadCategories(1, true);
        }, 400);
    });

    // Reset button
    document.getElementById('btnReset').addEventListener('click', resetFilters);
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>