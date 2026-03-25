<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ubah Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; padding: 20px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border-radius: 15px 15px 0 0 !important; }
        .password-strength { height: 5px; margin-top: 5px; border-radius: 5px; transition: all 0.3s; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow col-md-5 mx-auto">
        <div class="card-header fw-semibold">
            <i class="bi bi-shield-lock-fill me-2"></i>Ubah Password
        </div>

        <div class="card-body">
            
            <?php if(isset($_GET['status'])): ?>
                <div class="alert alert-<?= $_GET['status'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <i class="bi bi-<?= $_GET['status'] == 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <?= $_GET['msg'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="user_password_update.php" id="formPassword">
                <input type="hidden" name="id" value="<?= $user['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <div class="input-group">
                        <input type="password" name="password_lama" id="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_lama', 'iconLama')">
                            <i class="bi bi-eye" id="iconLama"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_baru" id="password_baru" class="form-control" placeholder="Masukkan password baru" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_baru', 'iconBaru')">
                            <i class="bi bi-eye" id="iconBaru"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="strengthBar"></div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Password minimal 6 karakter
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_konfirmasi" id="password_konfirmasi" class="form-control" placeholder="Konfirmasi password baru" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_konfirmasi', 'iconKonfirmasi')">
                            <i class="bi bi-eye" id="iconKonfirmasi"></i>
                        </button>
                    </div>
                    <small class="text-muted" id="matchMessage"></small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-info" id="btnSubmit">
                        <i class="bi bi-shield-check me-2"></i>Ubah Password
                    </button>
                    <a href="<?= $_SESSION['role'] == 'admin' ? 'home.php' : 'home.php' ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle show/hide password
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}

// Password strength checker
document.getElementById('password_baru').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    let strength = 0;
    
    if (password.length >= 6) strength += 25;
    if (password.match(/[a-z]+/)) strength += 25;
    if (password.match(/[A-Z]+/)) strength += 25;
    if (password.match(/[0-9]+/)) strength += 25;
    
    strengthBar.style.width = strength + '%';
    
    if (strength < 50) {
        strengthBar.style.backgroundColor = '#dc3545';
    } else if (strength < 75) {
        strengthBar.style.backgroundColor = '#ffc107';
    } else {
        strengthBar.style.backgroundColor = '#28a745';
    }
});

// Password confirmation match
document.getElementById('password_konfirmasi').addEventListener('input', function() {
    const password = document.getElementById('password_baru').value;
    const confirm = this.value;
    const message = document.getElementById('matchMessage');
    const submitBtn = document.getElementById('btnSubmit');
    
    if (password === confirm) {
        message.innerHTML = '<i class="bi bi-check-circle text-success"></i> Password cocok';
        message.style.color = '#28a745';
        submitBtn.disabled = false;
    } else {
        message.innerHTML = '<i class="bi bi-exclamation-circle text-danger"></i> Password tidak cocok';
        message.style.color = '#dc3545';
        submitBtn.disabled = true;
    }
    
    if (confirm === '') {
        message.innerHTML = '';
        submitBtn.disabled = false;
    }
});

// Form validation
document.getElementById('formPassword').addEventListener('submit', function(e) {
    const passwordBaru = document.getElementById('password_baru').value;
    const passwordKonfirmasi = document.getElementById('password_konfirmasi').value;
    
    if (passwordBaru !== passwordKonfirmasi) {
        e.preventDefault();
        alert('Password baru dan konfirmasi tidak cocok!');
    }
    
    if (passwordBaru.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter!');
    }
});
    
    // Update last activity setiap 2 menit
setInterval(function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).then(response => response.json())
    .then(data => {
        console.log('Activity updated');
    }).catch(error => {
        console.error('Error:', error);
    });
}, 120000); // 2 menit

// Set online status offline saat user menutup tab/browser
window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('update_activity.php?offline=1');
});
</script>
</body>
</html>