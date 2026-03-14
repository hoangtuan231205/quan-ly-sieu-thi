<?php
/**
 * =============================================================================
 * AUDIT LOG MODEL - LỊCH SỬ THAY ĐỔI HỆ THỐNG
 * =============================================================================
 * 
 * Bảng: audit_log
 * 
 * Chức năng:
 * - Ghi log mọi thay đổi trong hệ thống
 * - Cung cấp methods tĩnh để dễ dàng log từ bất kỳ đâu
 * - Truy vấn lịch sử thay đổi theo user, table, thời gian
 * 
 * Sử dụng:
 *   AuditLog::log('san_pham', 1, 'INSERT', null, $newData, 'Thêm sản phẩm');
 *   AuditLog::log('san_pham', 1, 'UPDATE', $oldData, $newData, 'Cập nhật giá');
 *   AuditLog::log('san_pham', 1, 'DELETE', $oldData, null, 'Xóa sản phẩm');
 */

class AuditLog {
    private static $db = null;
    private static $table = 'audit_log';
    
    /**
     * Khởi tạo database connection
     */
    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    /**
     * ==========================================================================
     * CORE METHOD: Log một thay đổi vào audit_log
     * ==========================================================================
     * 
     * @param string $tableName Tên bảng bị thay đổi
     * @param int $recordId ID của record
     * @param string $actionType INSERT|UPDATE|DELETE|LOGIN|LOGOUT|EXPORT|IMPORT
     * @param array|null $oldValues Giá trị cũ (null nếu INSERT)
     * @param array|null $newValues Giá trị mới (null nếu DELETE)
     * @param string $description Mô tả hành động
     * @return bool
     */
    public static function log($tableName, $recordId, $actionType, $oldValues = null, $newValues = null, $description = '') {
        try {
            $db = self::getDb();
            
            // Lấy thông tin user hiện tại
            $userId = Session::get('user_id') ?? 0;
            $userName = Session::get('user_name') ?? 'System';
            $userRole = Session::get('user_role') ?? 'SYSTEM';
            
            // Lấy thông tin request
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $requestUrl = $_SERVER['REQUEST_URI'] ?? '';
            
            // Xác định các fields đã thay đổi
            $changedFields = self::getChangedFields($oldValues, $newValues);
            
            $sql = "INSERT INTO " . self::$table . " 
                    (User_id, User_name, User_role, Table_name, Record_id, Action_type, 
                     Old_values, New_values, Changed_fields, Description,
                     Ip_address, User_agent, Request_url)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $userId,
                $userName,
                $userRole,
                $tableName,
                $recordId,
                $actionType,
                $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                $changedFields,
                $description,
                $ipAddress,
                $userAgent,
                $requestUrl
            ];
            
            $db->query($sql, $params);
            return true;
            
        } catch (Exception $e) {
            // Log error nhưng không throw để không ảnh hưởng main operation
            error_log("AuditLog Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ==========================================================================
     * HELPER: Xác định các fields đã thay đổi
     * ==========================================================================
     */
    private static function getChangedFields($oldValues, $newValues) {
        if (!$oldValues || !$newValues) {
            return null;
        }
        
        $changed = [];
        foreach ($newValues as $key => $value) {
            if (!isset($oldValues[$key]) || $oldValues[$key] !== $value) {
                $changed[] = $key;
            }
        }
        
        return implode(', ', $changed);
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log INSERT
     * ==========================================================================
     */
    public static function logInsert($tableName, $recordId, $newData, $description = '') {
        if (empty($description)) {
            $description = "Thêm mới record #{$recordId} vào {$tableName}";
        }
        return self::log($tableName, $recordId, 'INSERT', null, $newData, $description);
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log UPDATE
     * ==========================================================================
     */
    public static function logUpdate($tableName, $recordId, $oldData, $newData, $description = '') {
        if (empty($description)) {
            $description = "Cập nhật record #{$recordId} trong {$tableName}";
        }
        return self::log($tableName, $recordId, 'UPDATE', $oldData, $newData, $description);
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log DELETE
     * ==========================================================================
     */
    public static function logDelete($tableName, $recordId, $oldData, $description = '') {
        if (empty($description)) {
            $description = "Xóa record #{$recordId} khỏi {$tableName}";
        }
        return self::log($tableName, $recordId, 'DELETE', $oldData, null, $description);
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log LOGIN
     * ==========================================================================
     */
    public static function logLogin($userId, $userName) {
        return self::log('tai_khoan', $userId, 'LOGIN', null, ['Ho_ten' => $userName], "Đăng nhập hệ thống");
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log LOGOUT
     * ==========================================================================
     */
    public static function logLogout($userId = null) {
        $userId = $userId ?? (Session::get('user_id') ?? 0);
        return self::log('tai_khoan', $userId, 'LOGOUT', null, null, "Đăng xuất hệ thống");
    }
    
    /**
     * ==========================================================================
     * CONVENIENCE: Log EXPORT
     * ==========================================================================
     */
    public static function logExport($tableName, $recordCount, $description = '') {
        if (empty($description)) {
            $description = "Xuất {$recordCount} records từ {$tableName}";
        }
        return self::log($tableName, 0, 'EXPORT', null, ['count' => $recordCount], $description);
    }
    
    /**
     * ==========================================================================
     * QUERY: Lấy lịch sử thay đổi
     * ==========================================================================
     * 
     * @param array $filters [user_id, table_name, action_type, date_from, date_to]
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public static function getHistory($filters = [], $page = 1, $perPage = 20) {
        $db = self::getDb();
        
        $where = "1=1";
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $where .= " AND User_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['table_name'])) {
            $where .= " AND Table_name = ?";
            $params[] = $filters['table_name'];
        }
        
        if (!empty($filters['action_type'])) {
            $where .= " AND Action_type = ?";
            $params[] = $filters['action_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where .= " AND DATE(Created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where .= " AND DATE(Created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Đếm tổng
        $countSql = "SELECT COUNT(*) as total FROM " . self::$table . " WHERE {$where}";
        $total = $db->query($countSql, $params)->fetch()['total'] ?? 0;
        
        // Lấy dữ liệu có phân trang
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE {$where} 
                ORDER BY Created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
        $logs = $db->query($sql, $params)->fetchAll();
        
        return [
            'data' => $logs,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
    
    /**
     * ==========================================================================
     * QUERY: Lấy lịch sử của một record cụ thể
     * ==========================================================================
     */
    public static function getRecordHistory($tableName, $recordId) {
        $db = self::getDb();
        
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE Table_name = ? AND Record_id = ? 
                ORDER BY Created_at DESC";
        
        return $db->query($sql, [$tableName, $recordId])->fetchAll();
    }
    
    /**
     * ==========================================================================
     * QUERY: Lấy hoạt động gần đây của user
     * ==========================================================================
     */
    public static function getUserActivity($userId, $limit = 10) {
        $db = self::getDb();
        
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE User_id = ? 
                ORDER BY Created_at DESC 
                LIMIT ?";
        
        return $db->query($sql, [$userId, $limit])->fetchAll();
    }
    
    /**
     * ==========================================================================
     * STATS: Thống kê hoạt động
     * ==========================================================================
     */
    public static function getStats($days = 7) {
        $db = self::getDb();
        
        $sql = "SELECT 
                    Action_type,
                    COUNT(*) as count
                FROM " . self::$table . " 
                WHERE Created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY Action_type
                ORDER BY count DESC";
        
        return $db->query($sql, [$days])->fetchAll();
    }
}
