<?php
session_start();
include "koneksi.php";

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    // Set offline saat logout
    $conn->query("UPDATE users SET is_online = 0 WHERE id = '$user_id'");
}

session_destroy();
header("Location: login1.php");
exit;
?>