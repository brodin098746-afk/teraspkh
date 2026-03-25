<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($conn,"SELECT * FROM users 
                             WHERE username='$username' 
                             AND password='$password'");

$data = mysqli_fetch_assoc($query);

if($data){
    $_SESSION['user_id'] = $data['id'];
	$_SESSION['username'] = $data['nama'];
	$_SESSION['role'] = $data['role'];


    header("Location: home.php");
}else{
    header("Location: login.php?error=1");
}
?>
