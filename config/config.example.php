<?php
/**
 * =============================================================================
 * CONFIG FILE - SAO CHÉP FILE NÀY THÀNH config.php VÀ ĐIỀN THÔNG TIN THẬT
 * =============================================================================
 * 
 * CÁCH DÙNG:
 * 1. Copy file này: cp config.example.php config.php
 * 2. Điền thông tin database và URL của bạn vào config.php
 * 3. config.php đã được .gitignore (không bị commit lên git)
 */

// Enable error display for debugging (tắt khi production!)
error_reporting(E_ALL);
ini_set('display_errors', 1); // ← Đổi thành 0 khi deploy production
ini_set('log_errors', 1);

// AUTOLOADER
spl_autoload_register(function ($className) {
    $basePath = dirname(__DIR__);
    $classPaths = [
        'core'        => $basePath . '/app/core/',
        'controllers' => $basePath . '/app/controllers/',
        'models'      => $basePath . '/app/models/',
    ];
    foreach ($classPaths as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

define('SESSION_NAME',     'SIEU_THI_SESSION');
define('SESSION_LIFETIME', 86400); // 1 ngày

define('DEBUG_MODE', true); // ← Đổi thành false khi production

// PATH CONFIG
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('LOGS_PATH',   ROOT_PATH . '/logs');

// ========================
// ĐỔI CÁC GIÁ TRỊ NÀY ↓
// ========================

// APPLICATION CONFIG — đổi URL cho phù hợp với môi trường của bạn
define('BASE_URL',            'http://localhost/sieu_thi/public');
define('UPLOADS_DIR',         BASE_URL . '/uploads');
define('ASSETS_DIR',          BASE_URL . '/assets');
define('UPLOAD_PRODUCT_URL',  ASSETS_DIR . '/img/products');
define('UPLOAD_PRODUCT_PATH', PUBLIC_PATH . '/assets/img/products');

// DATABASE CONFIG — điền thông tin database thật của bạn
define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    'YOUR_DATABASE_PASSWORD_HERE'); // ← đổi cái này
define('DB_NAME',    'sieu_thi');
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');

// ========================

// INCLUDE HELPERS
require_once dirname(__DIR__) . '/app/helpers/functions.php';
require_once dirname(__DIR__) . '/app/helpers/image-helper.php';

// (Phần class Config giữ nguyên như file gốc)
