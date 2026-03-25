<?php
include 'koneksi.php';

$id       = $_POST['id'];
$petugas  = $_POST['petugas'];
$nip      = $_POST['nip'];
$jabatan  = $_POST['jabatan'];
$unit     = $_POST['unit'];
$wilayah  = $_POST['wilayah'];
$tanggal  = $_POST['tanggal'];

mysqli_query($conn, "UPDATE kegiatan SET
    petugas='$petugas',
    nip='$nip',
    jabatan='$jabatan',
    unit='$unit',
    wilayah='$wilayah',
    tanggal='$tanggal'
WHERE id='$id'");

header("Location: index.php");
?>
