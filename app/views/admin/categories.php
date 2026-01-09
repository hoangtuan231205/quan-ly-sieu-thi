<?php
/**
 * ADMIN - QUẢN LÝ DANH MỤC
 * Modern Card Grid Layout - Adapted from Tailwind Template
 * Theme: #7BC043 (Lime Green)
 */

// Data from controller
$categories = $categories ?? [];
$pagination = $pagination ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1];
$filters = $filters ?? [];

// Icon mapping based on category name patterns
function getCategoryIcon($name) {
    $name = mb_strtolower($name);
    $icons = [
        'đồ uống' => ['fa-glass-whiskey', 'orange'],
        'nước' => ['fa-glass-whiskey', 'orange'],
        'sữa' => ['fa-glass-whiskey', 'blue'],
        'thực phẩm' => ['fa-apple-alt', 'green'],
        'rau' => ['fa-leaf', 'green'],
        'trái cây' => ['fa-apple-alt', 'green'],
        'thịt' => ['fa-drumstick-bite', 'red'],
        'cá' => ['fa-fish', 'blue'],
        'bánh' => ['fa-cookie', 'yellow'],
        'kẹo' => ['fa-candy-cane', 'pink'],
        'gia dụng' => ['fa-home', 'rose'],
        'hóa phẩm' => ['fa-soap', 'purple'],
        'chăm sóc' => ['fa-heart', 'pink'],
        'gia vị' => ['fa-pepper-hot', 'red'],
        'đông lạnh' => ['fa-snowflake', 'cyan'],
        'đóng gói' => ['fa-box', 'amber'],
    ];
    
    foreach ($icons as $pattern => $config) {
        if (strpos($name, $pattern) !== false) {
            return $config;
        }
    }
    return ['fa-folder', 'blue']; // default
}
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<style>
/* ========================================
   CATEGORIES PAGE - Card Grid Layout
   ======================================== */

.category-page {
    padding: 24px;
    background: #f8fafc;
    min-height: calc(100vh - 60px);
}

.category-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.category-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 24px;
}

.category-header-text h1 {
    font-size: 32px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 8px 0;
    letter-spacing: -0.025em;
}

.category-header-text p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    max-width: 500px;
}

.btn-add-category {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #1e293b;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15);
}

.btn-add-category:hover {
    background: #334155;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 41, 59, 0.2);
    color: white;
}

/* Search & Filter Bar */
.category-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 20px;
    margin-bottom: 24px;
    border-bottom: 1px solid #e2e8f0;
    flex-wrap: wrap;
}

.category-search {
    position: relative;
    flex: 1;
    max-width: 320px;
}

.category-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.category-search input {
    width: 100%;
    height: 44px;
    padding: 0 14px 0 44px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    color: #1e293b;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.category-search input:focus {
    outline: none;
    border-color: #7BC043;
    box-shadow: 0 0 0 3px rgba(123, 192, 67, 0.15);
}

.category-search input::placeholder {
    color: #94a3b8;
}

.category-filter-btn {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
}

.category-filter-btn:hover {
    border-color: #7BC043;
    color: #7BC043;
}

/* Card Grid */
.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

@media (max-width: 1200px) {
    .category-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .category-grid { grid-template-columns: 1fr; }
}

/* Category Card */
.category-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.category-card:hover {
    box-shadow: 0 0 0 1px rgba(123, 192, 67, 0.3), 0 8px 24px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.category-card.inactive {
    opacity: 0.7;
    background: #fafafa;
}

.category-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

/* Icon Box */
.category-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.category-icon.orange { background: rgba(249, 115, 22, 0.1); color: #f97316; border: 1px solid rgba(249, 115, 22, 0.2); }
.category-icon.green { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); }
.category-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
.category-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); }
.category-icon.rose { background: rgba(244, 63, 94, 0.1); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.2); }
.category-icon.yellow { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
.category-icon.pink { background: rgba(236, 72, 153, 0.1); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.2); }
.category-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
.category-icon.cyan { background: rgba(6, 182, 212, 0.1); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.2); }
.category-icon.amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }

/* Action Buttons */
.category-actions {
    display: flex;
    gap: 4px;
    opacity: 0;
    transform: translateX(8px);
    transition: all 0.2s ease;
}

.category-card:hover .category-actions {
    opacity: 1;
    transform: translateX(0);
}

.category-action-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.category-action-btn:hover {
    background: #f1f5f9;
}

.category-action-btn.edit:hover { color: #7BC043; }
.category-action-btn.delete:hover { color: #ef4444; background: rgba(239,68,68,0.1); }

/* Card Content */
.category-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 8px 0;
    transition: color 0.2s ease;
}

.category-card:hover .category-card-title {
    color: #7BC043;
}

.category-card-desc {
    font-size: 14px;
    color: #64748b;
    margin: 0 0 20px 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}

/* Card Footer */
.category-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    margin-top: auto;
}

.category-product-count {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}

.category-code {
    font-size: 11px;
    color: #94a3b8;
    font-family: monospace;
    margin-left: 8px;
}

.category-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
}

.category-status .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.category-status.active { color: #16a34a; }
.category-status.active .dot { background: #22c55e; }

.category-status.inactive { color: #6b7280; }
.category-status.inactive .dot { background: #9ca3af; }

/* Add New Card */
.category-card-add {
    background: #fafafa;
    border: 2px dashed #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 280px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.category-card-add:hover {
    border-color: #7BC043;
    background: rgba(123, 192, 67, 0.05);
}

.category-card-add .add-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #94a3b8;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}

.category-card-add:hover .add-icon {
    color: #7BC043;
    transform: scale(1.1);
}

.category-card-add h4 {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.category-card-add:hover h4 {
    color: #7BC043;
}

.category-card-add p {
    font-size: 13px;
    color: #94a3b8;
    margin: 0;
}

/* Pagination */
.category-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.category-pagination-info {
    font-size: 14px;
    color: #64748b;
}

.category-pagination-info strong {
    color: #1e293b;
    font-weight: 600;
}

.category-pagination-btns {
    display: flex;
    gap: 8px;
}

.category-page-btn {
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.category-page-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
}

.category-page-btn.primary {
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    border-color: #7BC043;
    color: white;
}

.category-page-btn.primary:hover {
    box-shadow: 0 4px 12px rgba(123, 192, 67, 0.3);
}

.category-page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<div class="category-page">
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="admin-breadcrumb" style="margin-bottom: 8px;">
            <a href="<?= BASE_URL ?>/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Kho hàng</span>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span class="current">Quản lý danh mục</span>
        </div>
        
        <?php include __DIR__ . '/components/warehouse_tabs.php'; ?>
        
        <!-- Page Header -->
        <div class="category-header">
            <div class="category-header-text">
                <h1>Danh Mục Sản Phẩm</h1>
                <p>Quản lý và tổ chức danh mục hàng hóa của siêu thị. Cập nhật thông tin để tối ưu hóa tìm kiếm.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/category-add" class="btn-add-category">
                <i class="fas fa-plus"></i>
                <span>Thêm danh mục</span>
            </a>
        </div>
        
        <!-- Toolbar -->
        <div class="category-toolbar">
            <form method="GET" class="category-search">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" placeholder="Tìm kiếm danh mục..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
            </form>
            <button class="category-filter-btn" title="Lọc">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        
        <!-- Card Grid -->
        <div class="category-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $index => $cat): ?>
                    <?php 
                    $iconConfig = getCategoryIcon($cat['Ten_danh_muc']);
                    $isActive = ($cat['Trang_thai'] ?? 'active') === 'active';
                    $productCount = $cat['So_san_pham'] ?? $cat['product_count'] ?? 0;
                    ?>
                    <div class="category-card <?= $isActive ? '' : 'inactive' ?>">
                        <div class="category-card-header">
                            <div class="category-icon <?= $iconConfig[1] ?>">
                                <i class="fas <?= $iconConfig[0] ?>"></i>
                            </div>
                            <div class="category-actions">
                                <a href="<?= BASE_URL ?>/admin/category-edit/<?= $cat['ID_danh_muc'] ?>" class="category-action-btn edit" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteCategory(<?= $cat['ID_danh_muc'] ?>)" class="category-action-btn delete" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <h3 class="category-card-title"><?= htmlspecialchars($cat['Ten_danh_muc']) ?></h3>
                        <p class="category-card-desc"><?= htmlspecialchars($cat['Mo_ta'] ?? 'Chưa có mô tả') ?></p>
                        
                        <div class="category-card-footer">
                            <div>
                                <span class="category-product-count"><?= number_format($productCount) ?> SP</span>
                                <span class="category-code">#CAT-<?= str_pad($cat['ID_danh_muc'], 3, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="category-status <?= $isActive ? 'active' : 'inactive' ?>">
                                <span class="dot"></span>
                                <?= $isActive ? 'Hoạt động' : 'Tạm ẩn' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Add New Card -->
            <a href="<?= BASE_URL ?>/admin/category-add" class="category-card-add">
                <div class="add-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <h4>Tạo danh mục mới</h4>
                <p>Thêm danh mục sản phẩm vào kho</p>
            </a>
        </div>
        
        <!-- Pagination -->
        <?php if (($pagination['last_page'] ?? 1) > 1): ?>
        <div class="category-pagination">
            <span class="category-pagination-info">
                Hiển thị <strong><?= count($categories) ?></strong> / <?= $pagination['total'] ?? 0 ?> danh mục
            </span>
            <div class="category-pagination-btns">
                <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                    <a href="?page=<?= ($pagination['current_page'] ?? 1) - 1 ?>" class="category-page-btn">Trước</a>
                <?php else: ?>
                    <button class="category-page-btn" disabled>Trước</button>
                <?php endif; ?>
                
                <?php if (($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1)): ?>
                    <a href="?page=<?= ($pagination['current_page'] ?? 1) + 1 ?>" class="category-page-btn primary">Sau</a>
                <?php else: ?>
                    <button class="category-page-btn primary" disabled>Sau</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteCategory(id) {
    if (!confirm('Xóa danh mục này?\n\nLưu ý: Không thể xóa danh mục có sản phẩm hoặc danh mục con.')) return;
    
    const formData = new FormData();
    formData.append('category_id', id);
    formData.append('csrf_token', csrfToken);
    
    fetch(baseUrl + '/public/admin/category-delete', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Đã xóa danh mục', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Không thể xóa', 'error');
        }
    });
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>