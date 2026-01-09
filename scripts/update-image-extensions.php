<?php
/**
 * =============================================================================
 * UPDATE IMAGE EXTENSIONS IN DATABASE
 * =============================================================================
 * 
 * Script để cập nhật extension của ảnh trong database
 * sau khi chạy image optimization (PNG → JPG)
 * 
 * CÁCH DÙNG:
 * php scripts/update-image-extensions.php
 */

require_once __DIR__ . '/../config/config.php';

echo "=============================================================================\n";
echo "UPDATE IMAGE EXTENSIONS IN DATABASE\n";
echo "=============================================================================\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all products with images
    $stmt = $db->query("SELECT ID_sp, Hinh_anh FROM san_pham WHERE Hinh_anh IS NOT NULL AND Hinh_anh != ''");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($products) . " products with images\n\n";
    
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($products as $product) {
        $id = $product['ID_sp'];
        $currentImage = $product['Hinh_anh'];
        
        // Remove extension
        $nameWithoutExt = preg_replace('/\.(png|jpg|jpeg)$/i', '', $currentImage);
        
        // Check which file exists
        $jpgPath = __DIR__ . '/../public/assets/img/products/' . $nameWithoutExt . '.jpg';
        $pngPath = __DIR__ . '/../public/assets/img/products/' . $nameWithoutExt . '.png';
        
        $correctExtension = null;
        
        if (file_exists($jpgPath)) {
            $correctExtension = $nameWithoutExt . '.jpg';
        } elseif (file_exists($pngPath)) {
            $correctExtension = $nameWithoutExt . '.png';
        }
        
        if ($correctExtension && $correctExtension !== $currentImage) {
            // Update database
            $updateStmt = $db->prepare("UPDATE san_pham SET Hinh_anh = ? WHERE ID_sp = ?");
            $updateStmt->execute([$correctExtension, $id]);
            
            echo "✅ Updated ID $id: $currentImage → $correctExtension\n";
            $updated++;
        } elseif ($correctExtension === $currentImage) {
            $skipped++;
        } else {
            echo "⚠️  ID $id: File not found for $currentImage\n";
            $errors++;
        }
    }
    
    echo "\n=============================================================================\n";
    echo "SUMMARY:\n";
    echo "  Total products:  " . count($products) . "\n";
    echo "  Updated:         $updated\n";
    echo "  Skipped:         $skipped\n";
    echo "  Errors:          $errors\n";
    echo "=============================================================================\n";
    echo "\n✅ Database update complete!\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
