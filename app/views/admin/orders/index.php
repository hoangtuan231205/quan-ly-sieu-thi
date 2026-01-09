<?php
/**
 * =============================================================================
 * ADMIN - QUẢN LÝ GIAO HÀNG (DELIVERY MANAGEMENT)
 * =============================================================================
 * 
 * View: admin/orders/index.php
 * Giao diện quản lý trạng thái đơn hàng (Delivery Tracking)
 * Styles theo yêu cầu user (giống template cung cấp) + Theme màu project
 */

// Helper status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'dang_xu_ly': return 'status-pending';
        case 'dang_giao': return 'status-shipping';
        case 'da_giao': return 'status-delivered';
        case 'huy': return 'status-cancelled';
        default: return '';
    }
}

// Helper status text
function getStatusLabel($status) {
    switch ($status) {
        case 'dang_xu_ly': return 'Đang xử lý';
        case 'dang_giao': return 'Đang giao';
        case 'da_giao': return 'Đã giao';
        case 'huy': return 'Trả hàng / Hủy đơn';
        default: return $status;
    }
}
?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
/* ===== PAGE STYLE (USER REQUESTED TEMPLATE) ===== */

/* ===== PAGE STYLE (USER REQUESTED TEMPLATE) ===== */
/* ===== PAGE STYLE ===== */
.admin-page-wrapper {
    background: #f7f7f7;
    min-height: 80vh;
    padding-bottom: 60px;
}

.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 20px;
}

/* Breadcrumb */
.breadcrumb-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 20px 0;
    font-size: 14px;
    color: #6b7280;
}
.breadcrumb-section a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}
.breadcrumb-section a:hover {
    color: #496C2C;
}
.breadcrumb-section span {
    margin: 0 8px;
}

/* Page Header */
.page-header-custom {
    margin-bottom: 24px;
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

.header-action-buttons {
    display: flex;
    gap: 12px;
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
    text-decoration: none;
}

/* Stat Cards Grid */
.stat-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    margin: 0 0 8px 0;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 4px 0;
    line-height: 1;
}

.stat-note {
    font-size: 12px;
    font-weight: 500;
    margin: 0;
}

.stat-note.pending-note { color: #2563eb; }
.stat-note.shipping-note { color: #ea580c; }
.stat-note.delivered-note { color: #16a34a; }
.stat-note.cancelled-note { color: #dc2626; }
.stat-note.positive-change { color: #16a34a; }
.stat-note.negative-change { color: #dc2626; }

.stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.stat-icon-wrap.pending-icon {
    background: #eff6ff;
    color: #2563eb;
}

.stat-icon-wrap.shipping-icon {
    background: #fff7ed;
    color: #ea580c;
}

.stat-icon-wrap.delivered-icon {
    background: #f0fdf4;
    color: #16a34a;
}

.stat-icon-wrap.cancelled-icon {
    background: #fef2f2;
    color: #dc2626;
}

/* Responsive Stat Cards */
@media (max-width: 1100px) {
    .stat-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .stat-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header-actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-action-buttons {
        width: 100%;
    }
    
    .btn-action-secondary,
    .btn-action-primary {
        flex: 1;
        justify-content: center;
    }
}

/* Filters */
.filters-custom {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap; /* Responsive */
}

.search-box-custom {
    flex: 1;
    position: relative;
    min-width: 300px;
}

.search-icon-custom {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.search-input-custom {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.search-input-custom:focus {
    border-color: var(--color-woodland); /* Theme color */
}

.filter-select-custom {
    padding: 12px 40px 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1a1a;
    background: white;
    cursor: pointer;
    outline: none;
    min-width: 180px;
}

.filter-btn-custom {
    padding: 12px 16px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn-custom:hover {
    background: #e5e7eb;
}

/* Orders Table */
.orders-table-custom {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden; /* For rounded corners */
}

.table-header-custom {
    display: grid;
    /* Grid layout matching template: 50px check, 180px code, 1fr customer, 280px product, 140px total, 160px status, 80px action */
    grid-template-columns: 50px 180px 250px 1fr 140px 160px 80px;
    padding: 16px 24px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    gap: 16px;
}

.table-row-custom {
    display: grid;
    grid-template-columns: 50px 180px 250px 1fr 140px 160px 80px;
    padding: 20px 24px;
    border-bottom: 1px solid #f3f4f6;
    align-items: center;
    transition: background 0.2s;
    gap: 16px;
}

.table-row-custom:hover {
    background: #f9fafb;
}

.table-row-custom:last-child {
    border-bottom: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--color-woodland); /* Theme color */
}

.order-id-custom {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.order-code-custom {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-woodland); /* Theme color */
    cursor: pointer;
}

.order-code-custom:hover {
    text-decoration: underline;
}

.order-time-custom {
    font-size: 12px;
    color: #9ca3af;
}

.customer-info-custom {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.customer-name-custom {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.customer-phone-custom, .customer-address-custom {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-info-custom {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image-custom {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    background: #f3f4f6;
    flex-shrink: 0;
}

.product-details-custom {
    flex: 1;
    min-width: 0;
}

.product-name-custom {
    font-size: 14px;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-count-custom {
    font-size: 12px;
    color: #6b7280;
}

.order-total-custom {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    text-align: right;
}

/* STATUS BADGES - THEME COLORS */
.status-badge-custom {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    border: none; /* No border for badge */
}

/* Đang xử lý: pending (fef3c7 / 92400e) -> Theme: Light Yellow */
.status-pending {
    background: #fffbeb;
    color: #b45309;
}

/* Đang giao: shipping (dbeafe / 1e40af) -> Theme: Light Blue */
.status-shipping {
    background: #eff6ff;
    color: #1d4ed8;
}

/* Đã giao: delivered (d1fae5 / 065f46) -> Theme: Light Green */
.status-delivered {
    background: #ecfdf5;
    color: #047857;
}

/* Hủy: cancelled (fee2e2 / 991b1b) -> Theme: Light Red */
.status-cancelled {
    background: #fef2f2;
    color: #b91c1c;
}

.actions-custom {
    display: flex;
    justify-content: center;
    align-items: center;
}

.action-btn-custom {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #f3f4f6;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 18px;
    text-decoration: none; /* For link */
    transition: all 0.2s;
}

.action-btn-custom:hover {
    background: #e5e7eb;
    color: var(--color-woodland);
}

/* Pagination */
.pagination-custom {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
}

.page-info-custom {
    font-size: 13px;
    color: #6b7280;
    margin-right: 16px;
}

.page-btn-custom {
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
    text-decoration: none; /* For link */
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-btn-custom:hover {
    background: #f3f4f6;
    border-color: var(--color-woodland);
}

.page-btn-custom.active {
    background: var(--color-woodland);
    color: white;
    border-color: var(--color-woodland);
}

.page-btn-custom.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
}

/* Responsive */
@media (max-width: 1200px) {
    .table-header-custom, .table-row-custom {
        grid-template-columns: 50px 150px 200px 1fr 120px 140px 80px; 
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .table-header-custom {
        display: none; /* Hide header on mobile/tablet */
    }

    .table-row-custom {
        display: block; /* Stack content */
        position: relative;
    }
    
    .table-row-custom > div {
        margin-bottom: 12px;
    }

    .order-id-custom {
        flex-direction: row;
        justify-content: space-between;
        margin-bottom: 16px !important;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
    }
    
    .checkbox-custom {
        position: absolute;
        top: 20px;
        left: 0; 
        display: none; /* Hide checkbox on mobile for now */
    }

    .actions-custom {
        justify-content: flex-end;
    }
}
</style>

<div class="admin-page-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <a href="<?= BASE_URL ?>/">Trang chủ</a>
        <span>›</span>
        <span>Quản lý giao hàng</span>
    </div>

    <div class="admin-container">
        <!-- Page Header with Actions -->
        <div class="page-header-actions">
            <div class="page-header-text">
                <h1 class="page-title-custom">Theo Dõi Vận Chuyển</h1>
                <p class="page-subtitle-custom">Quản lý trạng thái đơn hàng và điều phối giao nhận.</p>
            </div>
            <div class="header-action-buttons">
                <button class="btn-action-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    <span>In danh sách</span>
                </button>
                <a href="<?= BASE_URL ?>/admin/exportOrders" class="btn-action-primary">
                    <i class="fas fa-download"></i>
                    <span>Xuất báo cáo</span>
                </a>
            </div>
        </div>

        <!-- 4 Stat Cards -->
        <div class="stat-cards-grid">
            <!-- Chờ Xử Lý -->
            <div class="stat-card-item">
                <div class="stat-info">
                    <p class="stat-label">Chờ Xử Lý</p>
                    <p class="stat-number"><?= $status_stats['pending'] ?? 0 ?></p>
                    <p class="stat-note pending-note">Đơn chờ xác nhận</p>
                </div>
                <div class="stat-icon-wrap pending-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            
            <!-- Đang Giao -->
            <div class="stat-card-item">
                <div class="stat-info">
                    <p class="stat-label">Đang Giao</p>
                    <p class="stat-number"><?= $status_stats['shipping'] ?? 0 ?></p>
                    <p class="stat-note shipping-note">Shipper đang di chuyển</p>
                </div>
                <div class="stat-icon-wrap shipping-icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
            
            <!-- Đã Giao -->
            <div class="stat-card-item">
                <div class="stat-info">
                    <p class="stat-label">Đã Giao</p>
                    <p class="stat-number"><?= $status_stats['delivered'] ?? 0 ?></p>
                    <?php 
                    $percentChange = $status_stats['percent_change'] ?? 0;
                    $changeClass = $percentChange >= 0 ? 'positive-change' : 'negative-change';
                    $changeIcon = $percentChange >= 0 ? 'trending_up' : 'trending_down';
                    $changeSign = $percentChange >= 0 ? '+' : '';
                    ?>
                    <p class="stat-note delivered-note <?= $changeClass ?>">
                        <i class="fas fa-arrow-<?= $percentChange >= 0 ? 'up' : 'down' ?>"></i> 
                        <?= $changeSign ?><?= $percentChange ?>% hôm nay
                    </p>
                </div>
                <div class="stat-icon-wrap delivered-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- Trả Hàng / Hủy -->
            <div class="stat-card-item">
                <div class="stat-info">
                    <p class="stat-label">Trả Hàng / Hủy</p>
                    <p class="stat-number"><?= $status_stats['cancelled'] ?? 0 ?></p>
                    <p class="stat-note cancelled-note">Cần kiểm tra</p>
                </div>
                <div class="stat-icon-wrap cancelled-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
            </div>
        </div>

                <!-- Filters Form -->
                <form action="" method="GET" class="filters-custom">
                    <div class="search-box-custom">
                        <span class="search-icon-custom"><i class="fas fa-search"></i></span>
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input-custom" 
                            placeholder="Tìm theo Mã đơn, Tên khách hàng hoặc SĐT..."
                            value="<?= htmlspecialchars($filters['keyword']) ?>"
                        >
                    </div>
                    
                    <select name="status" class="filter-select-custom" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="dang_xu_ly" <?= $filters['status'] == 'dang_xu_ly' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="dang_giao" <?= $filters['status'] == 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                        <option value="da_giao" <?= $filters['status'] == 'da_giao' ? 'selected' : '' ?>>Đã giao</option>
                        <option value="huy" <?= $filters['status'] == 'huy' ? 'selected' : '' ?>>Trả hàng / Hủy đơn</option>
                    </select>
                    
                    <!-- Removed Area Filter as database doesn't strictly have area column yet, can be added later -->
                    
                    <button type="submit" class="filter-btn-custom" title="Lọc">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>

                <!-- Orders Table -->
                <div class="orders-table-custom">
                    <!-- Table Header -->
                    <div class="table-header-custom">
                        <div><input type="checkbox" class="checkbox-custom" id="selectAll"></div>
                        <div>ĐƠN HÀNG</div>
                        <div>KHÁCH HÀNG</div>
                        <div>CHI TIẾT SP</div>
                        <div>TỔNG TIỀN</div>
                        <div>TRẠNG THÁI</div>
                        <div>HÀNH ĐỘNG</div>
                    </div>

                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <!-- Order Row -->
                        <div class="table-row-custom">
                            <input type="checkbox" class="checkbox-custom">
                            
                            <!-- Order ID & Time -->
                            <div class="order-id-custom">
                                <div class="order-code-custom" onclick="window.location.href='<?= BASE_URL ?>/admin/orderDetail/<?= $order['ID_dh'] ?>'">
                                    DH<?= date('Ymd', strtotime($order['Ngay_dat'])) ?><?= str_pad($order['ID_dh'], 2, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div class="order-time-custom">
                                    <?= date('H:i - d/m/Y', strtotime($order['Ngay_dat'])) ?>
                                </div>
                            </div>
                            
                            <!-- Customer Info -->
                            <div class="customer-info-custom">
                                <div class="customer-name-custom">
                                    <i class="fas fa-user-circle text-muted"></i> 
                                    <?= htmlspecialchars($order['Ten_nguoi_nhan']) ?>
                                </div>
                                <div class="customer-phone-custom">
                                    <i class="fas fa-phone-alt text-muted" style="font-size: 10px;"></i> 
                                    <?= htmlspecialchars($order['Sdt_nguoi_nhan']) ?>
                                </div>
                                <div class="customer-address-custom" title="<?= htmlspecialchars($order['Dia_chi_giao_hang']) ?>">
                                    <i class="fas fa-map-marker-alt text-muted" style="font-size: 10px;"></i> 
                                    <?= htmlspecialchars($order['Dia_chi_giao_hang']) ?>
                                </div>
                            </div>
                            
                            <!-- Product Info (First Product) -->
                            <div class="product-info-custom">
                                <?php 
                                // First item info (fetched via subquery in OrderModel)
                                $itemCount = $order['Tong_so_thuc'] ?? 0;
                                
                                // Fix Image Path: Check if image exists in assets (filenames like 1.png, 2.png)
                                // Data from DB is just filename (e.g., '1.png')
                                 $imgName = $order['Hinh_anh_dai_dien'] ?? 'no-image.png';
                                $firstItemImg = asset('img/products/' . $imgName);
                                ?>
                                <a href="<?= BASE_URL ?>/admin/orderDetail/<?= $order['ID_dh'] ?>" class="product-details-custom" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <img src="<?= $firstItemImg ?>" alt="Product" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.src='<?= asset('assets/img/products/no-image.png') ?>'">
                                    <div>
                                        <div class="product-name-custom">Sản phẩm (<?= $itemCount ?>)</div>
                                        <div class="product-count-custom">Xem chi tiết</div>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- Total -->
                            <div class="order-total-custom">
                                <?= number_format($order['Thanh_tien'], 0, ',', '.') ?>đ
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <span class="status-badge-custom <?= getStatusBadgeClass($order['Trang_thai']) ?>" 
                                      data-id="<?= $order['ID_dh'] ?>" 
                                      data-code="DH<?= date('Ymd', strtotime($order['Ngay_dat'])) ?><?= str_pad($order['ID_dh'], 2, '0', STR_PAD_LEFT) ?>"
                                      data-status="<?= $order['Trang_thai'] ?>">
                                    <?= getStatusLabel($order['Trang_thai']) ?>
                                </span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="actions-custom">
                                <!-- View Detail Button -->
                                <a href="<?= BASE_URL ?>/admin/orderDetail/<?= $order['ID_dh'] ?>" class="action-btn-custom" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>Không tìm thấy đơn hàng nào.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination-custom">
                    <span class="page-info-custom">
                        Hiển thị <?= count($orders) ?> trên tổng <?= $pagination['total_items'] ?> đơn hàng
                    </span>
                    
                    <a href="<?= "?page=" . ($pagination['current_page'] - 1) . "&" . http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                       class="page-btn-custom <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                        ‹
                    </a>
                    
                    <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $pagination['current_page'] || $i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                            <a href="<?= "?page=$i&" . http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                               class="page-btn-custom <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                            <span class="page-btn-custom disabled">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <a href="<?= "?page=" . ($pagination['current_page'] + 1) . "&" . http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                       class="page-btn-custom <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                        ›
                    </a>
                </div>
                <?php endif; ?>
    </div><!-- End container -->
</div><!-- End admin-page-wrapper -->

<!-- Include Footer nếu cần, hoặc chỉ script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Status Update Modal -->
<div id="statusModal" class="modal-custom">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3 class="modal-title-custom">Cập nhật trạng thái</h3>
            <span class="close-modal-custom">&times;</span>
        </div>
        <div class="modal-body-custom">
            <p style="margin-bottom: 15px; color: #4b5563;">Cập nhật trạng thái cho đơn hàng <strong id="modalOrderCode" style="color: var(--color-woodland);">#DH-000</strong></p>
            
            <form id="updateStatusForm">
                <input type="hidden" id="modalOrderId" name="order_id">
                
                <div class="form-group-custom">
                    <label class="radio-label-custom">
                        <input type="radio" name="status" value="dang_xu_ly">
                        <span class="radio-custom"></span>
                        <span class="status-badge-custom status-pending">Đang xử lý</span>
                    </label>
                    
                    <label class="radio-label-custom">
                        <input type="radio" name="status" value="dang_giao">
                        <span class="radio-custom"></span>
                        <span class="status-badge-custom status-shipping">Đang giao</span>
                    </label>
                    
                    <label class="radio-label-custom">
                        <input type="radio" name="status" value="da_giao">
                        <span class="radio-custom"></span>
                        <span class="status-badge-custom status-delivered">Đã giao</span>
                    </label>
                    
                    <label class="radio-label-custom">
                        <input type="radio" name="status" value="huy">
                        <span class="radio-custom"></span>
                        <span class="status-badge-custom status-cancelled">Trả hàng / Hủy đơn</span>
                    </label>
                </div>
                
                <div class="modal-footer-custom">
                    <button type="button" class="btn-cancel-custom" id="closeModalBtn">Hủy</button>
                    <button type="submit" class="btn-save-custom">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-custom {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}

.modal-content-custom {
    background-color: white;
    border-radius: 12px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header-custom {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title-custom {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
}

.close-modal-custom {
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    transition: color 0.2s;
}

.close-modal-custom:hover {
    color: #4b5563;
}

.modal-body-custom {
    padding: 24px;
}

.form-group-custom {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.radio-label-custom {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.2s;
}

.radio-label-custom:hover {
    background: #f3f4f6;
}

.radio-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}

input[type="radio"]:checked + .radio-custom {
    border-color: var(--color-woodland);
}

input[type="radio"]:checked + .radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background: var(--color-woodland);
    border-radius: 50%;
}

input[type="radio"] {
    display: none;
}

.modal-footer-custom {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.btn-cancel-custom {
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel-custom:hover {
    background: #f3f4f6;
}

.btn-save-custom {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    background: var(--color-woodland);
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-save-custom:hover {
    background: #233242; /* Darker woodland */
}

/* Ensure status text wraps if needed in modal */
.modal-body-custom .status-badge-custom {
    min-width: 120px;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('statusModal');
        const closeModalBtn = document.querySelector('.close-modal-custom');
        const cancelBtn = document.getElementById('closeModalBtn');
        const modalOrderCode = document.getElementById('modalOrderCode');
        const modalOrderId = document.getElementById('modalOrderId');
        const updateForm = document.getElementById('updateStatusForm');
        
        // Function to open modal
        window.openStatusModal = function(orderId, orderCode, currentStatus) {
            modalOrderId.value = orderId;
            modalOrderCode.textContent = '#DH-' + orderId; // Using Order ID as code based on PHP
            
            // Select current status
            const radioButton = updateForm.querySelector(`input[name="status"][value="${currentStatus}"]`);
            if (radioButton) radioButton.checked = true;
            
            modal.style.display = 'flex';
        };
        
        // Close modal logic
        function closeModal() {
            modal.style.display = 'none';
        }
        
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
        
        // Handle Form Submit (AJAX)
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('csrf_token', '<?= Session::getCsrfToken() ?>'); // Ensure CSRF token if needed, usually passed in POST
            
            // Assuming AdminController expects standard POST fields
            // URL: /public/admin/orderUpdateStatus
            
            fetch('<?= BASE_URL ?>/admin/orderUpdateStatus', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật trạng thái thành công!');
                    location.reload(); // Reload to reflect changes
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kết nối server');
            });
        });
        
        // Select All Checkbox Script
        const selectAll = document.getElementById('selectAll');
        if(selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.orders-table-custom .checkbox-custom');
                checkboxes.forEach(cb => {
                    if (cb.id !== 'selectAll') cb.checked = this.checked;
                });
            });
        }
        
        // Add click event to status badges
        document.querySelectorAll('.status-badge-custom').forEach(badge => {
            // Check if it's inside the table, not the modal
            if (badge.closest('.table-row-custom')) {
                badge.style.cursor = 'pointer';
                badge.title = 'Click để đổi trạng thái';
                badge.addEventListener('click', function() {
                    // Get data directly from attributes (Robust)
                    const orderId = this.dataset.id;
                    const orderCode = '#' + this.dataset.code;
                    const currentStatus = this.dataset.status;
                    
                    openStatusModal(orderId, orderCode, currentStatus);
                });
            }
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
