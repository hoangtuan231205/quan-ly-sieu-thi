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
    
    /**
     * ==========================================================================
     * ADMIN - QUẢN LÝ NGƯỜI DÙNG
     * ==========================================================================
     */
    
    /**
     * Lấy danh sách người dùng với tìm kiếm, lọc và phân trang
     * 
     * @param string|null $keyword Từ khóa tìm kiếm (ho_ten, email, sdt, username)
     * @param string|null $role Lọc theo vai trò (ADMIN, QUAN_LY_KHO, KH)
     * @param string|null $status Lọc theo trạng thái (active, locked)
     * @param int $limit Số bản ghi trên 1 trang
     * @param int $offset Vị trí bắt đầu
     * @return array Danh sách users
     */
    public function getUsers($keyword = null, $role = null, $status = null, $limit = 10, $offset = 0) {
        $sql = "SELECT 
                    ID,
                    Tai_khoan as username,
                    Ho_ten as ho_ten,
                    Email as email,
                    Sdt as sdt,
                    Phan_quyen as role,
                    Trang_thai as status,
                    Ngay_tao as created_at
                FROM {$this->table}
                WHERE 1=1";
        
        $params = [];
        
        // Tìm kiếm theo keyword
        if (!empty($keyword)) {
            $sql .= " AND (
                        Ho_ten LIKE ? OR 
                        Email LIKE ? OR 
                        Sdt LIKE ? OR 
                        Tai_khoan LIKE ?
                    )";
            $searchTerm = "%{$keyword}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Lọc theo vai trò
        if (!empty($role)) {
            $sql .= " AND Phan_quyen = ?";
            $params[] = $role;
        }
        
        // Lọc theo trạng thái
        if (!empty($status)) {
            $sql .= " AND Trang_thai = ?";
            $params[] = $status;
        }
        
        // Sắp xếp theo ngày tạo mới nhất
        $sql .= " ORDER BY Ngay_tao DESC";
        
        // Phân trang - PHẢI cast sang int vì PDO không thể bind LIMIT/OFFSET
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Đếm tổng số người dùng theo điều kiện
     * 
     * @param string|null $keyword Từ khóa tìm kiếm
     * @param string|null $role Lọc theo vai trò
     * @param string|null $status Lọc theo trạng thái
     * @return int Tổng số bản ghi
     */
    public function countUsers($keyword = null, $role = null, $status = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Tìm kiếm theo keyword
        if (!empty($keyword)) {
            $sql .= " AND (
                        Ho_ten LIKE ? OR 
                        Email LIKE ? OR 
                        Sdt LIKE ? OR 
                        Tai_khoan LIKE ?
                    )";
            $searchTerm = "%{$keyword}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Lọc theo vai trò
        if (!empty($role)) {
            $sql .= " AND Phan_quyen = ?";
            $params[] = $role;
        }
        
        // Lọc theo trạng thái
        if (!empty($status)) {
            $sql .= " AND Trang_thai = ?";
            $params[] = $status;
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Lấy thống kê người dùng cho dashboard
     * 
     * @return array Thống kê theo vai trò và trạng thái
     */
    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN Phan_quyen = 'KH' THEN 1 ELSE 0 END) as total_customers,
                    SUM(CASE WHEN Phan_quyen = 'QUAN_LY_KHO' THEN 1 ELSE 0 END) as total_staff,
                    SUM(CASE WHEN Phan_quyen = 'ADMIN' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN Trang_thai = 'active' THEN 1 ELSE 0 END) as total_active,
                    SUM(CASE WHEN Trang_thai = 'locked' THEN 1 ELSE 0 END) as total_locked,
                    SUM(CASE WHEN DATE(Ngay_tao) = CURDATE() THEN 1 ELSE 0 END) as new_today
                FROM {$this->table}";
        
        return $this->db->query($sql)->fetch();
    }
    
    
}