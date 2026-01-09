<?php
/**
 * ADMIN - BÁO CÁO TỔNG HỢP
 * Thiết kế giao diện hiện đại - Theme Xanh lá/Xanh đậm
 */
?>
<?php include __DIR__ . '/layouts/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/admin-modern.css') ?>">

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Trung tâm Báo cáo</h1>
        <p class="page-subtitle">Phân tích và thống kê hoạt động kinh doanh</p>
    </div>
</div>

<!-- Reports Section Tabs -->
<?php include __DIR__ . '/components/reports_tabs.php'; ?>

<!-- Report Cards Grid -->
<div class="row g-4 mb-4">
    <!-- Revenue Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/report-profit'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(123, 192, 67, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-chart-line" style="font-size: 36px; color: var(--admin-primary, #7BC043);"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Doanh thu & Lợi nhuận</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Phân tích doanh thu, chi phí và lãi/lỗ theo thời gian</p>
            </div>
        </div>
    </div>
    
    <!-- Expiry Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/report-expiry'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-clock" style="font-size: 36px; color: #ef4444;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Cảnh báo Hết hạn</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Theo dõi sản phẩm sắp hết hạn để xử lý kịp thời</p>
            </div>
        </div>
    </div>
    
    <!-- Top Products Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/report-top-products'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(59, 130, 246, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-star" style="font-size: 36px; color: #3b82f6;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Sản phẩm Bán chạy</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Top sản phẩm được mua nhiều nhất</p>
            </div>
        </div>
    </div>
    
    <!-- Orders Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/orders'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(139, 92, 246, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-shopping-cart" style="font-size: 36px; color: #8b5cf6;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Quản lý Đơn hàng</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Xem và xử lý đơn hàng từ khách</p>
            </div>
        </div>
    </div>
    
    <!-- Disposal Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/disposals'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-trash-alt" style="font-size: 36px; color: #f59e0b;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Phiếu Hủy hàng</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Quản lý hàng hư hỏng, hết hạn</p>
            </div>
        </div>
    </div>
    
    <!-- Inventory Report -->
    <div class="col-md-6 col-lg-4">
        <div class="admin-card" style="cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/public/admin/products?filter=low_stock'">
            <div class="admin-card-body" style="text-align: center; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-warehouse" style="font-size: 36px; color: #10b981;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 8px;">Tồn kho Cảnh báo</h3>
                <p style="font-size: 14px; color: var(--admin-text-muted, #6b7280); margin: 0;">Sản phẩm sắp hết hàng cần nhập thêm</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
