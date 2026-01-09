-- =====================================================
-- MIGRATION 010: Migrate Old Order Data
-- =====================================================
-- Author: System
-- Date: 2026-01-04
-- Purpose: Set Don_gia_von for existing orders (legacy data)
-- =====================================================

-- BACKUP TRƯỚC KHI CHẠY:
-- mysqldump -u root sieu_thi chi_tiet_don_hang > backup_ctdh_before_010.sql

-- 1. Set Don_gia_von cho các đơn cũ = Gia_nhap của sản phẩm
-- Nếu Gia_nhap = 0, dùng 70% giá bán làm ước tính
UPDATE chi_tiet_don_hang ct
JOIN san_pham sp ON ct.ID_sp = sp.ID_sp
SET ct.Don_gia_von = CASE 
    WHEN sp.Gia_nhap > 0 THEN sp.Gia_nhap
    ELSE sp.Gia_tien * 0.7
END
WHERE ct.Don_gia_von = 0 OR ct.Don_gia_von IS NULL;

-- 2. Log số lượng đã migrate
SELECT CONCAT('Migrated ', COUNT(*), ' order details with Don_gia_von') AS Status
FROM chi_tiet_don_hang 
WHERE Don_gia_von > 0;

-- NOTE: ID_chi_tiet_nhap để NULL cho đơn cũ
-- Vì không thể xác định lô nào đã xuất cho đơn cũ
-- Hệ thống chỉ track lô cho đơn MỚI từ giờ trở đi

-- =====================================================
-- Verification
-- =====================================================
-- Check remaining orders without Don_gia_von
SELECT COUNT(*) AS 'Orders_without_cost' 
FROM chi_tiet_don_hang 
WHERE Don_gia_von = 0 OR Don_gia_von IS NULL;
-- Should return 0

SELECT 'Migration 010 completed: Migrated old orders with Don_gia_von' AS Status;
