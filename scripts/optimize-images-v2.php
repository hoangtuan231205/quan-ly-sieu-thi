<?php
/**
 * =============================================================================
 * ENHANCED IMAGE OPTIMIZATION SCRIPT V2
 * =============================================================================
 * 
 * Tối ưu hóa hình ảnh sản phẩm với nén PNG tốt hơn
 * - Resize ảnh về kích thước phù hợp cho web (max 800x800px)
 * - Nén PNG với quality reduction
 * - Convert PNG to JPEG nếu không cần transparency
 * - Backup ảnh gốc
 * 
 * CÁCH DÙNG:
 * php scripts/optimize-images-v2.php
 */

// Configuration
$sourceDir = __DIR__ . '/../public/assets/img/products';
$backupDir = __DIR__ . '/../public/assets/img/products_backup';
$maxWidth = 800;
$maxHeight = 800;
$jpegQuality = 85;
$convertToJpeg = true; // Convert PNG to JPEG if no transparency

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
echo "ENHANCED IMAGE OPTIMIZATION SCRIPT V2\n";
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

// Get all PNG files
$imageFiles = glob($sourceDir . '/*.png');

$stats['total'] = count($imageFiles);

echo "Found {$stats['total']} PNG images to process\n";
echo "Max dimensions: {$maxWidth}x{$maxHeight}px\n";
echo "JPEG quality: {$jpegQuality}%\n";
echo "Convert to JPEG: " . ($convertToJpeg ? 'YES' : 'NO') . "\n\n";

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
        $result = optimizeImageV2($filePath, $maxWidth, $maxHeight, $jpegQuality, $convertToJpeg);
        
        if ($result['success']) {
            $newSize = filesize($filePath);
            $stats['new_size'] += $newSize;
            $saved = $filesize - $newSize;
            $percent = $filesize > 0 ? round(($saved / $filesize) * 100, 1) : 0;
            
            $action = $result['converted'] ? ' [→JPG]' : '';
            echo sprintf(" → %10s (-%s, -%d%%)%s\n", 
                formatBytes($newSize), 
                formatBytes($saved), 
                $percent,
                $action
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
echo "  Reduction:        " . ($stats['original_size'] > 0 ? round((($stats['original_size'] - $stats['new_size']) / $stats['original_size']) * 100, 1) : 0) . "%\n";
echo "\n✓ Optimization complete!\n";
echo "  Backups saved to: $backupDir\n\n";

/**
 * Optimize a single image with better compression
 */
function optimizeImageV2($filePath, $maxW, $maxH, $jpegQuality, $convertToJpeg) {
    $info = getimagesize($filePath);
    
    if ($info === false) {
        return ['success' => false, 'reason' => 'Invalid image', 'converted' => false];
    }
    
    list($width, $height, $type) = $info;
    
    // Skip if already small enough (< 150KB and small dimensions)
    if ($width <= $maxW && $height <= $maxH && filesize($filePath) < 150000) {
        return ['success' => false, 'reason' => 'Already optimized', 'converted' => false];
    }
    
    // Load PNG
    $source = imagecreatefrompng($filePath);
    if ($source === false) {
        return ['success' => false, 'reason' => 'Failed to load', 'converted' => false];
    }
    
    // Calculate new dimensions
    $ratio = min($maxW / $width, $maxH / $height, 1);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);
    
    // Create new image
    $destination = imagecreatetruecolor($newWidth, $newHeight);
    
    // Check if image has transparency
    $hasTransparency = false;
    if (function_exists('imagecolortransparent')) {
        $transparentIndex = imagecolortransparent($source);
        if ($transparentIndex >= 0) {
            $hasTransparency = true;
        }
    }
    
    // Check alpha channel
    if (!$hasTransparency && function_exists('imageistruecolor') && imageistruecolor($source)) {
        // Sample some pixels to check for alpha
        for ($x = 0; $x < min($width, 10); $x++) {
            for ($y = 0; $y < min($height, 10); $y++) {
                $rgba = imagecolorat($source, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                if ($alpha > 0) {
                    $hasTransparency = true;
                    break 2;
                }
            }
        }
    }
    
    $converted = false;
    
    // If no transparency and conversion enabled, convert to JPEG
    if (!$hasTransparency && $convertToJpeg) {
        // White background for JPEG
        $white = imagecolorallocate($destination, 255, 255, 255);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $white);
        
        // Resize
        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );
        
        // Save as JPEG
        $newPath = preg_replace('/\.png$/i', '.jpg', $filePath);
        $success = imagejpeg($destination, $newPath, $jpegQuality);
        
        // Delete old PNG if conversion successful
        if ($success && $newPath !== $filePath) {
            unlink($filePath);
            $converted = true;
        }
        
    } else {
        // Keep as PNG but optimize
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Resize
        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );
        
        // Save as PNG with maximum compression
        $success = imagepng($destination, $filePath, 9);
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($destination);
    
    return [
        'success' => $success, 
        'reason' => $success ? 'OK' : 'Save failed',
        'converted' => $converted
    ];
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
