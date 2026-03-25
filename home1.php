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


// Ambil statistik untuk dashboard
if($role == 'admin') {
    $total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
    $total_laporan = $conn->query("SELECT COUNT(*) as total FROM kegiatan")->fetch_assoc()['total'];
    $total_laporan_bulanan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE MONTH(tglkeg) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
} else {
    $total_laporan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id")->fetch_assoc()['total'];
    $total_laporan_bulanan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id AND MONTH(tglkeg) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
}

// Ambil data user untuk profile
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$nama_lengkap = $user_data['nama']; // Ambil nama lengkap dari database
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Dashboard - Teras RHK</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            min-height: 100vh;
        }

        /* Layout Flexbox untuk Responsive */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar Styles - Responsif */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1e2b4f 0%, #2a3a6e 100%);
            color: white;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 5px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            left: 0;
            top: 0;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        /* Overlay untuk mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: clamp(16px, 2vw, 20px);
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            transition: 0.3s;
        }

        .sidebar.collapsed .sidebar-header h3 {
            opacity: 0;
            width: 0;
        }

        .toggle-btn {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            flex-shrink: 0;
        }

        .toggle-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .profile-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .profile-image {
            width: clamp(60px, 8vw, 80px);
            height: clamp(60px, 8vw, 80px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(25px, 4vw, 35px);
            color: white;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .profile-name {
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 600;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-role {
            font-size: clamp(11px, 1.2vw, 12px);
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar.collapsed .profile-name,
        .sidebar.collapsed .profile-role {
            display: none;
        }

        .nav-menu {
            padding: 15px 0;
        }

        .nav-item {
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: 0.3s;
            cursor: pointer;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            white-space: nowrap;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-item i {
            width: 30px;
            font-size: clamp(18px, 2vw, 20px);
            flex-shrink: 0;
        }

        .nav-item span {
            transition: 0.3s;
            font-size: clamp(13px, 1.5vw, 14px);
        }

        .sidebar.collapsed .nav-item span {
            display: none;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background: #f4f7fc;
            width: 100%;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-title {
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 700;
            color: #333;
        }

        .page-title span {
            color: #667eea;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 20px);
            flex-wrap: wrap;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
        }

        .notification-badge i {
            font-size: clamp(18px, 2.5vw, 22px);
            color: #666;
        }

        .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 10px;
            transition: 0.3s;
            position: relative;
        }

        .user-dropdown:hover {
            background: #f0f2f5;
        }

        /* Dropdown Menu */
        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            min-width: 200px;
            display: none;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
        }

        .dropdown-menu-custom.show {
            display: block;
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #333;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #667eea;
        }

        .dropdown-item i {
            width: 20px;
            color: #667eea;
        }

        .dropdown-divider {
            height: 1px;
            background: #eef2f6;
            margin: 5px 0;
        }

        .user-avatar {
            width: clamp(35px, 5vw, 40px);
            height: clamp(35px, 5vw, 40px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: clamp(14px, 2vw, 16px);
            flex-shrink: 0;
        }

        .user-details {
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: clamp(13px, 1.8vw, 14px);
            white-space: nowrap;
        }

        .user-role {
            font-size: clamp(11px, 1.5vw, 12px);
            color: #666;
            white-space: nowrap;
        }

        .user-dropdown i {
            color: #666;
            font-size: 12px;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: clamp(15px, 3vw, 30px);
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: clamp(20px, 4vw, 30px);
            color: white;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(102,126,234,0.3);
        }

        .welcome-card h2 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: clamp(20px, 3.5vw, 28px);
        }

        .welcome-card p {
            opacity: 0.9;
            margin-bottom: 20px;
            font-size: clamp(13px, 2vw, 15px);
        }

        .date-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 25px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(12px, 1.8vw, 14px);
            flex-wrap: wrap;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: clamp(15px, 3vw, 25px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 20px);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: clamp(50px, 7vw, 60px);
            height: clamp(50px, 7vw, 60px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(22px, 3.5vw, 28px);
            color: white;
            flex-shrink: 0;
        }

        .stat-content {
            min-width: 0;
            flex: 1;
        }

        .stat-content h3 {
            font-size: clamp(22px, 3.5vw, 28px);
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .stat-content p {
            color: #666;
            margin-bottom: 0;
            font-size: clamp(12px, 1.8vw, 14px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Menu Cards */
        .menu-card {
            background: white;
            border-radius: 20px;
            padding: clamp(20px, 3vw, 30px) clamp(15px, 2.5vw, 20px);
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }

        .menu-icon {
            width: clamp(60px, 8vw, 80px);
            height: clamp(60px, 8vw, 80px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(28px, 4vw, 35px);
            color: white;
            margin: 0 auto 15px;
        }

        .menu-card h5 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: clamp(16px, 2.2vw, 18px);
        }

        .menu-card p {
            color: #666;
            font-size: clamp(12px, 1.6vw, 13px);
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .menu-badge {
            background: #eef2f6;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: clamp(11px, 1.5vw, 12px);
            color: #666;
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Chart Card */
        .chart-card {
            background: white;
            border-radius: 20px;
            padding: clamp(15px, 3vw, 25px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-top: 30px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-header h5 {
            font-weight: 700;
            color: #333;
            font-size: clamp(16px, 2.2vw, 18px);
            margin: 0;
        }

        .chart-header select {
            width: auto;
            min-width: 120px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #eef2f6;
            font-size: clamp(12px, 1.6vw, 14px);
        }

        canvas {
            max-height: 300px;
            width: 100% !important;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: #667eea;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-right: 10px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: flex;
            }

            .sidebar {
                left: -280px;
                transition: left 0.3s ease;
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .sidebar.collapsed {
                left: -80px;
            }

            .sidebar.collapsed.mobile-open {
                left: 0;
                width: 280px;
            }

            .sidebar.collapsed.mobile-open .sidebar-header h3,
            .sidebar.collapsed.mobile-open .nav-item span,
            .sidebar.collapsed.mobile-open .profile-name,
            .sidebar.collapsed.mobile-open .profile-role {
                display: block;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .main-content.expanded {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 12px 15px;
            }

            .user-details {
                display: none;
            }

            .user-dropdown i {
                display: none;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .stat-content h3 {
                font-size: 20px;
            }

            .stat-content p {
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-content {
                padding: 15px;
            }

            .welcome-card h2 {
                font-size: 18px;
            }

            .welcome-card p {
                font-size: 12px;
            }

            .date-badge {
                width: 100%;
                justify-content: center;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .chart-header select {
                width: 100%;
            }

            .row {
                margin-left: -8px;
                margin-right: -8px;
            }

            .col-md-4, .col-lg-3, .col-md-6 {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        @media (max-width: 400px) {
            .page-title {
                font-size: 16px;
            }

            .notification-badge i {
                font-size: 18px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .menu-card {
                padding: 15px 10px;
            }

            .menu-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .menu-card h5 {
                font-size: 14px;
            }

            .menu-card p {
                font-size: 11px;
            }
        }

        /* Landscape Mode */
        @media (max-height: 600px) and (orientation: landscape) {
            .sidebar {
                overflow-y: auto;
            }

            .profile-section {
                padding: 10px;
            }

            .profile-image {
                width: 40px;
                height: 40px;
                font-size: 20px;
                margin-bottom: 5px;
            }

            .nav-item {
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Teras RHK</h3>
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="profile-section">
                <div class="profile-image">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-name"><?= htmlspecialchars($nama_lengkap) ?></div>
                <div class="profile-role">
                    <?= $role == 'admin' ? 'Administrator' : 'Pegawai' ?>
                </div>
            </div>

            <div class="nav-menu">
                <a href="home.php" class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="index.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Master Data RHK</span>
                </a>
                <a href="tambah.php" class="nav-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Data</span>
                </a>
                <a href="generate_pdf_filter.php" class="nav-item">
                    <i class="fas fa-file-word"></i>
                    <span>Generate Laporan</span>
                </a>
                
                <?php if($role == 'admin'): ?>
                <div class="nav-item" style="margin-top: 20px; font-weight: 700; color: #ffd700;">
                    <i class="fas fa-crown"></i>
                    <span>Admin Menu</span>
                </div>
                <a href="user_tambah.php" class="nav-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah User</span>
                </a>
                <a href="user_list.php" class="nav-item">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola User</span>
                </a>
                <!-- Menu Monitoring User Online -->
				<a href="user_online.php" class="nav-item">
   			 		<i class="fas fa-users"></i>
    				<span>User Online</span>
				</a>
                
                <?php endif; ?>
                <a href="master_pegawai.php" class="nav-item">
    				<i class="fas fa-id-card"></i>
    				<span>Master Pegawai</span>
				</a>
                
                <!-- Menu Ubah Password untuk Semua User -->
                <a href="user_password.php" class="nav-item">
                    <i class="fas fa-key"></i>
                    <span>Ubah Password</span>
                </a>
                
                <a href="logout.php" class="nav-item" style="margin-top: 20px; color: #ff6b6b;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="page-title">
                        <span>Dash</span>board
                    </div>
                </div>
                <div class="user-info">
                    <div class="notification-badge">
                        <i class="far fa-bell"></i>
                        <span class="badge-count">3</span>
                    </div>
                    
                    <!-- User Dropdown dengan Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-avatar">
                            <?= strtoupper(substr($nama_lengkap, 0, 1)) ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name"><?= htmlspecialchars($nama_lengkap) ?></div>
                            <div class="user-role"><?= $role == 'admin' ? 'Administrator' : 'Pegawai' ?></div>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu-custom" id="dropdownMenu">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                Profil Saya
                            </a>
                            <a href="user_password.php" class="dropdown-item">
                                <i class="fas fa-key"></i>
                                Ubah Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <div class="row align-items-center g-4">
                        <div class="col-md-8">
                            <h2>Selamat Datang, <?= htmlspecialchars($nama_lengkap) ?>! 👋</h2>
                            <p>Kelola Rencana Hasil Kerja (RHK) Anda dengan mudah dan efisien melalui dashboard terintegrasi ini.</p>
                            <div class="date-badge">
                                <i class="far fa-calendar-alt"></i>
                                <?= date('l, d F Y') ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end d-none d-md-block">
                            <i class="fas fa-clipboard-check" style="font-size: 120px; opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="row g-3 g-md-4 mb-4">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $total_laporan ?></h3>
                                <p>Total Laporan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $total_laporan_bulanan ?></h3>
                                <p>Laporan Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                    <?php if($role == 'admin'): ?>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $total_users ?></h3>
                                <p>Total User</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $total_laporan > 0 ? round(($total_laporan_bulanan/$total_laporan)*100) : 0 ?>%</h3>
                                <p>Kinerja Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Menu Cards -->
                <div class="row g-3 g-md-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="index.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h5>Master Data RHK</h5>
                            <p>Lihat dan kelola semua data RHK</p>
                            <span class="menu-badge"><?= $total_laporan ?> Data</span>
                        </a>
                    </div>
                    
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="tambah.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h5>Tambah Data RHK</h5>
                            <p>Input data RHK baru ke sistem</p>
                            <span class="menu-badge">Form Input</span>
                        </a>
                    </div>
                    
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="generate_pdf_filter.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-file-word"></i>
                            </div>
                            <h5>Generate Laporan</h5>
                            <p>Cetak DOCX berbagai template</p>
                            <span class="menu-badge">Export WORD</span>
                        </a>
                    </div>
                    
                    <!-- Menu Ubah Password untuk Semua User -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="user_password.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-key"></i>
                            </div>
                            <h5>Ubah Password</h5>
                            <p>Perbarui password akun Anda secara berkala</p>
                            <span class="menu-badge">Keamanan Akun</span>
                        </a>
                    </div>
                    
                    <?php if($role == 'admin'): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="user_list.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h5>Kelola User</h5>
                            <p>Tambah, edit, atau hapus user</p>
                            <span class="menu-badge"><?= $total_users ?> User</span>
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="user_online.php" class="menu-card">
                            <div class="menu-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>User Online</h5>
                            <p>Monitoring Aktivitas User</p>
                            <span class="menu-badge"><?= $total_users ?> User</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Chart Section -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h5><i class="fas fa-chart-line me-2" style="color: #667eea;"></i>Statistik Laporan 7 Hari Terakhir</h5>
                        <select class="form-select">
                            <option>Minggu Ini</option>
                            <option>Bulan Ini</option>
                            <option>Tahun Ini</option>
                        </select>
                    </div>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle untuk Desktop
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileBtn = document.getElementById('mobileMenuBtn');

        // Desktop toggle
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile menu toggle
        mobileBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });

        // Handle window resize
        function handleResize() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        }

        window.addEventListener('resize', handleResize);

        // User Dropdown Toggle
        const userDropdown = document.getElementById('userDropdown');
        const dropdownMenu = document.getElementById('dropdownMenu');

        userDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Chart Data
        const ctx = document.getElementById('myChart').getContext('2d');
        
        // Generate labels for last 7 days
        const labels = [];
        for(let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            labels.push(d.toLocaleDateString('id-ID', { weekday: 'short' }));
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: window.innerWidth < 768 ? 3 : 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Active menu based on current page
        const currentPage = window.location.pathname.split('/').pop();
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            if (item.getAttribute('href') === currentPage) {
                item.classList.add('active');
            }
        });
        
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