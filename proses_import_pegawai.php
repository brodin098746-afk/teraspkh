<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
include "koneksi.php";

/* CEK LOGIN */

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

/* AMBIL DATA PREVIEW */

$data = $_SESSION['import_preview'] ?? [];
$headers = $_SESSION['import_headers'] ?? [];

/* STATISTIK */

$added=0;
$updated=0;
$skipped=0;

$errorLog=[];

foreach($data as $index=>$row){

$nik = trim($row['nik'] ?? '');

if($nik==''){
$errorLog[]="Baris ".($index+2)." : NIK kosong";
$skipped++;
continue;
}

/* NORMALISASI STATUS NIKAH */

if(isset($row['statusnikah'])){

$status = strtolower(trim($row['statusnikah']));

if($status=='belum menikah' || $status=='bk'){
$row['statusnikah']='Belum Menikah';
}
elseif($status=='menikah' || $status=='k'){
$row['statusnikah']='Menikah';
}
elseif($status=='cerai hidup' || $status=='ch'){
$row['statusnikah']='Cerai Hidup';
}
elseif($status=='cerai mati' || $status=='cm'){
$row['statusnikah']='Cerai Mati';
}

}

/* FORMAT TANGGAL LAHIR */

if(isset($row['tgllahir']) && $row['tgllahir']!=''){

$val=$row['tgllahir'];

if(is_numeric($val)){
$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
$val = $date->format('Y-m-d');
}else{
$timestamp=strtotime($val);
if($timestamp){
$val=date('Y-m-d',$timestamp);
}
}

$row['tgllahir']=$val;

/* HITUNG USIA */

try{

$birth=new DateTime($val);
$today=new DateTime();

$row['usia']=$today->diff($birth)->y;

}catch(Exception $e){

$errorLog[]="Baris ".($index+2)." : format tgllahir salah";

}

}

/* ============================= */
/* CEK ATAU BUAT USER OTOMATIS */
/* ============================= */

$cekUser = $conn->query("SELECT id FROM users WHERE username='$nik'");

if($cekUser && $cekUser->num_rows > 0){

$userData = $cekUser->fetch_assoc();
$user_id_pegawai = $userData['id'];

}else{

$namaUser = $conn->real_escape_string($row['nama'] ?? 'User');
$password = password_hash($nik, PASSWORD_DEFAULT);
$role = 'user';

$conn->query("INSERT INTO users (username,nama,password,role)
VALUES ('$nik','$namaUser','$password','$role')");

$user_id_pegawai = $conn->insert_id;

}

/* ============================= */
/* CEK DATA PEGAWAI */
/* ============================= */

$check=$conn->query("SELECT id FROM pegawai WHERE nik='$nik'");

if($check && $check->num_rows>0){

$id=$check->fetch_assoc()['id'];

$fields=[];

foreach($row as $col=>$val){

$val=trim($val);

if($val!=''){
$fields[]="`$col`='".$conn->real_escape_string($val)."'";
}

}

/* PASTIKAN USER TERHUBUNG */

$fields[]="user_id='".$conn->real_escape_string($user_id_pegawai)."'";

if(!empty($fields)){

$sql="UPDATE pegawai SET ".implode(",",$fields)." WHERE id=$id";

if($conn->query($sql)){
$updated++;
}else{
$errorLog[]="Baris ".($index+2)." gagal update : ".$conn->error;
}

}

}else{

$cols=[];
$vals=[];

foreach($row as $col=>$val){

$val=trim($val);

if($val!=''){
$cols[]="`$col`";
$vals[]="'".$conn->real_escape_string($val)."'";
}

}

/* TAMBAHKAN USER_ID */

$cols[]="user_id";
$vals[]="'".$conn->real_escape_string($user_id_pegawai)."'";

if(!empty($cols)){

$sql="INSERT INTO pegawai(".implode(",",$cols).") VALUES(".implode(",",$vals).")";

if($conn->query($sql)){
$added++;
}else{
$errorLog[]="Baris ".($index+2)." gagal insert : ".$conn->error;
}

}

}

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Hasil Import Pegawai</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">
Hasil Import Pegawai
</div>

<div class="card-body">

<div class="alert alert-success">

<b>Import Selesai</b><br><br>

Data Ditambahkan : <?= $added ?><br>
Data Diupdate : <?= $updated ?><br>
Data Dilewati : <?= $skipped ?>

</div>

<?php if(!empty($errorLog)){ ?>

<div class="alert alert-danger">

<b>Error Log</b>

<ul>

<?php foreach($errorLog as $e){ ?>

<li><?= $e ?></li>

<?php } ?>

</ul>

</div>

<?php } ?>

<a href="master_pegawai.php" class="btn btn-primary">
Kembali ke Master Pegawai
</a>

</div>

</div>

</div>

</body>
</html>