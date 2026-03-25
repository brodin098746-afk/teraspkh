<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data foto untuk dihapus
$foto = $conn->query("SELECT foto FROM pegawai WHERE id = $id")->fetch_assoc();

if($foto && $foto['foto']) {
    $target_file = "uploads/" . $foto['foto'];
    if(file_exists($target_file)) {
        unlink($target_file);
    }
}

$query = "DELETE FROM pegawai WHERE id = $id";

if($conn->query($query)) {
    $_SESSION['success'] = "Data pegawai berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus data: " . $conn->error;
}

header("Location: master_pegawai.php");
exit;
?>