<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['role']!='admin'){
    die("Akses ditolak");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; padding: 20px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow col-md-5 mx-auto">
        <div class="card-header">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah User Baru
        </div>

        <div class="card-body">
            
            <form method="POST" action="user_simpan.php">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                
        		<div class="mb-3">
    				<label class="form-label">Password</label>

    				<div class="input-group">
        				<input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
        
        			<span class="input-group-text" id="togglePassword" style="cursor:pointer;">
            	<i class="bi bi-eye"></i>
        	</span>
    	</div>

    <small class="text-muted">
        <i class="bi bi-shield-lock"></i> 
        Password akan dienkripsi dengan aman
    </small>
</div>
                
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-success"><i class="bi bi-save me-2"></i>Simpan</button>
                    <a href="user_list.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
const togglePassword = document.getElementById('togglePassword');
const passwordField = document.getElementById('password');
const icon = togglePassword.querySelector('i');

togglePassword.addEventListener('click', function () {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    // ganti ikon
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
});
</script>
</body>
</html>