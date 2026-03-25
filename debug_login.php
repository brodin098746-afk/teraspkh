<?php
include "koneksi.php";

echo "<h2>🔍 Debug Login System</h2>";

// 1. Cek struktur tabel
echo "<h3>1. Struktur Tabel Users:</h3>";
$struktur = $conn->query("DESCRIBE users");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while($kolom = $struktur->fetch_assoc()){
    echo "<tr>";
    echo "<td>" . $kolom['Field'] . "</td>";
    echo "<td>" . $kolom['Type'] . "</td>";
    echo "<td>" . $kolom['Null'] . "</td>";
    echo "<td>" . $kolom['Key'] . "</td>";
    echo "<td>" . $kolom['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Cek data user
echo "<h3>2. Data User di Database:</h3>";
$users = $conn->query("SELECT id, username, password, role FROM users");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Username</th><th>Password (hash)</th><th>Panjang Hash</th><th>Role</th><th>Status Hash</th></tr>";

while($user = $users->fetch_assoc()){
    $password_hash = $user['password'];
    $panjang = strlen($password_hash);
    
    // Cek apakah hash valid
    $hash_info = password_get_info($password_hash);
    $algo_name = $hash_info['algoName'];
    $is_valid = ($hash_info['algo'] > 0) ? '✅ Valid' : '❌ Tidak Valid';
    
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . $user['username'] . "</td>";
    echo "<td><small>" . substr($password_hash, 0, 30) . "...</small></td>";
    echo "<td>" . $panjang . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "<td>" . $is_valid . " (" . $algo_name . ")</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Test verifikasi dengan password yang umum
echo "<h3>3. Test Verifikasi Password:</h3>";
$test_passwords = ['admin123', 'user123', '12345678', 'password'];

foreach($test_passwords as $test_pass){
    echo "<h4>Testing password: <strong>'$test_pass'</strong></h4>";
    
    $users = $conn->query("SELECT id, username, password, role FROM users");
    while($user = $users->fetch_assoc()){
        if(password_verify($test_pass, $user['password'])){
            echo "✅ <strong style='color:green'>BERHASIL!</strong> User '{$user['username']}' bisa login dengan password '$test_pass'<br>";
            
            // Cek hash info
            $hash_info = password_get_info($user['password']);
            echo "&nbsp;&nbsp;&nbsp;Hash info: " . $hash_info['algoName'] . "<br>";
        }
    }
    echo "<br>";
}

// 4. Rekomendasi perbaikan
echo "<h3>4. Rekomendasi Perbaikan:</h3>";

// Cek apakah ada user
$jumlah_user = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
if($jumlah_user == 0){
    echo "❌ <strong>Tidak ada user sama sekali!</strong><br>";
    echo "👉 Jalankan script berikut untuk membuat user:<br>";
    echo "<pre>
&lt;?php
include 'koneksi.php';

// Buat password hash
\$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
\$user_password = password_hash('user123', PASSWORD_DEFAULT);

// Insert admin
\$conn->query(\"INSERT INTO users (username, password, role) VALUES ('admin', '\$admin_password', 'admin')\");
\$conn->query(\"INSERT INTO users (username, password, role) VALUES ('user', '\$user_password', 'user')\");

echo 'User berhasil dibuat';
?></pre>";
} else {
    // Cek apakah ada hash yang tidak valid
    $users = $conn->query("SELECT * FROM users");
    $perlu_fix = false;
    
    while($user = $users->fetch_assoc()){
        $hash_info = password_get_info($user['password']);
        if($hash_info['algo'] == 0){
            $perlu_fix = true;
            echo "❌ User '{$user['username']}' masih menggunakan hash TIDAK VALID (mungkin MD5 atau plain text)<br>";
        }
    }
    
    if($perlu_fix){
        echo "<br>👉 <strong>Jalankan script fix berikut:</strong><br>";
        echo "<pre>
&lt;?php
include 'koneksi.php';

// Reset semua password
\$users = \$conn->query(\"SELECT * FROM users\");
while(\$u = \$users->fetch_assoc()){
    \$id = \$u['id'];
    \$username = \$u['username'];
    
    // Set password default berdasarkan username
    if(\$username == 'admin'){
        \$new_password = password_hash('admin123', PASSWORD_DEFAULT);
    } else {
        \$new_password = password_hash('user123', PASSWORD_DEFAULT);
    }
    
    \$conn->query(\"UPDATE users SET password='\$new_password' WHERE id='\$id'\");
    echo \"User \$username password telah direset<br>\";
}
echo 'Selesai';
?></pre>";
    } else {
        echo "✅ Semua user sudah menggunakan hash yang valid!<br>";
    }
}

// 5. Cek koneksi database
echo "<h3>5. Informasi Koneksi Database:</h3>";
echo "Database: " . $conn->query("SELECT DATABASE()")->fetch_row()[0] . "<br>";
echo "Server: " . $conn->server_info . "<br>";

// 6. Form login test manual
echo "<h3>6. Test Login Manual:</h3>";
echo "<form method='POST' action='test_login.php'>";
echo "<input type='text' name='username' placeholder='Username'><br>";
echo "<input type='password' name='password' placeholder='Password'><br>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";

?>
<br>
<a href="login.php">Kembali ke Login</a>