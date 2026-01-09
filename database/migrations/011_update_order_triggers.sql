-- =====================================================
-- MIGRATION 011: Update Order Triggers for FEFO
-- =====================================================
-- Author: System
-- Date: 2026-01-04
-- Purpose: Update triggers to deduct from batch (chi_tiet_phieu_nhap)
-- =====================================================

-- =====================================================
-- 1. DROP OLD TRIGGER
-- =====================================================
DROP TRIGGER IF EXISTS trg_dat_hang_tru_kho;

DELIMITER $$

-- =====================================================
-- 2. NEW TRIGGER: Trừ cả san_pham VÀ lô hàng
-- =====================================================
CREATE TRIGGER trg_dat_hang_tru_kho
AFTER INSERT ON chi_tiet_don_hang
FOR EACH ROW
BEGIN
    -- 1. Trừ tồn kho tổng (san_pham)
    UPDATE san_pham 
    SET So_luong_ton = GREATEST(So_luong_ton - NEW.So_luong, 0) 
    WHERE ID_sp = NEW.ID_sp;
    
    -- 2. Trừ tồn lô nếu có ID_chi_tiet_nhap (FEFO tracking)
    IF NEW.ID_chi_tiet_nhap IS NOT NULL THEN
        UPDATE chi_tiet_phieu_nhap 
        SET So_luong_con = GREATEST(So_luong_con - NEW.So_luong, 0)
        WHERE ID_chi_tiet_nhap = NEW.ID_chi_tiet_nhap;
    END IF;
END$$

-- =====================================================
-- 3. UPDATE TRIGGER HỦY ĐƠN: Hoàn cả lô
-- =====================================================
DROP TRIGGER IF EXISTS trg_huy_don_hoan_kho$$

CREATE TRIGGER trg_huy_don_hoan_kho
AFTER UPDATE ON don_hang
FOR EACH ROW
BEGIN
    -- Chỉ xử lý khi chuyển sang trạng thái 'huy'
    IF NEW.Trang_thai = 'huy' AND OLD.Trang_thai != 'huy' THEN
        -- Hoàn tồn kho tổng (san_pham)
        UPDATE san_pham sp
        INNER JOIN chi_tiet_don_hang ct ON sp.ID_sp = ct.ID_sp
        SET sp.So_luong_ton = sp.So_luong_ton + ct.So_luong
        WHERE ct.ID_dh = NEW.ID_dh;
        
        -- Hoàn tồn lô nếu có ID_chi_tiet_nhap
        UPDATE chi_tiet_phieu_nhap pn
        INNER JOIN chi_tiet_don_hang ct ON pn.ID_chi_tiet_nhap = ct.ID_chi_tiet_nhap
        SET pn.So_luong_con = pn.So_luong_con + ct.So_luong
        WHERE ct.ID_dh = NEW.ID_dh 
          AND ct.ID_chi_tiet_nhap IS NOT NULL;
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- Verification
-- =====================================================
-- SHOW TRIGGERS LIKE 'don_hang';
-- SHOW TRIGGERS LIKE 'chi_tiet_don_hang';

SELECT 'Migration 011 completed: Updated triggers for FEFO batch tracking' AS Status;
