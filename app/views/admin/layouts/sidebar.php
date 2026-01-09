<?php
/**
 * Modern Admin Sidebar
 * Design: SuperMart Template style
 * Theme: #7BC043 (Lime Green)
 */

$currentUri = $_SERVER['REQUEST_URI'] ?? '';

// Helper to check if current URL matches
function isActive($pattern) {
    global $currentUri;
    return strpos($currentUri, $pattern) !== false ? 'active' : '';
}

// Check for dashboard (exact match)
function isDashboard() {
    global $currentUri;
    // Match /admin or /admin/ or /admin/dashboard
    return preg_match('#/admin/?$#', $currentUri) || strpos($currentUri, '/admin/dashboard') !== false;
}

// Get current user info
$userName = $_SESSION['user_name'] ?? 'Admin';
$userInitials = strtoupper(substr($userName, 0, 2));
?>

<aside class="sidebar-modern">
    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-store"></i>
        </div>
        <h1>FreshMart</h1>
    </div>
    
    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>/admin" class="sidebar-nav-item <?= isDashboard() ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Trang chủ</span>
        </a>
        
        <!-- Kho hàng -->
        <a href="<?= BASE_URL ?>/admin/products" class="sidebar-nav-item <?= isActive('products') ?>">
            <i class="fas fa-boxes-stacked"></i>
            <span>Kho hàng</span>
        </a>
        
        <!-- Danh mục -->
        <a href="<?= BASE_URL ?>/admin/categories" class="sidebar-nav-item <?= isActive('categories') ?>">
            <i class="fas fa-layer-group"></i>
            <span>Danh mục</span>
        </a>
        
        <!-- Đơn hàng -->
        <a href="<?= BASE_URL ?>/admin/orders" class="sidebar-nav-item <?= isActive('orders') ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Đơn hàng</span>
        </a>
        
        <!-- Nhà cung cấp -->
        <a href="<?= BASE_URL ?>/admin/suppliers" class="sidebar-nav-item <?= isActive('suppliers') ?>">
            <i class="fas fa-truck"></i>
            <span>Nhà cung cấp</span>
        </a>

        <!-- Phiếu nhập -->
        <a href="<?= BASE_URL ?>/warehouse" class="sidebar-nav-item <?= isActive('warehouse') ?>">
            <i class="fas fa-truck-loading"></i>
            <span>Phiếu nhập</span>
        </a>
        
        <!-- Phiếu hủy -->
        <a href="<?= BASE_URL ?>/admin/disposals" class="sidebar-nav-item <?= isActive('disposal') ?>">
            <i class="fas fa-trash-can"></i>
            <span>Phiếu hủy</span>
        </a>
        
        <!-- Báo cáo -->
        <a href="<?= BASE_URL ?>/admin/reports" class="sidebar-nav-item <?= isActive('report') ?>">
            <i class="fas fa-chart-line"></i>
            <span>Báo cáo</span>
        </a>
        
        <!-- Cài đặt Section -->
        <div class="sidebar-section-title">Cài đặt</div>
        
        <!-- Khách hàng -->
        <a href="<?= BASE_URL ?>/admin/users" class="sidebar-nav-item <?= isActive('users') ?>">
            <i class="fas fa-users"></i>
            <span>Khách hàng</span>
        </a>
        
        <!-- Hệ thống -->
        <a href="#" class="sidebar-nav-item">
            <i class="fas fa-cog"></i>
            <span>Hệ thống</span>
        </a>
    </nav>
    
    <!-- User Section -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= $userInitials ?>
        </div>
        <div class="sidebar-user-info">
            <h4><?= htmlspecialchars($userName) ?></h4>
            <p>Quản lý cửa hàng</p>
        </div>
    </div>
</aside>
