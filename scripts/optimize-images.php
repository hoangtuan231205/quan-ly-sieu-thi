<?php
/**
 * =============================================================================
 * IMAGE OPTIMIZATION SCRIPT
 * =============================================================================
 * 
 * Script để tối ưu hóa hình ảnh sản phẩm
 * - Resize ảnh về kích thước phù hợp cho web (max 800x800px)
 * - Nén ảnh để giảm dung lượng
 * - Backup ảnh gốc trước khi xử lý
 * 
 * CÁCH DÙNG:
 * php scripts/optimize-images.php
 */

// Configuration
$sourceDir = __DIR__ . '/../public/assets/img/products';
$backupDir = __DIR__ . '/../public/assets/img/products_backup';
$maxWidth = 800;
$maxHeight = 800;
$jpegQuality = 85;
$pngCompression = 6; // 0-9, higher = more compression

// Statistics
$stats = [
    'total' => 0,
    'optimized' => 0,
    'skipped' => 0,
    'errors' => 0,
    'original_size' => 0,
    'new_size' => 0
];

echo "=============================================================================\n";
echo "IMAGE OPTIMIZATION SCRIPT\n";
echo "=============================================================================\n\n";

// Check if GD library is available
if (!extension_loaded('gd')) {
    die("ERROR: GD library is not installed. Please install php-gd extension.\n");
}

// Create backup directory if it doesn't exist
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✓ Created backup directory: $backupDir\n";
}

// Get all image files
$imageFiles = array_merge(
    glob($sourceDir . '/*.jpg'),
    glob($sourceDir . '/*.jpeg'),
    glob($sourceDir . '/*.png'),
    glob($sourceDir . '/*.gif')
);

$stats['total'] = count($imageFiles);

echo "Found {$stats['total']} images to process\n";
echo "Max dimensions: {$maxWidth}x{$maxHeight}px\n";
echo "JPEG quality: {$jpegQuality}%\n\n";

// Confirm before proceeding
echo "This will optimize all product images. Original files will be backed up.\n";
echo "Do you want to continue? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    die("Operation cancelled.\n");
}

echo "\nProcessing images...\n";
echo "-----------------------------------------------------------------------------\n";

// Process each image
foreach ($imageFiles as $filePath) {
    $filename = basename($filePath);
    $filesize = filesize($filePath);
    $stats['original_size'] += $filesize;
    
    echo sprintf("%-30s %10s", $filename, formatBytes($filesize));
    
    try {
        // Backup original file
        $backupPath = $backupDir . '/' . $filename;
        if (!file_exists($backupPath)) {
            copy($filePath, $backupPath);
        }
        
        // Optimize image
        $result = optimizeImage($filePath, $maxWidth, $maxHeight, $jpegQuality, $pngCompression);
        
        if ($result['success']) {
            $newSize = filesize($filePath);
            $stats['new_size'] += $newSize;
            $saved = $filesize - $newSize;
            $percent = round(($saved / $filesize) * 100, 1);
            
            echo sprintf(" → %10s (-%s, -%d%%)\n", 
                formatBytes($newSize), 
                formatBytes($saved), 
                $percent
            );
            
            $stats['optimized']++;
        } else {
            echo " → SKIPPED ({$result['reason']})\n";
            $stats['skipped']++;
            $stats['new_size'] += $filesize;
        }
        
    } catch (Exception $e) {
        echo " → ERROR: {$e->getMessage()}\n";
        $stats['errors']++;
        $stats['new_size'] += $filesize;
    }
}

// Print summary
echo "-----------------------------------------------------------------------------\n";
echo "\nSUMMARY:\n";
echo "  Total images:     {$stats['total']}\n";
echo "  Optimized:        {$stats['optimized']}\n";
echo "  Skipped:          {$stats['skipped']}\n";
echo "  Errors:           {$stats['errors']}\n";
echo "  Original size:    " . formatBytes($stats['original_size']) . "\n";
echo "  New size:         " . formatBytes($stats['new_size']) . "\n";
echo "  Total saved:      " . formatBytes($stats['original_size'] - $stats['new_size']) . "\n";
echo "  Reduction:        " . round((($stats['original_size'] - $stats['new_size']) / $stats['original_size']) * 100, 1) . "%\n";
echo "\n✓ Optimization complete!\n";
echo "  Backups saved to: $backupDir\n\n";

/**
 * Optimize a single image
 */
function optimizeImage($filePath, $maxW, $maxH, $jpegQuality, $pngCompression) {
    $info = getimagesize($filePath);
    
    if ($info === false) {
        return ['success' => false, 'reason' => 'Invalid image'];
    }
    
    list($width, $height, $type) = $info;
    
    // Skip if already small enough
    if ($width <= $maxW && $height <= $maxH && filesize($filePath) < 200000) {
        return ['success' => false, 'reason' => 'Already optimized'];
    }
    
    // Load image based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($filePath);
            break;
        default:
            return ['success' => false, 'reason' => 'Unsupported format'];
    }
    
    if ($source === false) {
        return ['success' => false, 'reason' => 'Failed to load'];
    }
    
    // Calculate new dimensions
    $ratio = min($maxW / $width, $maxH / $height);
    
    if ($ratio < 1) {
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // Create new image
    $destination = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize
    imagecopyresampled(
        $destination, $source,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $width, $height
    );
    
    // Save optimized image
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($destination, $filePath, $jpegQuality);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($destination, $filePath, $pngCompression);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($destination, $filePath);
            break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($destination);
    
    return ['success' => $success, 'reason' => $success ? 'OK' : 'Save failed'];
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
