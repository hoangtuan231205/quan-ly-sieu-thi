<?php
/**
 * =============================================================================
 * AUTH CONTROLLER - QUẢN LÝ ĐĂNG NHẬP, ĐĂNG KÝ
 * =============================================================================
 */

class AuthController extends Controller {
    
    private $user;
    
    public function __construct() {
        $this->user = $this->model('User');
    }
    
    /**
     * Hiển thị form đăng nhập
     */
    public function login() {
        // Nếu đã đăng nhập, chuyển hướng
        if ($this->isLoggedIn()) {
            redirect(BASE_URL . '/');
        }
        
        $data = [];
        $this->view('auth/login', $data);
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập email và mật khẩu';
            redirect(BASE_URL . '/auth/login');
        }
        
        // Tìm user
        $user = $this->user->findByEmail($email);
        
        if (!$user) {
            $_SESSION['error'] = 'Email không tồn tại';
            redirect(BASE_URL . '/auth/login');
        }
        
        // Kiểm tra mật khẩu
        if (!password_verify($password, $user['Mat_khau'])) {
            $_SESSION['error'] = 'Mật khẩu không đúng';
            redirect(BASE_URL . '/auth/login');
        }
        
        // Đăng nhập thành công
        $_SESSION['user'] = [
            'ID' => $user['ID'],
            'Tai_khoan' => $user['Tai_khoan'] ?? '',
            'Ho_ten' => $user['Ho_ten'] ?? '',
            'Email' => $user['Email'] ?? '',
            'Sdt' => $user['Sdt'] ?? '',
            'Dia_chi' => $user['Dia_chi'] ?? '',
            'Phan_quyen' => $user['Phan_quyen'] ?? '',
            'Trang_thai' => $user['Trang_thai'] ?? '',
        ];
        
        // Set session for Session class
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_name'] = $user['Ho_ten'];
        $_SESSION['user_role'] = $user['Phan_quyen'];
        
        $_SESSION['success'] = 'Đăng nhập thành công';
        
        // Initialize cart count in session
        $cartModel = $this->model('Cart');
        $cartCount = $cartModel->getCartCount($user['ID']);
        Session::setCartCount($cartCount);
        
        // Chuyển hướng theo vai trò
        if ($user['Phan_quyen'] == 'ADMIN') {
            redirect(BASE_URL);
        } elseif ($user['Phan_quyen'] == 'QUAN_LY_KHO') {
            redirect(BASE_URL);
        } else {
            redirect(BASE_URL . '/');
        }
    }
    
    /**
     * Hiển thị form đăng ký
     */
    public function register() {
        // Nếu đã đăng nhập, chuyển hướng
        if ($this->isLoggedIn()) {
            redirect(BASE_URL . '/');
        }
        
        $data = [];
        $this->view('auth/register', $data);
    }
    
    /**
     * Xử lý đăng ký
     */
    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Validate
        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin';
            redirect(BASE_URL . '/auth/register');
        }
        
        // Validate username - cho phép chữ, số, dấu gạch dưới, tiếng Việt
        if (strlen($username) < 3) {
            $_SESSION['error'] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
            redirect(BASE_URL . '/auth/register');
        }
        
        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Mật khẩu không khớp';
            redirect(BASE_URL . '/auth/register');
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            redirect(BASE_URL . '/auth/register');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ';
            redirect(BASE_URL . '/auth/register');
        }
        
        // Kiểm tra email đã tồn tại
        if ($this->user->findByEmail($email)) {
            $_SESSION['error'] = 'Email đã được sử dụng';
            redirect(BASE_URL . '/auth/register');
        }
        
        // Tạo user mới
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $newUser = [
            'Tai_khoan' => $username,
            'Ho_ten' => $name,
            'Email' => $email,
            'Mat_khau' => $hashedPassword,
            'Phan_quyen' => 'KH'
        ];
        
        if ($this->user->create($newUser)) {
            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập';
            redirect(BASE_URL . '/auth/login');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại';
            redirect(BASE_URL . '/auth/register');
        }
    }
    
    /**
     * Đăng xuất
     */
    public function logout() {
        session_destroy();
        $_SESSION = [];
        redirect(BASE_URL . '/');
    }
    
    /**
     * Kiểm tra đã đăng nhập chưa
     */
    private function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
}
?>
