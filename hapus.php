<?php
include "koneksi.php";

$id = $_GET['id'];

$data = $conn->query("SELECT * FROM kegiatan WHERE id='$id'");
$d = $data->fetch_assoc();

unlink("uploads/".$d['fotottd']);
unlink("uploads/".$d['fotokeg1']);
unlink("uploads/".$d['fotokeg2']);

$conn->query("DELETE FROM kegiatan WHERE id='$id'");

header("Location:index.php");
?>
