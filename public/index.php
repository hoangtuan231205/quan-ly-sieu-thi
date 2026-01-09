<?php
// Start output buffering immediately to catch any spurious whitespace or errors before headers
ob_start();
/**
 * =============================================================================
 * INDEX.PHP - ENTRY POINT (ĐIỂM VÀO DUY NHẤT CỦA WEBSITE)
 * =============================================================================
 * 
 * Tất cả requests đều đi qua file này nhờ .htaccess
 * 
 * LUỒNG HOẠT ĐỘNG:
 * 1. Load config
 * 2. Khởi động session
 * 3. Load core classes
 * 4. Chạy router (App)
 * 5. Router gọi Controller tương ứng
 * 6. Controller gọi Model và View
 * 7. Trả về HTML cho người dùng
 */

// =============================================================================
// 1. LOAD CONFIG
// =============================================================================
// BẮT BUỘC: LOAD COMPOSER AUTOLOAD
require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/config.php';

// =============================================================================
// 2. KHỞI ĐỘNG SESSION
// =============================================================================

Session::start();

// =============================================================================
// 3. LOAD CORE CLASSES
// =============================================================================

// Nhờ có autoload trong config.php, không cần require thủ công
// Các class sẽ tự động load khi cần:
// - Database
// - Session
// - Middleware
// - Controller
// - Model

// =============================================================================
// 4. CHẠY ROUTER
// =============================================================================
$app = new App();

// Router sẽ tự động:
// - Phân tích URL
// - Gọi Controller tương ứng
// - Truyền params nếu có
// - Hiển thị 404 nếu không tìm thấy

?>