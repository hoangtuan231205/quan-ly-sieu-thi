-- =====================================================
-- MIGRATION: Create Suppliers Table
-- File: 006_create_suppliers_table.sql
-- =====================================================

CREATE TABLE IF NOT EXISTS nha_cung_cap (
    ID_ncc INT PRIMARY KEY AUTO_INCREMENT,
    Ma_hien_thi VARCHAR(20) UNIQUE,
    Ten_ncc VARCHAR(200) NOT NULL,
    Dia_chi TEXT,
    Sdt VARCHAR(20),
    Email VARCHAR(100),
    Nguoi_lien_he VARCHAR(100),
    Mo_ta TEXT,
    Trang_thai ENUM('active', 'inactive') DEFAULT 'active',
    Ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_trang_thai (Trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample suppliers
INSERT INTO nha_cung_cap (Ma_hien_thi, Ten_ncc, Dia_chi, Sdt, Email, Nguoi_lien_he, Mo_ta) VALUES
('NCC-001', 'Vinamilk', 'Số 10 Tân Trào, Quận 7, TP.HCM', '028 54155555', 'info@vinamilk.com.vn', 'Nguyễn Văn A', 'Nhà cung cấp sữa và các sản phẩm từ sữa'),
('NCC-002', 'Masan Consumer', 'Số 6 Nguyễn Văn Linh, Quận 7, TP.HCM', '028 62559999', 'contact@masan.com.vn', 'Trần Thị B', 'Thực phẩm đóng gói, gia vị, nước mắm'),
('NCC-003', 'Unilever Vietnam', 'Số 156 Nguyễn Lương Bằng, Quận 7, TP.HCM', '028 54131000', 'info@unilever.com', 'Lê Văn C', 'Hóa phẩm, chăm sóc cá nhân'),
('NCC-004', 'P&G Vietnam', 'VSIP, Bình Dương', '0274 3756789', 'contact@pg.com', 'Phạm Thị D', 'Chất tẩy rửa, chăm sóc gia đình'),
('NCC-005', 'Nestle Vietnam', 'Số 7 Tôn Đức Thắng, Quận 1, TP.HCM', '028 39101010', 'nestlevn@vn.nestle.com', 'Hoàng Văn E', 'Thực phẩm dinh dưỡng, cà phê');
