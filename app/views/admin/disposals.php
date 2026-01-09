<?php
/**
 * ADMIN - QUẢN LÝ PHIẾU HỦY
 * Thiết kế giao diện hiện đại - Chuyển thể từ Template Tailwind
 * Theme: #7BC043 (Xanh lá) + #2D3657 (Xanh đậm)
 */
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<style>
/* ========================================
   DISPOSAL PAGE STYLES - Modern Template
   ======================================== */

/* Base Container */
.disposal-page {
    padding: 24px;
    background: #f8fafc;
    min-height: calc(100vh - 60px);
}

.disposal-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.disposal-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 24px;
}

.disposal-header-text h1 {
    font-size: 28px;
    font-weight: 900;
    color: #1e293b;
    margin: 0 0 4px 0;
    letter-spacing: -0.025em;
}

.disposal-header-text p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.disposal-header-actions {
    display: flex;
    gap: 12px;
}

.btn-disposal-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(123, 192, 67, 0.3);
}

.btn-disposal-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(123, 192, 67, 0.4);
    color: white;
}

.btn-disposal-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-disposal-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
}

/* Stats Cards - 4 Column Grid */
.disposal-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 1024px) {
    .disposal-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 640px) {
    .disposal-stats { grid-template-columns: 1fr; }
}

.disposal-stat-card {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.disposal-stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.disposal-stat-label {
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    margin: 0;
}

.disposal-stat-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 16px;
}

.disposal-stat-icon.green { background: rgba(123, 192, 67, 0.15); color: #7BC043; }
.disposal-stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.disposal-stat-icon.orange { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.disposal-stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.disposal-stat-value {
    font-size: 26px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    line-height: 1;
}

.disposal-stat-trend {
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.disposal-stat-trend.up { color: #10b981; }
.disposal-stat-trend.down { color: #ef4444; }
.disposal-stat-trend.neutral { color: #94a3b8; }

/* Filters Toolbar */
.disposal-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 24px;
    align-items: center;
}

.disposal-search {
    flex: 1;
    min-width: 280px;
    position: relative;
}

.disposal-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.disposal-search input {
    width: 100%;
    height: 40px;
    padding: 0 14px 0 42px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #1e293b;
    transition: all 0.2s ease;
}

.disposal-search input:focus {
    outline: none;
    border-color: #7BC043;
    box-shadow: 0 0 0 3px rgba(123, 192, 67, 0.15);
    background: white;
}

.disposal-search input::placeholder {
    color: #94a3b8;
}

.disposal-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.disposal-filter-divider {
    width: 1px;
    height: 24px;
    background: #e2e8f0;
    margin: 0 6px;
}

.disposal-filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    height: 36px;
    padding: 0 14px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}

.disposal-filter-btn:hover {
    background: #f8fafc;
    border-color: #7BC043;
    color: #7BC043;
}

.disposal-filter-btn i {
    font-size: 12px;
    color: #94a3b8;
}

.disposal-filter-btn select {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: inherit;
    cursor: pointer;
    outline: none;
    padding-right: 4px;
}

/* Data Table */
.disposal-table-wrapper {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.disposal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.disposal-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.disposal-table th {
    padding: 14px 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    text-align: left;
}

.disposal-table th.text-right { text-align: right; }
.disposal-table th.text-center { text-align: center; }

.disposal-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.disposal-table tbody tr:last-child {
    border-bottom: none;
}

.disposal-table tbody tr:hover {
    background: #f8fafc;
}

.disposal-table td {
    padding: 16px 20px;
    color: #475569;
    vertical-align: middle;
}

.disposal-table .code-link {
    color: #7BC043;
    font-weight: 600;
    text-decoration: none;
}

.disposal-table .code-link:hover {
    text-decoration: underline;
}

.disposal-table .date-cell {
    font-size: 14px;
    color: #475569;
}

.disposal-table .date-cell small {
    display: block;
    font-size: 12px;
    color: #94a3b8;
    margin-top: 2px;
}

.disposal-table .user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.disposal-table .user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
}

.disposal-table .amount-cell {
    font-weight: 700;
    color: #ef4444;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

/* Type Badges */
.disposal-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.disposal-type-badge.hong {
    background: rgba(249, 115, 22, 0.1);
    color: #ea580c;
}

.disposal-type-badge.het_han {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
}

.disposal-type-badge.huy {
    background: rgba(100, 116, 139, 0.1);
    color: #475569;
}

.disposal-type-badge.dieu_chinh {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

/* Status Badges */
.disposal-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.disposal-status-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.disposal-status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}
.disposal-status-badge.pending .dot { background: #f59e0b; }

.disposal-status-badge.approved {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}
.disposal-status-badge.approved .dot { background: #10b981; }

.disposal-status-badge.rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}
.disposal-status-badge.rejected .dot { background: #ef4444; }

/* Action Buttons */
.disposal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.15s ease;
}

.disposal-table tbody tr:hover .disposal-actions {
    opacity: 1;
}

.disposal-action-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.disposal-action-btn:hover {
    background: #f1f5f9;
}

.disposal-action-btn.view:hover { color: #7BC043; }
.disposal-action-btn.approve:hover { color: #10b981; background: rgba(16,185,129,0.1); }
.disposal-action-btn.delete:hover { color: #ef4444; background: rgba(239,68,68,0.1); }

/* Pagination Footer */
.disposal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.disposal-footer-info {
    font-size: 13px;
    color: #64748b;
}

.disposal-footer-info strong {
    color: #1e293b;
    font-weight: 600;
}

.disposal-pagination {
    display: flex;
    align-items: center;
    gap: 6px;
}

.disposal-page-btn {
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 8px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.disposal-page-btn:hover {
    background: #e2e8f0;
}

.disposal-page-btn.active {
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(123, 192, 67, 0.3);
}

.disposal-page-btn.arrow {
    color: #64748b;
}

.disposal-page-btn.arrow:hover {
    color: #7BC043;
}

/* Empty State */
.disposal-empty {
    padding: 80px 20px;
    text-align: center;
}

.disposal-empty i {
    font-size: 56px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.disposal-empty h3 {
    font-size: 16px;
    font-weight: 600;
    color: #64748b;
    margin: 0 0 8px 0;
}

.disposal-empty p {
    font-size: 14px;
    color: #94a3b8;
    margin: 0;
}
</style>

<div class="disposal-page">
    <div class="disposal-container">
        <!-- Breadcrumb -->
        <div class="admin-breadcrumb" style="margin-bottom: 16px;">
            <a href="<?= BASE_URL ?>/public/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span class="current">Quản lý phiếu hủy</span>
        </div>
        
        <?php include __DIR__ . '/components/warehouse_tabs.php'; ?>
        
        <!-- Page Header -->
        <div class="disposal-header">
            <div class="disposal-header-text">
                <h1>Quản lý Phiếu Hủy</h1>
                <p>Theo dõi và kiểm soát thất thoát hàng hóa</p>
            </div>
            <div class="disposal-header-actions">
                <a href="<?= BASE_URL ?>/public/admin/export-disposal-excel?status=<?= $filters['trang_thai'] ?? '' ?>" class="btn-disposal-secondary">
                    <i class="fas fa-download"></i>
                    <span>Xuất Excel</span>
                </a>
                <a href="<?= BASE_URL ?>/public/admin/disposal-add" class="btn-disposal-primary">
                    <i class="fas fa-plus"></i>
                    <span>Tạo Phiếu Hủy Mới</span>
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="disposal-stats">
            <!-- Card 1: Tổng phiếu -->
            <div class="disposal-stat-card">
                <div class="disposal-stat-header">
                    <p class="disposal-stat-label">Phiếu hủy tháng này</p>
                    <div class="disposal-stat-icon green">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <p class="disposal-stat-value"><?= number_format($status_counts['all'] ?? 0) ?></p>
                <p class="disposal-stat-trend neutral">
                    <i class="fas fa-minus"></i>
                    Tổng số phiếu
                </p>
            </div>
            
            <!-- Card 2: Giá trị thiệt hại -->
            <div class="disposal-stat-card">
                <div class="disposal-stat-header">
                    <p class="disposal-stat-label">Giá trị thiệt hại</p>
                    <div class="disposal-stat-icon red">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <p class="disposal-stat-value"><?= number_format($total_value ?? 0, 0, ',', '.') ?>đ</p>
                <p class="disposal-stat-trend down">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cần chú ý
                </p>
            </div>
            
            <!-- Card 3: Hàng hỏng -->
            <div class="disposal-stat-card">
                <div class="disposal-stat-header">
                    <p class="disposal-stat-label">Hàng hỏng</p>
                    <div class="disposal-stat-icon orange">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
                <p class="disposal-stat-value"><?= number_format($status_counts['cho_duyet'] ?? 0) ?> phiếu</p>
                <p class="disposal-stat-trend neutral">
                    Chờ xử lý
                </p>
            </div>
            
            <!-- Card 4: Hết hạn -->
            <div class="disposal-stat-card">
                <div class="disposal-stat-header">
                    <p class="disposal-stat-label">Đã duyệt</p>
                    <div class="disposal-stat-icon purple">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <p class="disposal-stat-value"><?= number_format($status_counts['da_duyet'] ?? 0) ?> phiếu</p>
                <p class="disposal-stat-trend up">
                    <i class="fas fa-check"></i>
                    Đã trừ kho
                </p>
            </div>
        </div>
        
        <!-- Filters Toolbar -->
        <form method="GET" class="disposal-toolbar">
            <div class="disposal-search">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" placeholder="Tìm theo mã phiếu, sản phẩm..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
            </div>
            
            <div class="disposal-filters">
                <span class="disposal-filter-divider"></span>
                
                <div class="disposal-filter-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <select name="date_range" onchange="this.form.submit()">
                        <option value="">Thời gian</option>
                        <option value="today">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month" selected>Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                
                <div class="disposal-filter-btn">
                    <i class="fas fa-filter"></i>
                    <select name="type" onchange="this.form.submit()">
                        <option value="">Lý do: Tất cả</option>
                        <option value="hong" <?= ($filters['loai_phieu'] ?? '') == 'hong' ? 'selected' : '' ?>>Hàng hỏng</option>
                        <option value="het_han" <?= ($filters['loai_phieu'] ?? '') == 'het_han' ? 'selected' : '' ?>>Hết hạn</option>
                        <option value="huy" <?= ($filters['loai_phieu'] ?? '') == 'huy' ? 'selected' : '' ?>>Hủy bỏ</option>
                        <option value="dieu_chinh" <?= ($filters['loai_phieu'] ?? '') == 'dieu_chinh' ? 'selected' : '' ?>>Điều chỉnh</option>
                    </select>
                </div>
                
                <div class="disposal-filter-btn">
                    <i class="fas fa-user"></i>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Trạng thái</option>
                        <option value="cho_duyet" <?= ($filters['trang_thai'] ?? '') == 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="da_duyet" <?= ($filters['trang_thai'] ?? '') == 'da_duyet' ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="tu_choi" <?= ($filters['trang_thai'] ?? '') == 'tu_choi' ? 'selected' : '' ?>>Từ chối</option>
                    </select>
                </div>
                
                <a href="<?= BASE_URL ?>/public/admin/disposals" class="disposal-filter-btn" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
        
        <!-- Data Table -->
        <div class="disposal-table-wrapper">
            <div style="overflow-x: auto;">
                <table class="disposal-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" style="cursor: pointer;">
                            </th>
                            <th>Mã Phiếu</th>
                            <th>Ngày Tạo</th>
                            <th>Người Thực Hiện</th>
                            <th>Lý Do</th>
                            <th class="text-right">Thiệt Hại</th>
                            <th>Trạng Thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($disposals)): ?>
                            <?php 
                            $loaiLabels = ['huy' => 'Hủy bỏ', 'hong' => 'Hàng hỏng', 'het_han' => 'Hết hạn', 'dieu_chinh' => 'Điều chỉnh'];
                            $statusLabels = ['cho_duyet' => 'Chờ duyệt', 'da_duyet' => 'Đã duyệt', 'tu_choi' => 'Từ chối'];
                            $statusClasses = ['cho_duyet' => 'pending', 'da_duyet' => 'approved', 'tu_choi' => 'rejected'];
                            ?>
                            <?php foreach ($disposals as $d): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $d['ID_phieu_huy'] ?>" style="cursor: pointer;">
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/public/admin/disposal-detail/<?= $d['ID_phieu_huy'] ?>" class="code-link">
                                            <?= htmlspecialchars($d['Ma_hien_thi']) ?>
                                        </a>
                                    </td>
                                    <td class="date-cell">
                                        <?= date('d/m/Y', strtotime($d['Ngay_tao'])) ?>
                                        <small><?= date('H:i', strtotime($d['Ngay_tao'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($d['Ten_nguoi_tao'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <span><?= htmlspecialchars($d['Ten_nguoi_tao'] ?? 'Unknown') ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="disposal-type-badge <?= $d['Loai_phieu'] ?>">
                                            <?= $loaiLabels[$d['Loai_phieu']] ?? $d['Loai_phieu'] ?>
                                        </span>
                                    </td>
                                    <td class="amount-cell">
                                        <?= number_format($d['Tong_tien_huy'], 0, ',', '.') ?>đ
                                    </td>
                                    <td>
                                        <span class="disposal-status-badge <?= $statusClasses[$d['Trang_thai']] ?? '' ?>">
                                            <span class="dot"></span>
                                            <?= $statusLabels[$d['Trang_thai']] ?? $d['Trang_thai'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="disposal-actions">
                                            <a href="<?= BASE_URL ?>/public/admin/disposal-detail/<?= $d['ID_phieu_huy'] ?>" class="disposal-action-btn view" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($d['Trang_thai'] == 'cho_duyet'): ?>
                                                <button onclick="approveDisposal(<?= $d['ID_phieu_huy'] ?>)" class="disposal-action-btn approve" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="disposal-empty">
                                        <i class="fas fa-inbox"></i>
                                        <h3>Chưa có phiếu hủy nào</h3>
                                        <p>Tạo phiếu hủy mới để bắt đầu theo dõi</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <div class="disposal-footer">
                <div class="disposal-footer-info">
                    Hiển thị <strong><?= (($pagination['current_page'] ?? 1) - 1) * ($pagination['per_page'] ?? 10) + 1 ?></strong> 
                    đến <strong><?= min(($pagination['current_page'] ?? 1) * ($pagination['per_page'] ?? 10), $pagination['total'] ?? 0) ?></strong> 
                    của <strong><?= $pagination['total'] ?? 0 ?></strong> kết quả
                </div>
                <div class="disposal-pagination">
                    <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                        <a href="?page=<?= ($pagination['current_page'] ?? 1) - 1 ?>&status=<?= $filters['trang_thai'] ?? '' ?>" class="disposal-page-btn arrow">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= min(5, $pagination['last_page']); $i++): ?>
                        <a href="?page=<?= $i ?>&status=<?= $filters['trang_thai'] ?? '' ?>" class="disposal-page-btn <?= $i == ($pagination['current_page'] ?? 1) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if (($pagination['last_page'] ?? 1) > 5): ?>
                        <span style="color: #94a3b8;">...</span>
                        <a href="?page=<?= $pagination['last_page'] ?>&status=<?= $filters['trang_thai'] ?? '' ?>" class="disposal-page-btn">
                            <?= $pagination['last_page'] ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1)): ?>
                        <a href="?page=<?= ($pagination['current_page'] ?? 1) + 1 ?>&status=<?= $filters['trang_thai'] ?? '' ?>" class="disposal-page-btn arrow">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Select All Checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Approve Disposal
function approveDisposal(id) {
    if (!confirm('Duyệt phiếu hủy này?\n\nKho sẽ được TRỪ TỰ ĐỘNG.')) return;
    
    const formData = new FormData();
    formData.append('disposal_id', id);
    formData.append('csrf_token', csrfToken);
    
    fetch(baseUrl + '/public/admin/disposal-approve', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Lỗi', 'error');
        }
    });
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
