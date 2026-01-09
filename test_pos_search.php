<?php
// Test script for POS Search
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

echo "=== Testing POS Search ===\n\n";

$db = Database::getInstance();

// Test 1: Check if there are any products
$allProducts = $db->query("SELECT COUNT(*) as total FROM san_pham WHERE Trang_thai = 'active' AND So_luong_ton > 0")->fetch();
echo "1. Total active products with stock: " . $allProducts['total'] . "\n\n";

// Test 2: Search for "sữa"
echo "2. Search for 'sữa':\n";
$results = $db->query("SELECT ID_sp, Ma_hien_thi, Ten, Gia_tien, So_luong_ton, Don_vi_tinh FROM san_pham WHERE Trang_thai = 'active' AND So_luong_ton > 0 AND (Ten LIKE ? OR Ma_hien_thi LIKE ?) LIMIT 5", ['%sữa%', '%sữa%'])->fetchAll();

if (empty($results)) {
    echo "   No results found\n";
} else {
    foreach ($results as $p) {
        echo "   - {$p['Ma_hien_thi']} | {$p['Ten']} | {$p['Gia_tien']}d | Stock: {$p['So_luong_ton']}\n";
    }
}

// Test 3: Search for just "a"
echo "\n3. Search for 'a':\n";
$results2 = $db->query("SELECT ID_sp, Ma_hien_thi, Ten, Gia_tien FROM san_pham WHERE Trang_thai = 'active' AND So_luong_ton > 0 AND (Ten LIKE ? OR Ma_hien_thi LIKE ?) LIMIT 5", ['%a%', '%a%'])->fetchAll();

if (empty($results2)) {
    echo "   No results found\n";
} else {
    foreach ($results2 as $p) {
        echo "   - {$p['Ma_hien_thi']} | {$p['Ten']} | {$p['Gia_tien']}d\n";
    }
}

// Test 4: Check if POS account exists
echo "\n4. POS System Account (ID=999999):\n";
$posAccount = $db->query("SELECT ID, Tai_khoan, Ho_ten FROM tai_khoan WHERE ID = 999999")->fetch();
if ($posAccount) {
    echo "   EXISTS: {$posAccount['Tai_khoan']} - {$posAccount['Ho_ten']}\n";
} else {
    echo "   NOT FOUND! You need to run setup_pos_account.sql\n";
}

echo "\n=== Test Complete ===\n";
