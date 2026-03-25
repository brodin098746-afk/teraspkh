<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

include "koneksi.php";
require_once "autoload_phpspreadsheet_final.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

$previewData=[];
$headers=[];

if(isset($_POST['preview'])){

$file=$_FILES['file_excel'];

$spreadsheet=IOFactory::load($file['tmp_name']);
$sheet=$spreadsheet->getActiveSheet();

$rows=$sheet->toArray();

$headers=array_map(function($h){
return strtolower(preg_replace('/[^a-zA-Z0-9_]/','',$h));
},$rows[0]);

for($i=1;$i<count($rows);$i++){
$row=[];
foreach($headers as $index=>$col){
$row[$col]=$rows[$i][$index] ?? '';
}
$previewData[]=$row;
}

$_SESSION['import_preview']=$previewData;
$_SESSION['import_headers']=$headers;

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Import Pegawai Excel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:linear-gradient(135deg,#4e73df,#1cc88a);
min-height:100vh;
font-family:'Segoe UI',sans-serif;
}

.card{
border-radius:20px;
border:none;
}

.upload-box{
border:2px dashed #4e73df;
border-radius:15px;
padding:40px;
text-align:center;
cursor:pointer;
transition:0.3s;
background:#f8f9fc;
}

.upload-box:hover{
background:#eef2ff;
transform:scale(1.02);
}

.upload-box i{
font-size:55px;
color:#4e73df;
}

.table-preview{
max-height:450px;
overflow:auto;
}

thead th{
position:sticky;
top:0;
background:#fff;
}

.guide-box{
background:#f8f9fa;
border-left:5px solid #4e73df;
padding:15px;
border-radius:10px;
}

.badge-data{
font-size:14px;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow-lg">

<div class="card-header bg-primary text-white text-center">

<h4>
<i class="bi bi-file-earmark-excel"></i>
Import Data Pegawai dari Excel
</h4>

</div>

<div class="card-body p-4">

<!-- FORM UPLOAD -->

<form method="POST" enctype="multipart/form-data">

<div class="upload-box" onclick="document.getElementById('file_excel').click()">

<i class="bi bi-cloud-arrow-up"></i>

<h5 class="mt-3">Upload File Excel</h5>

<p class="text-muted">Klik atau drag file Excel (.xlsx / .xls)</p>

<input type="file" name="file_excel" id="file_excel" class="d-none" required>

<div id="fileName" class="text-success fw-bold mt-2"></div>

</div>

<div class="text-center mt-4">

<button name="preview" class="btn btn-primary btn-lg">

<i class="bi bi-search"></i> Preview Data

</button>

</div>

</form>

<hr>

<!-- PANDUAN IMPORT -->

<div class="guide-box mt-4">

<h5><i class="bi bi-info-circle"></i> Panduan Import</h5>

<ul class="mb-0">

<li>Baris pertama Excel harus berisi <b>nama kolom</b></li>

<li>Contoh header yang benar:</li>

</ul>

<code>
nik | nama | tgllahir | tmplahir | statusnikah | alamat | nohp
</code>

<ul class="mt-2 mb-0">

<li>NIK wajib diisi</li>

<li>Jika NIK sudah ada → data akan <b>UPDATE</b></li>

<li>Jika NIK belum ada → data akan <b>INSERT</b></li>

<li>Format tanggal lahir bebas (Excel akan otomatis dikonversi)</li>

</ul>

</div>

</div>

</div>


<!-- PREVIEW DATA -->

<?php if(!empty($previewData)){ ?>

<div class="card shadow-lg mt-4">

<div class="card-header bg-success text-white d-flex justify-content-between">

<div>
<i class="bi bi-table"></i>
Preview Data Excel
</div>

<span class="badge bg-light text-dark badge-data">

<?= count($previewData) ?> Data

</span>

</div>

<div class="card-body table-preview">

<table class="table table-bordered table-striped table-hover">

<thead>

<tr>

<?php foreach($headers as $h){ ?>

<th><?= $h ?></th>

<?php } ?>

</tr>

</thead>

<tbody>

<?php foreach($previewData as $row){ ?>

<tr>

<?php foreach($headers as $h){ ?>

<td><?= htmlspecialchars($row[$h]) ?></td>

<?php } ?>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="card-footer text-center">

<form method="POST" action="proses_import_pegawai.php">

<button class="btn btn-success btn-lg">

<i class="bi bi-database-add"></i>
Import Sekarang

</button>

</form>

</div>

</div>

<?php } ?>

</div>

</div>

</div>

<script>

document.getElementById("file_excel").addEventListener("change",function(){

let file=this.files[0];

if(file){
document.getElementById("fileName").innerHTML=
"<i class='bi bi-check-circle'></i> "+file.name;
}

});

</script>

</body>
</html>