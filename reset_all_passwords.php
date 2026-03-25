<?php
include "koneksi.php";

echo "<h2>Reset Semua Password</h2>";

// Ambil semua user
$users = $conn->query("SELECT * FROM users");

while($u = $users->fetch_assoc()){
    $id = $u['id'];
    $username = $u['username'];
    $old_password = $u['password'];
    
    // Tentukan password baru berdasarkan username
    if($username == 'admin'){
        $new_password = password_hash('admin123', PASSWORD_DEFAULT);
        echo "Admin: password baru = admin123<br>";
    } else {
        $new_password = password_hash('user123', PASSWORD_DEFAULT);
        echo "User: password baru = user123<br>";
    }
    
    // Update password
    $conn->query("UPDATE users SET password='$new_password' WHERE id='$id'");
    
    echo "✅ User $username password telah direset<br>";
    echo "&nbsp;&nbsp;Hash lama: " . substr($old_password, 0, 30) . "...<br>";
    echo "&nbsp;&nbsp;Hash baru: " . substr($new_password, 0, 30) . "...<br><br>";
}

echo "<br><strong>Selesai! Semua password telah direset.</strong><br>";
echo "Coba login dengan:<br>";
echo "- Admin: username = admin, password = admin123<br>";
echo "- User: username = user, password = user123<br>";
?>
<br>
<a href="login.php">Kembali ke Login</a>