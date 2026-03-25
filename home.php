<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil role dari session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

// Update last activity untuk tracking online
$conn->query("UPDATE users SET last_activity = NOW() WHERE id = $user_id");

// Ambil statistik untuk dashboard
if($role == 'admin') {
    $total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
    $total_laporan = $conn->query("SELECT COUNT(*) as total FROM kegiatan")->fetch_assoc()['total'];
    $total_laporan_bulanan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE MONTH(tglkeg) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
    
    // Hitung user online (5 menit terakhir)
    $online_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetch_assoc()['total'];
} else {
    $total_laporan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id")->fetch_assoc()['total'];
    $total_laporan_bulanan = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id AND MONTH(tglkeg) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
}

// Ambil data user untuk profile
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$nama_lengkap = $user_data['nama'];

// HITUNG TOTAL PEGAWAI
$total_pegawai = $conn->query("SELECT COUNT(*) as jml FROM pegawai")->fetch_assoc()['jml'];

// Hitung pegawai aktif dan tidak aktif (jika ada field status)
$pegawai_aktif = 0;
$pegawai_nonaktif = 0;

// Cek apakah kolom status ada di tabel pegawai
$check_status = $conn->query("SHOW COLUMNS FROM pegawai LIKE 'status'");
if($check_status->num_rows > 0) {
    $pegawai_aktif = $conn->query("SELECT COUNT(*) as total FROM pegawai WHERE status='Aktif'")->fetch_assoc()['total'];
    $pegawai_nonaktif = $conn->query("SELECT COUNT(*) as total FROM pegawai WHERE status='Tidak Aktif'")->fetch_assoc()['total'];
}

// HITUNG NOTIFIKASI
$notif_pensiun = 0;
$notif_laporan = 0;
$notifikasi_list = [];

// Cek apakah ada kolom tgl_lahir di tabel pegawai
$check_tgllahir = $conn->query("SHOW COLUMNS FROM pegawai LIKE 'tgllahir'");
if($check_tgllahir->num_rows > 0) {
    // Notifikasi pegawai usia > 55 tahun (mendekati pensiun)
    $notif_pensiun = $conn->query("
        SELECT COUNT(*) as total FROM pegawai 
        WHERE TIMESTAMPDIFF(YEAR, tgllahir, CURDATE()) > 55
    ")->fetch_assoc()['total'];
    
    // Detail pegawai pensiun
    $pegawai_pensiun = $conn->query("
        SELECT nip, nama, kecamatantugas, 
               TIMESTAMPDIFF(YEAR, tgllahir, CURDATE()) as usia 
        FROM pegawai 
        WHERE TIMESTAMPDIFF(YEAR, tgllahir, CURDATE()) > 55 
        ORDER BY usia DESC 
        LIMIT 5
    ");
    while($row = $pegawai_pensiun->fetch_assoc()) {
        $notifikasi_list[] = [
            'type' => 'pensiun',
            'icon' => 'fa-user-clock',
            'title' => 'Pegawai Mendekati Pensiun',
            'message' => $row['nama'] . ' (' . $row['usia'] . ' tahun)',
            'link' => 'master_pegawai.php?nip=' . $row['nip'],
            'time' => 'Perhatian'
        ];
    }
}

// Cek apakah ada kolom status di tabel kegiatan
$check_status_kegiatan = $conn->query("SHOW COLUMNS FROM kegiatan LIKE 'status'");
if($check_status_kegiatan->num_rows > 0) {
    // Notifikasi laporan perlu verifikasi
    $notif_laporan = $conn->query("
        SELECT COUNT(*) as total FROM kegiatan 
        WHERE MONTH(tglkeg) = MONTH(CURRENT_DATE()) 
        AND status = 'pending'
    ")->fetch_assoc()['total'];
    
    $laporan_pending = $conn->query("
        SELECT k.*, u.nama as user_nama 
        FROM kegiatan k
        JOIN users u ON k.user_id = u.id
        WHERE MONTH(k.tglkeg) = MONTH(CURRENT_DATE()) 
        AND k.status = 'pending'
        LIMIT 5
    ");
    while($row = $laporan_pending->fetch_assoc()) {
        $notifikasi_list[] = [
            'type' => 'laporan',
            'icon' => 'fa-file-excel',
            'title' => 'Laporan Perlu Verifikasi',
            'message' => $row['user_nama'] . ' - ' . substr($row['kegiatan'], 0, 30) . '...',
            'link' => 'index.php?id=' . $row['id'],
            'time' => date('d M', strtotime($row['tglkeg']))
        ];
    }
}

// Total notifikasi
$total_notifikasi = $notif_pensiun + $notif_laporan;

// DATA GRAFIK UTAMA
$grafik = $conn->query("
SELECT kecamatantugas, COUNT(*) as jumlah 
FROM pegawai 
GROUP BY kecamatantugas
ORDER BY jumlah DESC
LIMIT 10
");

$labels=[];
$data=[];

while($g=$grafik->fetch_assoc()){
    $labels[]=$g['kecamatantugas'];
    $data[]=$g['jumlah'];
}

// Ambil data pegawai terbaru
$pegawai_terbaru = $conn->query("
SELECT * FROM pegawai 
ORDER BY id DESC 
LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Dashboard Sistem Pegawai</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

html, body {
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
    font-family: 'Inter', sans-serif;
    background: #f4f7fc;
    min-height: 100vh;
}

body {
    position: relative;
    overflow-x: hidden;
}

/* Layout Flexbox untuk Responsive */
.app-wrapper {
    display: flex;
    min-height: 100vh;
    position: relative;
    width: 100%;
    overflow-x: hidden;
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
    overflow-x: hidden;
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
    width: calc(100% - 280px);
    max-width: calc(100% - 280px);
    overflow-x: hidden;
    position: relative;
}

.main-content.expanded {
    margin-left: 80px;
    width: calc(100% - 80px);
    max-width: calc(100% - 80px);
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
    width: 100%;
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

/* Notification Styles */
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

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: -10px;
    width: 350px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    margin-top: 15px;
    display: none;
    z-index: 1001;
    overflow: hidden;
}

.notification-dropdown.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-header {
    padding: 15px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.notification-count-badge {
    background: rgba(255,255,255,0.2);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.notification-body {
    max-height: 350px;
    overflow-y: auto;
}

.notification-item {
    padding: 15px 20px;
    display: flex;
    align-items: start;
    gap: 15px;
    border-bottom: 1px solid #eef2f6;
    text-decoration: none;
    color: inherit;
    transition: 0.3s;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.notification-icon i {
    font-size: 18px;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 3px;
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
    line-height: 1.4;
}

.notification-time {
    font-size: 11px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 5px;
}

.notification-time::before {
    content: '';
    width: 4px;
    height: 4px;
    background: #999;
    border-radius: 50%;
    display: inline-block;
}

.notification-empty {
    padding: 40px 20px;
    text-align: center;
    color: #999;
}

.notification-empty i {
    font-size: 40px;
    margin-bottom: 10px;
    opacity: 0.3;
}

.notification-empty p {
    margin: 0;
    font-size: 14px;
}

.notification-footer {
    padding: 12px 20px;
    text-align: center;
    border-top: 1px solid #eef2f6;
    background: #f8f9fa;
}

.notification-footer a {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.notification-footer a:hover {
    color: #764ba2;
}

/* User Dropdown */
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

/* Dashboard Content */
.dashboard-content {
    padding: clamp(15px, 3vw, 30px);
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: clamp(20px, 4vw, 30px);
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(102,126,234,0.3);
    width: 100%;
}

.welcome-card h2 {
    font-weight: 700;
    margin-bottom: 10px;
    font-size: clamp(20px, 3.5vw, 28px);
    word-wrap: break-word;
}

.welcome-card p {
    opacity: 0.9;
    margin-bottom: 20px;
    font-size: clamp(13px, 2vw, 15px);
    word-wrap: break-word;
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
    word-wrap: break-word;
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
    word-wrap: break-word;
}

.menu-card p {
    color: #666;
    font-size: clamp(12px, 1.6vw, 13px);
    margin-bottom: 15px;
    line-height: 1.5;
    word-wrap: break-word;
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
    width: 100%;
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
    word-wrap: break-word;
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
    height: auto !important;
}

/* IFRAME PAGE */
#framePage {
    display: none;
    width: 100%;
    min-height: calc(100vh - 80px);
    border: none;
    overflow-x: hidden;
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
    flex-shrink: 0;
}

/* Container fixes for Bootstrap */
.container, .container-fluid, .row {
    margin-left: 0;
    margin-right: 0;
}

.row {
    --bs-gutter-x: 1.5rem;
    margin-right: calc(-0.5 * var(--bs-gutter-x));
    margin-left: calc(-0.5 * var(--bs-gutter-x));
}

.row > * {
    padding-right: calc(var(--bs-gutter-x) * 0.5);
    padding-left: calc(var(--bs-gutter-x) * 0.5);
}

/* Tabel Pegawai */
.pegawai-table-card {
    background: white;
    border-radius: 20px;
    padding: clamp(15px, 3vw, 25px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-top: 30px;
    width: 100%;
}

.pegawai-table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.pegawai-table-header h5 {
    font-weight: 700;
    color: #333;
    font-size: clamp(16px, 2.2vw, 18px);
    margin: 0;
    word-wrap: break-word;
}

.pegawai-table {
    width: 100%;
    border-collapse: collapse;
}

.pegawai-table th {
    background: #f8f9fa;
    padding: 12px 15px;
    font-size: clamp(12px, 1.6vw, 14px);
    font-weight: 600;
    color: #333;
    text-align: left;
    border-bottom: 2px solid #eef2f6;
}

.pegawai-table td {
    padding: 12px 15px;
    font-size: clamp(12px, 1.6vw, 14px);
    color: #666;
    border-bottom: 1px solid #eef2f6;
}

.pegawai-table tr:hover td {
    background: #f8f9fa;
}

.badge-kecamatan {
    background: #eef2f6;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    color: #666;
    display: inline-block;
}

.badge-success {
    background: #d4edda;
    color: #155724;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    display: inline-block;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    display: inline-block;
}

.view-all-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.view-all-link:hover {
    color: #764ba2;
}

/* Responsive table */
@media (max-width: 768px) {
    .pegawai-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .pegawai-table th,
    .pegawai-table td {
        padding: 10px;
    }
    
    .notification-dropdown {
        width: 300px;
        right: -20px;
    }
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
        width: 100% !important;
        max-width: 100% !important;
    }

    .main-content.expanded {
        margin-left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
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
        --bs-gutter-x: 1rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }

    .col-12, .col-sm-6, .col-md-4, .col-lg-3 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
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

/* Hide scrollbar */
.main-content::-webkit-scrollbar,
.dashboard-content::-webkit-scrollbar,
body::-webkit-scrollbar {
    display: none;
    width: 0;
    background: transparent;
}

.main-content,
.dashboard-content,
body {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>terasPKH</h3>
            <button class="toggle-btn" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="profile-section">
            <div class="profile-image">
                <?= strtoupper(substr($nama_lengkap, 0, 1)) ?>
            </div>
            <div class="profile-name"><?= htmlspecialchars($nama_lengkap) ?></div>
            <div class="profile-role"><?= $role == 'admin' ? 'Administrator' : 'Pegawai' ?></div>
        </div>

        <div class="nav-menu">
            <a href="#" onclick="showDashboard()" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-id-card me-2"></i>
                <span>PEGAWAI</span>
            </div>
            
            <a href="#" onclick="loadPage('master_pegawai.php')" class="nav-item">
                <i class="fas fa-id-card"></i>
                <span>Master Data Pegawai</span>
            </a>
            
            <a href="#" onclick="loadPage('tambah_pegawai.php')" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Pegawai</span>
            </a>
            
            <a href="#" onclick="loadPage('import_preview_pegawai.php')" class="nav-item">
                <i class="fas fa-upload"></i>
                <span>Import Pegawai</span>
            </a>

            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-file-alt me-2"></i>
                <span>SKP-RHK</span>
            </div>
            
            <a href="#" onclick="loadPage('index.php')" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Master Data RHK</span>
            </a>

            <a href="#" onclick="loadPage('tambah.php')" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Data RHK</span>
            </a>

            <a href="#" onclick="loadPage('generate_pdf_filter.php')" class="nav-item">
                <i class="fas fa-file-word"></i>
                <span>Generate Laporan</span>
            </a>
            
            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-file-alt me-2"></i>
                <span>Verifikasi Komitmen</span>
            </div>
            
            <a href="#" onclick="loadPage('verdik.php')" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Verifikasi Pendidikan</span>
            </a>
            
            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-file-alt me-2"></i>
                <span>Absensi</span>
            </div>
            
            <a href="#" onclick="loadPage('absensiesdm.php')" class="nav-item">
                <i class="fas fa-fingerprint"></i>
                <span>Konfersi Absensi eSDMPKH</span>
            </a>
            
            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-file-alt me-2"></i>
                <span>Data Salur</span>
            </div>
            
            <a href="#" onclick="loadPage('dalur.php')" class="nav-item">
                <i class="fas fa-donate"></i>
                <span>Konfersi Data Salur</span>
            </a>

            <?php if($role == 'admin'): ?>
            <div style="padding: 15px 20px 5px; color: rgba(255,255,255,0.5); font-size: 12px; text-transform: uppercase;">
                <i class="fas fa-crown me-2"></i>
                <span>Admin Menu</span>
            </div>

            <a href="#" onclick="loadPage('user_tambah.php')" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Tambah User</span>
            </a>

            <a href="#" onclick="loadPage('user_list.php')" class="nav-item">
                <i class="fas fa-users-cog"></i>
                <span>Kelola User</span>
            </a>

            <a href="#" onclick="loadPage('user_online.php')" class="nav-item">
                <i class="fas fa-users"></i>
                <span>User Online</span>
            </a>
            <?php endif; ?>

            <a href="#" onclick="loadPage('user_password.php')" class="nav-item">
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
                <!-- Notification Bell -->
                <div class="notification-badge" id="notificationBadge">
                    <i class="far fa-bell"></i>
                    <?php if($total_notifikasi > 0): ?>
                    <span class="badge-count"><?= $total_notifikasi > 9 ? '9+' : $total_notifikasi ?></span>
                    <?php endif; ?>
                    
                    <!-- Dropdown Notifikasi -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h6>Notifikasi</h6>
                            <span class="notification-count-badge"><?= $total_notifikasi ?> baru</span>
                        </div>
                        <div class="notification-body">
                            <?php if(!empty($notifikasi_list)): ?>
                                <?php foreach($notifikasi_list as $notif): ?>
                                <a href="#" onclick="loadPage('<?= $notif['link'] ?>')" class="notification-item">
                                    <div class="notification-icon" style="background: <?= $notif['type'] == 'pensiun' ? 'linear-gradient(135deg, #ff6b6b, #ee5253)' : 'linear-gradient(135deg, #f093fb, #f5576c)' ?>">
                                        <i class="fas <?= $notif['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title"><?= $notif['title'] ?></div>
                                        <div class="notification-message"><?= $notif['message'] ?></div>
                                        <div class="notification-time"><?= $notif['time'] ?></div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="notification-empty">
                                    <i class="far fa-bell-slash"></i>
                                    <p>Tidak ada notifikasi</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="notification-footer">
                            <a href="#" onclick="loadPage('notifikasi.php')">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Dropdown -->
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
        <div class="dashboard-content" id="dashboardHome">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="row align-items-center g-4">
                    <div class="col-md-8">
                        <h2>Selamat Datang, <?= htmlspecialchars($nama_lengkap) ?>! 👋</h2>
                        <p>Kelola Rencana Hasil Kerja (RHK) dan Data Pegawai dengan mudah dan efisien melalui dashboard terintegrasi ini.</p>
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
                <!-- Card Total Pegawai -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $total_pegawai ?></h3>
                            <p>Total Pegawai</p>
                        </div>
                    </div>
                </div>
                
                <?php if($pegawai_aktif > 0): ?>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $pegawai_aktif ?></h3>
                            <p>Pegawai Aktif</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($pegawai_nonaktif > 0): ?>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b, #ee5253);">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $pegawai_nonaktif ?></h3>
                            <p>Pegawai Tidak Aktif</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $total_laporan ?></h3>
                            <p>Total Laporan RHK</p>
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
                        <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $total_users ?></h3>
                            <p>Total User</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?= $online_users ?></h3>
                            <p>User Online</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Menu Cards -->
            <div class="row g-3 g-md-4">
                <!-- Card Master Pegawai -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('master_pegawai.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h5>Master Pegawai</h5>
                        <p>Kelola data pegawai, tambah, edit, hapus</p>
                        <span class="menu-badge"><?= $total_pegawai ?> Pegawai</span>
                    </a>
                </div>
                
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('index.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5>Master Data RHK</h5>
                        <p>Lihat dan kelola semua data RHK</p>
                        <span class="menu-badge"><?= $total_laporan ?> Data</span>
                    </a>
                </div>
                
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('tambah.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h5>Tambah Data RHK</h5>
                        <p>Input data RHK baru ke sistem</p>
                        <span class="menu-badge">Form Input</span>
                    </a>
                </div>
                
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('generate_pdf_filter.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-file-word"></i>
                        </div>
                        <h5>Generate Laporan</h5>
                        <p>Cetak DOCX berbagai template</p>
                        <span class="menu-badge">Export WORD</span>
                    </a>
                </div>
                
                <!-- Menu Tambah Pegawai -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('tambah_pegawai.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5>Tambah Pegawai</h5>
                        <p>Input data pegawai baru</p>
                        <span class="menu-badge">Form Input</span>
                    </a>
                </div>
                
                <!-- Menu Import Pegawai -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('import_preview_pegawai.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #ff6b6b, #ee5253);">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h5>Import Pegawai</h5>
                        <p>Import data pegawai dari Excel</p>
                        <span class="menu-badge">Excel</span>
                    </a>
                </div>
                
                <!-- Menu Ubah Password -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('user_password.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-key"></i>
                        </div>
                        <h5>Ubah Password</h5>
                        <p>Perbarui password akun Anda</p>
                        <span class="menu-badge">Keamanan</span>
                    </a>
                </div>
                
                <?php if($role == 'admin'): ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('user_list.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h5>Kelola User</h5>
                        <p>Tambah, edit, atau hapus user</p>
                        <span class="menu-badge"><?= $total_users ?> User</span>
                    </a>
                </div>
                
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="#" onclick="loadPage('user_online.php')" class="menu-card">
                        <div class="menu-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>User Online</h5>
                        <p>Monitoring aktivitas user</p>
                        <span class="menu-badge"><?= $online_users ?> Online</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Chart Section -->
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line me-2" style="color: #667eea;"></i>Statistik Pegawai per Kecamatan</h5>
                    <a href="#" onclick="loadPage('master_pegawai.php')" class="view-all-link">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <canvas id="grafik"></canvas>
            </div>

            <!-- Tabel Pegawai Terbaru -->
            <div class="pegawai-table-card">
                <div class="pegawai-table-header">
                    <h5><i class="fas fa-id-card me-2" style="color: #667eea;"></i>Data Pegawai Terbaru</h5>
                    <a href="#" onclick="loadPage('master_pegawai.php')" class="view-all-link">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="pegawai-table">
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Kecamatan</th>
                                <th>Jabatan</th>
                                <?php if($check_status->num_rows > 0): ?>
                                <th>Status</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($pegawai_terbaru && $pegawai_terbaru->num_rows > 0): ?>
                                <?php while($p = $pegawai_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($p['nip']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['nama']) ?></td>
                                    <td><span class="badge-kecamatan"><?= htmlspecialchars($p['kecamatantugas']) ?></span></td>
                                    <td><?= htmlspecialchars($p['jabatan']) ?></td>
                                    <?php if($check_status->num_rows > 0): ?>
                                    <td>
                                        <?php if(isset($p['status']) && $p['status'] == 'Aktif'): ?>
                                            <span class="badge-success">Aktif</span>
                                        <?php elseif(isset($p['status'])): ?>
                                            <span class="badge-secondary">Tidak Aktif</span>
                                        <?php else: ?>
                                            <span class="badge-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $check_status->num_rows > 0 ? '5' : '4' ?>" style="text-align: center; padding: 30px; color: #999;">
                                        <i class="fas fa-database me-2"></i>Belum ada data pegawai
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- IFRAME PAGE -->
        <iframe id="framePage" style="display:none; width:100%; min-height: calc(100vh - 80px); border:none;"></iframe>
    </div>

    <script>
        // Sidebar Toggle untuk Desktop
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileBtn = document.getElementById('mobileMenuBtn');

        // Desktop toggle
        if(toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Simpan status sidebar di localStorage
                let isCollapsed = sidebar.classList.contains("collapsed");
                localStorage.setItem("sidebarCollapsed", isCollapsed);
            });
        }

        // Mobile menu toggle
        if(mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });
        }

        // Close sidebar when clicking overlay
        if(overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

        // Handle window resize
        function handleResize() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('mobile-open');
                if(overlay) overlay.classList.remove('active');
            }
        }

        window.addEventListener('resize', handleResize);

        // User Dropdown Toggle
        const userDropdown = document.getElementById('userDropdown');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if(userDropdown) {
            userDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
                
                // Tutup notification dropdown jika terbuka
                if(notificationDropdown) notificationDropdown.classList.remove('show');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (userDropdown && !userDropdown.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Notification Dropdown Toggle
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if(notificationBadge) {
            notificationBadge.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                
                // Tutup user dropdown jika terbuka
                if(dropdownMenu) dropdownMenu.classList.remove('show');
            });
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (notificationBadge && !notificationBadge.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Cek status sidebar yang tersimpan
        if(localStorage.getItem("sidebarCollapsed") === "true"){
            sidebar.classList.add("collapsed");
            mainContent.classList.add("expanded");
        }

        // Load page ke iframe
        function loadPage(page){
            let dashboardHome = document.getElementById("dashboardHome");
            let framePage = document.getElementById("framePage");
            
            if(dashboardHome) dashboardHome.style.display = "none";
            if(framePage) {
                framePage.style.display = "block";
                framePage.src = page;
            }
            
            // Scroll ke atas
            window.scrollTo(0,0);
            
            // Tutup semua dropdown
            if(dropdownMenu) dropdownMenu.classList.remove('show');
            if(notificationDropdown) notificationDropdown.classList.remove('show');
        }

        // Kembali ke dashboard
        function showDashboard(){
            let dashboardHome = document.getElementById("dashboardHome");
            let framePage = document.getElementById("framePage");
            
            if(dashboardHome) dashboardHome.style.display = "block";
            if(framePage) {
                framePage.style.display = "none";
                framePage.src = "";
            }
        }

        // Handle jika iframe kosong atau error
        const framePage = document.getElementById('framePage');
        if(framePage) {
            framePage.onerror = function(){
                alert('Halaman tidak dapat dimuat');
                showDashboard();
            };
        }

        // Keyboard shortcut: ESC untuk kembali ke dashboard
        document.addEventListener('keydown', function(e){
            if(e.key === "Escape"){
                showDashboard();
                // Tutup semua dropdown
                if(dropdownMenu) dropdownMenu.classList.remove('show');
                if(notificationDropdown) notificationDropdown.classList.remove('show');
            }
        });

        // Grafik dengan Chart.js
        <?php if(!empty($labels) && !empty($data)): ?>
        const ctx = document.getElementById('grafik').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?=json_encode($labels)?>,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: <?=json_encode($data)?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 5
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
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>