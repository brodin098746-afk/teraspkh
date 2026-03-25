<?php
include "koneksi.php";

// Buat password hash
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$user_password = password_hash('user123', PASSWORD_DEFAULT);

// Insert admin
$sql_admin = "INSERT INTO users (username, password, role) VALUES ('admin', '$admin_password', 'admin')";
if($conn->query($sql_admin)){
    echo "✅ User admin berhasil dibuat<br>";
} else {
    echo "❌ Gagal membuat admin: " . $conn->error . "<br>";
}

// Insert user biasa
$sql_user = "INSERT INTO users (username, password, role) VALUES ('user', '$user_password', 'user')";
if($conn->query($sql_user)){
    echo "✅ User biasa berhasil dibuat<br>";
} else {
    echo "❌ Gagal membuat user: " . $conn->error . "<br>";
}

// Tampilkan semua user
$result = $conn->query("SELECT * FROM users");
echo "<br>📋 Daftar User:<br>";
while($row = $result->fetch_assoc()){
    echo "ID: {$row['id']} | Username: {$row['username']} | Role: {$row['role']}<br>";
}
?>