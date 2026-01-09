-- =====================================================
-- FIX ORDER STATUS: Cập nhật trạng thái đơn hàng
-- =====================================================
-- 
-- Thay đổi các trạng thái:
-- - cho_xac_nhan -> dang_xu_ly (Đang xử lý)
-- - dang_giao (giữ nguyên - Đang giao)
-- - da_giao_hang -> da_giao (Đã giao)
-- - huy (giữ nguyên - Trả hàng/Hủy đơn)
--
-- CÁCH DÙNG: Copy toàn bộ vào phpMyAdmin -> Tab SQL -> Go
-- =====================================================

-- Bước 1: Thêm giá trị ENUM mới tạm thời
ALTER TABLE don_hang MODIFY COLUMN Trang_thai 
    ENUM('cho_xac_nhan', 'dang_giao', 'da_giao_hang', 'huy', 'dang_xu_ly', 'da_giao') 
    DEFAULT 'dang_xu_ly';

-- Bước 2: Cập nhật dữ liệu từ giá trị cũ sang giá trị mới
UPDATE don_hang SET Trang_thai = 'dang_xu_ly' WHERE Trang_thai = 'cho_xac_nhan';
UPDATE don_hang SET Trang_thai = 'da_giao' WHERE Trang_thai = 'da_giao_hang';

-- Bước 3: Xóa giá trị ENUM cũ, chỉ giữ lại 4 giá trị mới
ALTER TABLE don_hang MODIFY COLUMN Trang_thai 
    ENUM('dang_xu_ly', 'dang_giao', 'da_giao', 'huy') 
    DEFAULT 'dang_xu_ly';

-- Xác nhận cấu trúc mới
DESCRIBE don_hang;

-- Kiểm tra và xóa nếu đã tồn tại
DELETE FROM tai_khoan WHERE ID = 999999;

-- Tạo tài khoản POS System
INSERT INTO tai_khoan (ID, Tai_khoan, Mat_khau, Email, Ho_ten, Phan_quyen, Trang_thai)
VALUES (999999, 'POS_SYSTEM', 'LOCKED_NO_LOGIN', 'pos@system.internal', 'Khách vãng lai (Tại quầy)', 'KH', 'active');

-- Verify
SELECT * FROM tai_khoan WHERE ID = 999999;

