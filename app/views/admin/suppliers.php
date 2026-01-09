<?php
/**
 * SUPPLIERS PAGE - QUẢN LÝ NHÀ CUNG CẤP
 * Modern design based on Tailwind template - converted to Bootstrap + inline CSS
 * Theme: #7BC043 (primary), #2D3657 (secondary)
 */

// Data from controller
$suppliers = $suppliers ?? [];
$pagination = $pagination ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1, 'per_page' => 12];
$stats = $stats ?? ['all' => 0, 'active' => 0, 'inactive' => 0];
$filters = $filters ?? [];
$csrf_token = $csrf_token ?? '';
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<style>
/* =============================================================================
   SUPPLIERS PAGE - MODERN DESIGN
   ============================================================================= */

/* Page Container */
.supplier-page {
    background: #f6f7f8;
    min-height: calc(100vh - 64px);
    padding-bottom: 40px;
}

.supplier-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

/* Breadcrumb */
.supplier-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #64748b;
    margin-bottom: 16px;
}

.supplier-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.2s;
}

.supplier-breadcrumb a:hover {
    color: #7BC043;
}

.supplier-breadcrumb span {
    color: #1e293b;
    font-weight: 500;
}

/* Page Header */
.supplier-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    margin-bottom: 24px;
}

.supplier-header-info h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
}

.supplier-header-info p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.btn-add-supplier {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(123, 192, 67, 0.3);
}

.btn-add-supplier:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(123, 192, 67, 0.4);
}

.btn-add-supplier:active {
    transform: scale(0.98);
}

/* Filter Toolbar */
.supplier-toolbar {
    background: white;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.toolbar-search {
    position: relative;
    flex: 1;
    max-width: 400px;
    min-width: 200px;
}

.toolbar-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.toolbar-search input {
    width: 100%;
    height: 44px;
    padding: 0 16px 0 42px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    background: #f8fafc;
    transition: all 0.2s;
}

.toolbar-search input:focus {
    outline: none;
    border-color: #7BC043;
    box-shadow: 0 0 0 3px rgba(123, 192, 67, 0.15);
    background: white;
}

.toolbar-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.toolbar-select {
    height: 44px;
    padding: 0 36px 0 14px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #475569;
    background: white url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 10px center/16px no-repeat;
    cursor: pointer;
    min-width: 160px;
}

.toolbar-select:focus {
    outline: none;
    border-color: #7BC043;
}

.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 44px;
    padding: 0 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-export:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Data Table Card */
.supplier-table-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.supplier-table {
    width: 100%;
    border-collapse: collapse;
}

.supplier-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.supplier-table th {
    padding: 14px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}

.supplier-table th:last-child {
    text-align: right;
}

.supplier-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}

.supplier-table tbody tr:last-child {
    border-bottom: none;
}

.supplier-table tbody tr:hover {
    background: #fafbfc;
}

.supplier-table td {
    padding: 16px 20px;
    font-size: 14px;
    color: #1e293b;
    vertical-align: middle;
}

/* Supplier Info Cell */
.supplier-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.supplier-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}

.avatar-blue { background: #dbeafe; color: #2563eb; }
.avatar-orange { background: #ffedd5; color: #ea580c; }
.avatar-purple { background: #f3e8ff; color: #9333ea; }
.avatar-teal { background: #ccfbf1; color: #0d9488; }
.avatar-rose { background: #ffe4e6; color: #e11d48; }
.avatar-green { background: #dcfce7; color: #16a34a; }

.supplier-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 2px;
}

.supplier-address {
    font-size: 12px;
    color: #64748b;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Contact Cell */
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.contact-item i {
    color: #94a3b8;
    font-size: 12px;
    width: 14px;
}

.contact-item.phone {
    color: #475569;
}

.contact-item.email {
    color: #64748b;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-active {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.status-active .dot {
    background: #22c55e;
}

.status-inactive {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}

.status-inactive .dot {
    background: #94a3b8;
}

/* Action Buttons */
.action-cell {
    text-align: right;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
}

.supplier-table tbody tr:hover .action-buttons {
    opacity: 1;
}

.btn-action {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
}

.btn-action-edit {
    color: #64748b;
}

.btn-action-edit:hover {
    background: rgba(123, 192, 67, 0.15);
    color: #7BC043;
}

.btn-action-delete {
    color: #64748b;
}

.btn-action-delete:hover {
    background: #fee2e2;
    color: #ef4444;
}

/* Pagination */
.supplier-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-top: 1px solid #e2e8f0;
}

.pagination-info {
    font-size: 14px;
    color: #64748b;
}

.pagination-info strong {
    color: #1e293b;
    font-weight: 600;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 4px;
}

.page-btn {
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
    color: #475569;
    text-decoration: none;
}

.page-btn:hover {
    background: #f1f5f9;
}

.page-btn.active {
    background: #7BC043;
    color: white;
}

.page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-dots {
    padding: 0 8px;
    color: #94a3b8;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 16px;
    color: #64748b;
    margin: 0 0 8px;
}

.empty-state p {
    font-size: 14px;
    color: #94a3b8;
    margin: 0;
}

/* Modal Styles */
.supplier-modal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.supplier-modal .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.supplier-modal .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
}

.supplier-modal .modal-body {
    padding: 24px;
}

.supplier-modal .form-label {
    font-size: 14px;
    font-weight: 500;
    color: #475569;
    margin-bottom: 6px;
}

.supplier-modal .form-control {
    height: 44px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    padding: 0 14px;
}

.supplier-modal .form-control:focus {
    border-color: #7BC043;
    box-shadow: 0 0 0 3px rgba(123, 192, 67, 0.15);
}

.supplier-modal textarea.form-control {
    height: auto;
    padding: 12px 14px;
}

.supplier-modal .modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    gap: 12px;
}

.btn-modal-cancel {
    padding: 10px 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
}

.btn-modal-cancel:hover {
    background: #f8fafc;
}

.btn-modal-save {
    padding: 10px 20px;
    background: linear-gradient(135deg, #7BC043 0%, #5a9a32 100%);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    cursor: pointer;
}

.btn-modal-save:hover {
    box-shadow: 0 4px 12px rgba(123, 192, 67, 0.3);
}

/* Responsive */
@media (max-width: 1024px) {
    .supplier-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .toolbar-search {
        max-width: none;
    }
    
    .toolbar-filters {
        justify-content: space-between;
    }
}

@media (max-width: 768px) {
    .supplier-container {
        padding: 16px;
    }
    
    .supplier-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .supplier-table th:nth-child(3),
    .supplier-table td:nth-child(3),
    .supplier-table th:nth-child(4),
    .supplier-table td:nth-child(4) {
        display: none;
    }
    
    .supplier-pagination {
        flex-direction: column;
        gap: 12px;
    }
}
</style>

<div class="supplier-page">
    <div class="supplier-container">
        <!-- Breadcrumb -->
        <nav class="supplier-breadcrumb">
            <a href="<?= BASE_URL ?>/">Trang chủ</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <a href="<?= BASE_URL ?>/admin">Quản lý</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Nhà cung cấp</span>
        </nav>
        
        <!-- Page Header -->
        <div class="supplier-header">
            <div class="supplier-header-info">
                <h1>Quản Lý Nhà Cung Cấp</h1>
                <p>Danh sách và thông tin chi tiết các đối tác cung ứng cho siêu thị.</p>
            </div>
            <button class="btn-add-supplier" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="openAddModal()">
                <i class="fas fa-plus"></i>
                <span>Thêm Nhà Cung Cấp</span>
            </button>
        </div>
        
        <!-- Filter Toolbar -->
        <form class="supplier-toolbar" method="GET" action="">
            <div class="toolbar-search">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" placeholder="Tìm kiếm theo tên, SĐT, Email..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
            </div>
            <div class="toolbar-filters">
                <select name="status" class="toolbar-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hợp tác</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng hợp tác</option>
                </select>
                <button type="button" class="btn-export" onclick="exportExcel()">
                    <i class="fas fa-download"></i>
                    Xuất Excel
                </button>
            </div>
        </form>
        
        <!-- Data Table Card -->
        <div class="supplier-table-card">
            <?php if (empty($suppliers)): ?>
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h3>Chưa có nhà cung cấp nào</h3>
                <p>Nhấn "Thêm Nhà Cung Cấp" để bắt đầu</p>
            </div>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="supplier-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nhà Cung Cấp</th>
                            <th>Liên Hệ</th>
                            <th>Người Đại Diện</th>
                            <th>Trạng Thái</th>
                            <th style="width: 100px;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $avatarColors = ['avatar-blue', 'avatar-orange', 'avatar-purple', 'avatar-teal', 'avatar-rose', 'avatar-green'];
                        foreach ($suppliers as $index => $supplier): 
                            $avatarColor = $avatarColors[$index % count($avatarColors)];
                            $firstLetter = mb_substr($supplier['Ten_ncc'] ?? 'N', 0, 1, 'UTF-8');
                        ?>
                        <tr>
                            <td style="color: #64748b; font-weight: 500;">#<?= str_pad($supplier['ID_ncc'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div class="supplier-info">
                                    <div class="supplier-avatar <?= $avatarColor ?>"><?= strtoupper($firstLetter) ?></div>
                                    <div>
                                        <div class="supplier-name"><?= htmlspecialchars($supplier['Ten_ncc']) ?></div>
                                        <div class="supplier-address"><?= htmlspecialchars($supplier['Dia_chi'] ?? 'Chưa có địa chỉ') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <div class="contact-item phone">
                                        <i class="fas fa-phone"></i>
                                        <?= htmlspecialchars($supplier['Sdt'] ?? '---') ?>
                                    </div>
                                    <div class="contact-item email">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($supplier['Email'] ?? '---') ?>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #475569;">
                                <?= htmlspecialchars($supplier['Nguoi_lien_he'] ?? '---') ?>
                            </td>
                            <td>
                                <?php if ($supplier['Trang_thai'] === 'active'): ?>
                                <span class="status-badge status-active">
                                    <span class="dot"></span>
                                    Đang hợp tác
                                </span>
                                <?php else: ?>
                                <span class="status-badge status-inactive">
                                    <span class="dot"></span>
                                    Ngừng hợp tác
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="action-cell">
                                <div class="action-buttons">
                                    <button class="btn-action btn-action-edit" onclick="editSupplier(<?= $supplier['ID_ncc'] ?>)" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-action-delete" onclick="deleteSupplier(<?= $supplier['ID_ncc'] ?>, '<?= htmlspecialchars($supplier['Ten_ncc']) ?>')" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="supplier-pagination">
                <span class="pagination-info">
                    Hiển thị <strong><?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?>-<?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?></strong> 
                    trong tổng số <strong><?= $pagination['total'] ?></strong> nhà cung cấp
                </span>
                <div class="pagination-buttons">
                    <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" class="page-btn">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php else: ?>
                    <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    <?php endif; ?>
                    
                    <?php 
                    $lastPage = $pagination['last_page'];
                    $currentPage = $pagination['current_page'];
                    
                    for ($i = 1; $i <= min(3, $lastPage); $i++): ?>
                    <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                       class="page-btn <?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($lastPage > 5): ?>
                    <span class="page-dots">...</span>
                    <a href="?page=<?= $lastPage ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                       class="page-btn <?= $lastPage === $currentPage ? 'active' : '' ?>">
                        <?= $lastPage ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                    <a href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" class="page-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php else: ?>
                    <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade supplier-modal" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm Nhà Cung Cấp Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" id="supplierId">
                    
                    <div class="mb-3">
                        <label class="form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="supplierName" placeholder="Nhập tên công ty..." required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" name="phone" id="supplierPhone" placeholder="0xx-xxx-xxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="supplierEmail" placeholder="contact@company.com">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" id="supplierAddress" rows="2" placeholder="Địa chỉ công ty..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Người liên hệ</label>
                            <input type="text" class="form-control" name="contact_person" id="supplierContact" placeholder="Họ tên người đại diện">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control" name="status" id="supplierStatus">
                                <option value="active">Đang hợp tác</option>
                                <option value="inactive">Ngừng hợp tác</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="description" id="supplierDescription" rows="2" placeholder="Ghi chú thêm (nếu có)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn-modal-save">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Base URLs
const baseUrl = '<?= BASE_URL ?>';
const csrfToken = '<?= $csrf_token ?>';

// Open Add Modal
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Nhà Cung Cấp Mới';
    document.getElementById('supplierForm').reset();
    document.getElementById('supplierId').value = '';
}

// Edit Supplier
function editSupplier(id) {
    document.getElementById('modalTitle').textContent = 'Chỉnh Sửa Nhà Cung Cấp';
    document.getElementById('supplierId').value = id;
    
    // Fetch supplier data
    fetch(`${baseUrl}/public/admin/supplier-get/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const s = data.supplier;
                document.getElementById('supplierName').value = s.Ten_ncc || '';
                document.getElementById('supplierPhone').value = s.Sdt || '';
                document.getElementById('supplierEmail').value = s.Email || '';
                document.getElementById('supplierAddress').value = s.Dia_chi || '';
                document.getElementById('supplierContact').value = s.Nguoi_lien_he || '';
                document.getElementById('supplierStatus').value = s.Trang_thai || 'active';
                document.getElementById('supplierDescription').value = s.Mo_ta || '';
                
                const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
                modal.show();
            } else {
                alert('Không thể tải thông tin nhà cung cấp');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
}

// Delete Supplier
function deleteSupplier(id, name) {
    if (confirm(`Bạn có chắc muốn xóa nhà cung cấp "${name}"?`)) {
        fetch(`${baseUrl}/public/admin/supplier-delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `id=${id}&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Không thể xóa nhà cung cấp');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
    }
}

// Form Submit
document.getElementById('supplierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEdit = document.getElementById('supplierId').value !== '';
    const url = isEdit 
        ? `${baseUrl}/public/admin/supplier-update`
        : `${baseUrl}/public/admin/supplier-add`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
});

// Export Excel (placeholder)
function exportExcel() {
    alert('Tính năng xuất Excel đang được phát triển');
}
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
