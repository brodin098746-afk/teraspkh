<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// hanya admin
if($_SESSION['role']!='admin'){
    die("Akses ditolak");
}

// ambil data user
$id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id'"));
if(!$user){
    header("Location: user_list.php?status=error&msg=User tidak ditemukan");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; padding: 20px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #333; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow col-md-5 mx-auto">
        <div class="card-header fw-semibold">
            <i class="bi bi-pencil-square me-2"></i>Edit User
        </div>

        <div class="card-body">
            
            <form method="POST" action="user_update.php">
                <input type="hidden" name="id" value="<?= $user['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Kosongkan jika tidak ingin mengubah password
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= $user['role']=="user"?'selected':'' ?>>User</option>
                        <option value="admin" <?= $user['role']=="admin"?'selected':'' ?>>Admin</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-warning"><i class="bi bi-save me-2"></i>Update User</button>
                    <a href="user_list.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>