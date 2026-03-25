<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// Hanya admin yang bisa hapus semua data
if($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Akses ditolak. Hanya admin yang dapat menghapus semua data.";
    header("Location: master_pegawai.php");
    exit;
}

include "koneksi.php";

// Konfirmasi keamanan
if(!isset($_POST['konfirmasi']) || $_POST['konfirmasi'] != 'HAPUS') {
    $_SESSION['error'] = "Konfirmasi tidak valid. Ketik 'HAPUS' untuk menghapus semua data.";
    header("Location: master_pegawai.php");
    exit;
}

// Ambil semua foto untuk dihapus
$foto_query = "SELECT foto FROM pegawai WHERE foto IS NOT NULL AND foto != ''";
$foto_result = $conn->query($foto_query);

$foto_terhapus = 0;
if($foto_result && $foto_result->num_rows > 0) {
    while($row = $foto_result->fetch_assoc()) {
        $file_path = "uploads/" . $row['foto'];
        if(file_exists($file_path)) {
            if(unlink($file_path)) {
                $foto_terhapus++;
            }
        }
    }
}

// Hapus semua data pegawai
$query = "DELETE FROM pegawai";
if($conn->query($query)) {
    $jumlah_terhapus = $conn->affected_rows;
    $_SESSION['success'] = "Berhasil menghapus $jumlah_terhapus data pegawai dan $foto_terhapus file foto.";
} else {
    $_SESSION['error'] = "Gagal menghapus data: " . $conn->error;
}

// Redirect kembali ke halaman master
header("Location: master_pegawai.php");
exit;
?>