<?php
/**
 * ADMIN - QUẢN LÝ NGƯỜI DÙNG
 * [ĐANG PHÁT TRIỂN] - Tự implement giao diện tại đây
 * 
 * Data có sẵn từ Controller:
 * - $users      : Danh sách users (khi implement xong)
 * - $filters    : Các filter hiện tại (keyword, role, status)
 * - $pagination : Thông tin phân trang
 * - $csrf_token : Token bảo mật cho form
 * 
 * Cấu trúc $users[]:
 * - ID           : ID user
 * - Tai_khoan    : Username
 * - Ho_ten       : Họ tên
 * - Email        : Email
 * - Sdt          : Số điện thoại
 * - Phan_quyen   : ADMIN / QUAN_LY_KHO / KH
 * - Trang_thai   : active / inactive / banned
 * - Ngay_tao     : Ngày tạo tài khoản
 */
?>
<?php include __DIR__ . '/layouts/header.php'; ?>

<style>
/* ========================================
   USER MANAGEMENT - TỰ VIẾT CSS Ở ĐÂY
   ======================================== */

.user-page {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

.user-page-header {
    margin-bottom: 24px;
}

.user-page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 8px 0;
}

.user-page-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

/* TODO: Thêm CSS của bạn ở đây... */

</style>

<div class="user-page">
    <!-- Header -->
    <div class="user-page-header">
        <h1 class="user-page-title">Quản lý Người dùng</h1>
        <p class="user-page-subtitle">Quản lý tài khoản khách hàng và nhân viên</p>
    </div>
    
    <!-- TODO: Bắt đầu thiết kế của bạn từ đây -->
    <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 60px 40px; text-align: center;">
        <div style="width: 80px; height: 80px; background: rgba(123, 192, 67, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="fas fa-paint-brush" style="font-size: 32px; color: #7BC043;"></i>
        </div>
        <h3 style="font-size: 20px; font-weight: 600; color: #1e293b; margin: 0 0 8px;">Trang đang được phát triển</h3>
        <p style="font-size: 14px; color: #64748b; margin: 0;">
            Tự implement giao diện và logic quản lý người dùng tại đây
        </p>
    </div>
    
</div>

<script>
// JavaScript của bạn ở đây
const csrfToken = '<?= $csrf_token ?? '' ?>';
const baseUrl = '<?= BASE_URL ?>';

// TODO: Implement các functions gọi API
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>