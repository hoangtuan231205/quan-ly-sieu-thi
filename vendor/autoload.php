<?php
/**
 * =============================================================================
 * SIMPLE AUTOLOADER - Thay thế vendor/autoload.php
 * =============================================================================
 * 
 * File này thay thế autoloader của Composer khi không cần external dependencies
 */

// Autoload classes từ các thư mục của project
spl_autoload_register(function ($className) {
    $basePath = dirname(__DIR__);
    
    $classPaths = [
        'core' => $basePath . '/app/core/',
        'controllers' => $basePath . '/app/controllers/',
        'models' => $basePath . '/app/models/',
    ];
    
    foreach ($classPaths as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Nếu cần PHPSpreadsheet, có thể bỏ comment dòng này và cài composer
// require_once __DIR__ . '/phpoffice/phpspreadsheet/src/Bootstrap.php';
