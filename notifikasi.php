<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Query semua notifikasi
// ... (query lengkap untuk semua notifikasi)

?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi - Sistem Pegawai</title>
    <!-- Include CSS -->
</head>
<body>
    <div class="container mt-4">
        <h2>Semua Notifikasi</h2>
        <!-- Tampilkan semua notifikasi di sini -->
    </div>
</body>
</html>