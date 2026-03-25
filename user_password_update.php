<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$id = $_POST['id'];
$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$password_konfirmasi = $_POST['password_konfirmasi'];

// Validasi password baru dan konfirmasi
if($password_baru !== $password_konfirmasi){
    header("Location: user_password.php?status=error&msg=Password baru dan konfirmasi tidak cocok");
    exit;
}

// Validasi panjang password minimal 6 karakter
if(strlen($password_baru) < 6){
    header("Location: user_password.php?status=error&msg=Password minimal 6 karakter");
    exit;
}

// Ambil data user dari database
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

if(!$user){
    header("Location: user_password.php?status=error&msg=User tidak ditemukan");
    exit;
}

// Verifikasi password lama
if(password_verify($password_lama, $user['password'])){
    // Hash password baru
    $password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    
    // Update password di database
    $update = mysqli_query($conn, "UPDATE users SET password='$password_baru_hash' WHERE id='$id'");
    
    if($update){
        header("Location: user_password.php?status=success&msg=Password berhasil diubah");
    } else {
        header("Location: user_password.php?status=error&msg=Gagal mengubah password: " . mysqli_error($conn));
    }
} else {
    header("Location: user_password.php?status=error&msg=Password lama salah");
}
?>