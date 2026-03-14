</main>
<!-- Main Content End -->

<!-- ============================================================================
     FOOTER - Thông tin công ty, liên hệ, mạng xã hội
     ============================================================================ -->
<footer class="main-footer">
    
    <!-- Footer Top - Thông tin chính -->
    <div class="footer-top">
        <div class="container">
            <div class="row g-4">
                
                <!-- Cột 1: Về FreshMart -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Về FreshMart</h5>
                        <div class="footer-logo mb-3">
                            <div class="logo">
                                <div class="logo-icon">FM</div>
                                <span class="logo-text">FreshMart</span>
                            </div>
                        </div>
                        <p class="footer-desc">
                            Siêu thị thực phẩm tươi sống chất lượng cao, 
                            giao hàng nhanh chóng đến tận nhà.
                        </p>
                        <div class="social-links">
                            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" title="Youtube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Cột 2: Danh mục sản phẩm -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Danh Mục</h5>
                        <ul class="footer-links">
                            <li><a href="<?= BASE_URL ?>/products?category=1"><i class="fas fa-chevron-right me-2"></i>Sữa các loại</a></li>
                            <li><a href="<?= BASE_URL ?>/products?category=5"><i class="fas fa-chevron-right me-2"></i>Rau - Củ - Trái Cây</a></li>
                            <li><a href="<?= BASE_URL ?>/products?category=17"><i class="fas fa-chevron-right me-2"></i>Thịt - Hải Sản</a></li>
                            <li><a href="<?= BASE_URL ?>/products?category=20"><i class="fas fa-chevron-right me-2"></i>Đồ Ăn</a></li>
                            <li><a href="<?= BASE_URL ?>/products?category=26"><i class="fas fa-chevron-right me-2"></i>Đồ Uống</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Cột 3: Hỗ trợ khách hàng -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Hỗ Trợ Khách Hàng</h5>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Chính sách giao hàng</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Chính sách đổi trả</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Chính sách bảo mật</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Điều khoản sử dụng</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Câu hỏi thường gặp</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Cột 4: Liên hệ -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Liên Hệ</h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <span>Hotline: <strong>1900-xxxx</strong></span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>support@freshmart.vn</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Thứ 2 - CN: 7:00 - 22:00</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom - Copyright -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="copyright mb-0">
                        &copy; <?= date('Y') ?> FreshMart. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <div class="payment-methods">
                        <span class="me-3">Phương thức thanh toán: Visa, Mastercard, MoMo, ZaloPay</span>
                        <?php /* Payment icons temporarily disabled
                        <img src="<?= asset('img/payment/visa.png') ?>" alt="Visa" class="payment-icon">
                        <img src="<?= asset('img/payment/mastercard.png') ?>" alt="Mastercard" class="payment-icon">
                        <img src="<?= asset('img/payment/momo.png') ?>" alt="MoMo" class="payment-icon">
                        <img src="<?= asset('img/payment/zalopay.png') ?>" alt="ZaloPay" class="payment-icon">
                        */ ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTop">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Notification Toast Container -->
<div class="notification-container" id="notificationContainer"></div>

<!-- Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 1. Error Handler FIRST -->
<script src="<?= asset('js/error-handler.js') ?>"></script>

<!-- 2. Core Utilities -->
<script src="<?= asset('js/utils.js') ?>"></script>

<!-- 3. Feature Scripts -->
<script src="<?= asset('js/form-validation.js') ?>"></script>

<!-- 4. Main Application -->
<script src="<?= asset('js/main.js') ?>?v=1.0.7"></script>

<!-- 5. Additional Page-specific Scripts -->
<?php if (isset($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?= asset('js/' . $js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Flash Messages Handler -->
<?php if (Session::hasFlash('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof showNotification === 'function') {
                showNotification('<?= Session::getFlash('success') ?>', 'success');
            }
        });
    </script>
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof showNotification === 'function') {
                showNotification('<?= Session::getFlash('error') ?>', 'error');
            }
        });
    </script>
<?php endif; ?>