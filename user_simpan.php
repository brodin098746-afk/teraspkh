<?php
include "koneksi.php";

$nama = $_POST['nama'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Ganti md5 dengan password_hash
$role = $_POST['role'];

$query = "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')";

if(mysqli_query($conn, $query)){
    header("Location: user_list.php?status=success&msg=User berhasil ditambahkan");
} else {
    header("Location: user_list.php?status=error&msg=Gagal menambahkan user: " . mysqli_error($conn));
}
?>