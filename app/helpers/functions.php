<?php
/**
 * =============================================================================
 * HELPER FUNCTIONS - Các hàm tiện ích
 * =============================================================================
 * 
 * Các hàm giúp việc development dễ dàng hơn
 */

/**
 * Lấy đường dẫn đến asset (CSS, JS, hình ảnh)
 * 
 * @param string $path Đường dẫn tương đối từ thư mục assets
 * @return string URL đầy đủ
 * 
 * CÁCH DÙNG:
 * echo asset('css/style.css');     // http://localhost/sieu_thi/public/assets/css/style.css
 * echo asset('js/main.js');        // http://localhost/sieu_thi/public/assets/js/main.js
 * echo asset('img/logo.png');      // http://localhost/sieu_thi/public/assets/img/logo.png
 */
if (!function_exists('asset')) {
    function asset($path) {
        return ASSETS_DIR . '/' . ltrim($path, '/');
    }
}

/**
 * Lấy đường dẫn đến file upload
 * 
 * @param string $path Đường dẫn tương đối từ thư mục uploads
 * @return string URL đầy đủ
 * 
 * CÁCH DÙNG:
 * echo upload('products/image1.jpg');  // http://localhost/sieu_thi/public/uploads/products/image1.jpg
 */
if (!function_exists('upload')) {
    function upload($path) {
        return UPLOADS_DIR . '/' . ltrim($path, '/');
    }
}

/**
 * Redirect đến URL khác
 * 
 * @param string $url URL cần redirect đến
 * @param int $statusCode HTTP status code
 * 
 * CÁCH DÙNG:
 * redirect(BASE_URL . '/products');
 * redirect(BASE_URL . '/auth/login', 302);
 */
if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit();
    }
}

/**
 * Kiểm tra xem request có phải POST không
 * 
 * @return bool
 */
if (!function_exists('is_post')) {
    function is_post() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

/**
 * Kiểm tra xem request có phải GET không
 * 
 * @return bool
 */
if (!function_exists('is_get')) {
    function is_get() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

/**
 * Lấy giá trị từ $_GET
 * 
 * @param string $key Tên key
 * @param mixed $default Giá trị mặc định nếu không tồn tại
 * @return mixed
 */
if (!function_exists('get_input')) {
    function get_input($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}

/**
 * Lấy giá trị từ $_GET (shorthand)
 * 
 * @param string $key Tên key
 * @param mixed $default Giá trị mặc định nếu không tồn tại
 * @return mixed
 */
if (!function_exists('get')) {
    function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}

/**
 * Lấy giá trị từ $_POST
 * 
 * @param string $key Tên key
 * @param mixed $default Giá trị mặc định nếu không tồn tại
 * @return mixed
 */
if (!function_exists('post_input')) {
    function post_input($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
}

// Alias cho post_input
if (!function_exists('post')) {
    function post($key, $default = null) {
        return post_input($key, $default);
    }
}

/**
 * Escape HTML (ngăn XSS attack)
 * 
 * @param string $string
 * @return string
 */
if (!function_exists('escape')) {
    function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Format tiền tệ VND
 * 
 * @param float $amount
 * @return string
 * 
 * CÁCH DÙNG:
 * echo format_currency(150000);  // 150.000 ₫
 */
if (!function_exists('format_currency')) {
    function format_currency($amount) {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
}

/**
 * Format giá tiền (alias cho format_currency)
 */
if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        return format_currency($price);
    }
}

/**
 * Format ngày tháng
 * 
 * @param string $date Ngày cần format (Y-m-d)
 * @param string $format Format mới
 * @return string
 * 
 * CÁCH DÙNG:
 * echo format_date('2024-12-25', 'd/m/Y');  // 25/12/2024
 */
if (!function_exists('format_date')) {
    function format_date($date, $format = 'd/m/Y') {
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    }
}

/**
 * Lấy URL của page hiện tại
 * 
 * @return string
 */
if (!function_exists('current_url')) {
    function current_url() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
               '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}

/**
 * Lấy controller hiện tại
 * 
 * @return string
 */
if (!function_exists('current_controller')) {
    function current_controller() {
        return $_GET['controller'] ?? 'home';
    }
}

/**
 * Lấy action hiện tại
 * 
 * @return string
 */
if (!function_exists('current_action')) {
    function current_action() {
        return $_GET['action'] ?? 'index';
    }
}

/**
 * Kiểm tra user đã login chưa
 * 
 * @return bool
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

/**
 * Lấy user hiện tại
 * 
 * @return array|null
 */
if (!function_exists('current_user')) {
    function current_user() {
        return $_SESSION['user'] ?? null;
    }
}

/**
 * Kiểm tra user có phải admin không
 * 
 * @return bool
 */
if (!function_exists('is_admin')) {
    function is_admin() {
        return is_logged_in() && (current_user()['vai_tro'] ?? null) === 'admin';
    }
}

/**
 * Dump và dừng chương trình (debug)
 * 
 * @param mixed $var Biến cần dump
 */
if (!function_exists('dd')) {
    function dd($var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        exit();
    }
}

/**
 * Dump biến (debug) mà không dừng
 * 
 * @param mixed $var Biến cần dump
 */
if (!function_exists('dump')) {
    function dump($var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}
/**
 * Láy giá trị từ $_REQUEST (GET hoặc POST)
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('request')) {
    function request($key, $default = null) {
        return $_REQUEST[$key] ?? $default;
    }
}

/**
 * Redirect về trang trước
 */
if (!function_exists('back')) {
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        redirect($referer);
    }
}

/**
 * Lấy URL đầy đủ từ path
 * @param string $path
 * @return string
 */
if (!function_exists('url')) {
    function url($path = '') {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}

/**
 * Escape HTML (alias cho escape)
 * @param string $string
 * @return string
 */
if (!function_exists('e')) {
    function e($string) {
        return escape($string); // Make 'e' an alias for the existing 'escape' function
    }
}

/**
 * Kiểm tra email hợp lệ
 */
if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

/**
 * Kiểm tra số điện thoại Việt Nam
 */
if (!function_exists('isValidPhone')) {
    function isValidPhone($phone) {
        return preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone);
    }
}

/**
 * Sanitize string
 */
if (!function_exists('sanitize')) {
    function sanitize($string) {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Lấy giá trị từ array bằng dot notation
 */
if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null) {
        if (isset($array[$key])) {
            return $array[$key];
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
}

/**
 * Tạo slug từ string
 */
if (!function_exists('str_slug')) {
    function str_slug($string) {
        $string = strtolower($string);
        // Replace Vietnamese characters to latin
        $q = [
            "a" => "á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ",
            "d" => "đ",
            "e" => "é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ",
            "i" => "í|ì|ỉ|ĩ|ị",
            "o" => "ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ",
            "u" => "ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự",
            "y" => "ý|ỳ|ỷ|ỹ|ỵ",
        ];
        foreach ($q as $replace => $search) {
            $string = preg_replace("/($search)/i", $replace, $string);
        }
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }
}

/**
 * Cắt chuỗi và thêm ...
 */
if (!function_exists('str_limit')) {
    function str_limit($string, $length = 100, $suffix = '...') {
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        return mb_substr($string, 0, $length) . $suffix;
    }
}