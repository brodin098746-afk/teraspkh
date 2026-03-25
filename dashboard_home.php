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

// Ambil data user untuk profile
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$nama_lengkap = $user_data['nama'];
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
            overflow: hidden;
        }

        /* Layout Flexbox untuk Responsive */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Loading Screen */
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Sidebar Styles */
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
            height: 100vh;
            overflow: hidden;
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

        /* Content Area */
        .content-area {
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 0;
            position: relative;
        }

        /* IFRAME */
        #framePage {
            border: none;
            width: 100%;
            height: 100%;
            background: #f4f7fc;
            display: none;
        }

        /* Dashboard Home */
        #dashboardHome {
            display: block;
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
            #dashboardHome {
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
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading">
        <div class="spinner-border text-primary"></div>
    </div>

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
                <a href="#" onclick="loadPage('dashboard_home.php'); return false;" class="nav-item active" id="nav-dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" onclick="loadPage('index_content.php'); return false;" class="nav-item" id="nav-master">
                    <i class="fas fa-file-alt"></i>
                    <span>Master Data RHK</span>
                </a>
                <a href="#" onclick="loadPage('tambah.php'); return false;" class="nav-item" id="nav-tambah">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Data</span>
                </a>
                <a href="#" onclick="loadPage('generate_pdf_filter.php'); return false;" class="nav-item" id="nav-generate">
                    <i class="fas fa-file-word"></i>
                    <span>Generate Laporan</span>
                </a>
                
                <?php if($role == 'admin'): ?>
                <div class="nav-item" style="margin-top: 20px; font-weight: 700; color: #ffd700;">
                    <i class="fas fa-crown"></i>
                    <span>Admin Menu</span>
                </div>
                <a href="#" onclick="loadPage('user_tambah.php'); return false;" class="nav-item" id="nav-user-tambah">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah User</span>
                </a>
                <a href="#" onclick="loadPage('user_list.php'); return false;" class="nav-item" id="nav-user-list">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola User</span>
                </a>
                <a href="#" onclick="loadPage('user_online.php'); return false;" class="nav-item" id="nav-user-online">
                    <i class="fas fa-users"></i>
                    <span>User Online</span>
                </a>
                <?php endif; ?>
                
                <a href="#" onclick="loadPage('master_pegawai.php'); return false;" class="nav-item" id="nav-pegawai">
                    <i class="fas fa-id-card"></i>
                    <span>Master Pegawai</span>
                </a>
                
                <a href="#" onclick="loadPage('user_password.php'); return false;" class="nav-item" id="nav-password">
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
                        <span>Teras</span> RHK
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
                            <a href="#" onclick="loadPage('profile.php'); return false;" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                Profil Saya
                            </a>
                            <a href="#" onclick="loadPage('user_password.php'); return false;" class="dropdown-item">
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

            <!-- Content Area -->
            <div class="content-area" id="contentArea">
                <!-- Dashboard Home (akan diisi dari dashboard_home.php melalui iframe) -->
                <iframe id="framePage" src="dashboard_home.php"></iframe>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hilangkan loading setelah halaman selesai dimuat
        window.addEventListener('load', function() {
            document.getElementById('loading').style.opacity = '0';
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
            }, 500);
        });

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
            
            // Simpan status di localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Cek status sidebar yang tersimpan
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

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

        // Fungsi Load Page dengan SPA
        function loadPage(page) {
            const framePage = document.getElementById('framePage');
            
            // Tampilkan loading
            document.getElementById('loading').style.display = 'flex';
            document.getElementById('loading').style.opacity = '1';
            
            // Set src iframe
            framePage.src = page;
            
            // Hapus class active dari semua nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Set active class berdasarkan page yang diload
            if (page.includes('dashboard')) {
                document.getElementById('nav-dashboard')?.classList.add('active');
            } else if (page.includes('index_content')) {
                document.getElementById('nav-master')?.classList.add('active');
            } else if (page.includes('tambah')) {
                document.getElementById('nav-tambah')?.classList.add('active');
            } else if (page.includes('generate')) {
                document.getElementById('nav-generate')?.classList.add('active');
            } else if (page.includes('user_tambah')) {
                document.getElementById('nav-user-tambah')?.classList.add('active');
            } else if (page.includes('user_list')) {
                document.getElementById('nav-user-list')?.classList.add('active');
            } else if (page.includes('user_online')) {
                document.getElementById('nav-user-online')?.classList.add('active');
            } else if (page.includes('master_pegawai')) {
                document.getElementById('nav-pegawai')?.classList.add('active');
            } else if (page.includes('user_password')) {
                document.getElementById('nav-password')?.classList.add('active');
            } else if (page.includes('profile')) {
                // Profile tidak ada di sidebar
            }
            
            // Update judul halaman
            updatePageTitle(page);
            
            return false;
        }

        // Update judul halaman
        function updatePageTitle(page) {
            const titleMap = {
                'dashboard_home.php': 'Dashboard',
                'index_content.php': 'Master Data RHK',
                'tambah.php': 'Tambah Data',
                'generate_pdf_filter.php': 'Generate Laporan',
                'user_tambah.php': 'Tambah User',
                'user_list.php': 'Kelola User',
                'user_online.php': 'User Online',
                'master_pegawai.php': 'Master Pegawai',
                'user_password.php': 'Ubah Password',
                'profile.php': 'Profil Saya'
            };
            
            let title = 'Teras RHK';
            for (const [key, value] of Object.entries(titleMap)) {
                if (page.includes(key)) {
                    title = value + ' - Teras RHK';
                    break;
                }
            }
            document.title = title;
        }

        // Handle iframe load selesai
        document.getElementById('framePage').addEventListener('load', function() {
            // Sembunyikan loading
            document.getElementById('loading').style.opacity = '0';
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
            }, 500);
        });

        // Handle error iframe
        document.getElementById('framePage').addEventListener('error', function() {
            alert('Halaman tidak dapat dimuat');
            document.getElementById('loading').style.display = 'none';
        });

        // Update last activity setiap 30 detik
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
        }, 30000);

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
    </script>
</body>
</html>