<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];

// Validasi form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hapus_opsi = isset($_POST['hapus_opsi']) ? $_POST['hapus_opsi'] : 'sendiri';
    
    // Untuk user biasa, paksa opsi 'sendiri'
    if($role != 'admin') {
        $hapus_opsi = 'sendiri';
    }
    
    // Tentukan query berdasarkan opsi
    if($hapus_opsi == 'semua' && $role == 'admin') {
        // Hapus semua data
        $query = "DELETE FROM kegiatan";
        $log_message = "Menghapus SEMUA data kegiatan";
    } else {
        // Hapus hanya data user sendiri
        $query = "DELETE FROM kegiatan WHERE user_id = $user_id";
        $log_message = "Menghapus semua data kegiatan sendiri";
    }
    
    // Eksekusi query
    if($conn->query($query)) {
        $affected = $conn->affected_rows;
        
        // Buat tabel log jika belum ada
        $conn->query("CREATE TABLE IF NOT EXISTS log_aktivitas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            aktivitas TEXT NOT NULL,
            waktu DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        // Catat log
        $log_query = "INSERT INTO log_aktivitas (user_id, aktivitas, waktu) 
                      VALUES ($user_id, '$log_message ($affected data)', NOW())";
        $conn->query($log_query);
        
        $_SESSION['success'] = "Berhasil menghapus $affected data kegiatan.";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . $conn->error;
    }
}

// Redirect kembali ke halaman sebelumnya
header("Location: index.php");
exit;
?>