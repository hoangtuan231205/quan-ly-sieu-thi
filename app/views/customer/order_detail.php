<?php
/**
 * Chi tiết đơn hàng
 * File: app/views/customer/order_detail.php
 */
include __DIR__ . '/../layouts/header.php';
?>

<style>
/* Main Layout */
.detail-page-wrapper { background: #f7f7f7; min-height: 80vh; padding-bottom: 60px; }
.detail-container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

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
.detail-box { background: var(--color-white); border-radius: var(--radius-lg); padding: var(--spacing-lg); margin-bottom: var(--spacing-md); box-shadow: var(--shadow-sm); }
.detail-title { font-size: 18px; font-weight: var(--font-weight-bold); color: var(--color-port-gore); margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-sm); border-bottom: 2px solid var(--color-willow-brook); }
.info-row { display: flex; justify-content: space-between; padding: var(--spacing-sm) 0; border-bottom: 1px solid var(--color-gray-200); }
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--color-gray-600); font-weight: var(--font-weight-semibold); }
.info-value { color: var(--color-gray-700); font-weight: var(--font-weight-bold); }
.status-badge { padding: 8px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
.status-badge.pending { background: #FFF3CD; color: #856404; }
.status-badge.shipping { background: #CCE5FF; color: #004085; }
.status-badge.completed { background: #D4EDDA; color: #155724; }
.status-badge.cancelled { background: #F8D7DA; color: #721C24; }
.order-item-detail { display: flex; gap: var(--spacing-md); padding: var(--spacing-md); background: var(--color-gray-50); border-radius: var(--radius-md); margin-bottom: var(--spacing-sm); }
.order-item-img { width: 64px; height: 64px; object-fit: cover; border-radius: var(--radius-md); }
.order-item-info { flex: 1; }
.order-item-name { font-weight: var(--font-weight-bold); color: var(--color-port-gore); margin-bottom: var(--spacing-xs); }
.order-item-price { color: var(--color-woodland); font-weight: var(--font-weight-bold); font-size: 16px; }
.timeline { padding: var(--spacing-md) 0; }
.timeline-item { display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-lg); position: relative; }
.timeline-item.completed .timeline-dot { background: var(--color-woodland); }
.timeline-item.pending .timeline-dot { background: var(--color-gray-400); }
.timeline-dot { width: 20px; height: 20px; border-radius: var(--radius-full); background: var(--color-gray-400); flex-shrink: 0; position: relative; top: 4px; display: flex; align-items: center; justify-content: center; color: var(--color-white); font-size: 11px; }
.timeline-item.completed .timeline-dot::before { content: '✓'; }
.timeline-item.cancelled .timeline-dot { background: #dc3545; }
.timeline-item.cancelled .timeline-dot::before { content: '✕'; }
.timeline-label { font-weight: var(--font-weight-bold); color: var(--color-port-gore); margin-bottom: 4px; }
.timeline-date { color: var(--color-gray-600); font-size: 14px; }
.btn-back { padding: 10px 24px; background: #E1EBDA; color: #496C2C; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; }
.btn-back:hover { background: #CEEEBF; }
.btn-cancel { padding: 10px 24px; background: #dc3545; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
.btn-cancel:hover { background: #a71d2a; }
.total-section { background: #f9f9f9; padding: 16px; border-radius: 8px; }
.total-row { display: flex; justify-content: space-between; padding: var(--spacing-sm) 0; }
.total-row.grand-total { font-size: 18px; font-weight: var(--font-weight-bold); color: var(--color-woodland); border-top: 2px solid var(--color-willow-brook); padding-top: var(--spacing-md); }
.back-button-container { margin-bottom: 20px; }
</style>

<div class="detail-page-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <a href="<?= BASE_URL ?>/">Trang chủ</a>
        <span>›</span>
        <a href="<?= BASE_URL ?>/orders">Đơn hàng của tôi</a>
        <span>›</span>
        <span>Chi tiết đơn hàng</span>
    </div>

    <div class="detail-container">
        <div class="back-button-container">
            <a href="<?= BASE_URL ?>/orders" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>
                Quay lại
            </a>
        </div>

        <h2 class="page-title">
            <i class="fas fa-receipt me-2"></i>
            Đơn hàng #DH<?= date('Ymd', strtotime($order['Ngay_dat'])) ?><?= str_pad($order['ID_dh'], 2, '0', STR_PAD_LEFT) ?>
        </h2>

        <div class="row">
            <!-- Left Column - Order Details -->
            <div class="col-lg-8">
                <!-- Order Status -->
                <div class="detail-box">
                    <div class="detail-title">Trạng thái đơn hàng</div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">Ngày đặt hàng</div>
                            <div class="fw-bold" style="font-weight: var(--font-weight-bold); color: var(--color-port-gore); font-size: 16px;">
                                <?= date('d/m/Y H:i', strtotime($order['Ngay_dat'])) ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <?php
                            $statusClass = 'pending';
                            $statusText = 'Chờ xác nhận';
                            
                            switch ($order['Trang_thai']) {
                                case 'dang_xu_ly':
                                    $statusClass = 'pending';
                                    $statusText = 'Chờ xác nhận';
                                    break;
                                case 'dang_giao':
                                    $statusClass = 'shipping';
                                    $statusText = 'Đang giao hàng';
                                    break;
                                case 'da_giao':
                                    $statusClass = 'completed';
                                    $statusText = 'Đã giao hàng';
                                    break;
                                case 'huy':
                                    $statusClass = 'cancelled';
                                    $statusText = 'Đã hủy';
                                    break;
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="timeline">
                        <?php foreach ($timeline as $item): ?>
                        <div class="timeline-item <?= $item['completed'] ? 'completed' : 'pending' ?> <?= isset($item['is_cancelled']) && $item['is_cancelled'] ? 'cancelled' : '' ?>">
                            <div class="timeline-dot"></div>
                            <div style="flex: 1; padding-top: 2px;">
                                <div class="timeline-label"><?= $item['label'] ?></div>
                                <?php if ($item['date']): ?>
                                <div class="timeline-date"><?= $item['date'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="detail-box">
                    <div class="detail-title">Thông tin giao hàng</div>
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-user me-2"></i>
                            Người nhận
                        </span>
                        <span class="info-value"><?= htmlspecialchars($order['Ten_nguoi_nhan']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-phone me-2"></i>
                            Số điện thoại
                        </span>
                        <span class="info-value"><?= htmlspecialchars($order['Sdt_nguoi_nhan']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Địa chỉ
                        </span>
                        <span class="info-value"><?= htmlspecialchars($order['Dia_chi_giao_hang']) ?></span>
                    </div>
                    
                    <?php if (!empty($order['Ghi_chu'])): ?>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-sticky-note me-2"></i>
                            Ghi chú
                        </span>
                        <span class="info-value"><?= htmlspecialchars($order['Ghi_chu']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Items -->
                <div class="detail-box">
                    <div class="detail-title">Sản phẩm đã đặt</div>
                    
                    <?php if (!empty($order_details)): ?>
                        <?php foreach ($order_details as $item): ?>
                        <div class="order-item-detail">
                            <img src="<?= asset('img/products/' . ($item['Hinh_anh'] ?? 'placeholder-product.png')) ?>" 
                                 alt="<?= htmlspecialchars($item['Ten_sp']) ?>"
                                 class="order-item-img"
                                 onerror="this.src='<?= asset('img/placeholder-product.png') ?>'">
                            <div class="order-item-info">
                                <div class="order-item-name">
                                    <a href="<?= BASE_URL ?>/products/detail/<?= $item['ID_sp'] ?>" 
                                       style="text-decoration: none; color: #291D51;">
                                        <?= htmlspecialchars($item['Ten_sp']) ?>
                                    </a>
                                </div>
                                <div style="color: #666; font-size: 14px; margin-bottom: 8px;">
                                    Số lượng: <strong><?= $item['So_luong'] ?></strong> x <?= htmlspecialchars($item['Don_vi_tinh'] ?? '') ?>
                                </div>
                                <div class="order-item-price">
                                    <?= number_format($item['Thanh_tien'], 0, ',', '.') ?>đ
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="detail-box">
                    <div class="detail-title">Tóm tắt đơn hàng</div>
                    
                    <div class="total-section">
                        <div class="total-row">
                            <span>Tạm tính:</span>
                            <span><?= number_format($order['Thanh_tien'] - ($order['Phi_van_chuyen'] ?? 0), 0, ',', '.') ?>đ</span>
                        </div>
                        
                        <div class="total-row">
                            <span>Phí vận chuyển:</span>
                            <span><?= number_format($order['Phi_van_chuyen'] ?? 0, 0, ',', '.') ?>đ</span>
                        </div>
                        
                        <div class="total-row grand-total">
                            <span>Tổng cộng:</span>
                            <span><?= number_format($order['Thanh_tien'], 0, ',', '.') ?>đ</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="detail-box">
                    <div class="detail-title">Phương thức thanh toán</div>
                    <div style="padding: 16px; background: #FFF3CD; border-radius: 8px; color: #856404; text-align: center;">
                        <i class="fas fa-credit-card me-2"></i>
                        <strong>Thanh toán online</strong>
                    </div>
                </div>

                <!-- Actions -->
                <div class="detail-box">
                    <div style="display: flex; gap: 12px; flex-direction: column;">
                        <a href="<?= BASE_URL ?>/orders" class="btn-back" style="text-align: center; width: 100%; box-sizing: border-box;">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại lịch sử
                        </a>
                        
                        <?php if ($can_cancel): ?>
                        <button onclick="cancelOrder(<?= $order['ID_dh'] ?>)" class="btn-cancel" style="width: 100%;">
                            <i class="fas fa-times me-2"></i>
                            Hủy đơn hàng
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="detail-box">
                    <div style="text-align: center; padding: 16px 0;">
                        <div style="font-size: 32px; margin-bottom: 16px;">
                            <i class="fas fa-headset" style="color: #496C2C;"></i>
                        </div>
                        <div style="color: #666; margin-bottom: 12px;">Cần hỗ trợ?</div>
                        <a href="<?= BASE_URL ?>/contact" style="color: #496C2C; font-weight: 700; text-decoration: none;">
                            Liên hệ với chúng tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) return;
    
    fetch('<?= BASE_URL ?>/orders/cancel', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(r => {
        console.log('Response status:', r.status);
        console.log('Response headers:', r.headers.get('content-type'));
        return r.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            if (data.success) {
                showNotification('Đã hủy đơn hàng thành công', 'success');
                setTimeout(() => location.href = '<?= BASE_URL ?>/orders', 1000);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            showNotification('Lỗi: Response không phải JSON hợp lệ', 'error');
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>