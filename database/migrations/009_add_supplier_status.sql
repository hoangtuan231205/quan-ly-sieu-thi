-- =====================================================
-- MIGRATION 009: Add Supplier and Status to phieu_nhap_kho
-- =====================================================
-- Author: System
-- Date: 2026-01-04
-- Purpose: Add FK to nha_cung_cap and Trang_thai for workflow
-- =====================================================

-- 1. Thêm FK nhà cung cấp
ALTER TABLE phieu_nhap_kho
ADD COLUMN IF NOT EXISTS ID_ncc INT DEFAULT NULL
    COMMENT 'FK → nha_cung_cap';

-- 2. Thêm trạng thái phiếu nhập
ALTER TABLE phieu_nhap_kho
ADD COLUMN IF NOT EXISTS Trang_thai ENUM('nhap', 'da_duyet', 'huy') DEFAULT 'da_duyet'
    COMMENT 'nhap=draft, da_duyet=confirmed, huy=cancelled';

-- 3. Set tất cả phiếu cũ = da_duyet (đã nhập kho thực tế)
UPDATE phieu_nhap_kho 
SET Trang_thai = 'da_duyet' 
WHERE Trang_thai IS NULL OR Trang_thai = '';

-- 4. Tạo index
CREATE INDEX IF NOT EXISTS idx_pnk_ncc 
ON phieu_nhap_kho(ID_ncc);

CREATE INDEX IF NOT EXISTS idx_pnk_trang_thai 
ON phieu_nhap_kho(Trang_thai);

-- =====================================================
-- Verification
-- =====================================================
-- SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'phieu_nhap_kho' 
-- AND COLUMN_NAME IN ('ID_ncc', 'Trang_thai');

SELECT 'Migration 009 completed: Added ID_ncc and Trang_thai to phieu_nhap_kho' AS Status;
