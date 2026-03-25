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

// Set timezone untuk PHP
date_default_timezone_set('Asia/Jakarta');
$current_time = date('Y-m-d H:i:s');

// Update status offline - gunakan perbandingan dengan waktu PHP
$conn->query("UPDATE users SET is_online = 0 
              WHERE TIMESTAMPDIFF(MINUTE, last_activity, '$current_time') >= 2");

// Ambil semua user
$users = $conn->query("SELECT * FROM users ORDER BY is_online DESC, last_activity DESC");

$online_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_online = 1")->fetch_assoc()['total'];
$offline_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_online = 0")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Fungsi untuk format waktu yang akurat
function formatTimeAgo($datetime) {
    if(!$datetime) return 'Tidak pernah';
    
    $now = new DateTime();
    $last = new DateTime($datetime);
    $diff = $now->diff($last);
    
    $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    $seconds = $minutes * 60 + $diff->s;
    
    if($seconds < 60) {
        return 'baru saja';
    } elseif($minutes < 60) {
        return $minutes . ' menit yang lalu';
    } elseif($minutes < 1440) {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . ' jam ' . $mins . ' menit yang lalu';
    } else {
        $days = floor($minutes / 1440);
        return $days . ' hari yang lalu';
    }
}

// Fungsi untuk mendeteksi browser dan OS dari User Agent
function getBrowser($user_agent) {
    if(!$user_agent) return ['browser' => 'Unknown', 'os' => 'Unknown', 'icon' => 'bi-question-circle'];
    
    $browser = 'Unknown';
    $os = 'Unknown';
    $icon = 'bi-display';
    
    // Deteksi Browser
    if(strpos($user_agent, 'Chrome') !== false && strpos($user_agent, 'Edg') === false && strpos($user_agent, 'OPR') === false) {
        $browser = 'Chrome';
        $icon = 'bi-browser-chrome';
    } elseif(strpos($user_agent, 'Firefox') !== false) {
        $browser = 'Firefox';
        $icon = 'bi-browser-firefox';
    } elseif(strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false) {
        $browser = 'Safari';
        $icon = 'bi-browser-safari';
    } elseif(strpos($user_agent, 'Edg') !== false) {
        $browser = 'Edge';
        $icon = 'bi-browser-edge';
    } elseif(strpos($user_agent, 'OPR') !== false || strpos($user_agent, 'Opera') !== false) {
        $browser = 'Opera';
        $icon = 'bi-browser-opera';
    } elseif(strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
        $browser = 'Internet Explorer';
        $icon = 'bi-browser-ie';
    }
    
    // Deteksi OS
    if(strpos($user_agent, 'Windows NT 10.0') !== false) {
        $os = 'Windows 10/11';
        $icon = 'bi-windows';
    } elseif(strpos($user_agent, 'Windows NT 6.3') !== false) {
        $os = 'Windows 8.1';
        $icon = 'bi-windows';
    } elseif(strpos($user_agent, 'Windows NT 6.2') !== false) {
        $os = 'Windows 8';
        $icon = 'bi-windows';
    } elseif(strpos($user_agent, 'Windows NT 6.1') !== false) {
        $os = 'Windows 7';
        $icon = 'bi-windows';
    } elseif(strpos($user_agent, 'Windows') !== false) {
        $os = 'Windows';
        $icon = 'bi-windows';
    } elseif(strpos($user_agent, 'Mac OS X') !== false) {
        $os = 'macOS';
        $icon = 'bi-apple';
    } elseif(strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false) {
        $os = 'iOS';
        $icon = 'bi-phone';
    } elseif(strpos($user_agent, 'Android') !== false) {
        $os = 'Android';
        $icon = 'bi-android2';
    } elseif(strpos($user_agent, 'Linux') !== false) {
        $os = 'Linux';
        $icon = 'bi-ubuntu';
    }
    
    return [
        'browser' => $browser,
        'os' => $os,
        'icon' => $icon
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Online - Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta http-equiv="refresh" content="30">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; padding: 20px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .online-indicator { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .online { background-color: #28a745; box-shadow: 0 0 10px #28a745; animation: pulse 2s infinite; }
        .offline { background-color: #dc3545; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .badge-online { background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; }
        .badge-offline { background: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 20px; }
        .user-avatar { width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .device-info { font-size: 0.85rem; }
        .device-info i { margin-right: 5px; color: #667eea; }
        
        /* Style untuk refresh indicator */
        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 2px solid #667eea;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .countdown-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            position: relative;
        }
        
        .countdown-circle::before {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #667eea;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .refresh-info {
            display: flex;
            flex-direction: column;
        }
        
        .refresh-info .label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .refresh-info .time {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
        }
        
        .refresh-info .progress-bar {
            width: 150px;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .refresh-info .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 100%;
            transition: width 1s linear;
        }
        
        .btn-refresh-manual {
            background: #f8f9fa;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-refresh-manual:hover {
            background: #667eea;
            color: white;
            transform: rotate(180deg);
        }
        
        /* Server time indicator */
        .server-time {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 1000;
            border: 1px solid #e9ecef;
        }
        
        .server-time i {
            color: #667eea;
            margin-right: 8px;
        }
    </style>
</head>
<body>

<!-- Server Time -->
<div class="server-time">
    <i class="bi bi-clock-history"></i>
    <span id="serverTimeDisplay"><?= date('d/m/Y H:i:s') ?></span> WIB
</div>

<!-- Refresh Indicator dengan Animasi Countdown -->
<div class="refresh-indicator" id="refreshIndicator">
    <div class="countdown-circle" id="countdownCircle">30</div>
    <div class="refresh-info">
        <span class="label">Auto Refresh</span>
        <span class="time" id="countdownText">30 detik</span>
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill" style="width: 100%"></div>
        </div>
    </div>
    <div class="btn-refresh-manual" onclick="location.reload()" title="Refresh Manual">
        <i class="bi bi-arrow-repeat"></i>
    </div>
</div>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-people-fill me-2"></i>Monitoring User Online</h4>
        <div>
            <a href="home.php" class="btn btn-secondary me-2"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button class="btn btn-primary" onclick="location.reload()"><i class="bi bi-arrow-repeat"></i> Refresh</button>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success"><?= $online_count ?></h1>
                    <p class="mb-0">User Online</p>
                    <small class="text-muted">Aktif sekarang</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-danger"><?= $offline_count ?></h1>
                    <p class="mb-0">User Offline</p>
                    <small class="text-muted">Tidak aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h1><?= $total_users ?></h1>
                    <p class="mb-0">Total User</p>
                    <small class="text-muted">Terdaftar di sistem</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel User -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Daftar User</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Activity</th>
                            <th>IP Address</th>
                            <th>Device/Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($user = $users->fetch_assoc()): 
                            $device_info = getBrowser($user['last_user_agent'] ?? '');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($user['nama']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'success' ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if($user['is_online']): ?>
                                    <span class="badge-online">
                                        <span class="online-indicator online"></span> Online
                                    </span>
                                <?php else: ?>
                                    <span class="badge-offline">
                                        <span class="online-indicator offline"></span> Offline
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user['last_activity']): ?>
                                    <?= date('d/m/Y H:i:s', strtotime($user['last_activity'])) ?><br>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> <?= formatTimeAgo($user['last_activity']) ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">Belum pernah login</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user['last_ip']): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-geo-alt-fill me-1"></i><?= $user['last_ip'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user['last_user_agent']): ?>
                                    <div class="device-info">
                                        <div><i class="bi <?= $device_info['icon'] ?>"></i> <?= $device_info['os'] ?></div>
                                        <div><small class="text-muted"><i class="bi bi-browser-chrome"></i> <?= $device_info['browser'] ?></small></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Informasi -->
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Informasi:</strong> 
                <ul class="mb-0 mt-2">
                    <li>Waktu server menggunakan <strong>Asia/Jakarta (WIB)</strong></li>
                    <li>User online jika aktivitas dalam <strong>2 menit terakhir</strong></li>
                    <li>Halaman refresh otomatis setiap <strong>30 detik</strong> (lihat animasi countdown di pojok kanan bawah)</li>
                    <li>Device/Browser terdeteksi dari User Agent saat login/aktivitas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Animasi Countdown untuk Auto Refresh
let seconds = 30;
const countdownCircle = document.getElementById('countdownCircle');
const countdownText = document.getElementById('countdownText');
const progressFill = document.getElementById('progressFill');
const serverTimeDisplay = document.getElementById('serverTimeDisplay');

// Update countdown setiap detik
const countdownInterval = setInterval(function() {
    seconds--;
    
    // Update tampilan countdown
    countdownCircle.textContent = seconds;
    countdownText.textContent = seconds + ' detik';
    
    // Update progress bar
    const progressPercent = (seconds / 30) * 100;
    progressFill.style.width = progressPercent + '%';
    
    // Update server time (simulasi)
    const now = new Date();
    const formattedTime = now.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }).replace(/\./g, ':');
    serverTimeDisplay.textContent = formattedTime;
    
    // Jika waktu habis, reload halaman
    if(seconds <= 0) {
        clearInterval(countdownInterval);
        location.reload();
    }
}, 1000);

// Efek hover pada refresh indicator
const refreshIndicator = document.getElementById('refreshIndicator');
refreshIndicator.addEventListener('mouseenter', function() {
    this.style.transform = 'scale(1.05)';
    this.style.transition = 'transform 0.3s ease';
});

refreshIndicator.addEventListener('mouseleave', function() {
    this.style.transform = 'scale(1)';
});

// Pause countdown saat mouse di atas indicator
refreshIndicator.addEventListener('mouseenter', function() {
    clearInterval(countdownInterval);
});

refreshIndicator.addEventListener('mouseleave', function() {
    // Lanjutkan countdown dari angka yang sama
    const newInterval = setInterval(function() {
        seconds--;
        
        countdownCircle.textContent = seconds;
        countdownText.textContent = seconds + ' detik';
        
        const progressPercent = (seconds / 30) * 100;
        progressFill.style.width = progressPercent + '%';
        
        if(seconds <= 0) {
            clearInterval(newInterval);
            location.reload();
        }
    }, 1000);
});

// Animasi masuk untuk refresh indicator
setTimeout(function() {
    refreshIndicator.style.animation = 'slideIn 0.5s ease';
}, 500);
    
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