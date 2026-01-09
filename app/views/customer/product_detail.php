<?php
/**
 * Chi tiết sản phẩm
 * File: app/views/customer/product_detail.php
 */
include __DIR__ . '/../layouts/header.php';
?>

<style>
.product-detail { padding: 40px 0; }
.product-image-main { border-radius: var(--radius-lg); overflow: hidden; background: var(--color-gray-100); }
.product-image-main img { width: 100%; }
.product-info-box { background: var(--color-white); padding: var(--spacing-xl); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
.product-title { font-size: 26px; font-weight: var(--font-weight-bold); color: var(--color-port-gore); margin-bottom: var(--spacing-md); }
.product-price-big { font-size: 28px; font-weight: var(--font-weight-bold); color: var(--color-woodland); margin-bottom: var(--spacing-lg); }
.product-meta-item { display: flex; align-items: center; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm); padding: var(--spacing-sm); background: var(--color-willow-brook); border-radius: var(--radius-md); }
.product-meta-item i { color: var(--color-woodland); font-size: 18px; width: 20px; }
.quantity-selector { display: flex; gap: var(--spacing-sm); align-items: center; margin-bottom: var(--spacing-lg); }
.qty-btn { width: 32px; height: 32px; border: none; background: var(--color-white); color: var(--color-woodland); border-radius: var(--radius-full); cursor: pointer; font-size: 16px; font-weight: var(--font-weight-bold); box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.2s ease; }
.qty-btn:hover { background: var(--color-woodland); color: var(--color-white); }
.qty-input { width: 50px; text-align: center; border: none; background: transparent; padding: var(--spacing-xs); font-size: 16px; font-weight: var(--font-weight-semibold); }
.btn-add-big { width: 100%; height: 48px; background: var(--color-woodland); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-size: 16px; font-weight: var(--font-weight-bold); cursor: pointer; transition: all 0.3s ease; }
.btn-add-big:hover { background: var(--color-port-gore); transform: translateY(-2px); }
.product-tabs { margin-top: var(--spacing-4xl); }
.nav-tabs { border-bottom: 3px solid var(--color-willow-brook); }
.nav-tabs .nav-link { border: none; color: var(--color-gray-600); font-weight: var(--font-weight-semibold); padding: var(--spacing-sm) var(--spacing-lg); }
.nav-tabs .nav-link.active { color: var(--color-woodland); border-bottom: 3px solid var(--color-woodland); }
.tab-content { padding: var(--spacing-xl); background: var(--color-white); border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
.related-products { margin-top: var(--spacing-4xl); }
.stock-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: var(--font-weight-semibold); }
.stock-badge.in-stock { background: var(--color-tea-green); color: var(--color-woodland); }
.stock-badge.low-stock { background: #ffc107; color: #fff; }
.stock-badge.out-of-stock { background: #dc3545; color: #fff; }
</style>

<!-- Breadcrumb -->
<section class="bg-gray-100" style="padding: 20px 0;">
    <div class="container">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/public">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products">Sản phẩm</a></li>
                <?php if (!empty($product['Ten_danh_muc'])): ?>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products?category=<?= $product['ID_danh_muc'] ?>"><?= $product['Ten_danh_muc'] ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['Ten']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Detail -->
<section class="product-detail">
    <div class="container">
        <div class="row">
            
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="product-image-main">
                    <?php if (!empty($product['Hinh_anh'])): ?>
                        <img src="<?= asset('img/products/' . $product['Hinh_anh']) ?>" alt="<?= htmlspecialchars($product['Ten']) ?>">
                    <?php else: ?>
                        <img src="<?= asset('img/placeholder-product.png') ?>" alt="<?= htmlspecialchars($product['Ten']) ?>">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info-box">
                    
                    <!-- Category -->
                    <?php if (!empty($product['Ten_danh_muc'])): ?>
                        <div class="text-muted text-uppercase mb-2" style="font-size: 14px;">
                            <?= htmlspecialchars($product['Ten_danh_muc']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Product Name -->
                    <h1 class="product-title"><?= htmlspecialchars($product['Ten']) ?></h1>
                    
                    <!-- Price -->
                    <div class="product-price-big">
                        <?= number_format($product['Gia_tien'], 0, ',', '.') ?>đ
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-4">
                        <?php if ($product['So_luong_ton'] > 10): ?>
                            <span class="stock-badge in-stock">
                                <i class="fas fa-check-circle me-1"></i> Còn hàng
                            </span>
                        <?php elseif ($product['So_luong_ton'] > 0): ?>
                            <span class="stock-badge low-stock">
                                <i class="fas fa-exclamation-triangle me-1"></i> Chỉ còn <?= $product['So_luong_ton'] ?> sản phẩm
                            </span>
                        <?php else: ?>
                            <span class="stock-badge out-of-stock">
                                <i class="fas fa-times-circle me-1"></i> Hết hàng
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Meta Info -->
                    <div class="product-meta-item">
                        <i class="fas fa-box"></i>
                        <div>
                            <strong>Đơn vị tính:</strong> <?= htmlspecialchars($product['Don_vi_tinh']) ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($product['Xuat_xu'])): ?>
                    <div class="product-meta-item">
                        <i class="fas fa-globe"></i>
                        <div>
                            <strong>Xuất xứ:</strong> <?= htmlspecialchars($product['Xuat_xu']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['Thanh_phan'])): ?>
                    <div class="product-meta-item">
                        <i class="fas fa-list"></i>
                        <div>
                            <strong>Thành phần:</strong> <?= htmlspecialchars($product['Thanh_phan']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($product['So_luong_ton'] > 0): ?>
                    <!-- Quantity Selector & Action Buttons -->
                    <div class="mt-4">
                        <label class="fw-semibold mb-3 d-block text-muted">Số lượng</label>
                        <div class="qty-selector-refined mb-4">
                            <button class="qty-btn-refined" onclick="decreaseQty()">-</button>
                            <input type="number" id="quantity" class="qty-input-refined" value="1" min="1" max="<?= $product['So_luong_ton'] ?>">
                            <button class="qty-btn-refined" onclick="increaseQty()">+</button>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <!-- Add to Cart Button -->
                        <button class="btn-add-big" onclick="addToCart(<?= $product['ID_sp'] ?>, document.getElementById('quantity').value)" style="flex: 1;">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Thêm vào giỏ hàng
                        </button>
                        <!-- Buy Now Button -->
                        <button class="btn-add-big" onclick="buyNow(<?= $product['ID_sp'] ?>, document.getElementById('quantity').value)" style="background: var(--color-port-gore); flex: 1;">
                            Mua ngay
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <div class="d-flex gap-4 mt-5 pt-4 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #f0fdf4; color: #00D084;">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size: 12px; color: var(--color-port-gore);">FAST DELIVERY</div>
                                <div class="text-muted" style="font-size: 11px;">Within 2 hours</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #f0fdf4; color: #00D084;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size: 12px; color: var(--color-port-gore);">QUALITY CHECK</div>
                                <div class="text-muted" style="font-size: 11px;">100% Organic</div>
                            </div>
                        </div>
                    </div>

                    <?php else: ?>
                    <button class="btn-add-big" disabled style="background: var(--color-gray-500); cursor: not-allowed;">
                        <i class="fas fa-ban me-2"></i>
                        Sản phẩm đã hết hàng
                    </button>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
        
        <!-- Product Tabs -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Mô tả sản phẩm</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="description">
                    <?php if (!empty($product['Mo_ta_sp'])): ?>
                        <div style="line-height: 1.8;" class="text-dark">
                            <?= nl2br(htmlspecialchars($product['Mo_ta_sp'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có mô tả cho sản phẩm này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <h3 class="section-title">Sản phẩm liên quan</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $item): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <a href="<?= BASE_URL ?>/products/detail/<?= $item['ID_sp'] ?>">
                                <?php if (!empty($item['Hinh_anh'])): ?>
                                    <img src="<?= asset('img/products/' . $item['Hinh_anh']) ?>" alt="<?= htmlspecialchars($item['Ten']) ?>">
                                <?php else: ?>
                                    <img src="<?= asset('img/placeholder-product.png') ?>" alt="<?= htmlspecialchars($item['Ten']) ?>">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="product-info">
                            <h5 class="product-name">
                                <a href="<?= BASE_URL ?>/products/detail/<?= $item['ID_sp'] ?>"><?= htmlspecialchars($item['Ten']) ?></a>
                            </h5>
                            <div class="product-price">
                                <span class="price-current"><?= number_format($item['Gia_tien'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<script>
function increaseQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

// Chức năng Mua ngay - Thanh toán trực tiếp
function buyNow(productId, quantity) {
    // Lấy CSRF token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf_token"]')?.content || '';
    
    // Tạo form và submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/cart/buy-now';
    
    // Thêm product_id
    const productIdInput = document.createElement('input');
    productIdInput.type = 'hidden';
    productIdInput.name = 'product_id';
    productIdInput.value = productId;
    form.appendChild(productIdInput);
    
    // Thêm số lượng
    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = 'quantity';
    quantityInput.value = quantity;
    form.appendChild(quantityInput);
    
    // Thêm CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Thêm form vào body và submit
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>