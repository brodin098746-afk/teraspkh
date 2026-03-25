<?php
// MATIKAN error reporting ke output
error_reporting(0);
ini_set('display_errors', 0);

// Mulai output buffering
ob_start();

session_start();

// Fungsi untuk mengirim response JSON
function sendJSON($status, $message, $data = null) {
    // Bersihkan semua output
    ob_clean();
    
    // Set header
    header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    
    $response = ['status' => $status, 'message' => $message];
    if ($data) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response);
    exit;
}

// Cek session
if(!isset($_SESSION['user_id'])){
    sendJSON('error', 'Sesi tidak valid');
}

// Cek apakah ada file
if(!isset($_FILES['file_word'])) {
    sendJSON('error', 'Tidak ada file yang diupload');
}

// Cek error upload
if($_FILES['file_word']['error'] !== UPLOAD_ERR_OK) {
    $error = $_FILES['file_word']['error'];
    $error_msg = "Error upload: $error";
    sendJSON('error', $error_msg);
}

$file = $_FILES['file_word'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Validasi ekstensi
if(!in_array($file_ext, ['doc', 'docx'])) {
    sendJSON('error', 'Hanya file Word (.doc, .docx) yang diperbolehkan');
}

// Buat direktori upload
$upload_dir = __DIR__ . '/uploads/convert/';
if(!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        sendJSON('error', 'Gagal membuat direktori upload');
    }
}

// Generate nama file
$unique_id = uniqid() . '_' . date('Ymd_His');
$input_file = $upload_dir . $unique_id . '.' . $file_ext;

// Pindahkan file
if(!move_uploaded_file($file_tmp, $input_file)) {
    sendJSON('error', 'Gagal menyimpan file');
}

// Untuk sementara, kita buat file PDF sederhana dulu untuk test
// Ini akan membuktikan apakah upload berfungsi

try {
    // Buat konten PDF sederhana
    $pdf_content = "%PDF-1.4\n";
    $pdf_content .= "1 0 obj<</Type/Catalog/Pages 2 0 R>>\nendobj\n";
    $pdf_content .= "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n";
    $pdf_content .= "3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>/Contents 4 0 R>>\nendobj\n";
    $pdf_content .= "4 0 obj<</Length 100>>stream\n";
    $pdf_content .= "BT /F1 24 Tf 100 700 Td (File: " . basename($file_name) . ") Tj ET\n";
    $pdf_content .= "BT /F1 16 Tf 100 650 Td (Upload berhasil pada: " . date('Y-m-d H:i:s') . ") Tj ET\n";
    $pdf_content .= "endstream\nendobj\n";
    $pdf_content .= "xref\n0 5\n0000000000 65535 f\n0000000010 00000 n\n0000000056 00000 n\n0000000111 00000 n\n0000000212 00000 n\n";
    $pdf_content .= "trailer<</Size 5/Root 1 0 R>>\n";
    $pdf_content .= "startxref\n345\n%%EOF";
    
    $output_file = $upload_dir . $unique_id . '.pdf';
    file_put_contents($output_file, $pdf_content);
    
    // Hapus file input
    unlink($input_file);
    
    if(file_exists($output_file)) {
        $file_url = 'uploads/convert/' . basename($output_file);
        
        sendJSON('success', 'File berhasil diupload dan dikonversi (Mode Test)', [
            'file_url' => $file_url,
            'file_name' => basename($output_file),
            'file_size' => round(filesize($output_file) / 1024, 2) . ' KB',
            'mode' => 'test'
        ]);
    } else {
        sendJSON('error', 'Gagal membuat file PDF');
    }
    
} catch (Exception $e) {
    if(file_exists($input_file)) unlink($input_file);
    sendJSON('error', 'Error: ' . $e->getMessage());
}
?>