<?php
include "koneksi.php";

$id       = $_POST['id'];
$nama     = $_POST['nama'];
$username = $_POST['username'];
$role     = $_POST['role'];

// jika password diisi → update password
if(!empty($_POST['password'])){
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Ganti md5 dengan password_hash
    
    $query = "UPDATE users SET 
              nama='$nama', 
              username='$username', 
              password='$password', 
              role='$role' 
              WHERE id='$id'";
} else {
    $query = "UPDATE users SET 
              nama='$nama', 
              username='$username', 
              role='$role' 
              WHERE id='$id'";
}

if(mysqli_query($conn, $query)){
    header("Location: user_list.php?status=success&msg=User berhasil diupdate");
} else {
    header("Location: user_list.php?status=error&msg=Gagal mengupdate user: " . mysqli_error($conn));
}
?>