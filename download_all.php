<?php
session_start();
if(!isset($_SESSION['user_id'])){
    die("Akses ditolak");
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
if(empty($dir)) {
    die("Directory tidak ditemukan");
}

$full_path = "output/" . $dir . "/";

if(!file_exists($full_path)) {
    die("Folder tidak ditemukan");
}

// Buat file ZIP
$zip_file = "output/laporan_" . $dir . ".zip";
$zip = new ZipArchive();

if($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = scandir($full_path);
    
    foreach($files as $file) {
        if($file != "." && $file != ".." && is_file($full_path . $file)) {
            $zip->addFile($full_path . $file, $file);
        }
    }
    
    $zip->close();
    
    // Download file ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
    header('Content-Length: ' . filesize($zip_file));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    readfile($zip_file);
    
    // Hapus file ZIP setelah download
    unlink($zip_file);
    exit;
} else {
    die("Gagal membuat file ZIP");
}
?>