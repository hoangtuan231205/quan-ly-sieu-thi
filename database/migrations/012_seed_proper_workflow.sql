-- =====================================================
-- MIGRATION 012: Reset and Seed Data via Proper Workflow (FIXED)
-- =====================================================
-- Author: System
-- Date: 2026-01-04
-- Purpose: Reset stock to 0 and create proper warehouse import data
-- FIX: Disable triggers during seed to avoid conflict
-- =====================================================

-- Disable triggers temporarily
SET @DISABLE_TRIGGERS = 1;

-- STEP 1: Reset all product stock to 0
UPDATE san_pham SET So_luong_ton = 0, Gia_nhap = Gia_tien * 0.8;

-- STEP 2: Clear old import data
DELETE FROM chi_tiet_phieu_nhap;
DELETE FROM phieu_nhap_kho WHERE ID_phieu_nhap > 0;
DELETE FROM ma_phieu_sequence;

-- STEP 3: Create warehouse import record
INSERT INTO phieu_nhap_kho (Ma_hien_thi, Nguoi_tao, Ngay_nhap, Ghi_chu, ID_ncc, Trang_thai) 
VALUES ('PNK20260104-INIT', 1, '2026-01-04', 'Nhập kho khởi tạo hệ thống - Full HSD', 1, 'da_duyet');

SET @phieu_id = LAST_INSERT_ID();

-- STEP 4: Create temp table with product info
DROP TEMPORARY TABLE IF EXISTS tmp_products;
CREATE TEMPORARY TABLE tmp_products AS
SELECT ID_sp, Ten, Gia_tien, ID_danh_muc FROM san_pham;

-- STEP 5: Insert chi tiết phiếu nhập theo danh mục
-- SỮA (HSD 6 tháng, qty 100)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 100, Gia_tien * 0.8, Gia_tien * 0.8 * 100, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 100
FROM tmp_products WHERE ID_danh_muc IN (1,2,3,4);

-- RAU LÁ (HSD 10 ngày, qty 50)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 50, Gia_tien * 0.8, Gia_tien * 0.8 * 50, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 50
FROM tmp_products WHERE ID_danh_muc = 6;

-- CỦ QUẢ (HSD 21 ngày, qty 80)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 80, Gia_tien * 0.8, Gia_tien * 0.8 * 80, DATE_ADD(CURDATE(), INTERVAL 21 DAY), 80
FROM tmp_products WHERE ID_danh_muc = 7;

-- TRÁI CÂY (HSD 14 ngày, qty 60)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 60, Gia_tien * 0.8, Gia_tien * 0.8 * 60, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 60
FROM tmp_products WHERE ID_danh_muc = 8;

-- HÓA PHẨM (HSD 24 tháng, qty 150)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 150, Gia_tien * 0.8, Gia_tien * 0.8 * 150, DATE_ADD(CURDATE(), INTERVAL 24 MONTH), 150
FROM tmp_products WHERE ID_danh_muc IN (9,10,11,12);

-- CHĂM SÓC CÁ NHÂN (HSD 18 tháng, qty 120)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 120, Gia_tien * 0.8, Gia_tien * 0.8 * 120, DATE_ADD(CURDATE(), INTERVAL 18 MONTH), 120
FROM tmp_products WHERE ID_danh_muc IN (13,14,15,16);

-- THỊT TƯƠI (HSD 7 ngày, qty 30)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 30, Gia_tien * 0.8, Gia_tien * 0.8 * 30, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 30
FROM tmp_products WHERE ID_danh_muc = 18;

-- HẢI SẢN (HSD 5 ngày, qty 25)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 25, Gia_tien * 0.8, Gia_tien * 0.8 * 25, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 25
FROM tmp_products WHERE ID_danh_muc = 19;

-- BÁNH KẸO (HSD 12 tháng, qty 200)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 200, Gia_tien * 0.8, Gia_tien * 0.8 * 200, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 200
FROM tmp_products WHERE ID_danh_muc = 21;

-- MÌ (HSD 8 tháng, qty 300)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 300, Gia_tien * 0.8, Gia_tien * 0.8 * 300, DATE_ADD(CURDATE(), INTERVAL 8 MONTH), 300
FROM tmp_products WHERE ID_danh_muc = 22;

-- THỰC PHẨM KHÔ (HSD 12 tháng, qty 150)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 150, Gia_tien * 0.8, Gia_tien * 0.8 * 150, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 150
FROM tmp_products WHERE ID_danh_muc = 23;

-- THỰC PHẨM CHẾ BIẾN (HSD 30 ngày, qty 40)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 40, Gia_tien * 0.8, Gia_tien * 0.8 * 40, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 40
FROM tmp_products WHERE ID_danh_muc = 24;

-- THỰC PHẨM ĐÔNG LẠNH (HSD 6 tháng, qty 50)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 50, Gia_tien * 0.8, Gia_tien * 0.8 * 50, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 50
FROM tmp_products WHERE ID_danh_muc = 25;

-- BIA (HSD 12 tháng, qty 200)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 200, Gia_tien * 0.8, Gia_tien * 0.8 * 200, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 200
FROM tmp_products WHERE ID_danh_muc = 27;

-- GIẢI KHÁT (HSD 9 tháng, qty 150)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 150, Gia_tien * 0.8, Gia_tien * 0.8 * 150, DATE_ADD(CURDATE(), INTERVAL 9 MONTH), 150
FROM tmp_products WHERE ID_danh_muc = 28;

-- ĐỒ DÙNG BẾP (Không HSD, qty 20)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 20, Gia_tien * 0.8, Gia_tien * 0.8 * 20, NULL, 20
FROM tmp_products WHERE ID_danh_muc = 29;

-- GIA VỊ (HSD 18 tháng, qty 100)
INSERT INTO chi_tiet_phieu_nhap (ID_phieu_nhap, ID_sp, Ten_sp, So_luong, Don_gia_nhap, Thanh_tien, Ngay_het_han, So_luong_con)
SELECT @phieu_id, ID_sp, Ten, 100, Gia_tien * 0.8, Gia_tien * 0.8 * 100, DATE_ADD(CURDATE(), INTERVAL 18 MONTH), 100
FROM tmp_products WHERE ID_danh_muc = 30;

-- STEP 6: Update stock manually (since triggers are disabled)
UPDATE san_pham sp
JOIN (
    SELECT ID_sp, SUM(So_luong) as total_qty
    FROM chi_tiet_phieu_nhap
    WHERE ID_phieu_nhap = @phieu_id
    GROUP BY ID_sp
) ct ON sp.ID_sp = ct.ID_sp
SET sp.So_luong_ton = ct.total_qty;

-- STEP 7: Update phieu_nhap tổng tiền
UPDATE phieu_nhap_kho 
SET Tong_tien = (SELECT SUM(Thanh_tien) FROM chi_tiet_phieu_nhap WHERE ID_phieu_nhap = @phieu_id)
WHERE ID_phieu_nhap = @phieu_id;

DROP TEMPORARY TABLE IF EXISTS tmp_products;
SET @DISABLE_TRIGGERS = 0;

-- =====================================================
-- VERIFICATION
-- =====================================================
SELECT 'Phiếu nhập kho:' AS Info;
SELECT ID_phieu_nhap, Ma_hien_thi, FORMAT(Tong_tien, 0) as Tong_tien, Trang_thai FROM phieu_nhap_kho;

SELECT 'Chi tiết nhập kho:' AS Info;
SELECT COUNT(*) AS 'Total_batches', SUM(So_luong) AS 'Total_items' FROM chi_tiet_phieu_nhap;

SELECT 'Tồn kho sản phẩm:' AS Info;
SELECT COUNT(*) AS 'Products_with_stock' FROM san_pham WHERE So_luong_ton > 0;

SELECT 'Sản phẩm sắp hết hạn (30 ngày):' AS Info;
SELECT COUNT(*) AS 'Expiring_soon' FROM chi_tiet_phieu_nhap 
WHERE Ngay_het_han IS NOT NULL AND Ngay_het_han <= DATE_ADD(CURDATE(), INTERVAL 30 DAY);

SELECT 'Migration 012 FIXED completed successfully!' AS Status;
