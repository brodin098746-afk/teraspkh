<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

/* CEK TABEL */
$check_table = $conn->query("SHOW TABLES LIKE 'pegawai'");
if($check_table->num_rows == 0){
    die("Tabel pegawai tidak ditemukan.");
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$kecamatan = isset($_GET['kecamatan']) ? $conn->real_escape_string($_GET['kecamatan']) : '';

// Filter berdasarkan role
if($role == 'admin') {
    // Admin bisa melihat semua data
    $where = "WHERE 1=1";
    $where_total = "WHERE 1=1"; // Untuk query total
} else {
    // User biasa hanya bisa melihat data miliknya
    $where = "WHERE user_id = $user_id";
    $where_total = "WHERE user_id = $user_id";
}

// Tambahkan filter search
if($search){
    $where .= " AND (nama LIKE '%$search%' OR nip LIKE '%$search%' OR nik LIKE '%$search%')";
    // Untuk admin, tambahkan juga di where_total
    if($role == 'admin') {
        $where_total .= " AND (nama LIKE '%$search%' OR nip LIKE '%$search%' OR nik LIKE '%$search%')";
    } else {
        $where_total = $where; // Untuk user biasa, where_total sama dengan where
    }
}

// Tambahkan filter kecamatan
if($kecamatan){
    $where .= " AND kecamatantugas = '$kecamatan'";
    // Untuk admin, tambahkan juga di where_total
    if($role == 'admin') {
        $where_total .= " AND kecamatantugas = '$kecamatan'";
    } else {
        $where_total = $where; // Untuk user biasa, where_total sama dengan where
    }
}

/* PAGINATION */
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data dengan filter yang benar
$total_data = $conn->query("SELECT COUNT(*) as total FROM pegawai $where_total")->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data dengan filter yang benar
$data = $conn->query("SELECT * FROM pegawai $where ORDER BY id DESC LIMIT $offset, $limit");

// Ambil daftar kecamatan - sesuaikan dengan role user
if($role == 'admin') {
    // Admin lihat semua kecamatan yang ada di database
    $kecamatan_list = $conn->query("SELECT DISTINCT kecamatantugas FROM pegawai WHERE kecamatantugas != '' ORDER BY kecamatantugas");
} else {
    // User biasa hanya lihat kecamatan dari data miliknya
    $kecamatan_list = $conn->query("SELECT DISTINCT kecamatantugas FROM pegawai WHERE user_id = $user_id AND kecamatantugas != '' ORDER BY kecamatantugas");
}
?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Master Data Pegawai</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<style>

body{
background:linear-gradient(135deg,#667eea,#764ba2);
font-family:'Segoe UI';
min-height:100vh;
}

.container{
max-width:1600px;
}

.main-card{
background:white;
border-radius:20px;
box-shadow:0 30px 70px rgba(0,0,0,.25);
overflow:hidden;
}

/* HEADER */

.header{
background:linear-gradient(135deg,#3a0ca3,#4361ee);
color:white;
padding:20px;
display:flex;
justify-content:space-between;
align-items:center;
}

.header h2{
font-weight:700;
}

/* STAT */

.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:15px;
padding:20px;
background:#f8f9fc;
}

.stat-card{
background:white;
border-radius:12px;
padding:20px;
display:flex;
gap:15px;
align-items:center;
box-shadow:0 6px 20px rgba(0,0,0,0.1);
}

.stat-icon{
font-size:30px;
color:#4361ee;
}

/* TOOLBAR */

.toolbar{
padding:20px;
display:flex;
flex-wrap:wrap;
gap:10px;
border-bottom:1px solid #eee;
}

.btn-modern{
border:none;
padding:10px 16px;
border-radius:10px;
color:white;
font-weight:500;
display:flex;
align-items:center;
gap:6px;
}

.btn-green{background:#2ecc71}
.btn-blue{background:#17a2b8}
.btn-purple{background:#4361ee}
.btn-dark{background:#6c757d}
.btn-red{
background:#dc3545;
color:white;
}

.btn-red:hover{
background:#bb2d3b;
color:white;
}

/* FILTER */

.filter-box{
padding:20px;
background:#fafafa;
border-bottom:1px solid #eee;
}

/* TABLE */

.table-container{
padding:20px;
overflow:auto;
max-height:650px;
}

.table{
font-size:13px;
}

.table th{
background:#212529;
color:white;
position:sticky;
top:0;
white-space:nowrap;
}

.table td{
white-space:nowrap;
}

.table tbody tr{
cursor:pointer;
transition:.2s;
}

.table tbody tr:hover{
background:#eef2ff;
transform:scale(1.01);
}

/* FOTO */

.foto-thumb{
width:45px;
height:45px;
border-radius:50%;
object-fit:cover;
border:2px solid #4361ee;
cursor:pointer;
}

/* AKSI */

.btn-aksi{
padding:6px 10px;
border-radius:6px;
margin:2px;
color:white;
border:none;
font-size:13px;
}

.btn-profil{background:#17a2b8}
.btn-edit{background:#ffc107;color:black}
.btn-hapus{background:#dc3545}

/* PAGINATION */

.pagination{
display:flex;
justify-content:center;
gap:5px;
padding:25px;
}

.page-link{
padding:8px 14px;
border-radius:8px;
border:1px solid #dee2e6;
background:white;
color:#4361ee;
}

.page-link.active{
background:#4361ee;
color:white;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="main-card">
    
    <?php if(isset($_SESSION['success'])){ ?>
<div class="alert alert-success alert-dismissible fade show m-3" role="alert">
<i class="bi bi-check-circle"></i>
<?= $_SESSION['success']; ?>
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); } ?>

<?php if(isset($_SESSION['error'])){ ?>
<div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
<i class="bi bi-exclamation-triangle"></i>
<?= $_SESSION['error']; ?>
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['error']); } ?>

<div class="header">

<h2><i class="bi bi-people-fill"></i> Master Data Pegawai</h2>

<div>

<?= $_SESSION['nama'] ?> (<?= $role ?>)



</div>

</div>

<!-- STAT -->

<div class="stats">

<div class="stat-card">

<div class="stat-icon"><i class="bi bi-people"></i></div>

<div>
<div>Total Pegawai</div>
<h4><?= $total_data ?></h4>
</div>

</div>

<div class="stat-card">

<div class="stat-icon"><i class="bi bi-building"></i></div>

<div>
<div>Total Kecamatan</div>
<h4><?= $kecamatan_list->num_rows ?></h4>
</div>

</div>

</div>

<!-- TOOLBAR -->

<div class="toolbar">

<a href="tambah_pegawai.php" class="btn-modern btn-green">
<i class="bi bi-plus-circle"></i> Tambah Pegawai
</a>

<a href="import_preview_pegawai.php" class="btn-modern btn-blue">
<i class="bi bi-upload"></i> Import Pegawai
</a>

<a href="export_template_pegawai.php" class="btn-modern btn-purple">
<i class="bi bi-download"></i> Download Template
</a>

<a href="export_pegawai.php" class="btn-modern btn-green">
<i class="bi bi-file-earmark-excel"></i> Export Pegawai
</a>

<button class="btn-modern btn-red" data-bs-toggle="modal" data-bs-target="#modalHapus">
<i class="bi bi-trash"></i> Hapus Semua
</button>

<a href="home.php" class="btn-modern btn-dark">
<i class="bi bi-house"></i> Home
</a>

</div>

<!-- FILTER -->

<div class="filter-box">

<form class="row g-3">

<div class="col-md-5">

<input type="text" name="search" class="form-control" placeholder="Cari Nama / NIP / NIK" value="<?= $search ?>">

</div>

<div class="col-md-4">

<select name="kecamatan" class="form-select">

<option value="">Semua Kecamatan</option>

<?php while($k=$kecamatan_list->fetch_assoc()){ ?>

<option value="<?= $k['kecamatantugas'] ?>" <?= $kecamatan==$k['kecamatantugas']?'selected':'' ?>>

<?= $k['kecamatantugas'] ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-3 d-flex gap-2">

<button type="submit" class="btn btn-primary w-50">
<i class="bi bi-search"></i> Filter
</button>

<a href="master_pegawai.php" class="btn btn-secondary w-50">
<i class="bi bi-arrow-clockwise"></i> Reset
</a>

</div>

</form>

</div>

<!-- TABLE -->

<div class="table-container">

<table class="table table-bordered">

<thead>

<tr>
	
	<th>No</th>
    <th>Aksi</th>
	<th>Foto</th>
	<th>No ID</th> 
	<th>NIP</th> 
	<th>NIK</th> 
	<th>No KK</th> 
    <th>Nama</th> 
    <th>TMT Tugas</th> 
    <th>Kec. Tugas</th> 
    <th>Desa Dampingan</th> 
    <th>Jml KPM</th> 
    <th>Alamat</th> 
    <th>Kabupaten</th> 
    <th>Kecamatan</th> 
    <th>Desa</th> 
    <th>JK</th> 
    <th>Tmp Lahir</th> 
    <th>Tgl Lahir</th> 
    <th>Usia</th> 
    <th>Agama</th>
    <th>Status Nikah</th>
    <th>Jml Anak</th> 
    <th>Pekerjaan</th>
    <th>Jabatan</th> 
    <th>No HP</th> 
    <th>User Zimbra</th> 
    <th>Email Zimbra</th>
    <th>Email</th> 
    <th>Universitas</th> 
    <th>Jurusan</th> 
    <th>No Ijazah</th>
    <th>Tgl Lulus</th> 
    <th>Tahun Lulus</th>
    <th>Jenjang</th> 
    <th>No Rek BTN</th> 
    <th>No Rek JATIM</th> 
    <th>No Rek BNI</th> 
    <th>No Rek BRI</th> 
    <th>Nama Rekening</th> 
    <th>Ibu Kandung</th> 
    <th>NPWP</th> 
    <th>BPJS Kes</th> 
    <th>BPJS TK</th>
    

</tr>

</thead>

<tbody>

<?php

$no=$offset+1;

while($row=$data->fetch_assoc()){

?>

<tr onclick="window.location='profil_pegawai.php?id=<?= $row['id'] ?>'">

<td><?= $no++ ?></td>
<td onclick="event.stopPropagation()">

<a href="profil_pegawai.php?id=<?= $row['id'] ?>" class="btn-aksi btn-profil"><i class="bi bi-eye"></i></a>

<a href="edit_pegawai.php?id=<?= $row['id'] ?>" class="btn-aksi btn-edit"><i class="bi bi-pencil"></i></a>

<a href="hapus_pegawai.php?id=<?= $row['id'] ?>" class="btn-aksi btn-hapus" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>

</td>

<td onclick="event.stopPropagation()">

<?php if($row['foto']){ ?>

<img src="uploads/<?= $row['foto'] ?>" class="foto-thumb">

<?php } ?>

</td>
	<td><?= htmlspecialchars($row['no_idpegawai'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['nip'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['nik'] ?? '-') ?></td>
    <td><?= htmlspecialchars($row['nokk'] ?? '-') ?></td> 
    <td><strong><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td> 
    <td><?= isset($row['tmttugas']) && $row['tmttugas'] ? date('d/m/Y', strtotime($row['tmttugas'])) : '-' ?></td> 
    <td><?= htmlspecialchars(trim($row['kecamatantugas'] ?? '-')) ?></td> 
    <td><?= htmlspecialchars($row['desadampingan'] ?? '-') ?></td> 
    <td><?= $row['jmlkpm'] ?: 0 ?></td> 
    <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['kabupaten'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['desa'] ?? '-') ?></td>
    <td><?= ($row['jeniskelamin'] ?? '-') == 'Laki-laki' ? 'L' : 'P' ?></td> 
    <td><?= htmlspecialchars($row['tmplahir'] ?? '-') ?></td> 
    <td><?= isset($row['tgllahir']) && $row['tgllahir'] ? date('d/m/Y', strtotime($row['tgllahir'])) : '-' ?></td> 
    <td><?= $row['usia'] ?? 0 ?> th</td> 
    <td><?= htmlspecialchars($row['agama'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['statusnikah'] ?? '-') ?></td> 
    <td><?= $row['jmlanak'] ?: 0 ?></td> 
    <td><?= htmlspecialchars($row['pekerjaan'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['nohp'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['userzimbra'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['emailzimbra'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['email'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['universitas'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['jurusan'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['noijazah'] ?? '-') ?></td> 
    <td><?= isset($row['tgllulus']) && $row['tgllulus'] ? date('d/m/Y', strtotime($row['tgllulus'])) : '-' ?></td>
    <td><?= $row['tahunlulus'] ?: '-' ?></td> 
    <td><?= htmlspecialchars($row['jenjang'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['norekbtn'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['norekjatim'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['norekbni'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['norekbri'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['namarekening'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['ibukandung'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['npwp'] ?? '-') ?></td> 
    <td><?= htmlspecialchars($row['bpjskes'] ?? '-') ?></td>
    <td><?= htmlspecialchars($row['bpjstk'] ?? '-') ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<!-- PAGINATION -->

<?php if($total_pages>1){ ?>

<div class="pagination">

<?php for($i=1;$i<=$total_pages;$i++){ ?>

<a href="?page=<?= $i ?>&search=<?= $search ?>&kecamatan=<?= $kecamatan ?>" class="page-link <?= $i==$page?'active':'' ?>">

<?= $i ?>

</a>

<?php } ?>

</div>

<?php } ?>
</div>
</div>
    
    <div class="modal fade" id="modalHapus" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5 class="modal-title">
<i class="bi bi-exclamation-triangle"></i>
Konfirmasi Hapus Semua Data
</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<p class="text-danger">
<b>PERINGATAN!</b><br>
Tindakan ini akan menghapus <b>SEMUA DATA PEGAWAI</b>.
</p>

<p>Ketik kata <b>HAPUS</b> untuk melanjutkan.</p>

<form method="POST" action="hapus_semua_pegawai.php">

<input 
type="text" 
name="konfirmasi" 
id="inputHapus"
class="form-control mb-3"
placeholder="Ketik HAPUS untuk konfirmasi"
onkeyup="cekKonfirmasi()"
required>

<div class="d-flex gap-2">

<button 
type="button" 
class="btn btn-secondary w-50"
data-bs-dismiss="modal">
Batal
</button>

<button 
type="submit" 
id="btnHapus"
class="btn btn-danger w-50"
disabled>
<i class="bi bi-trash"></i> Hapus Semua
</button>

</div>

</form>

</div>

</div>
</div>
</div>
    
<script>

function cekKonfirmasi(){

let input = document.getElementById("inputHapus").value.trim();
let tombol = document.getElementById("btnHapus");

if(input === "HAPUS"){
tombol.disabled = false;
}else{
tombol.disabled = true;
}

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>