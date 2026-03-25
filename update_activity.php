<?php
session_start();
include "koneksi.php"; // Pastikan ini termasuk timezone yang sudah diset

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle offline status
if(isset($_GET['offline'])){
    $conn->query("UPDATE users SET is_online = 0 WHERE id = '$user_id'");
    echo json_encode(['status' => 'success', 'message' => 'User set offline']);
    exit;
}

// Gunakan waktu PHP (yang sudah diset timezone Asia/Jakarta)
$current_time = date('Y-m-d H:i:s');
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Update dengan waktu PHP, bukan NOW() database
$query = "UPDATE users SET 
          last_activity = '$current_time',
          is_online = 1,
          last_ip = '$ip_address',
          last_user_agent = '$user_agent'
          WHERE id = '$user_id'";

if($conn->query($query)){
    // Set offline untuk user yang tidak aktif (gunakan INTERVAL dalam detik)
    $conn->query("UPDATE users SET is_online = 0 
                  WHERE TIMESTAMPDIFF(MINUTE, last_activity, '$current_time') >= 2
                  AND id != '$user_id'");
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Activity updated',
        'server_time' => $current_time,
        'php_time' => date('Y-m-d H:i:s'),
        'timezone' => date_default_timezone_get()
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
?>