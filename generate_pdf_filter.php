<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role']; // Ambil role dari session
$pesan = '';
$error = '';

// Tampilkan error dari session jika ada
if(isset($_SESSION['generate_error'])) {
    $error = $_SESSION['generate_error'];
    unset($_SESSION['generate_error']); // Hapus setelah ditampilkan
}

// Proses generate PDF
if(isset($_POST['generate'])) {
    $jenis_laporan = $conn->real_escape_string($_POST['jenis_laporan']);
    $filter_user = isset($_POST['filter_user']) ? (int)$_POST['filter_user'] : 0;
    $tanggal_awal = !empty($_POST['tanggal_awal']) ? $_POST['tanggal_awal'] : '';
    $tanggal_akhir = !empty($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : '';
    
    // Build query dengan filter berdasarkan role
    if($role == 'admin') {
        // Admin bisa filter semua user atau user tertentu
        $where = "WHERE 1=1";
        if($filter_user > 0) {
            $where .= " AND user_id=$filter_user";
        }
    } else {
        // User biasa hanya datanya sendiri
        $where = "WHERE user_id=$user_id";
    }
    
    // Tambahkan filter jenis laporan
    if(!empty($jenis_laporan)) {
        $where .= " AND jenis_laporan='$jenis_laporan'";
    }
    
    // Tambahkan filter tanggal
    if(!empty($tanggal_awal)) {
        $where .= " AND tglkeg >= '$tanggal_awal'";
    }
    if(!empty($tanggal_akhir)) {
        $where .= " AND tglkeg <= '$tanggal_akhir'";
    }
    
    $query = $conn->query("SELECT * FROM kegiatan $where ORDER BY tglkeg DESC");
    
    if($query->num_rows == 0) {
        $error = "Tidak ada data dengan filter tersebut!";
    } else {
        // Simpan data ke session untuk generate PDF
        $_SESSION['filter_data'] = [
            'jenis_laporan' => $jenis_laporan,
            'filter_user' => $filter_user,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'query' => $where,
            'role' => $role,
            'user_id' => $user_id
        ];
        
        // Redirect ke halaman preview
        header("Location: preview_pdf.php");
        exit;
    }
}

// Ambil daftar jenis laporan unik (untuk user biasa)
if($role == 'admin') {
    // Admin melihat semua jenis laporan dari semua user
    $jenis_laporan_list = $conn->query("SELECT DISTINCT jenis_laporan FROM kegiatan ORDER BY jenis_laporan");
    // Ambil daftar user untuk filter admin
    $users = $conn->query("SELECT id, username, role FROM users ORDER BY username");
} else {
    // User biasa hanya melihat jenis laporannya sendiri
    $jenis_laporan_list = $conn->query("SELECT DISTINCT jenis_laporan FROM kegiatan WHERE user_id=$user_id ORDER BY jenis_laporan");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Generate Laporan PDF</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 40px 0;
    }
    .filter-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: slideUp 0.5s ease;
    }
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        border: none;
    }
    .card-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 24px;
    }
    .card-header p {
        margin: 5px 0 0;
        opacity: 0.9;
        font-size: 14px;
    }
    .card-body {
        padding: 30px;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25);
    }
    .btn-generate {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s;
        cursor: pointer;
    }
    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102,126,234,0.4);
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
    }
    .info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        border-left: 4px solid #667eea;
    }
    .info-box i {
        color: #667eea;
        font-size: 24px;
        margin-right: 10px;
    }
    .alert {
        border-radius: 12px;
        padding: 15px 20px;
    }
    .role-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .role-admin {
        background: #dc3545;
        color: white;
    }
    .role-user {
        background: #28a745;
        color: white;
    }
</style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="filter-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0"><i class="bi bi-file-word-fill me-2"></i>Generate Laporan Docx</h3>
                        <p class="mb-0 mt-1">Pilih filter untuk menghasilkan laporan yang diinginkan</p>
                    </div>
                    <div>
                        <?php if($role == 'admin'): ?>
                            <span class="role-badge role-admin">
                                <i class="bi bi-shield-lock me-1"></i>Administrator
                            </span>
                        <?php else: ?>
                            <span class="role-badge role-user">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['username']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($pesan): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $pesan ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-tag-fill me-2"></i>Jenis Laporan
                                </label>
                                <select name="jenis_laporan" class="form-select">
                                    <option value="">Semua Jenis Laporan</option>
                                    <?php while($jl = $jenis_laporan_list->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($jl['jenis_laporan']) ?>">
                                            <?= htmlspecialchars($jl['jenis_laporan']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php if($role == 'admin'): ?>
                        <!-- Filter User khusus Admin -->
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-people-fill me-2"></i>Filter User
                                </label>
                                <select name="filter_user" class="form-select">
                                    <option value="0">Semua User</option>
                                    <?php while($u = $users->fetch_assoc()): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">Pilih user tertentu atau biarkan 'Semua User' untuk mengambil data semua user</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-fill me-2"></i>Tanggal Awal
                                </label>
                                <input type="date" name="tanggal_awal" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-fill me-2"></i>Tanggal Akhir
                                </label>
                                <input type="date" name="tanggal_akhir" class="form-control">
                            </div>
                        </div>
                        
                        <div class="info-box mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill"></i>
                                <div>
                                    <strong>Informasi:</strong><br>
                                    <?php if($role == 'admin'): ?>
                                    - Anda login sebagai <strong class="text-danger">ADMIN</strong>, dapat memfilter data semua user<br>
                                    - Pilih user tertentu untuk melihat data user tersebut<br>
                                    <?php else: ?>
                                    - Anda hanya dapat melihat data Anda sendiri<br>
                                    <?php endif; ?>
                                    - Kosongkan filter untuk mengambil semua data<br>
                                    - Satu jenis laporan akan menghasilkan satu file PDF per data KPM<br>
                                    - Total 20+ jenis template laporan tersedia
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="home.php" class="btn-back w-100 text-center">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" name="generate" class="btn-generate">
                                    <i class="bi bi-file-earmark-word-fill me-2"></i>Preview & Generate
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    
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