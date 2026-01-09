<?php
/**
 * Trang thành công - Đặt hàng thành công
 * File: app/views/customer/checkout_success.php
 */
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .success-page {
        padding: 60px 0;
        background: linear-gradient(135deg, #E1EBDA 0%, #F0F8F0 100%);
        min-height: 100vh;
    }
    
    .success-container {
        max-width: var(--max-width-narrow);
        margin: 0 auto;
    }
    
    .success-box {
        background: var(--color-white);
        border-radius: var(--radius-lg);
        padding: var(--spacing-2xl) var(--spacing-xl);
        text-align: center;
        box-shadow: var(--shadow-md);
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        background: var(--color-woodland);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--spacing-lg);
        font-size: 40px;
        color: var(--color-white);
    }
    
    .success-title {
        font-size: 28px;
        font-weight: var(--font-weight-bold);
        color: var(--color-port-gore);
        margin-bottom: var(--spacing-md);
    }
    
    .success-message {
        font-size: 16px;
        color: var(--color-gray-600);
        margin-bottom: var(--spacing-lg);
    }
    
    .order-info {
        background: var(--color-gray-100);
        border-radius: var(--radius-md);
        padding: var(--spacing-lg);
        margin: var(--spacing-lg) 0;
        text-align: left;
    }
    
    .order-info-row {
        display: flex;
        justify-content: space-between;
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--color-willow-brook);
    }
    
    .order-info-row:last-child {
        border-bottom: none;
    }
    
    .order-info-label {
        font-weight: var(--font-weight-semibold);
        color: var(--color-gray-700);
    }
    
    .order-info-value {
        color: var(--color-gray-600);
    }
    
    .order-items {
        text-align: left;
        margin: 30px 0;
    }
    
    .order-items-title {
        font-size: 18px;
        font-weight: 700;
        color: #291D51;
        margin-bottom: 16px;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #E1EBDA;
    }
    
    .order-item-name {
        flex: 1;
    }
    
    .order-item-qty {
        text-align: center;
        min-width: 50px;
    }
    
    .order-item-price {
        text-align: right;
        min-width: 100px;
        font-weight: var(--font-weight-semibold);
        color: var(--color-woodland);
    }
    
    .action-buttons {
        display: flex;
        gap: 16px;
        justify-content: center;
        margin-top: 40px;
        flex-wrap: wrap;
    }
    
    .btn-primary {
        padding: 14px 40px;
        background: #496C2C;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary:hover {
        background: #291D51;
    }
    
    .btn-secondary {
        padding: 14px 40px;
        background: #E1EBDA;
        color: #496C2C;
        border: 2px solid #496C2C;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-secondary:hover {
        background: #D5E5CC;
    }
    
    .delivery-info {
        background: #FFF3CD;
        border-left: 4px solid #FFC107;
        padding: 16px;
        border-radius: 4px;
        margin: 30px 0;
        text-align: left;
    }
    
    .delivery-info-title {
        font-weight: 700;
        color: #856404;
        margin-bottom: 8px;
    }
    
    .delivery-info-text {
        color: #856404;
        font-size: 14px;
        line-height: 1.6;
    }
</style>

<section class="success-page">
    <div class="success-container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/public/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/public/cart">Giỏ hàng</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/public/checkout">Thanh toán</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hoàn tất</li>
            </ol>
        </nav>
        
        <div class="success-box">
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <!-- Success Message -->
            <h1 class="success-title">Đặt hàng thành công!</h1>
            <p class="success-message">
                Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận.
            </p>
            
            <!-- Order Information -->
            <div class="order-info">
                <div class="order-info-row">
                    <span class="order-info-label">Mã đơn hàng:</span>
                    <span class="order-info-value">
                        <strong>#<?= htmlspecialchars($order['ID_dh']) ?></strong>
                    </span>
                </div>
                
                <div class="order-info-row">
                    <span class="order-info-label">Ngày đặt hàng:</span>
                    <span class="order-info-value">
                        <?= date('d/m/Y H:i', strtotime($order['Ngay_dat'])) ?>
                    </span>
                </div>
                
                <div class="order-info-row">
                    <span class="order-info-label">Tên người nhận:</span>
                    <span class="order-info-value">
                        <?= htmlspecialchars($order['Ten_nguoi_nhan']) ?>
                    </span>
                </div>
                
                <div class="order-info-row">
                    <span class="order-info-label">Số điện thoại:</span>
                    <span class="order-info-value">
                        <?= htmlspecialchars($order['Sdt_nguoi_nhan']) ?>
                    </span>
                </div>
                
                <div class="order-info-row">
                    <span class="order-info-label">Địa chỉ giao hàng:</span>
                    <span class="order-info-value">
                        <?= htmlspecialchars($order['Dia_chi_giao_hang']) ?>
                    </span>
                </div>
                
                <div class="order-info-row">
                    <span class="order-info-label">Trạng thái đơn hàng:</span>
                    <span class="order-info-value">
                        <span style="background: #FFC107; color: #000; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            Chờ xác nhận
                        </span>
                    </span>
                </div>
            </div>
            
            <!-- Delivery Info -->
            <div class="delivery-info">
                <div class="delivery-info-title">
                    <i class="fas fa-truck me-2"></i> Thông tin giao hàng
                </div>
                <div class="delivery-info-text">
                    Đơn hàng của bạn sẽ được giao trong vòng 2-3 ngày làm việc. 
                    Bạn sẽ nhận được thông báo cập nhật qua email và SMS khi hàng được giao.
                </div>
            </div>
            
            <!-- Order Items -->
            <?php if (!empty($order_details)): ?>
            <div class="order-items">
                <div class="order-items-title">Danh sách sản phẩm</div>
                
                <?php foreach ($order_details as $item): ?>
                <div class="order-item">
                    <div class="order-item-name">
                        <?= htmlspecialchars($item['Ten_sp']) ?>
                    </div>
                    <div class="order-item-qty">
                        x<?= $item['So_luong'] ?>
                    </div>
                    <div class="order-item-price">
                        <?= number_format($item['Thanh_tien'], 0, ',', '.') ?>đ
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div style="padding-top: 16px; border-top: 2px solid #496C2C; margin-top: 16px;">
                    <div class="order-item" style="border-bottom: none;">
                        <strong>Tổng cộng</strong>
                        <strong style="color: #496C2C;">
                            <?= number_format($order['Thanh_tien'], 0, ',', '.') ?>đ
                        </strong>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= BASE_URL ?>/public/products" class="btn-secondary">
                    <i class="fas fa-shopping-bag me-2"></i> Tiếp tục mua hàng
                </a>
                <a href="<?= BASE_URL ?>/public/orders" class="btn-primary">
                    <i class="fas fa-list me-2"></i> Xem đơn hàng của tôi
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// Cập nhật giỏ hàng trên header
document.addEventListener('DOMContentLoaded', function() {
    console.log('Updating cart count...');
    fetch('<?= BASE_URL ?>/public/cart/count', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Cart count response:', data);
            if (data.count !== undefined) {
                const badge = document.querySelector('.cart-badge');
                console.log('Cart badge element:', badge);
                if (badge) {
                    badge.textContent = data.count;
                    console.log('Updated cart badge to:', data.count);
                }
            }
        })
        .catch(err => console.error('Error updating cart count:', err));
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
