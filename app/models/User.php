<?php
/**
 * =============================================================================
 * USER MODEL - QUẢN LÝ TÀI KHOẢN
 * =============================================================================
 * 
 * Bảng: tai_khoan
 * 
 * Chức năng:
 * - Đăng nhập / Đăng ký
 * - Cập nhật thông tin profile cá nhân
 */

class User extends Model {
    
    protected $table = 'tai_khoan';
    protected $primaryKey = 'ID';
    
    /**
     * ==========================================================================
     * AUTHENTICATION
     * ==========================================================================
     */
    
    /**
     * Đăng nhập
     * 
     * @param string $username Tên đăng nhập hoặc email
     * @param string $password Mật khẩu
     * @return array|false Thông tin user nếu thành công, false nếu thất bại
     */
    public function login($username, $password) {
        // Tìm user theo username hoặc email
        $sql = "SELECT * FROM {$this->table} 
                WHERE (Tai_khoan = ? OR Email = ?) 
                AND Trang_thai = 'active'
                LIMIT 1";
        
        $user = $this->db->query($sql, [$username, $username])->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Kiểm tra password
        if (password_verify($password, $user['Mat_khau'])) {
            // Không trả về password
            unset($user['Mat_khau']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Tìm user theo username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE Tai_khoan = ? LIMIT 1";
        return $this->db->query($sql, [$username])->fetch();
    }
    
    /**
     * Tìm user theo email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE Email = ? LIMIT 1";
        return $this->db->query($sql, [$email])->fetch();
    }
    
    /**
     * Đăng ký user mới
     * 
     * @param array $data
     * @return int|false User ID nếu thành công
     */
    public function register($data) {
        // Hash password
        if (isset($data['Mat_khau'])) {
            $data['Mat_khau'] = password_hash($data['Mat_khau'], PASSWORD_DEFAULT);
        }
        
        // Mặc định role = KH
        if (!isset($data['Phan_quyen'])) {
            $data['Phan_quyen'] = 'KH';
        }
        
        return $this->create($data);
    }
    
    /**
     * ==========================================================================
     * PROFILE - THÔNG TIN CÁ NHÂN
     * ==========================================================================
     */
    
    /**
     * Cập nhật profile user
     * 
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile($userId, $data) {
        // Loại bỏ các field không được phép update
        unset($data['Tai_khoan'], $data['Phan_quyen'], $data['Trang_thai'], $data['Ngay_tao']);
        
        return $this->update($userId, $data);
    }
    
    /**
     * Đổi mật khẩu
     * 
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        // Lấy user
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }
        
        // Kiểm tra mật khẩu cũ
        if (!password_verify($oldPassword, $user['Mat_khau'])) {
            return false;
        }
        
        // Hash mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật
        $sql = "UPDATE {$this->table} SET Mat_khau = ? WHERE ID = ?";
        $this->db->query($sql, [$hashedPassword, $userId]);
        
        return $this->db->rowCount() > 0;
    }
    
    
}