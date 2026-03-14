<?php
/**
 * ADMIN - BÁO CÁO SẢN PHẨM BÁN CHẠY
 * Thiết kế giao diện hiện đại - Theme xanh lá/Xanh đậm
 * 
 * Dữ liệu từ controller:
 * - $top_products: Mảng sản phẩm bán chạy nhất
 * - $date_from, $date_to: Ngày lọc
 * - $summary: Tổng số lượng bán, doanh thu, v.v.
 */

// Dữ liệu mặc định nếu không được truyền
$top_products = $top_products ?? [];
$date_from = $date_from ?? date('Y-m-01');
$date_to = $date_to ?? date('Y-m-d');
$summary = $summary ?? ['total_qty' => 0, 'total_revenue' => 0, 'total_products' => 0];
?>
<?php include __DIR__ . '/layouts/header.php'; ?>
<link rel="stylesheet" href="<?= asset('css/admin-modern.css') ?>">

<div class="admin-modern">
    <div class="admin-modern-container">
        <!-- Breadcrumb -->
        <div class="admin-breadcrumb">
            <a href="<?= BASE_URL ?>/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <a href="<?= BASE_URL ?>/admin/reports">Báo cáo</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span class="current">Sản phẩm bán chạy</span>
        </div>
        
        
        <!-- Sales Section Tabs -->
        <?php include __DIR__ . '/components/sales_tabs.php'; ?>
        
        <!-- Page Header -->
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Sản phẩm Bán chạy</h1>
                <p class="admin-page-subtitle">Top sản phẩm được mua nhiều nhất trong khoảng thời gian</p>
            </div>
            <div class="admin-header-actions">
                <a href="<?= BASE_URL ?>/admin/export-top-products?date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" class="btn-admin-primary">
                    <i class="fas fa-download"></i>
                    <span>Xuất Excel</span>
                </a>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <form method="GET" class="admin-filter-bar">
            <div class="form-group">
                <label>Từ ngày</label>
                <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>">
            </div>
            <div class="form-group">
                <label>Đến ngày</label>
                <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>">
            </div>
            <button type="submit" class="btn-admin-secondary">
                <i class="fas fa-search"></i>
                <span>Xem báo cáo</span>
            </button>
            <div style="display: flex; gap: 8px; margin-left: auto;">
                <a href="?date_from=<?= date('Y-m-d', strtotime('-7 days')) ?>&date_to=<?= date('Y-m-d') ?>" class="btn-filter">7 ngày</a>
                <a href="?date_from=<?= date('Y-m-d', strtotime('-30 days')) ?>&date_to=<?= date('Y-m-d') ?>" class="btn-filter">30 ngày</a>
                <a href="?date_from=<?= date('Y-m-01') ?>&date_to=<?= date('Y-m-d') ?>" class="btn-filter">Tháng này</a>
            </div>
        </form>
        
        <!-- Stat Cards -->
        <div class="stat-cards-row">
            <!-- Tổng SL bán -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-info">
                        <h4>Tổng SL đã bán</h4>
                        <p class="stat-card-value"><?= number_format($summary['total_qty'] ?? 0) ?></p>
                    </div>
                    <div class="stat-card-icon primary">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <span style="font-size: 13px; color: var(--admin-text-muted);">Trong khoảng thời gian</span>
                </div>
            </div>
            
            <!-- Doanh thu -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-info">
                        <h4>Doanh thu</h4>
                        <p class="stat-card-value"><?= number_format($summary['total_revenue'] ?? 0, 0, ',', '.') ?>đ</p>
                    </div>
                    <div class="stat-card-icon success">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <span style="font-size: 13px; color: var(--admin-text-muted);">Từ top sản phẩm</span>
                </div>
            </div>
            
            <!-- Số SP -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-info">
                        <h4>Sản phẩm</h4>
                        <p class="stat-card-value"><?= count($top_products) ?></p>
                    </div>
                    <div class="stat-card-icon info">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <span style="font-size: 13px; color: var(--admin-text-muted);">Đã được mua</span>
                </div>
            </div>
            
            <!-- Avg Order -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-info">
                        <h4>Giá TB/SP</h4>
                        <?php 
                        $avgPrice = $summary['total_qty'] > 0 
                            ? ($summary['total_revenue'] / $summary['total_qty']) 
                            : 0;
                        ?>
                        <p class="stat-card-value"><?= number_format($avgPrice, 0, ',', '.') ?>đ</p>
                    </div>
                    <div class="stat-card-icon warning">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <span style="font-size: 13px; color: var(--admin-text-muted);">Giá trung bình</span>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Main Table -->
            <div class="col-lg-8">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Bảng xếp hạng sản phẩm</h3>
                        <span style="font-size: 13px; color: var(--admin-text-muted);">
                            <?= count($top_products) ?> sản phẩm
                        </span>
                    </div>
                    <div class="admin-card-body no-padding" style="max-height: 600px; overflow-y: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px; text-align: center;">#</th>
                                    <th>Sản phẩm</th>
                                    <th style="text-align: center;">Đã bán</th>
                                    <th style="text-align: right;">Doanh thu</th>
                                    <th style="text-align: center;">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($top_products)): ?>
                                    <?php 
                                    $maxQty = max(array_column($top_products, 'So_luong_ban') ?: [1]);
                                    foreach ($top_products as $i => $p): 
                                        $percent = $maxQty > 0 ? round(($p['So_luong_ban'] / $maxQty) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php if ($i < 3): ?>
                                                <span style="width: 32px; height: 32px; border-radius: 50%; background: <?= ['#ffd700', '#c0c0c0', '#cd7f32'][$i] ?>; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                                    <?= $i + 1 ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="font-weight: 500; color: var(--admin-text-muted);"><?= $i + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <img src="<?= asset('img/products/' . ($p['Hinh_anh'] ?: 'placeholder.png')) ?>" 
                                                     onerror="this.src='<?= asset('img/placeholder-product.png') ?>'" 
                                                     style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #f8fafc;">
                                                <div>
                                                    <div style="font-weight: 500;"><?= htmlspecialchars($p['Ten']) ?></div>
                                                    <div style="font-size: 12px; color: var(--admin-text-muted);"><?= htmlspecialchars($p['Ma_hien_thi'] ?? 'N/A') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center; font-weight: 600;"><?= number_format($p['So_luong_ban']) ?></td>
                                        <td style="text-align: right; font-weight: 600; color: var(--admin-success, #10b981);">
                                            <?= number_format($p['Doanh_thu'] ?? 0, 0, ',', '.') ?>đ
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="flex: 1; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                                    <div style="height: 100%; width: <?= $percent ?>%; background: var(--admin-primary, #7BC043); border-radius: 4px;"></div>
                                                </div>
                                                <span style="font-size: 12px; font-weight: 500; min-width: 36px;"><?= $percent ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 60px 20px;">
                                            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(123, 192, 67, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                                <i class="fas fa-chart-bar" style="font-size: 36px; color: var(--admin-primary);"></i>
                                            </div>
                                            <h4 style="font-size: 18px; font-weight: 600; margin: 0 0 8px 0;">Chưa có dữ liệu</h4>
                                            <p style="color: var(--admin-text-muted); margin: 0;">Không có sản phẩm nào được bán trong khoảng thời gian này</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Chart -->
            <div class="col-lg-4">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Biểu đồ Top 5</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="chart-container" style="height: 280px;">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="admin-card" style="margin-top: 24px;">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Thống kê nhanh</h3>
                    </div>
                    <div class="admin-card-body" style="padding: 0;">
                        <?php 
                        $topCategories = [];
                        foreach ($top_products as $p) {
                            $cat = $p['Ten_danh_muc'] ?? 'Chưa phân loại';
                            if (!isset($topCategories[$cat])) {
                                $topCategories[$cat] = 0;
                            }
                            $topCategories[$cat] += $p['So_luong_ban'];
                        }
                        arsort($topCategories);
                        $topCategories = array_slice($topCategories, 0, 5, true);
                        ?>
                        <?php foreach ($topCategories as $catName => $qty): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 14px; color: var(--admin-text);"><?= htmlspecialchars($catName) ?></span>
                            <span style="font-weight: 600;"><?= number_format($qty) ?> SP</span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($topCategories)): ?>
                        <div style="text-align: center; padding: 40px;">
                            <p style="color: var(--admin-text-muted);">Chưa có dữ liệu</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('topProductsChart');
    if (!ctx) return;
    
    const topProducts = <?= json_encode(array_slice($top_products, 0, 5)) ?>;
    
    if (topProducts.length === 0) {
        ctx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--admin-text-muted);">Chưa có dữ liệu</div>';
        return;
    }
    
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: topProducts.map(p => p.Ten.substring(0, 15) + (p.Ten.length > 15 ? '...' : '')),
            datasets: [{
                data: topProducts.map(p => p.So_luong_ban),
                backgroundColor: [
                    '#7BC043',
                    '#3b82f6',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ef4444'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 16,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
