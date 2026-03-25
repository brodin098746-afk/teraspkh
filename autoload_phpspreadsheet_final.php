<?php
/**
 * Autoloader manual untuk PhpSpreadsheet
 * Versi Final - Hanya load file yang diperlukan
 */

// Cegah multiple loading
if (defined('PHPSPREADSHEET_AUTOLOAD_LOADED')) {
    return;
}
define('PHPSPREADSHEET_AUTOLOAD_LOADED', true);

/**
 * Fungsi untuk load semua file PHP dalam folder (hanya level 1, tidak rekursif ke semua subfolder)
 */
function loadPhpFilesInDirectory($dir, $recursive = false) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        // Jika file PHP langsung di folder ini
        if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            require_once $path;
        }
        
        // Jika rekursif dan ini folder, jangan load dulu (kita handle manual)
        if ($recursive && is_dir($path) && !in_array($file, ['tests', 'PHPStan', 'vendor', 'node_modules'])) {
            loadPhpFilesInDirectory($path, true);
        }
    }
}

/**
 * Autoloader dengan PSR-4
 */
spl_autoload_register(function ($class) {
    // Mapping namespace ke folder - SESUAIKAN DENGAN STRUKTUR ANDA!
    $maps = [
        'PhpOffice\\PhpSpreadsheet\\' => __DIR__ . '/PhpSpreadsheet/PhpOffice/PhpSpreadsheet/',
        'Composer\\Pcre\\' => __DIR__ . '/Composer/Pcre/src/',  // Perhatikan: tambah /src/
        'Psr\\SimpleCache\\' => __DIR__ . '/Psr/SimpleCache/',
        'ZipStream\\' => __DIR__ . '/ZipStream/',
    ];
    
    foreach ($maps as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});

/**
 * Load semua file penting secara manual (hanya file inti)
 */

// 1. Load PSR SimpleCache (paling dasar)
$psr_dir = __DIR__ . '/Psr/SimpleCache';
if (is_dir($psr_dir)) {
    loadPhpFilesInDirectory($psr_dir);
}

// 2. Load Composer Pcre (hanya dari folder src)
$pcre_dir = __DIR__ . '/Composer/Pcre/src';
if (is_dir($pcre_dir)) {
    loadPhpFilesInDirectory($pcre_dir);
} else {
    // Fallback: coba tanpa src
    $pcre_dir = __DIR__ . '/Composer/Pcre';
    if (is_dir($pcre_dir)) {
        loadPhpFilesInDirectory($pcre_dir);
    }
}

// 3. Load ZipStream
$zipstream_dir = __DIR__ . '/ZipStream';
if (is_dir($zipstream_dir)) {
    loadPhpFilesInDirectory($zipstream_dir);
}

// 4. Load PhpSpreadsheet (hanya dari folder utama, jangan masuk ke subfolder yang salah)
$spreadsheet_dir = __DIR__ . '/PhpSpreadsheet/PhpOffice/PhpSpreadsheet';
if (is_dir($spreadsheet_dir)) {
    loadPhpFilesInDirectory($spreadsheet_dir);
    
    // Load subfolder penting secara manual
    $important_subdirs = [
        'Writer',
        'Reader',
        'Style',
        'Cell',
        'Worksheet',
        'Shared',
        'Calculation',
        'Collection'
    ];
    
    foreach ($important_subdirs as $subdir) {
        $subpath = $spreadsheet_dir . '/' . $subdir;
        if (is_dir($subpath)) {
            loadPhpFilesInDirectory($subpath);
        }
    }
}

// Debug di log (opsional)
if (!class_exists('Composer\\Pcre\\Preg')) {
    error_log("PERINGATAN: Composer\\Pcre\\Preg tidak ditemukan setelah autoload!");
}
?>