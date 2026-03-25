<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Ambil role dari session
$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];

// Tampilkan pesan sukses/error
if(isset($_SESSION['success'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert' style='position: fixed; top: 20px; right: 20px; z-index: 9999;'>
            <i class='bi bi-check-circle-fill me-2'></i>" . $_SESSION['success'] . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' style='position: fixed; top: 20px; right: 20px; z-index: 9999;'>
            <i class='bi bi-exclamation-triangle-fill me-2'></i>" . $_SESSION['error'] . "
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    unset($_SESSION['error']);
}

// Filter berdasarkan role
if($role == 'admin') {
    $where = "WHERE 1=1";
    $filter_user = "";
} else {
    $where = "WHERE user_id=$user_id";
    $filter_user = "AND user_id=$user_id";
}

// Filter jenis laporan
$jenis_laporan = "";
if(isset($_POST['jenis']) && $_POST['jenis'] != ""){
    $jenis_laporan = $conn->real_escape_string($_POST['jenis']);
    $where .= " AND jenis_laporan='$jenis_laporan'";
}

// Filter user (khusus admin)
if($role == 'admin' && isset($_POST['filter_user']) && $_POST['filter_user'] != ""){
    $filter_user_id = (int)$_POST['filter_user'];
    $where .= " AND user_id=$filter_user_id";
}

// Filter tanggal
if(isset($_POST['tanggal_awal']) && $_POST['tanggal_awal'] != ""){
    $tgl_awal = $conn->real_escape_string($_POST['tanggal_awal']);
    $where .= " AND tglkeg >= '$tgl_awal'";
}
if(isset($_POST['tanggal_akhir']) && $_POST['tanggal_akhir'] != ""){
    $tgl_akhir = $conn->real_escape_string($_POST['tanggal_akhir']);
    $where .= " AND tglkeg <= '$tgl_akhir'";
}

// Hitung total data untuk user
if($role == 'admin') {
    $total_data = $conn->query("SELECT COUNT(*) as total FROM kegiatan $where")->fetch_assoc()['total'];
    $user_total_data = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id")->fetch_assoc()['total'];
} else {
    $total_data = $conn->query("SELECT COUNT(*) as total FROM kegiatan $where")->fetch_assoc()['total'];
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_pages = ceil($total_data / $limit);

$data = $conn->query("SELECT k.*, u.username as pembuat 
                      FROM kegiatan k 
                      LEFT JOIN users u ON k.user_id = u.id 
                      $where 
                      ORDER BY k.id DESC 
                      LIMIT $offset, $limit");

$users = $conn->query("SELECT * FROM users ORDER BY username");
$jenis_list = $conn->query("SELECT DISTINCT jenis_laporan FROM kegiatan $where ORDER BY jenis_laporan");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Data Kegiatan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root {
    --primary: #4361ee;
    --secondary: #3f37c9;
    --success: #4cc9f0;
    --danger: #f72585;
    --warning: #f8961e;
    --dark: #1e1b4b;
    --light: #f8f9fa;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container-fluid {
    max-width: 1600px;
    margin: 0 auto;
}

.card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    border: none;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 25px 30px;
    border: none;
}

.card-header h4 {
    font-weight: 700;
    font-size: 1.8rem;
    margin: 0;
}

.badge-custom {
    padding: 8px 16px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.badge-admin {
    background: linear-gradient(135deg, var(--danger), #b5179e);
    color: white;
}

.badge-user {
    background: linear-gradient(135deg, var(--success), #4895ef);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin: 20px 0;
}

.btn-action {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #06d6a0, #118ab2);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #ffd166, #f8961e);
    color: #1e1b4b;
}

.btn-danger {
    background: linear-gradient(135deg, #ef476f, #f72585);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
}

.filter-box {
    background: #f8f9fa;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 25px;
    border: 2px solid #e9ecef;
}

.form-label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--dark);
    margin-bottom: 8px;
}

.form-select, .form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
}

.btn-filter {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    width: 100%;
}

.alert-custom {
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    border-radius: 16px;
    padding: 20px 25px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Table Styles */
.table-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid #e9ecef;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.table-container:hover {
    border-color: var(--primary);
    box-shadow: 0 15px 40px rgba(67, 97, 238, 0.1);
}

.table-responsive {
    max-height: 600px;
    overflow: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary) #e9ecef;
}

.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #e9ecef;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 10px;
}

.table {
    margin: 0;
    min-width: 3200px;
}

.table thead th {
    background: linear-gradient(135deg, var(--dark), #312e81);
    color: white;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 16px 12px;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.table tbody tr {
    transition: all 0.3s ease;
    cursor: default;
    position: relative;
}

.table tbody tr:hover {
    background: linear-gradient(135deg, #f0f4ff, #e6ecfe);
    transform: scale(1.002);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    z-index: 5;
}

.table tbody td {
    padding: 16px 12px;
    font-size: 0.8rem;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
    transition: all 0.2s ease;
}

.badge-table {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.badge-table:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.image-thumb {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    object-fit: cover;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 3px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.image-thumb:hover {
    transform: scale(2) translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    z-index: 1000;
    border-color: var(--primary);
}

[data-tooltip] {
    position: relative;
    cursor: pointer;
}

[data-tooltip]:before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-5px);
    background: linear-gradient(135deg, var(--dark), #312e81);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 500;
    white-space: nowrap;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    pointer-events: none;
}

[data-tooltip]:after {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(5px);
    border-width: 5px;
    border-style: solid;
    border-color: var(--dark) transparent transparent transparent;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
}

[data-tooltip]:hover:before {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(-10px);
}

[data-tooltip]:hover:after {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

.btn-table {
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    margin: 2px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.btn-table i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.btn-table:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.15);
}

.btn-table:hover i {
    transform: scale(1.1);
}

.btn-table-edit {
    background: linear-gradient(135deg, #ffd166, #f8961e);
    color: #1e1b4b;
}

.btn-table-delete {
    background: linear-gradient(135deg, #ef476f, #f72585);
    color: white;
}

.btn-table-view {
    background: linear-gradient(135deg, #4895ef, #4361ee);
    color: white;
}

.pagination {
    gap: 5px;
    justify-content: center;
}

.page-link {
    border: none;
    padding: 10px 18px;
    border-radius: 12px;
    color: var(--dark);
    font-weight: 600;
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.page-link:hover {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
}

.modal-image {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .filter-box .row {
        gap: 15px;
    }
    
    .btn-filter {
        margin-top: 10px;
    }
    
    .alert-custom {
        flex-direction: column;
        text-align: center;
    }
}
</style>
</head>
<body>

<div class="container-fluid">
<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-grid-3x3-gap-fill me-2"></i>Data RHK
        <?php if($role == 'admin'): ?>
            <span class="badge-custom badge-admin ms-3"><i class="bi bi-shield-fill-check"></i>Administrator</span>
        <?php endif; ?>
    </h4>
    <div>
        <?php
        // Ambil nama dari database
        $user_id = $_SESSION['user_id'];
        $user_query = $conn->query("SELECT nama FROM users WHERE id = $user_id");
        $user_data = $user_query->fetch_assoc();
        $nama_lengkap = $user_data['nama'];
        ?>
        <span class="text-white me-3">
            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nama_lengkap) ?>
        </span>
    </div>
</div>

<div class="card-body">

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="tambah.php" class="btn-action btn-success"><i class="bi bi-plus-circle-fill"></i>Tambah Data</a>
    <a href="generate_pdf_filter.php" class="btn-action btn-warning"><i class="bi bi-file-earmark-word-fill"></i>Generate Laporan</a>
    <?php if($role == 'admin'): ?>
    <button type="button" class="btn-action btn-danger" onclick="bukaModalHapus('semua')"><i class="bi bi-trash-fill"></i>Hapus Semua Data</button>
    <a href="home.php" class="btn-action btn-secondary"><i class="bi bi-house-fill"></i>Home</a>
    <?php else: ?>
    <button type="button" class="btn-action btn-danger" onclick="bukaModalHapus('sendiri')"><i class="bi bi-trash-fill"></i>Hapus Data Saya</button>
    <?php endif; ?>
</div>

<!-- Filter Box -->
<div class="filter-box">
    <form method="POST" class="row g-4">
        <div class="col-md-3">
            <label class="form-label"><i class="bi bi-tag-fill me-1 text-primary"></i>Jenis Laporan</label>
            <select name="jenis" class="form-select">
                <option value="">Semua Laporan</option>
                <?php while($jl = $jenis_list->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($jl['jenis_laporan']) ?>" <?= ($jenis_laporan == $jl['jenis_laporan']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($jl['jenis_laporan']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <?php if($role == 'admin'): ?>
        <div class="col-md-3">
            <label class="form-label"><i class="bi bi-people-fill me-1 text-primary"></i>Filter User</label>
            <select name="filter_user" class="form-select">
                <option value="">Semua User</option>
                <?php while($u = $users->fetch_assoc()): ?>
                <option value="<?= $u['id'] ?>" <?= (isset($_POST['filter_user']) && $_POST['filter_user'] == $u['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="col-md-2">
            <label class="form-label"><i class="bi bi-calendar-fill me-1 text-primary"></i>Tanggal Awal</label>
            <input type="date" name="tanggal_awal" class="form-control" value="<?= isset($_POST['tanggal_awal']) ? $_POST['tanggal_awal'] : '' ?>">
        </div>
        
        <div class="col-md-2">
            <label class="form-label"><i class="bi bi-calendar-fill me-1 text-primary"></i>Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" class="form-control" value="<?= isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : '' ?>">
        </div>
        
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn-filter"><i class="bi bi-funnel-fill"></i>Terapkan Filter</button>
        </div>
    </form>
</div>

<!-- Info Alert -->
<div class="alert-custom">
    <div><i class="bi bi-info-circle-fill fs-4 me-2"></i>Total Data: <strong><?= $total_data ?></strong> | Halaman: <strong><?= $page ?> / <?= $total_pages ?></strong></div>
    <?php if($role == 'admin'): ?>
    <span class="badge-custom badge-admin"><i class="bi bi-eye-fill"></i>Mode Admin - Melihat Semua Data (Data Anda: <?= $user_total_data ?>)</span>
    <?php else: ?>
    <span class="badge-custom badge-user"><i class="bi bi-person-fill"></i>Mode User - Melihat Data Sendiri</span>
    <?php endif; ?>
</div>

<!-- Form Export Multi -->
<form method="POST" action="generate_pdf.php" target="_blank" id="exportForm">

<div class="table-container">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
    <th><input type="checkbox" id="checkAll" class="form-check-input"></th>
    <th>No</th>
    <?php if($role == 'admin'): ?><th>User</th><?php endif; ?>
    <th>Petugas</th><th>NIP</th><th>Jabatan</th><th>Unit Kerja</th><th>Wilayah</th>
    <th>Jenis Kegiatan</th><th>Pimpinan Rapat</th><th>Peserta</th><th>Hari</th><th>Waktu</th>
    <th>Lokasi</th><th>Nama Kelompok</th><th>Modul</th><th>Sesi</th>
    <th>Pemateri 1</th><th>Pemateri 2</th><th>Pemateri 3</th><th>Pemateri 4</th>
    <th>KPM</th><th>NIK</th><th>KK</th><th>Alamat</th><th>Desa</th><th>Kecamatan</th><th>Kabupaten</th>
    <th>Tgl Keg</th><th>Tgl Lap</th><th>Jenis Laporan</th>
    <th>TTD</th><th>Foto 1</th><th>Foto 2</th><th>Foto Absen</th><th>Aksi</th>
</tr>
</thead>
<tbody>
    
<?php 
$no = $offset + 1;
while($d = $data->fetch_assoc()){ 
?>
<tr>
    <td><input type="checkbox" name="ids[]" value="<?= $d['id']; ?>" class="form-check-input checkbox-item"></td>
    <td><span class="fw-semibold"><?= $no++ ?></span></td>
    
    <?php if($role == 'admin'): ?>
    <td><span class="badge-table" style="background:#e0e7ff;color:var(--primary);"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($d['pembuat']) ?></span></td>
    <?php endif; ?>
    
    <td><?= htmlspecialchars($d['namapetugas']) ?></td>
    <td><?= htmlspecialchars($d['nip']) ?></td>
    <td><?= htmlspecialchars($d['jabatan']) ?></td>
    <td><?= htmlspecialchars($d['unitkerja']) ?></td>
    <td><?= htmlspecialchars($d['wilayahtugas']) ?></td>
    <td><?= htmlspecialchars($d['jeniskegiatan']) ?></td>
    <td><?= htmlspecialchars($d['pemateri']) ?></td>
    <td><?= htmlspecialchars($d['peserta']) ?></td>
    <td><?= htmlspecialchars($d['hari']) ?></td>
    <td><?= htmlspecialchars($d['waktupel']) ?></td>
    <td><?= htmlspecialchars($d['lokasikeg']) ?></td>
    <td><?= htmlspecialchars($d['namakelompok']) ?></td>
    <td><?= htmlspecialchars($d['modul']) ?></td>
    <td><?= htmlspecialchars($d['sesi']) ?></td>
    <td><?= htmlspecialchars($d['pemateri1']) ?></td>
    <td><?= htmlspecialchars($d['pemateri2']) ?></td>
    <td><?= htmlspecialchars($d['pemateri3']) ?></td>
    <td><?= htmlspecialchars($d['pemateri4']) ?></td>
    <td><?= htmlspecialchars($d['namakpm']) ?></td>
    <td><?= htmlspecialchars($d['nik']) ?></td>
    <td><?= htmlspecialchars($d['nokk']) ?></td>
    <td><?= htmlspecialchars($d['alamat']) ?></td>
    <td><?= htmlspecialchars($d['desa']) ?></td>
    <td><?= htmlspecialchars($d['kecamatan']) ?></td>
    <td><?= htmlspecialchars($d['kabupaten']) ?></td>
    <td><span class="badge-table" style="background:#e8f5e9;color:#2e7d32;"><?= date('d/m/Y', strtotime($d['tglkeg'])) ?></span></td>
    <td><span class="badge-table" style="background:#fff3e0;color:#f57c00;"><?= date('d/m/Y', strtotime($d['tgllaporan'])) ?></span></td>
    <td><span class="badge-table" style="background:#e3f2fd;color:#1976d2;"><?= htmlspecialchars($d['jenis_laporan']) ?></span></td>
    
    <td>
        <?php if($d['fotottd']): ?>
        <img src="uploads/<?= htmlspecialchars($d['fotottd']) ?>" class="image-thumb" onclick="tampilkanGambar(this.src)" data-tooltip="Klik untuk memperbesar">
        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
    </td>
    <td>
        <?php if($d['fotokeg1']): ?>
        <img src="uploads/<?= htmlspecialchars($d['fotokeg1']) ?>" class="image-thumb" onclick="tampilkanGambar(this.src)" data-tooltip="Klik untuk memperbesar">
        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
    </td>
    <td>
        <?php if($d['fotokeg2']): ?>
        <img src="uploads/<?= htmlspecialchars($d['fotokeg2']) ?>" class="image-thumb" onclick="tampilkanGambar(this.src)" data-tooltip="Klik untuk memperbesar">
        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
    </td>
    <td>
        <?php if($d['fotoabsen']): ?>
        <img src="uploads/<?= htmlspecialchars($d['fotoabsen']) ?>" class="image-thumb" onclick="tampilkanGambar(this.src)" data-tooltip="Klik untuk memperbesar">
        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
    </td>
    <td>
        <div class="d-flex gap-1">
            <?php if($role == 'admin' || $d['user_id'] == $user_id): ?>
            <a href="edit.php?id=<?= $d['id'] ?>" class="btn-table btn-table-edit" data-tooltip="Edit Data">
                <i class="bi bi-pencil-fill"></i>
            </a>
            <a href="hapus.php?id=<?= $d['id'] ?>" class="btn-table btn-table-delete" data-tooltip="Hapus Data" onclick="return confirm('Yakin ingin menghapus?')">
                <i class="bi bi-trash-fill"></i>
            </a>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php } ?>

<?php if($data->num_rows == 0): ?>
<tr><td colspan="<?= $role == 'admin' ? 37 : 36 ?>" class="empty-state">
    <i class="bi bi-inbox-fill"></i>
    <h5>Tidak Ada Data</h5>
    <p>Belum ada data kegiatan.</p>
</td></tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>

<!-- Pagination -->
<?php if($total_pages > 1): ?>
<nav class="mt-4">
    <ul class="pagination">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page-1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page+1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>

</form>

<!-- Modal Image -->
<div class="modal fade" id="modalGambar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0">
                <img src="" id="gambarPreview" class="modal-image">
            </div>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Data -->
<div class="modal fade" id="modalHapusData" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusTitle"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-trash-fill text-danger" style="font-size:48px;"></i>
                </div>
                <p class="fw-bold text-center" id="modalHapusMessage">Anda akan menghapus data!</p>
                <p class="text-muted text-center">Total data yang akan dihapus: <strong id="totalDataHapus"><?= $total_data ?></strong> data</p>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
                
                <form id="formHapusData" action="hapus_semua.php" method="POST">
                    <?php if($role == 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Opsi Hapus:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hapus_opsi" id="hapusSemua" value="semua" checked>
                            <label class="form-check-label" for="hapusSemua">Hapus SEMUA data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hapus_opsi" id="hapusSendiri" value="sendiri">
                            <label class="form-check-label" for="hapusSendiri">Hapus hanya data saya sendiri</label>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="hapus_opsi" value="sendiri">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ketik "HAPUS" untuk konfirmasi:</label>
                        <input type="text" class="form-control" id="inputKonfirmasi" placeholder="Ketik HAPUS">
                    </div>
                    
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                <button type="button" class="btn btn-danger" id="btnHapus" onclick="prosesHapus()" disabled><i class="bi bi-trash-fill"></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Inisialisasi modal
var modalGambar = new bootstrap.Modal(document.getElementById('modalGambar'));
var modalHapus = new bootstrap.Modal(document.getElementById('modalHapusData'));

// Check All
document.getElementById('checkAll').onclick = function() {
    document.querySelectorAll('.checkbox-item').forEach(cb => cb.checked = this.checked);
};

// Tampilkan Gambar
function tampilkanGambar(src) {
    document.getElementById('gambarPreview').src = src;
    modalGambar.show();
}

// Buka Modal Hapus dengan parameter tipe
function bukaModalHapus(tipe) {
    document.getElementById('inputKonfirmasi').value = '';
    document.getElementById('btnHapus').disabled = true;
    
    var title = document.getElementById('modalHapusTitle');
    var message = document.getElementById('modalHapusMessage');
    var totalData = document.getElementById('totalDataHapus');
    
    <?php
    // Hitung total data user jika perlu
    if($role != 'admin') {
        $user_total = $conn->query("SELECT COUNT(*) as total FROM kegiatan WHERE user_id=$user_id")->fetch_assoc()['total'];
        echo "totalData.textContent = '$user_total';";
    }
    ?>
    
    if(tipe === 'semua') {
        title.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus SEMUA Data';
        message.textContent = 'Anda akan menghapus SEMUA data kegiatan!';
    } else {
        title.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Data Saya';
        message.textContent = 'Anda akan menghapus SEMUA data kegiatan Anda sendiri!';
    }
    
    modalHapus.show();
}

// Input konfirmasi
document.getElementById('inputKonfirmasi').addEventListener('input', function() {
    document.getElementById('btnHapus').disabled = this.value.toUpperCase() !== 'HAPUS';
});

// Proses Hapus
function prosesHapus() {
    var form = document.getElementById('formHapusData');
    var opsi = document.querySelector('input[name="hapus_opsi"]:checked');
    
    <?php if($role == 'admin'): ?>
    if(!opsi) {
        alert('Pilih opsi hapus!');
        return;
    }
    
    var pesan = opsi.value === 'semua' ? 
        'PERINGATAN! Anda akan menghapus SEMUA data. Lanjutkan?' : 
        'Anda akan menghapus semua data Anda sendiri. Lanjutkan?';
    <?php else: ?>
    var pesan = 'Anda akan menghapus SEMUA data Anda sendiri. Lanjutkan?';
    <?php endif; ?>
    
    if(confirm(pesan)) {
        form.submit();
    }
}

// Stop propagation pada checkbox dan link
document.querySelectorAll('.checkbox-item, .btn-table, .btn-action').forEach(el => {
    el.addEventListener('click', e => e.stopPropagation());
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

// Update saat user melakukan klik
document.addEventListener('click', function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).catch(error => console.error('Error:', error));
});
</script>

</body>
</html>