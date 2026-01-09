<?php
/**
 * Admin Top Header Component
 */
?>
<header class="admin-header">
    <!-- Search Bar -->
    <div class="header-search">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="Tìm kiếm sản phẩm, đơn hàng..." class="search-input">
    </div>

    <!-- Right Actions -->
    <div class="header-actions">
        <!-- Notifications -->
        <button class="header-btn notification-btn">
            <i class="fas fa-bell"></i>
            <?php if (!empty($pending_count) && $pending_count > 0): ?>
                <span class="notification-badge"><?= $pending_count ?></span>
            <?php endif; ?>
        </button>

        <!-- Divider -->
        <div class="header-divider"></div>

        <!-- Quick Action -->
        <a href="<?= BASE_URL ?>/public/pos" class="btn-primary-action">
            <i class="fas fa-cash-register"></i>
            <span>Bán hàng</span>
        </a>
    </div>
</header>
