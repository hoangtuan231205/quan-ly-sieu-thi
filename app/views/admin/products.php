<?php
/**
 * =============================================================================
 * ADMIN - QUẢN LÝ SẢN PHẨM (PRODUCT MANAGEMENT)
 * =============================================================================
 * 
 * View: admin/products.php
 * Giao diện quản lý sản phẩm - Redesign theo template mới
 * Theme: #7BC043 (Lime Green) + #2D3657 (Navy)
 */

// Nhận dữ liệu từ controller
$products = $data['products'] ?? [];
$categories = $data['categories'] ?? [];
$filters = $data['filters'] ?? [];
$pagination = $data['pagination'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';
$total_products = $data['total_products'] ?? 0;

// Helper: Render category options với tree structure
function renderCategoryOptions(array $cats, $selected = '') {
    foreach ($cats as $cat) {
        if (!empty($cat['children'])) {
            echo '<optgroup label="📁 ' . htmlspecialchars($cat['Ten_danh_muc']) . '">';
            foreach ($cat['children'] as $child) {
                $sel = ($selected !== '' && $selected == $child['ID_danh_muc']) ? 'selected' : '';
                echo '<option value="' . $child['ID_danh_muc'] . '" ' . $sel . '>';
                echo '&nbsp;&nbsp;&nbsp;📄 ' . htmlspecialchars($child['Ten_danh_muc']);
                echo '</option>';
            }
            echo '</optgroup>';
        } else {
            $sel = ($selected !== '' && $selected == $cat['ID_danh_muc']) ? 'selected' : '';
            echo '<option value="' . $cat['ID_danh_muc'] . '" ' . $sel . '>';
            echo '📦 ' . htmlspecialchars($cat['Ten_danh_muc']);
            echo '</option>';
        }
    }
}

// Helper: Get stock status với 3 loại badge
function getStockStatus($quantity) {
    if ($quantity <= 0) {
        return ['class' => 'status-out', 'text' => 'Hết hàng', 'icon' => 'bg-slate-400'];
    }
    if ($quantity < 30) {
        return ['class' => 'status-low', 'text' => 'Sắp hết', 'icon' => 'bg-amber-500'];
    }
    return ['class' => 'status-in', 'text' => 'Còn hàng', 'icon' => 'bg-emerald-500'];
}
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<style>
/* ===== ADMIN PRODUCTS PAGE STYLES ===== */

/* Page Wrapper */
.admin-products-wrapper {
    background: #f7f7f7;
    min-height: 80vh;
    padding-bottom: 60px;
}

.products-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 20px 0px 50px;
}

/* Breadcrumb */
.breadcrumb-section {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 20px 0px 50px;
    font-size: 14px;
    color: #6b7280;
}
.breadcrumb-section a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}
.breadcrumb-section a:hover {
    color: var(--color-woodland, #496C2C);
}
.breadcrumb-section span {
    margin: 0 8px;
}

/* Page Header with Actions */
.page-header-actions {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.page-header-text {
    flex: 1;
}

.page-title-custom {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.page-subtitle-custom {
    font-size: 14px;
    color: #6b7280;
}

.header-action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-action-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-action-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-action-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--color-woodland, #496C2C);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(73, 108, 44, 0.3);
}

.btn-action-primary:hover {
    background: #3a5623;
    color: white;
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    position: relative;
    min-width: 280px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.search-input {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.search-input:focus {
    border-color: var(--color-woodland, #496C2C);
}

.filter-select {
    padding: 12px 40px 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1a1a;
    background: white;
    cursor: pointer;
    outline: none;
    min-width: 160px;
}

.filter-btn {
    padding: 12px 16px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    color: #374151;
}

.filter-btn:hover {
    background: #e5e7eb;
}

.clear-filters {
    color: var(--color-woodland, #496C2C);
    font-size: 14px;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.clear-filters:hover {
    text-decoration: underline;
}

/* Products Table */
.products-table-wrapper {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.products-table {
    width: 100%;
    border-collapse: collapse;
}

.products-table thead {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.products-table th {
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.products-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.products-table tbody tr:hover {
    background: #f9fafb;
}

.products-table tbody tr:last-child {
    border-bottom: none;
}

.products-table td {
    padding: 16px;
    font-size: 14px;
    color: #1a1a1a;
    vertical-align: middle;
}

/* Product Image */
.product-image-cell {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
}

.product-image-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

/* Product Name & SKU */
.product-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
}

.product-sku {
    font-size: 12px;
    color: #9ca3af;
}

/* Category Badge */
.category-badge {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    background: #f3f4f6;
    color: #4b5563;
}

/* Price */
.product-price {
    font-weight: 600;
    color: #1a1a1a;
}

/* Stock Status Badges */
.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.stock-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-in {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.status-in .dot { background: #10b981; }

.status-low {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.status-low .dot { background: #f59e0b; }

.status-out {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}
.status-out .dot { background: #9ca3af; }

/* Sale Status Badge */
.sale-badge {
    display: inline-flex;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.sale-active {
    background: #047857;
    color: white;
}

.sale-inactive {
    background: #6b7280;
    color: white;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.action-btn-edit {
    background: #fef3c7;
    color: #d97706;
}

.action-btn-edit:hover {
    background: #fde68a;
}

.action-btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.action-btn-delete:hover {
    background: #fecaca;
}

/* Pagination */
.pagination-section {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
    flex-wrap: wrap;
    gap: 16px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.pagination-buttons {
    display: flex;
    gap: 6px;
}

.page-btn {
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.page-btn:hover {
    background: #f3f4f6;
    border-color: var(--color-woodland, #496C2C);
}

.page-btn.active {
    background: var(--color-woodland, #496C2C);
    color: white;
    border-color: var(--color-woodland, #496C2C);
}

.page-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

/* Empty State */
.empty-state {
    padding: 60px 20px;
    text-align: center;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

/* Responsive */
@media (max-width: 1100px) {
    .products-table th:nth-child(1),
    .products-table td:nth-child(1) {
        display: none;
    }
}

@media (max-width: 768px) {
    .page-header-actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-action-buttons {
        width: 100%;
    }
    
    .filters-section {
        flex-direction: column;
    }
    
    .search-box {
        width: 100%;
        min-width: unset;
    }
    
    .filter-select {
        width: 100%;
    }
}

/* Modal Styles - Keep existing */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    z-index: 1000; /* Fixed: was 50, now above sidebar (100) */
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Hidden utility class */
.hidden {
    display: none !important;
}
</style>

<div class="admin-products-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <a href="<?= BASE_URL ?>/public/">Trang chủ</a>
        <span>›</span>
        <span>Quản lý sản phẩm</span>
    </div>

    <?php include __DIR__ . '/components/warehouse_tabs.php'; ?>

    <div class="products-container">
        <!-- Page Header with Actions -->
        <div class="page-header-actions">
            <div class="page-header-text">
                <h1 class="page-title-custom">Quản lý Sản phẩm</h1>
                <p class="page-subtitle-custom">Tổng cộng: <strong><?= number_format($total_products) ?></strong> sản phẩm</p>
            </div>
            <div class="header-action-buttons">
                <button class="btn-action-secondary" onclick="openImportModal()">
                    <i class="fas fa-file-upload"></i>
                    <span>Import Excel</span>
                </button>
                <a href="<?= BASE_URL ?>/public/admin/export-products" class="btn-action-secondary">
                    <i class="fas fa-file-download"></i>
                    <span>Export Excel</span>
                </a>
                <button onclick="openAddModal()" class="btn-action-primary">
                    <i class="fas fa-plus"></i>
                    <span>Thêm sản phẩm</span>
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <form method="GET" action="<?= BASE_URL ?>/public/admin/products" class="filters-section">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Tìm theo tên, MSP..."
                    value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>"
                >
            </div>
            
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php renderCategoryOptions($categories, $filters['category_id'] ?? ''); ?>
            </select>
            
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="active" <?= (($filters['status'] ?? '') == 'active') ? 'selected' : '' ?>>Đang bán</option>
                <option value="inactive" <?= (($filters['status'] ?? '') == 'inactive') ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
            
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i>
            </button>
            
            <a href="<?= BASE_URL ?>/public/admin/products" class="clear-filters">Xóa bộ lọc</a>
        </form>

        <!-- Products Table -->
        <div class="products-table-wrapper">
            <div style="overflow-x: auto;">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">
                                <input type="checkbox" id="selectAll" style="cursor: pointer;">
                            </th>
                            <th style="width: 70px;">Ảnh</th>
                            <th style="min-width: 200px;">Tên sản phẩm & MSP</th>
                            <th>Danh mục</th>
                            <th style="text-align: right;">Giá bán</th>
                            <th style="text-align: center;">Tồn kho</th>
                            <th>Trạng thái</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <p>Không có sản phẩm nào</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?php $stockStatus = getStockStatus($product['So_luong_ton']); ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="product-checkbox" value="<?= $product['ID_sp'] ?>">
                                    </td>
                                    <td>
                                        <?php if ($product['Hinh_anh']): ?>
                                            <img src="<?= asset('img/products/' . $product['Hinh_anh']) ?>" 
                                                 alt="<?= htmlspecialchars($product['Ten']) ?>"
                                                 class="product-image-cell"
                                                 onerror="this.src='<?= asset('img/placeholder-product.png') ?>'">
                                        <?php else: ?>
                                            <div class="product-image-placeholder">📦</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="product-name"><?= htmlspecialchars($product['Ten']) ?></div>
                                        <div class="product-sku">MSP: <?= htmlspecialchars($product['Ma_hien_thi'] ?? 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <span class="category-badge"><?= htmlspecialchars($product['Ten_danh_muc'] ?? 'Chưa phân loại') ?></span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="product-price"><?= number_format($product['Gia_tien']) ?>₫</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="stock-badge <?= $stockStatus['class'] ?>">
                                            <span class="dot"></span>
                                            <?= $product['So_luong_ton'] ?> - <?= $stockStatus['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($product['Trang_thai'] == 'active'): ?>
                                            <span class="sale-badge sale-active">Đang bán</span>
                                        <?php else: ?>
                                            <span class="sale-badge sale-inactive">Ngừng bán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($product)) ?>)" 
                                                    class="action-btn action-btn-edit" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="openDeleteModal(<?= $product['ID_sp'] ?>, '<?= htmlspecialchars($product['Ten']) ?>', '<?= htmlspecialchars($product['Ma_hien_thi']) ?>')" 
                                                    class="action-btn action-btn-delete" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination) && isset($pagination['total']) && $pagination['total'] > 0): ?>
            <div class="pagination-section">
                <div class="pagination-info">
                    Hiển thị <strong><?= $pagination['from'] ?? 1 ?></strong> đến <strong><?= $pagination['to'] ?? 0 ?></strong> 
                    trong tổng <strong><?= number_format($pagination['total']) ?></strong> sản phẩm
                </div>
                <div class="pagination-buttons">
                    <?php 
                    $queryParams = [];
                    if (!empty($filters['category_id'])) $queryParams['category'] = $filters['category_id'];
                    if (!empty($filters['keyword'])) $queryParams['search'] = $filters['keyword'];
                    if (!empty($filters['status'])) $queryParams['status'] = $filters['status'];
                    ?>
                    
                    <!-- Previous -->
                    <a href="?page=<?= max(1, $pagination['current_page'] - 1) ?>&<?= http_build_query($queryParams) ?>" 
                       class="page-btn <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    
                    <!-- Page Numbers -->
                    <?php 
                    $lastPage = $pagination['last_page'] ?? 1;
                    $currentPage = $pagination['current_page'] ?? 1;
                    for ($i = 1; $i <= min($lastPage, 5); $i++): 
                    ?>
                        <a href="?page=<?= $i ?>&<?= http_build_query($queryParams) ?>" 
                           class="page-btn <?= $i == $currentPage ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($lastPage > 5): ?>
                        <span class="page-btn disabled">...</span>
                        <a href="?page=<?= $lastPage ?>&<?= http_build_query($queryParams) ?>" 
                           class="page-btn <?= $lastPage == $currentPage ? 'active' : '' ?>">
                            <?= $lastPage ?>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Next -->
                    <a href="?page=<?= min($lastPage, $pagination['current_page'] + 1) ?>&<?= http_build_query($queryParams) ?>" 
                       class="page-btn <?= $pagination['current_page'] >= $lastPage ? 'disabled' : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="modal-title">Thêm sản phẩm mới</h3>
            <button onclick="closeProductModal()" class="modal-close">&times;</button>
        </div>

        <form id="productForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="product_id" name="product_id" value="">
            
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Left Column -->
                    <div>
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Thông tin chung</h4>
                        
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">
                                Tên sản phẩm <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                   placeholder="Ví dụ: Táo Fuji Mỹ">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Mã SKU</label>
                                <input type="text" id="sku" name="sku" 
                                       style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                       placeholder="VD: SP-00123">
                            </div>
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Xuất xứ</label>
                                <input type="text" id="origin" name="origin" 
                                       style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                       placeholder="VD: Việt Nam">
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Mô tả</label>
                            <textarea id="description" name="description" rows="4" 
                                      style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical;"
                                      placeholder="Nhập mô tả sản phẩm..."></textarea>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Hình ảnh</label>
                            <div style="border: 2px dashed #e5e7eb; border-radius: 8px; padding: 24px; text-align: center;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #9ca3af; margin-bottom: 8px;"></i>
                                <p style="margin-bottom: 8px; color: #6b7280;">Kéo thả hoặc click để chọn ảnh</p>
                                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" 
                                       style="display: none;">
                                <label for="image" style="display: inline-block; padding: 8px 16px; background: white; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                    Chọn file
                                </label>
                            </div>
                            <div id="currentImage" style="margin-top: 12px; display: none;">
                                <img id="currentImagePreview" src="" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Phân loại & Giá</h4>
                        
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Trạng thái</label>
                            <select id="status" name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                <option value="active">Đang bán</option>
                                <option value="inactive">Ngừng bán</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">
                                Danh mục <span style="color: #dc2626;">*</span>
                            </label>
                            <select id="category_id" name="category_id" required style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                <option value="">Chọn danh mục</option>
                                <?php renderCategoryOptions($categories); ?>
                            </select>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Đơn vị tính</label>
                            <input type="text" id="unit" name="unit" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                   placeholder="VD: Kg, Hộp, Cái">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">
                                Giá bán (VNĐ) <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="number" id="price" name="price" required min="0" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                   placeholder="0">
                        </div>

                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Tồn kho ban đầu</label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <button type="button" onclick="decrementStock()" 
                                        style="width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; font-size: 18px;">−</button>
                                <input type="number" id="stock" name="stock" value="0" min="0" 
                                       style="flex: 1; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; text-align: center; font-size: 14px;">
                                <button type="button" onclick="incrementStock()" 
                                        style="width: 40px; height: 40px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; font-size: 18px;">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeProductModal()" class="btn-action-secondary">Hủy</button>
                <button type="submit" class="btn-action-primary">
                    <i class="fas fa-save"></i> Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 400px;">
        <div style="padding: 32px; text-align: center;">
            <div style="width: 64px; height: 64px; border: 4px solid #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-exclamation" style="font-size: 24px; color: #f59e0b;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Xác nhận xóa?</h3>
            <p style="color: #6b7280; margin-bottom: 4px;">Bạn có chắc muốn xóa sản phẩm:</p>
            <p style="font-weight: 600;" id="deleteProductName"></p>
            <p style="font-size: 14px; color: #9ca3af;" id="deleteProductCode"></p>
        </div>

        <div style="display: flex; gap: 12px; padding: 16px 24px; border-top: 1px solid #e5e7eb;">
            <button type="button" onclick="closeDeleteModal()" 
                    style="flex: 1; padding: 12px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                Hủy bỏ
            </button>
            <button type="button" onclick="submitDelete()" 
                    style="flex: 1; padding: 12px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                Xóa ngay
            </button>
        </div>
        <input type="hidden" id="delete_product_id">
    </div>
</div>

<!-- Import Excel Modal -->
<div id="importModal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 class="modal-title">Import sản phẩm từ Excel</h3>
            <button onclick="closeImportModal()" class="modal-close">&times;</button>
        </div>
        
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <!-- Hướng dẫn định dạng - Bảng giống Excel -->
            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <h4 style="color: #92400e; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-excel"></i> Ví dụ định dạng file Excel
                </h4>
                <p style="font-size: 13px; color: #78350f; margin-bottom: 12px;">
                    File Excel phải có định dạng như bảng dưới đây (dòng 1 là header, dòng 2+ là dữ liệu):
                </p>
                
                <!-- Bảng giống Excel -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 12px; border-collapse: collapse; background: white; border: 2px solid #496C2C;">
                        <!-- Header cột A, B, C... -->
                        <thead>
                            <tr style="background: #e5e7eb;">
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; width: 30px; background: #d1d5db;"></th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">A</th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">B</th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">C</th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">D</th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">E</th>
                                <th style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; font-weight: 600;">F</th>
                            </tr>
                            <!-- Header row (dòng 1) -->
                            <tr style="background: #496C2C; color: white; font-weight: 600;">
                                <td style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; color: #374151;">1</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">Tên sản phẩm</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">ID danh mục</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">Giá tiền</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">Số lượng</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">Đơn vị</td>
                                <td style="border: 1px solid #374151; padding: 6px 8px;">Xuất xứ</td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dòng ví dụ 1 -->
                            <tr>
                                <td style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; color: #374151;">2</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Sữa tiệt trùng Meiji 946ml</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: center;">2</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: right;">84500</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: center;">100</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Hộp</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Nhật Bản</td>
                            </tr>
                            <!-- Dòng ví dụ 2 -->
                            <tr>
                                <td style="border: 1px solid #9ca3af; padding: 4px 8px; text-align: center; background: #d1d5db; color: #374151;">3</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Chin-Su Tương ớt 250g</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: center;">30</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: right;">18300</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; text-align: center;">300</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Chai</td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px;">Việt Nam</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <p style="font-size: 12px; color: #92400e; margin-top: 10px;">
                    <strong>Lưu ý:</strong> Cột A-D là bắt buộc. Giá tiền chỉ nhập số (không có dấu phẩy).
                </p>
            </div>
            
            <!-- Danh sách danh mục - Sắp xếp theo ID -->
            <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <h4 style="color: #166534; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-folder"></i> Danh sách danh mục (ID để nhập vào cột B)
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; font-size: 13px;">
                    <?php 
                    // Flatten categories and sort by ID
                    function flattenCategories($cats, &$result = []) {
                        foreach ($cats as $cat) {
                            $result[] = $cat;
                            if (!empty($cat['children'])) {
                                flattenCategories($cat['children'], $result);
                            }
                        }
                        return $result;
                    }
                    
                    $flatCats = flattenCategories($categories);
                    usort($flatCats, function($a, $b) {
                        return $a['ID_danh_muc'] - $b['ID_danh_muc'];
                    });
                    
                    foreach ($flatCats as $cat) {
                        echo '<div style="background: white; padding: 6px 10px; border-radius: 4px; border: 1px solid #e5e7eb;">';
                        echo '<strong style="color: #496C2C; min-width: 25px; display: inline-block;">' . $cat['ID_danh_muc'] . '</strong> - ';
                        echo htmlspecialchars($cat['Ten_danh_muc']);
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Upload file -->
            <form id="importForm" method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/public/admin/product-import">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div style="border: 2px dashed #e5e7eb; border-radius: 8px; padding: 24px; text-align: center;">
                    <i class="fas fa-file-excel" style="font-size: 40px; color: #16a34a; margin-bottom: 12px;"></i>
                    <p style="margin-bottom: 8px; font-weight: 500;">Chọn file Excel (.xlsx, .xls)</p>
                    <input type="file" id="importFile" name="import_file" accept=".xlsx,.xls" style="display: none;">
                    <label for="importFile" class="btn-action-secondary" style="display: inline-flex; cursor: pointer;">
                        <i class="fas fa-upload"></i> Chọn file
                    </label>
                    <div id="importFileName" style="margin-top: 12px; font-size: 14px; color: #374151; font-weight: 500;"></div>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                    <button type="button" onclick="closeImportModal()" class="btn-action-secondary">Hủy</button>
                    <button type="submit" class="btn-action-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="notification" class="fixed top-4 right-4 z-50 hidden">
    <div style="background: #16a34a; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('currentImage').style.display = 'none';
    document.getElementById('productForm').action = '<?= BASE_URL ?>/public/admin/product-add';
    document.getElementById('productModal').classList.remove('hidden');
}

function openEditModal(product) {
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa sản phẩm';
    document.getElementById('product_id').value = product.ID_sp;
    document.getElementById('name').value = product.Ten || '';
    document.getElementById('sku').value = product.Ma_hien_thi || '';
    document.getElementById('description').value = product.Mo_ta || '';
    document.getElementById('category_id').value = product.ID_danh_muc || '';
    document.getElementById('price').value = product.Gia_tien || 0;
    document.getElementById('stock').value = product.So_luong_ton || 0;
    document.getElementById('status').value = product.Trang_thai || 'active';
    document.getElementById('unit').value = product.Don_vi_tinh || '';
    document.getElementById('origin').value = product.Xuat_xu || '';
    
    if (product.Hinh_anh) {
        document.getElementById('currentImagePreview').src = '<?= asset('img/products/') ?>' + product.Hinh_anh;
        document.getElementById('currentImage').style.display = 'block';
    } else {
        document.getElementById('currentImage').style.display = 'none';
    }
    
    document.getElementById('productForm').action = '<?= BASE_URL ?>/public/admin/product-edit/' + product.ID_sp;
    document.getElementById('productModal').classList.remove('hidden');
}

function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
}

function openDeleteModal(id, name, code) {
    document.getElementById('delete_product_id').value = id;
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteProductCode').textContent = 'SKU: ' + code;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function submitDelete() {
    const productId = document.getElementById('delete_product_id').value;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/public/admin/product-delete';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'product_id';
    idInput.value = productId;
    form.appendChild(idInput);
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?= $csrf_token ?>';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
}

function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

// Stock increment/decrement
function incrementStock() {
    const input = document.getElementById('stock');
    input.value = parseInt(input.value || 0) + 1;
}

function decrementStock() {
    const input = document.getElementById('stock');
    const val = parseInt(input.value || 0);
    if (val > 0) input.value = val - 1;
}

// Import file name display
document.getElementById('importFile')?.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || '';
    document.getElementById('importFileName').textContent = fileName;
});

// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Notification
function showNotification(message) {
    const notification = document.getElementById('notification');
    document.getElementById('notification-message').textContent = message;
    notification.classList.remove('hidden');
    setTimeout(() => notification.classList.add('hidden'), 3000);
}

// Close modals on outside click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});

<?php if (isset($_SESSION['success'])): ?>
showNotification('<?= $_SESSION['success'] ?>');
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>