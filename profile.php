<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login1.php");
    exit;
}

include "koneksi.php";

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Ambil data user lengkap
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$nama_lengkap = $user_data['nama'] ?? $username; // Gunakan username jika nama tidak ada

// Hitung statistik user
$total_laporan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id")->fetch_assoc()['total'];
$total_laporan_bulanan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id AND MONTH(tglkeg) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];

// Ambil session online jika ada (opsional, hapus jika tabel tidak ada)
$session_info = null;
$tables = $conn->query("SHOW TABLES LIKE 'user_sessions'");
if($tables->num_rows > 0) {
    $session_info = $conn->query("SELECT * FROM user_sessions WHERE user_id=$user_id ORDER BY last_activity DESC LIMIT 1")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Teras RHK</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        /* Animated Background */
        .bg-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-bubbles span {
            position: absolute;
            display: block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            bottom: -150px;
            border-radius: 50%;
            animation: float 25s infinite linear;
        }

        .bg-bubbles span:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .bg-bubbles span:nth-child(2) { left: 20%; width: 40px; height: 40px; animation-delay: 2s; }
        .bg-bubbles span:nth-child(3) { left: 30%; width: 100px; height: 100px; animation-delay: 4s; }
        .bg-bubbles span:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 1s; }
        .bg-bubbles span:nth-child(5) { left: 50%; width: 120px; height: 120px; animation-delay: 3s; }
        .bg-bubbles span:nth-child(6) { left: 60%; width: 30px; height: 30px; animation-delay: 6s; }
        .bg-bubbles span:nth-child(7) { left: 70%; width: 90px; height: 90px; animation-delay: 2.5s; }
        .bg-bubbles span:nth-child(8) { left: 80%; width: 50px; height: 50px; animation-delay: 5s; }
        .bg-bubbles span:nth-child(9) { left: 90%; width: 70px; height: 70px; animation-delay: 3.5s; }
        .bg-bubbles span:nth-child(10) { left: 95%; width: 110px; height: 110px; animation-delay: 4.5s; }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1200px) rotate(720deg); opacity: 0; }
        }

        .profile-container {
            position: relative;
            z-index: 10;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }

        .profile-cover {
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .profile-avatar {
            position: absolute;
            bottom: -50px;
            left: 50px;
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            padding: 5px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: 700;
        }

        .profile-status {
            position: absolute;
            bottom: 20px;
            right: 30px;
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(5px);
            padding: 8px 20px;
            border-radius: 30px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .online-indicator {
            width: 10px;
            height: 10px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
            100% { opacity: 1; transform: scale(1); }
        }

        .profile-info {
            padding: 70px 30px 30px;
        }

        .info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .role-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 13px;
            display: inline-block;
        }

        .btn-logout {
            background: #fff1f0;
            color: #dc3545;
            padding: 10px 25px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: white;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            font-weight: 500;
            padding-left: 28px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f6 100%);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: white;
            font-size: 20px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 12px;
        }

        /* Logout Modal */
        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 30px;
            text-align: center;
            display: block;
        }

        .modal-header i {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .modal-header h5 {
            font-size: 20px;
            font-weight: 700;
        }

        .modal-body {
            padding: 30px;
            text-align: center;
        }

        .user-avatar-modal {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 28px;
            font-weight: 700;
        }

        .modal-footer {
            border: none;
            padding: 0 30px 30px;
            justify-content: center;
            gap: 10px;
        }

        .btn-modal {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .btn-cancel {
            background: #eef2f6;
            color: #666;
        }

        .btn-logout-modal {
            background: #dc3545;
            color: white;
        }

        .btn-logout-modal:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .profile-avatar {
                left: 50%;
                transform: translateX(-50%);
            }

            .profile-status {
                right: 50%;
                transform: translateX(50%);
            }

            .profile-info {
                padding-top: 80px;
            }

            .info-header {
                flex-direction: column;
                text-align: center;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-bubbles">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="profile-container">
        <!-- Back Button -->
        <a href="home.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-cover">
                <div class="profile-avatar">
                    <div class="avatar-inner">
                        <?= strtoupper(substr($nama_lengkap, 0, 1)) ?>
                    </div>
                </div>
                <div class="profile-status">
                    <span class="online-indicator"></span>
                    <span>Online</span>
                </div>
                 
            </div>

            <div class="profile-info">
                <div class="info-header">
                    <div class="info-title">
                        <h1><?= htmlspecialchars($nama_lengkap) ?></h1>
                        <span class="role-badge">
                            <i class="fas fa-<?= $role == 'admin' ? 'crown' : 'user' ?> me-2"></i>
                            <?= $role == 'admin' ? 'Administrator' : 'Pegawai' ?>
                        </span>
                    </div>
                   
                    <button class="btn-logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-user"></i>
                            <span>Username</span>
                        </div>
                        <div class="detail-value">
                            @<?= htmlspecialchars($username) ?>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-tag"></i>
                            <span>Role</span>
                        </div>
                        <div class="detail-value">
                            <?= $role == 'admin' ? 'Administrator' : 'Pegawai' ?>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-calendar"></i>
                            <span>ID User</span>
                        </div>
                        <div class="detail-value">
                            #<?= $user_id ?>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-clock"></i>
                            <span>Session</span>
                        </div>
                        <div class="detail-value">
                            <?= session_id() ?>
                        </div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-value"><?= $total_laporan ?></div>
                        <div class="stat-label">Total Laporan</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-value"><?= $total_laporan_bulanan ?></div>
                        <div class="stat-label">Bulan Ini</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-value"><?= $total_laporan > 0 ? round(($total_laporan_bulanan/$total_laporan)*100) : 0 ?>%</div>
                        <div class="stat-label">Kinerja</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <i class="fas fa-sign-out-alt"></i>
                    <h5 class="modal-title">Logout dari Sistem?</h5>
                </div>
                <div class="modal-body">
                    <div class="user-avatar-modal">
                        <?= strtoupper(substr($nama_lengkap, 0, 1)) ?>
                    </div>
                    <p class="mb-0">Apakah Anda yakin ingin keluar, <strong><?= htmlspecialchars($nama_lengkap) ?></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <a href="logout.php" class="btn-modal btn-logout-modal">
                        Ya, Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
        
    // Update last activity setiap 30 detik (lebih sering)
setInterval(function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Activity updated at:', new Date().toLocaleTimeString());
    })
    .catch(error => {
        console.error('Error updating activity:', error);
    });
}, 30000); // Update setiap 30 detik, bukan 2 menit

// Set offline saat meninggalkan halaman
window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('update_activity.php?offline=1');
});

// Update saat halaman pertama dimuat
document.addEventListener('DOMContentLoaded', function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).catch(error => console.error('Error:', error));
});

// Update saat user melakukan klik (opsional)
document.addEventListener('click', function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).catch(error => console.error('Error:', error));
});
    </script>
</body>
</html>