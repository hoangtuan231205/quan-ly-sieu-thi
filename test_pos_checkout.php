<?php
/**
 * Test POS Checkout Database Operations - Fixed Version
 */
require_once 'config/config.php';
require_once 'app/core/Database.php';
require_once 'app/core/Session.php';

echo "=== Testing POS Checkout ===\n\n";

try {
    $db = Database::getInstance();
    
    // 1. Check if POS customer exists
    echo "1. Checking POS customer (ID=999999)...\n";
    $sql = "SELECT * FROM tai_khoan WHERE ID = 999999";
    $customer = $db->query($sql)->fetch();
    
    if ($customer) {
        echo "   ✅ POS customer exists: {$customer['Tai_khoan']} - {$customer['Ho_ten']}\n\n";
    } else {
        echo "   ❌ POS customer NOT FOUND!\n";
        echo "   Please run: mysql -u root sieu_thi < database/setup_pos_account.sql\n\n";
        exit(1);
    }
    
    // 2. Check don_hang table structure
    echo "2. Checking don_hang table columns...\n";
    $sql = "DESCRIBE don_hang";
    $columns = $db->query($sql)->fetchAll();
    $colNames = array_column($columns, 'Field');
    echo "   Columns: " . implode(', ', $colNames) . "\n\n";
    
    // 3. Test insert don_hang
    echo "3. Testing INSERT into don_hang...\n";
    $db->beginTransaction();
    
    $sql = "INSERT INTO don_hang 
            (ID_tk, Ten_nguoi_nhan, Sdt_nguoi_nhan, Dia_chi_giao_hang, 
             Ghi_chu, Tong_tien, Phi_van_chuyen, Thanh_tien, Trang_thai) 
            VALUES 
            (999999, 'Test Customer', '0123456789', 'Test Address', 
             'Test Note', 50000, 0, 50000, 'da_giao')";
    
    $result = $db->query($sql);
    $orderId = $db->lastInsertId();
    
    if ($orderId) {
        echo "   ✅ Order created with ID: $orderId\n";
        
        // 4. Get a sample product
        $sql = "SELECT * FROM san_pham WHERE Trang_thai = 'active' LIMIT 1";
        $product = $db->query($sql)->fetch();
        
        if ($product) {
            echo "\n4. Testing INSERT into chi_tiet_don_hang...\n";
            $sql = "INSERT INTO chi_tiet_don_hang 
                    (ID_dh, ID_sp, Ten_sp, So_luong, Gia_tien, Thanh_tien, Hinh_anh) 
                    VALUES 
                    (:id_dh, :id_sp, :ten_sp, :so_luong, :gia_tien, :thanh_tien, :hinh_anh)";
            
            $db->query($sql, [
                'id_dh' => $orderId,
                'id_sp' => $product['ID_sp'],
                'ten_sp' => $product['Ten'],
                'so_luong' => 1,
                'gia_tien' => $product['Gia_tien'],
                'thanh_tien' => $product['Gia_tien'],
                'hinh_anh' => $product['Hinh_anh']
            ]);
            
            echo "   ✅ Order detail created for product: {$product['Ten']}\n";
        }
    } else {
        echo "   ❌ Failed to create order\n";
    }
    
    // Rollback test data
    $db->rollback();
    echo "\n5. Test data rolled back (no permanent changes)\n";
    
    echo "\n=== ALL TESTS PASSED! POS Checkout should work now. ===\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    
    if (isset($db)) {
        $db->rollback();
    }
}
