<?php
/**
 * WAREHOUSE - QUẢN LÝ PHIẾU NHẬP
 * Thiết kế giao diện hiện đại - Phù hợp với style disposals.php
 * Theme: #7BC043 (Xanh lá)
 */

$imports = $imports ?? [];
$filters = $filters ?? ['ma_phieu'=>'','nguoi_tao'=>'','ngay_nhap'=>'','page'=>1];
$pagination = $pagination ?? ['current_page'=>1,'total_pages'=>1,'per_page'=>10,'offset'=>0];
$csrf_token = $csrf_token ?? '';
$total = $total ?? count($imports);
?>
<?php include dirname(__DIR__) . '/admin/layouts/header.php'; ?>
<link rel="stylesheet" href="<?= ASSETS_DIR ?>/css/warehouse.css">

<style>
/* ========================================
   WAREHOUSE PAGE STYLES - Modern Template
   ======================================== */

.warehouse-page {
    padding: 24px;
    background: #f8fafc;
    min-height: calc(100vh - 60px);
}

.warehouse-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.warehouse-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 24px;
}

.warehouse-header-text h1 {
    font-size: 28px;
    font-weight: 900;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.warehouse-header-text p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.warehouse-header-actions {
    display: flex;
    gap: 12px;
}

.btn-warehouse-primary {
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

.btn-warehouse-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(123, 192, 67, 0.4);
    color: white;
}

.btn-warehouse-secondary {
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

.btn-warehouse-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
}

/* Filters Toolbar */
.warehouse-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 24px;
    align-items: flex-end;
}

.warehouse-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 180px;
}

.warehouse-field label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.warehouse-field input {
    height: 40px;
    padding: 0 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    transition: all 0.2s ease;
}

.warehouse-field input:focus {
    outline: none;
    border-color: #7BC043;
    box-shadow: 0 0 0 3px rgba(123, 192, 67, 0.15);
    background: white;
}

.warehouse-filter-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
}

.warehouse-filter-btn {
    height: 40px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
}

.warehouse-filter-btn.search {
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
}

.warehouse-filter-btn.search:hover {
    box-shadow: 0 2px 8px rgba(123, 192, 67, 0.3);
}

.warehouse-filter-btn.reset {
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
}

.warehouse-filter-btn.reset:hover {
    background: #f8fafc;
}

/* Data Table */
.warehouse-table-wrapper {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.warehouse-table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
}

.warehouse-table-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.warehouse-table-header .count {
    font-size: 14px;
    color: #64748b;
    font-weight: 400;
    margin-left: 8px;
}

.warehouse-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.warehouse-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.warehouse-table th {
    padding: 14px 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    text-align: left;
}

.warehouse-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.warehouse-table tbody tr:last-child {
    border-bottom: none;
}

.warehouse-table tbody tr:hover {
    background: #f8fafc;
}

.warehouse-table td {
    padding: 16px 20px;
    color: #475569;
    vertical-align: middle;
}

.warehouse-table .code-link {
    color: #7BC043;
    font-weight: 600;
}

.warehouse-table .amount-cell {
    font-weight: 700;
    color: #7BC043;
}

/* Action Buttons */
.warehouse-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.15s ease;
}

.warehouse-table tbody tr:hover .warehouse-actions {
    opacity: 1;
}

.warehouse-action-btn {
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
}

.warehouse-action-btn:hover {
    background: #f1f5f9;
}

.warehouse-action-btn.edit:hover { color: #7BC043; }
.warehouse-action-btn.delete:hover { color: #ef4444; background: rgba(239,68,68,0.1); }

/* Pagination Footer */
.warehouse-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.warehouse-footer-info {
    font-size: 13px;
    color: #64748b;
}

.warehouse-pagination {
    display: flex;
    align-items: center;
    gap: 6px;
}

.warehouse-page-btn {
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

.warehouse-page-btn:hover {
    background: #e2e8f0;
}

.warehouse-page-btn.active {
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
}

/* Empty State */
.warehouse-empty {
    padding: 60px 20px;
    text-align: center;
    color: #94a3b8;
}

.warehouse-empty i {
    font-size: 48px;
    margin-bottom: 16px;
}
</style>

<div class="warehouse-page">
    <div class="warehouse-container">
        <!-- Breadcrumb -->
        <div class="admin-breadcrumb" style="margin-bottom: 16px;">
            <a href="<?= BASE_URL ?>/public/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span class="current">Quản lý phiếu nhập</span>
        </div>
        
        <?php include dirname(__DIR__) . '/admin/components/warehouse_tabs.php'; ?>
        
        <!-- Page Header -->
        <div class="warehouse-header">
            <div class="warehouse-header-text">
                <h1>Quản lý Phiếu Nhập</h1>
                <p>Tìm kiếm và quản lý lịch sử nhập hàng hóa</p>
            </div>
            <div class="warehouse-header-actions">
                <?php
                $qs = http_build_query([
                    'ma_phieu'  => $filters['ma_phieu'] ?? '',
                    'nguoi_tao' => $filters['nguoi_tao'] ?? '',
                    'ngay_nhap' => $filters['ngay_nhap'] ?? '',
                ]);
                ?>
                <a href="<?= BASE_URL ?>/public/index.php?url=warehouse/exportImport&<?= $qs ?>" class="btn-warehouse-secondary">
                    <i class="fas fa-download"></i>
                    <span>Xuất Excel</span>
                </a>
                <button class="btn-warehouse-primary" type="button" onclick="openAdd()">
                    <i class="fas fa-plus"></i>
                    <span>Tạo Phiếu Nhập</span>
                </button>
            </div>
        </div>
        
        <!-- Filters Toolbar -->
        <form id="wh-search-form" class="warehouse-toolbar" method="GET" action="<?= BASE_URL ?>/public/warehouse/dashboard">
            <div class="warehouse-field">
                <label>Mã phiếu</label>
                <input name="ma_phieu" value="<?= htmlspecialchars($filters['ma_phieu']) ?>" placeholder="VD: PNK20251229">
            </div>
            <div class="warehouse-field">
                <label>Người tạo</label>
                <input name="nguoi_tao" value="<?= htmlspecialchars($filters['nguoi_tao']) ?>" placeholder="Tên nhân viên...">
            </div>
            <div class="warehouse-field">
                <label>Ngày nhập</label>
                <input type="date" name="ngay_nhap" value="<?= htmlspecialchars($filters['ngay_nhap']) ?>">
            </div>
            <div class="warehouse-filter-actions">
                <button class="warehouse-filter-btn search" type="submit">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <button type="button" class="warehouse-filter-btn reset" id="wh-reset-search">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
        
        <!-- Data Table -->
        <div class="warehouse-table-wrapper">
            <div class="warehouse-table-header">
                <h3>Danh sách phiếu nhập <span class="count">(<?= $total ?>)</span></h3>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="warehouse-table">
                    <thead>
                        <tr>
                            <th>Mã Phiếu</th>
                            <th>Ngày Nhập</th>
                            <th>Người Tạo</th>
                            <th>Tổng Tiền</th>
                            <th>Ghi Chú</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($imports)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="warehouse-empty">
                                        <i class="fas fa-inbox"></i>
                                        <p>Không có dữ liệu</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($imports as $row): ?>
                                <tr>
                                    <td class="code-link"><?= htmlspecialchars($row['Ma_hien_thi'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['Ngay_nhap'] ?? 'now'))) ?></td>
                                    <td><?= htmlspecialchars($row['Nguoi_tao_ten'] ?? $row['Nguoi_tao'] ?? '') ?></td>
                                    <td class="amount-cell"><?= number_format((float)($row['Tong_tien'] ?? 0), 0, ',', '.') ?> đ</td>
                                    <td><?= htmlspecialchars($row['Ghi_chu'] ?? '') ?></td>
                                    <td>
                                        <div class="warehouse-actions">
                                            <button class="warehouse-action-btn edit" type="button" title="Sửa" onclick="openEdit(<?= (int)$row['ID_phieu_nhap'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="warehouse-action-btn delete" type="button" title="Xóa" onclick="deleteImport(<?= (int)$row['ID_phieu_nhap'] ?>)">
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
            
            <!-- Pagination Footer -->
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <div class="warehouse-footer">
                <div class="warehouse-footer-info">
                    Hiển thị trang <?= $pagination['current_page'] ?> / <?= $pagination['total_pages'] ?>
                </div>
                <div class="warehouse-pagination">
                    <?php for ($i=1; $i<= (int)$pagination['total_pages']; $i++): ?>
                        <a class="warehouse-page-btn <?= ($i == (int)$pagination['current_page']) ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/public/warehouse/dashboard?ma_phieu=<?= urlencode($filters['ma_phieu']) ?>&nguoi_tao=<?= urlencode($filters['nguoi_tao']) ?>&ngay_nhap=<?= urlencode($filters['ngay_nhap']) ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
  window.WH_BASE = "<?= rtrim(BASE_URL, '/') ?>";
  window.WH_CSRF = "<?= htmlspecialchars($csrf_token) ?>";
  
  // Reset tìm kiếm
  document.getElementById('wh-reset-search')?.addEventListener('click', function() {
      window.location.href = '<?= BASE_URL ?>/public/warehouse/dashboard';
  });
</script>
<script src="<?= ASSETS_DIR ?>/js/warehouse.js" defer></script>

<?php require __DIR__ . '/modal_import_add.php'; ?>
<?php require __DIR__ . '/modal_import_edit.php'; ?>

<?php include dirname(__DIR__) . '/admin/layouts/footer.php'; ?>
