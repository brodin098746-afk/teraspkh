<?php
$conn = new mysqli(
    "sql202.infinityfree.com",  // HOST database
    "if0_41161862",              // username database
    "8OsDuTMWhG3m0",              // password database
    "if0_41161862_rhk"           // nama database
);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set timezone untuk koneksi database ke WIB (UTC+7)
$conn->query("SET time_zone = '+07:00'");

// Set timezone untuk PHP
date_default_timezone_set('Asia/Jakarta');

// FUNGSI HITUNG USIA - PASTIKAN FUNGSI INI ADA!
function hitungUsia($tgllahir) {
    if ($tgllahir && $tgllahir != '0000-00-00') {
        $lahir = new DateTime($tgllahir);
        $sekarang = new DateTime();
        $usia = $sekarang->diff($lahir);
        return $usia->y;
    }
    return 0;
}

// Opsional: Buat fungsi untuk mendapatkan waktu sekarang
function now() {
    return date('Y-m-d H:i:s');
}
?>