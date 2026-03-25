<?php
session_start();
include "koneksi.php";

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];

// Cek method POST
if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['hapus_opsi'])){
    header("Location: index.php");
    exit;
}

$hapus_opsi = $_POST['hapus_opsi'];
$total_data = (int)$_POST['total_data'];

// Mulai transaksi
$conn->begin_transaction();

try {
    $data_terhapus = 0;
    $foto_terhapus = 0;
    
    if($role == 'admin' && $hapus_opsi == 'semua') {
        // Admin hapus semua data (termasuk semua user)
        
        // Ambil daftar foto sebelum dihapus
        $foto = $conn->query("SELECT fotottd, fotokeg1, fotokeg2 FROM kegiatan WHERE fotottd != '' OR fotokeg1 != '' OR fotokeg2 != ''");
        
        while($f = $foto->fetch_assoc()){
            // Hapus file foto dari folder uploads
            if(!empty($f['fotottd']) && file_exists("uploads/".$f['fotottd'])){
                unlink("uploads/".$f['fotottd']);
                $foto_terhapus++;
            }
            if(!empty($f['fotokeg1']) && file_exists("uploads/".$f['fotokeg1'])){
                unlink("uploads/".$f['fotokeg1']);
                $foto_terhapus++;
            }
            if(!empty($f['fotokeg2']) && file_exists("uploads/".$f['fotokeg2'])){
                unlink("uploads/".$f['fotokeg2']);
                $foto_terhapus++;
            }
        }
        
        // Hapus semua data
        $conn->query("DELETE FROM kegiatan");
        $data_terhapus = $conn->affected_rows;
        
    } else {
        // Hapus data milik user sendiri (bisa admin atau user biasa)
        $user_filter = isset($_POST['user_id']) ? (int)$_POST['user_id'] : $user_id;
        
        // Ambil daftar foto milik user sebelum dihapus
        $foto = $conn->query("SELECT fotottd, fotokeg1, fotokeg2 FROM kegiatan WHERE user_id='$user_filter' AND (fotottd != '' OR fotokeg1 != '' OR fotokeg2 != '')");
        
        while($f = $foto->fetch_assoc()){
            if(!empty($f['fotottd']) && file_exists("uploads/".$f['fotottd'])){
                unlink("uploads/".$f['fotottd']);
                $foto_terhapus++;
            }
            if(!empty($f['fotokeg1']) && file_exists("uploads/".$f['fotokeg1'])){
                unlink("uploads/".$f['fotokeg1']);
                $foto_terhapus++;
            }
            if(!empty($f['fotokeg2']) && file_exists("uploads/".$f['fotokeg2'])){
                unlink("uploads/".$f['fotokeg2']);
                $foto_terhapus++;
            }
        }
        
        // Hapus data user
        $conn->query("DELETE FROM kegiatan WHERE user_id='$user_filter'");
        $data_terhapus = $conn->affected_rows;
    }
    
    // Commit transaksi
    $conn->commit();
    
    // Redirect dengan pesan sukses
    $pesan = urlencode("✅ Berhasil menghapus $data_terhapus data dan $foto_terhapus file foto");
    header("Location: index.php?status=success&msg=$pesan");
    
} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    
    // Redirect dengan pesan error
    $pesan = urlencode("❌ Gagal menghapus data: " . $e->getMessage());
    header("Location: index.php?status=error&msg=$pesan");
}
?>