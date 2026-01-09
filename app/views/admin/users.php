<?php
/**
 * =============================================================================
 * ADMIN - QUẢN LÝ NGƯỜI DÙNG
 * =============================================================================
 * 
 * Data từ Controller:
 * - $users         : Danh sách users
 * - $stats         : Thống kê tổng quan
 * - $filters       : Các filter hiện tại (keyword, role, status)
 * - $pagination    : Thông tin phân trang
 * - $csrf_token    : Token bảo mật
 */

/**
 * Helper function để build pagination URL
 */
function buildUrl($page) {
    global $filters;
    
    $params = ['page' => $page];
    
    if (!empty($filters['keyword'])) {
        $params['q'] = $filters['keyword'];
    }
    
    if (!empty($filters['role'])) {
        $params['role'] = $filters['role'];
    }
    
    if (!empty($filters['status'])) {
        $params['status'] = $filters['status'];
    }
    
    return BASE_URL . '/admin/users?' . http_build_query($params);
}
?>
<?php 
// Thêm CSS vào header trước khi include
$additional_css = asset('css/admin-users.css');
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="user-management-page">
    <div class="user-content-wrapper">
        
        <!-- Breadcrumbs -->
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/admin" class="breadcrumb-link">Trang chủ</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Quản lý Người dùng</span>
        </nav>
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title-section">
                <h1>Quản lý Người dùng</h1>
                <p class="page-subtitle">Quản lý tài khoản khách hàng, nhân viên và quản trị viên</p>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Total Users -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <p class="stat-label">Tổng người dùng</p>
                    <div class="stat-icon green">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                </div>
                <div>
                    <p class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></p>
                    
                </div>
            </div>
            
            <!-- New Today -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <p class="stat-label">Khách hàng mới</p>
                    <div class="stat-icon blue">
                        <span class="material-symbols-outlined">person_add</span>
                    </div>
                </div>
                <div>
                    <p class="stat-value">+<?= $stats['new_today'] ?? 0 ?></p>

                </div>
            </div>
            
            <!-- Staff -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <p class="stat-label">Nhân viên kho</p>
                    <div class="stat-icon orange">
                        <span class="material-symbols-outlined">inventory</span>
                    </div>
                </div>
                <div>
                    <p class="stat-value"><?= $stats['total_staff'] ?? 0 ?></p>
                    <div class="stat-change">
                        <span class="stat-change-text">Đang hoạt động</span>
                    </div>
                </div>
            </div>
            
            <!-- Admins -->
            <div class="stat-card">
                <div class="stat-card-header">
                    <p class="stat-label">Quản trị viên</p>
                    <div class="stat-icon purple">
                        <span class="material-symbols-outlined">security</span>
                    </div>
                </div>
                <div>
                    <p class="stat-value"><?= $stats['total_admins'] ?? 0 ?></p>
                    <div class="stat-change">
                        <span class="stat-change-text">Toàn quyền</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Data Table -->
        <div class="data-table-container">
            
            <!-- Filters Toolbar -->
            <div class="filters-toolbar">
                <!-- Search Box -->
                <div class="search-box">
                    <span class="search-icon material-symbols-outlined">search</span>
                    <input 
                        type="text" 
                        class="search-input" 
                        placeholder="Tìm kiếm theo tên, email hoặc SĐT..."
                        value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>"
                        id="searchInput"
                    >
                </div>
                
                <!-- Filter Actions -->
                <div class="filter-actions">
                    <select class="filter-select" id="roleFilter">
                        <option value="">Tất cả vai trò</option>
                        <option value="KH" <?= ($filters['role'] ?? '') === 'KH' ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="QUAN_LY_KHO" <?= ($filters['role'] ?? '') === 'QUAN_LY_KHO' ? 'selected' : '' ?>>Nhân viên kho</option>
                        <option value="ADMIN" <?= ($filters['role'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>Quản trị viên</option>
                    </select>
                    
                    <select class="filter-select" id="statusFilter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                        <option value="locked" <?= ($filters['status'] ?? '') === 'locked' ? 'selected' : '' ?>>Đã khóa</option>
                    </select>
                    
                    <button class="btn-filter" id="applyFilterBtn" title="Áp dụng bộ lọc">
                        <span class="material-symbols-outlined">filter_alt</span>
                        Lọc
                    </button>
                </div>
            </div>
            
            <!-- Filter Result Info -->
            <?php if (!empty($filters['role']) || !empty($filters['status']) || !empty($filters['keyword'])): ?>
            <div style="padding: 0.75rem 1rem; background: #e8f5e9; border-left: 3px solid #4e9767; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <strong>Đang lọc:</strong>
                <?php if (!empty($filters['keyword'])): ?>
                    <span style="display: inline-block; padding: 0.25rem 0.625rem; background: white; border-radius: 0.375rem; margin: 0 0.25rem;">
                        🔍 "<?= htmlspecialchars($filters['keyword']) ?>"
                    </span>
                <?php endif; ?>
                <?php if (!empty($filters['role'])): ?>
                    <span style="display: inline-block; padding: 0.25rem 0.625rem; background: white; border-radius: 0.375rem; margin: 0 0.25rem;">
                        👤 <?= $filters['role'] === 'KH' ? 'Khách hàng' : ($filters['role'] === 'QUAN_LY_KHO' ? 'Nhân viên kho' : 'Quản trị viên') ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($filters['status'])): ?>
                    <span style="display: inline-block; padding: 0.25rem 0.625rem; background: white; border-radius: 0.375rem; margin: 0 0.25rem;">
                        ✓ <?= $filters['status'] === 'active' ? 'Đang hoạt động' : 'Đã khóa' ?>
                    </span>
                <?php endif; ?>
                <span style="margin-left: 0.5rem; color: #4e9767; font-weight: 600;">
                    → Tìm thấy <?= $pagination['total_records'] ?> kết quả
                </span>
            </div>
            <?php endif; ?>
            
            <!-- Table -->
            <div class="table-wrapper">
                <?php if (!empty($users)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tham gia</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <!-- User Info -->
                            <td>
                                <div class="user-cell">
                                    <?php if (!empty($user['avatar_url'])): ?>
                                        <div class="user-avatar" style="background-image: url('<?= htmlspecialchars($user['avatar_url']) ?>');"></div>
                                    <?php else: ?>
                                        <div class="user-avatar-default">
                                            <?= strtoupper(mb_substr($user['ho_ten'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="user-info">
                                        <div class="user-name"><?= htmlspecialchars($user['ho_ten']) ?></div>
                                        <div class="user-username">@<?= htmlspecialchars($user['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Contact -->
                            <td class="contact-cell">
                                <div class="contact-email"><?= htmlspecialchars($user['email']) ?></div>
                                <div class="contact-phone"><?= htmlspecialchars($user['sdt'] ?? 'N/A') ?></div>
                            </td>
                            
                            <!-- Role -->
                            <td>
                                <?php
                                $roleClass = 'customer';
                                $roleText = 'Khách hàng';
                                
                                if ($user['role'] === 'QUAN_LY_KHO') {
                                    $roleClass = 'staff';
                                    $roleText = 'Nhân viên Kho';
                                } elseif ($user['role'] === 'ADMIN') {
                                    $roleClass = 'admin';
                                    $roleText = 'Quản trị viên';
                                }
                                ?>
                                <span class="role-badge <?= $roleClass ?>"><?= $roleText ?></span>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                <?php
                                $statusClass = $user['status'] === 'active' ? 'active' : 'locked';
                                $statusText = $user['status'] === 'active' ? 'Hoạt động' : 'Đã khóa';
                                ?>
                                <span class="status-badge <?= $statusClass ?>">
                                    <span class="status-dot <?= $statusClass ?>"></span>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            
                            <!-- Date -->
                            <td class="date-cell">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                            
                            <!-- Actions -->
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined" style="font-size: 1.25rem;">edit</span>
                                    </button>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <button class="action-btn danger" title="Khóa tài khoản">
                                            <span class="material-symbols-outlined" style="font-size: 1.25rem;">lock</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="action-btn success" title="Mở khóa">
                                            <span class="material-symbols-outlined" style="font-size: 1.25rem;">lock_open</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <span class="material-symbols-outlined" style="font-size: 2rem;">search_off</span>
                        </div>
                        <h3 class="empty-title">Không tìm thấy người dùng</h3>
                        <p class="empty-text">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($users) && $pagination['total_pages'] > 0): ?>
            <div class="pagination-wrapper">
                <div class="pagination-container">
                    <!-- Info -->
                    <div class="pagination-info">
                        Hiển thị <strong><?= $pagination['from'] ?></strong> đến <strong><?= $pagination['to'] ?></strong> của <strong><?= number_format($pagination['total_records']) ?></strong> kết quả
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="pagination-nav" aria-label="Pagination">
                        <!-- Previous -->
                        <?php if ($pagination['has_prev']): ?>
                            <a href="<?= buildUrl($pagination['prev_page']) ?>" class="pagination-link pagination-icon">
                                <span class="material-symbols-outlined" style="font-size: 1.25rem;">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <span class="pagination-link pagination-icon disabled">
                                <span class="material-symbols-outlined" style="font-size: 1.25rem;">chevron_left</span>
                            </span>
                        <?php endif; ?>
                        
                        <!-- Page Numbers -->
                        <?php
                        $currentPage = $pagination['current_page'];
                        $totalPages = $pagination['total_pages'];
                        
                        // Logic hiển thị trang: 1 ... 4 5 [6] 7 8 ... 100
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);
                        
                        // Trang đầu
                        if ($start > 1) {
                            echo '<a href="' . buildUrl(1) . '" class="pagination-link">1</a>';
                            if ($start > 2) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                        }
                        
                        // Các trang giữa
                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $currentPage) {
                                echo '<a href="' . buildUrl($i) . '" class="pagination-link active" aria-current="page">' . $i . '</a>';
                            } else {
                                echo '<a href="' . buildUrl($i) . '" class="pagination-link' . ($i > 3 && $i < $totalPages - 2 ? ' hide-mobile' : '') . '">' . $i . '</a>';
                            }
                        }
                        
                        // Trang cuối
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                            echo '<a href="' . buildUrl($totalPages) . '" class="pagination-link hide-mobile">' . $totalPages . '</a>';
                        }
                        ?>
                        
                        <!-- Next -->
                        <?php if ($pagination['has_next']): ?>
                            <a href="<?= buildUrl($pagination['next_page']) ?>" class="pagination-link pagination-icon">
                                <span class="material-symbols-outlined" style="font-size: 1.25rem;">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span class="pagination-link pagination-icon disabled">
                                <span class="material-symbols-outlined" style="font-size: 1.25rem;">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
    </div>
</div>

<script>
// ============================================================================
// JAVASCRIPT - XỬ LÝ TÌM KIẾM VÀ LỌC
// ============================================================================

const BASE_URL = '<?= BASE_URL ?>';

// Function để build URL với các filters
function applyFilters() {
    const keyword = document.getElementById('searchInput').value.trim();
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    // Build URL với query params
    const params = new URLSearchParams();
    
    if (keyword) {
        params.append('q', keyword);
    }
    
    if (role) {
        params.append('role', role);
    }
    
    if (status) {
        params.append('status', status);
    }
    
    // Redirect với params mới
    const queryString = params.toString();
    const newUrl = BASE_URL + '/admin/users' + (queryString ? '?' + queryString : '');
    window.location.href = newUrl;
}

// Search input - tìm kiếm khi gõ (debounce)
let searchTimeout;
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 800); // Đợi 800ms sau khi người dùng ngừng gõ
});

// Enter trong search input
searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        applyFilters();
    }
});

// Button lọc
document.getElementById('applyFilterBtn').addEventListener('click', function() {
    applyFilters();
});

// Enter trong combobox
document.getElementById('roleFilter').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

document.getElementById('statusFilter').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>