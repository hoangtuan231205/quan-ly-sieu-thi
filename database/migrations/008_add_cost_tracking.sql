-- =====================================================
-- MIGRATION 008: Add Cost Tracking to chi_tiet_don_hang
-- =====================================================
-- Author: System
-- Date: 2026-01-04
-- Purpose: Add Don_gia_von and ID_chi_tiet_nhap for FEFO tracking
-- =====================================================

-- 1. Thêm cột giá vốn (để tính lãi/lỗ chính xác)
ALTER TABLE chi_tiet_don_hang
ADD COLUMN IF NOT EXISTS Don_gia_von DECIMAL(15,2) DEFAULT 0 
    COMMENT 'Giá vốn tại thời điểm bán (copy từ lô hàng)';

-- 2. Thêm cột FK đến lô hàng (cho FEFO tracking)
ALTER TABLE chi_tiet_don_hang
ADD COLUMN IF NOT EXISTS ID_chi_tiet_nhap INT DEFAULT NULL
    COMMENT 'FK → chi_tiet_phieu_nhap (lô hàng được xuất)';

-- 3. Tạo index cho join nhanh
CREATE INDEX IF NOT EXISTS idx_ctdh_lo 
ON chi_tiet_don_hang(ID_chi_tiet_nhap);

-- 4. Tạo index cho query profit report
CREATE INDEX IF NOT EXISTS idx_ctdh_gia_von 
ON chi_tiet_don_hang(Don_gia_von);

-- =====================================================
-- Verification
-- =====================================================
-- SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'chi_tiet_don_hang' 
-- AND COLUMN_NAME IN ('Don_gia_von', 'ID_chi_tiet_nhap');

SELECT 'Migration 008 completed: Added Don_gia_von and ID_chi_tiet_nhap to chi_tiet_don_hang' AS Status;
