-- =====================================================
-- MIGRATION: Create Audit Log Table
-- File: 007_create_audit_log_table.sql
-- Purpose: Log ALL changes (WHO changed WHAT WHEN)
-- =====================================================

CREATE TABLE IF NOT EXISTS audit_log (
    ID_log INT PRIMARY KEY AUTO_INCREMENT,
    
    -- WHO: Người thực hiện thay đổi
    User_id INT NOT NULL COMMENT 'ID người dùng thực hiện',
    User_name VARCHAR(100) COMMENT 'Tên người dùng (cache)',
    User_role VARCHAR(50) COMMENT 'Quyền của người dùng',
    
    -- WHAT: Thay đổi gì
    Table_name VARCHAR(100) NOT NULL COMMENT 'Tên bảng bị thay đổi',
    Record_id INT NOT NULL COMMENT 'ID record bị thay đổi',
    Action_type ENUM('INSERT', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'EXPORT', 'IMPORT') NOT NULL,
    
    -- DETAILS: Chi tiết thay đổi
    Old_values JSON COMMENT 'Giá trị cũ (JSON)',
    New_values JSON COMMENT 'Giá trị mới (JSON)',
    Changed_fields TEXT COMMENT 'Danh sách fields đã thay đổi',
    Description TEXT COMMENT 'Mô tả hành động',
    
    -- WHEN & WHERE: Thời gian và nguồn
    Ip_address VARCHAR(45) COMMENT 'IP người dùng',
    User_agent TEXT COMMENT 'Browser/Device info',
    Request_url VARCHAR(500) COMMENT 'URL của request',
    
    -- Timestamps
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for fast queries
    INDEX idx_user_action (User_id, Action_type),
    INDEX idx_table_record (Table_name, Record_id),
    INDEX idx_created_at (Created_at),
    INDEX idx_action_type (Action_type)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng lưu lịch sử thay đổi hệ thống';

-- =====================================================
-- Sample data để test
-- =====================================================
-- INSERT INTO audit_log (User_id, User_name, User_role, Table_name, Record_id, Action_type, New_values, Description, Ip_address) 
-- VALUES (1, 'Admin', 'ADMIN', 'san_pham', 1, 'INSERT', '{"Ten": "Sữa tươi"}', 'Thêm sản phẩm mới', '127.0.0.1');
