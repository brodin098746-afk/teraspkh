<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit; 
}
include "koneksi.php";

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM kegiatan WHERE id='$id'");
$d = $data->fetch_assoc();

if(isset($_POST['update'])){
    $uploadDir = "uploads/";
    
    // Buat folder jika belum ada
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fotottd = $d['fotottd'];
    $fotokeg1 = $d['fotokeg1'];
    $fotokeg2 = $d['fotokeg2'];
    $fotoabsen = $d['fotoabsen'];

    // Upload file baru jika ada
    if(isset($_FILES['fotottd']) && $_FILES['fotottd']['error'] == 0){
        // Hapus file lama jika ada
        if($fotottd && file_exists($uploadDir . $fotottd)){
            unlink($uploadDir . $fotottd);
        }
        $fotottd = time() . '_ttd_' . $_FILES['fotottd']['name'];
        move_uploaded_file($_FILES['fotottd']['tmp_name'], $uploadDir . $fotottd);
    }

    if(isset($_FILES['fotokeg1']) && $_FILES['fotokeg1']['error'] == 0){
        if($fotokeg1 && file_exists($uploadDir . $fotokeg1)){
            unlink($uploadDir . $fotokeg1);
        }
        $fotokeg1 = time() . '_keg1_' . $_FILES['fotokeg1']['name'];
        move_uploaded_file($_FILES['fotokeg1']['tmp_name'], $uploadDir . $fotokeg1);
    }

    if(isset($_FILES['fotokeg2']) && $_FILES['fotokeg2']['error'] == 0){
        if($fotokeg2 && file_exists($uploadDir . $fotokeg2)){
            unlink($uploadDir . $fotokeg2);
        }
        $fotokeg2 = time() . '_keg2_' . $_FILES['fotokeg2']['name'];
        move_uploaded_file($_FILES['fotokeg2']['tmp_name'], $uploadDir . $fotokeg2);
    }

    if(isset($_FILES['fotoabsen']) && $_FILES['fotoabsen']['error'] == 0){
        if($fotoabsen && file_exists($uploadDir . $fotoabsen)){
            unlink($uploadDir . $fotoabsen);
        }
        $fotoabsen = time() . '_absen_' . $_FILES['fotoabsen']['name'];
        move_uploaded_file($_FILES['fotoabsen']['tmp_name'], $uploadDir . $fotoabsen);
    }
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("UPDATE kegiatan SET
        user_id=?, namapetugas=?, nip=?, jabatan=?, unitkerja=?, wilayahtugas=?,
        jeniskegiatan=?, pemateri=?, peserta=?, hari=?, waktupel=?, lokasikeg=?,
        namakelompok=?, modul=?, sesi=?, pemateri1=?, pemateri2=?, pemateri3=?, pemateri4=?,
        namakpm=?, nik=?, nokk=?, alamat=?, desa=?, kecamatan=?, kabupaten=?,
        tglkeg=?, tgllaporan=?, jenis_laporan=?, fotottd=?, fotokeg1=?, fotokeg2=?, fotoabsen=?
        WHERE id=?");
    
    if (!$stmt) {
        die("Error prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("issssssssssssssssssssssssssssssssi", 
        $user_id,
        $_POST['namapetugas'],
        $_POST['nip'],
        $_POST['jabatan'],
        $_POST['unitkerja'],
        $_POST['wilayahtugas'],
        $_POST['jeniskegiatan'],
        $_POST['pemateri'],
        $_POST['peserta'],
        $_POST['hari'],
        $_POST['waktupel'],
        $_POST['lokasikeg'],
        $_POST['namakelompok'],
        $_POST['modul'],
        $_POST['sesi'],
        $_POST['pemateri1'],
        $_POST['pemateri2'],
        $_POST['pemateri3'],
        $_POST['pemateri4'],
        $_POST['namakpm'],
        $_POST['nik'],
        $_POST['nokk'],
        $_POST['alamat'],
        $_POST['desa'],
        $_POST['kecamatan'],
        $_POST['kabupaten'],
        $_POST['tglkeg'],
        $_POST['tgllaporan'],
        $_POST['jenis_laporan'],
        $fotottd,
        $fotokeg1,
        $fotokeg2,
        $fotoabsen,
        $id
    );
    
    if ($stmt->execute()) {
        header("Location: edit.php?id=" . $id . "&status=success&msg=Data berhasil diupdate");
        exit;
    } else {
        header("Location: edit.php?id=" . $id . "&status=error&msg=Gagal mengupdate data: " . urlencode($stmt->error));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Data Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .card-header {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            border-radius: 20px 20px 0 0 !important;
            padding: 20px 30px;
            border: none;
        }
        .card-header h4 {
            margin: 0;
            font-weight: 600;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .section-title {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            padding: 12px 20px;
            margin: 20px 0 15px 0;
            border-left: 5px solid #ffc107;
            border-radius: 0 10px 10px 0;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .section-title i {
            margin-right: 10px;
            color: #ffc107;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
        .input-group-text {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
            border: none;
            border-radius: 12px 0 0 12px;
        }
        .preview-image {
            border: 3px solid #ffc107;
            padding: 5px;
            border-radius: 15px;
            margin-bottom: 8px;
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(255,193,7,0.3);
        }
        .btn-update {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
            color: white;
        }
        .btn-kembali {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            border: none;
            color: white;
            font-weight: 500;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        .btn-kembali:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            color: white;
        }
        .card-dokumentasi {
            border: 2px dashed #ffc107;
            border-radius: 15px;
            transition: all 0.3s;
            height: 100%;
        }
        .card-dokumentasi:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255,193,7,0.2);
        }
        hr {
            border: 2px solid #e9ecef;
            border-radius: 2px;
            opacity: 0.5;
            margin: 25px 0;
        }
        
        /* Style untuk select */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffc107' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }
        select.form-control option {
            padding: 10px;
            white-space: normal;
            word-wrap: break-word;
            max-width: 100%;
        }
        select.form-control optgroup {
            font-weight: 600;
            color: #ff9800;
        }
        
        .badge-jenis {
            background: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 20px !important;
            }
            .btn-update, .btn-kembali {
                width: 100%;
                margin: 5px 0 !important;
            }
            .section-title {
                font-size: 1rem;
            }
            select.form-control option {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card shadow-lg">
            <div class="card-header">
                <h4><i class="bi bi-pencil-square me-2"></i>EDIT DATA RENCANA HASIL KERJA</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Jenis RHK -->
                    <div class="section-title">
                        <i class="bi bi-person-badge"></i> Jenis RHK
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Jenis Laporan RHK yang akan dibuat<span class="text-danger">*</span></label>
                            <select name="jenis_laporan" class="form-control" id="jenis_laporan" required>
                                <option value="">-- Pilih Jenis Laporan --</option>
                                <!-- Kategori 1: Edukasi dan Sosialisasi -->
                                <optgroup label="Kategori RHK 1: Edukasi dan Sosialisasi">
                                    <option value="1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai" <?= ($d['jenis_laporan'] == '1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai') ? 'selected' : '' ?>>1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai</option>
                                    <option value="1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial" <?= ($d['jenis_laporan'] == '1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial') ? 'selected' : '' ?>>1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial</option>
                                    <option value="1. Laporan Penelitian penyaluran bantuan Sosial" <?= ($d['jenis_laporan'] == '1. Laporan Penelitian penyaluran bantuan Sosial') ? 'selected' : '' ?>>1. Laporan Penelitian penyaluran bantuan Sosial</option>
                                    <option value="1. Laporan Supervisi Permasalahan Bantuan Sosial" <?= ($d['jenis_laporan'] == '1. Laporan Supervisi Permasalahan Bantuan Sosial') ? 'selected' : '' ?>>1. Laporan Supervisi Permasalahan Bantuan Sosial</option>
                                </optgroup>
                                <!-- Kategori 2: P2K2 -->
                                <optgroup label="Kategori RHK 2: P2K2">
                                    <option value="2. Laporan Pelaksanaan P2K2" <?= ($d['jenis_laporan'] == '2. Laporan Pelaksanaan P2K2') ? 'selected' : '' ?>>2. Laporan Pelaksanaan P2K2</option>
                                </optgroup>
                                <!-- Kategori 3: Pendampingan dan Verifikasi -->
                                <optgroup label="Kategori RHK 3: Pendampingan dan Verifikasi">
                                    <option value="3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif" <?= ($d['jenis_laporan'] == '3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif') ? 'selected' : '' ?>>3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif</option>
                                    <option value="3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial" <?= ($d['jenis_laporan'] == '3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial') ? 'selected' : '' ?>>3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial</option>
                                </optgroup>
                                <!-- Kategori 4: Graduasi -->
                                <optgroup label="Kategori RHK 4: Graduasi">
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)" <?= ($d['jenis_laporan'] == '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)') ? 'selected' : '' ?>>4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)</option>
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)" <?= ($d['jenis_laporan'] == '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)') ? 'selected' : '' ?>>4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)</option>
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE" <?= ($d['jenis_laporan'] == '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE') ? 'selected' : '' ?>>4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE</option>
                                </optgroup>
                                <!-- Kategori 5: Pemutakhiran Data -->
                                <optgroup label="Kategori RHK 5: Pemutakhiran Data">
                                    <option value="5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)" <?= ($d['jenis_laporan'] == '5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)') ? 'selected' : '' ?>>5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)</option>
                                    <option value="5. Laporan Pemutakhiran Data KPM PKH" <?= ($d['jenis_laporan'] == '5. Laporan Pemutakhiran Data KPM PKH') ? 'selected' : '' ?>>5. Laporan Pemutakhiran Data KPM PKH</option>
                                    <option value="5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial" <?= ($d['jenis_laporan'] == '5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial') ? 'selected' : '' ?>>5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial</option>
                                </optgroup>
                                <!-- Kategori 6: Pengaduan -->
                                <optgroup label="Kategori RHK 6: Pengaduan">
                                    <option value="6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan" <?= ($d['jenis_laporan'] == '6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan') ? 'selected' : '' ?>>6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan</option>
                                </optgroup>
                                <!-- Kategori 7: Laporan Bulanan -->
                                <optgroup label="Kategori RHK 7: Laporan Bulanan">
                                    <option value="7. LAPORAN BULANAN ASN PPPK" <?= ($d['jenis_laporan'] == '7. LAPORAN BULANAN ASN PPPK') ? 'selected' : '' ?>>7. LAPORAN BULANAN ASN PPPK</option>
                                </optgroup>
                                <!-- Kategori 8: Rapat Koordinasi dan Sosialisasi -->
                                <optgroup label="Kategori RHK 8: Rapat Koordinasi dan Sosialisasi">
                                    <option value="8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)" <?= ($d['jenis_laporan'] == '8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)') ? 'selected' : '' ?>>8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)</option>
                                    <option value="8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)" <?= ($d['jenis_laporan'] == '8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)') ? 'selected' : '' ?>>8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)</option>
                                    <option value="8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll" <?= ($d['jenis_laporan'] == '8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll') ? 'selected' : '' ?>>8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll</option>
                                    <option value="8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)" <?= ($d['jenis_laporan'] == '8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)') ? 'selected' : '' ?>>8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)</option>
                                </optgroup>
                                <!-- Kategori 9: Media Sosial -->
                                <optgroup label="Kategori RHK 9: Media Sosial">
                                    <option value="9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial" <?= ($d['jenis_laporan'] == '9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial') ? 'selected' : '' ?>>9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial</option>
                                </optgroup>
                            </select>
                            <small class="text-muted">Pilih jenis laporan sesuai dengan RHK</small>
                        </div>
                    </div>

                    <!-- Data Petugas (Selalu Tampil) -->
                    <div class="section-title">
                        <i class="bi bi-person-badge"></i> Data Petugas
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Petugas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="namapetugas" class="form-control" value="<?= htmlspecialchars($d['namapetugas']) ?>" placeholder="Masukkan nama lengkap petugas" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($d['nip']) ?>" placeholder="Masukkan NIP">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                <select name="jabatan" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Penata Layanan Operasional" <?= ($d['jabatan'] == 'Penata Layanan Operasional') ? 'selected' : '' ?>>Penata Layanan Operasional</option>
                                    <option value="Pengelola Layanan Operasional" <?= ($d['jabatan'] == 'Pengelola Layanan Operasional') ? 'selected' : '' ?>>Pengelola Layanan Operasional</option>
                                    <option value="Operator Layanan Operasional" <?= ($d['jabatan'] == 'Operator Layanan Operasional') ? 'selected' : '' ?>>Operator Layanan Operasional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select name="unitkerja" class="form-control" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <option value="Direktorat Perlindungan Sosial Non Kebencanaan" <?= ($d['unitkerja'] == 'Direktorat Perlindungan Sosial Non Kebencanaan') ? 'selected' : '' ?>>Direktorat Perlindungan Sosial Non Kebencanaan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Wilayah Tugas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="wilayahtugas" class="form-control" value="<?= htmlspecialchars($d['wilayahtugas']) ?>" placeholder="Masukkan wilayah tugas">
                            </div>
                        </div>
                    </div>

                    <!-- Data Kegiatan (Akan di-toggle) -->
                    <div class="section-title" id="dataKegiatanTitle">
                        <i class="bi bi-calendar-event"></i> Data Kegiatan
                    </div>
                    <div class="row g-3" id="dataKegiatanSection">
                        <!-- Field khusus untuk RHK 8 (akan ditampilkan jika diperlukan) -->
                        <div class="col-md-6 rhk8-field" style="display: none;">
                            <label class="form-label">Agenda Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" name="jeniskegiatan" class="form-control" value="<?= htmlspecialchars($d['jeniskegiatan']) ?>" placeholder="Masukkan agenda rapat (untuk point 8)">
                            </div>
                        </div>
                        <div class="col-md-6 rhk8-field" style="display: none;">
                            <label class="form-label">Pimpinan Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-mic"></i></span>
                                <input type="text" name="pemateri" class="form-control" value="<?= htmlspecialchars($d['pemateri']) ?>" placeholder="Masukkan nama pemimpin rapat (untuk point 8)">
                            </div>
                        </div>
                        <div class="col-md-6 rhk8-field" style="display: none;">
                            <label class="form-label">Peserta Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <input type="text" name="peserta" class="form-control" value="<?= htmlspecialchars($d['peserta']) ?>" placeholder="Masukkan peserta rapat (untuk point 8)">
                            </div>
                        </div>
                        
                        <!-- Field umum untuk semua RHK -->
                        <div class="col-md-4">
                            <label class="form-label">Hari</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-sun"></i></span>
                                <select name="hari" class="form-control">
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" <?= ($d['hari'] == 'Senin') ? 'selected' : '' ?>>Senin</option>
                                    <option value="Selasa" <?= ($d['hari'] == 'Selasa') ? 'selected' : '' ?>>Selasa</option>
                                    <option value="Rabu" <?= ($d['hari'] == 'Rabu') ? 'selected' : '' ?>>Rabu</option>
                                    <option value="Kamis" <?= ($d['hari'] == 'Kamis') ? 'selected' : '' ?>>Kamis</option>
                                    <option value="Jumat" <?= ($d['hari'] == 'Jumat') ? 'selected' : '' ?>>Jumat</option>
                                    <option value="Sabtu" <?= ($d['hari'] == 'Sabtu') ? 'selected' : '' ?>>Sabtu</option>
                                    <option value="Minggu" <?= ($d['hari'] == 'Minggu') ? 'selected' : '' ?>>Minggu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Pelaksanaan Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="time" name="waktupel" class="form-control" value="<?= htmlspecialchars($d['waktupel']) ?>" step="60">
                            </div>
                            <small class="text-muted">Format 24 jam (Contoh: 08:00 atau 14:30)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" name="tglkeg" class="form-control" value="<?= htmlspecialchars($d['tglkeg']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Laporan Dibuat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="tgllaporan" class="form-control" value="<?= htmlspecialchars($d['tgllaporan']) ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lokasi Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="lokasikeg" class="form-control" rows="2" placeholder="Masukkan lokasi kegiatan secara lengkap"><?= htmlspecialchars($d['lokasikeg']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Data KPM (Akan di-toggle) -->
                    <div class="section-title" id="dataKPMTitle">
                        <i class="bi bi-people-fill"></i> Data KPM (Keluarga Penerima Manfaat)
                    </div>
                    <div class="row g-3" id="dataKPMSection">
                        <div class="col-md-6">
                            <label class="form-label">Nama KPM</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                <input type="text" name="namakpm" class="form-control" value="<?= htmlspecialchars($d['namakpm']) ?>" placeholder="Nama lengkap KPM">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($d['nik']) ?>" placeholder="Nomor NIK KPM">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No KK</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-folder"></i></span>
                                <input type="text" name="nokk" class="form-control" value="<?= htmlspecialchars($d['nokk']) ?>" placeholder="Nomor Kartu Keluarga">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-house"></i></span>
                                <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($d['alamat']) ?>" placeholder="Alamat lengkap">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Desa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tree"></i></span>
                                <input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($d['desa']) ?>" placeholder="Desa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kecamatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($d['kecamatan']) ?>" placeholder="Kecamatan">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kabupaten</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="kabupaten" class="form-control" value="<?= htmlspecialchars($d['kabupaten']) ?>" placeholder="Kabupaten">
                            </div>
                        </div>
                    </div>

                    <!-- Data Kelompok dan Modul P2K2 (Khusus RHK 2) -->
                    <div class="section-title" id="dataKelompokTitle">
                        <i class="bi bi-people-fill"></i> Data Kelompok dan Modul P2K2
                    </div>
                    <div class="row g-3" id="dataKelompokSection">
                        <div class="col-md-6">
                            <label class="form-label">Nama Kelompok</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <input type="text" name="namakelompok" class="form-control" value="<?= htmlspecialchars($d['namakelompok']) ?>" placeholder="Masukkan nama kelompok">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modul</label>
                            <select name="modul" class="form-control" id="modul" onchange="loadSesi()">
                                <option value="">-- Pilih Modul --</option>
                                <option value="MODUL 1: Pengasuhan dan Pendidikan Anak" <?= ($d['modul'] == 'MODUL 1: Pengasuhan dan Pendidikan Anak') ? 'selected' : '' ?>>MODUL 1: Pengasuhan dan Pendidikan Anak</option>
                                <option value="MODUL 2: Kesehatan dan Gizi" <?= ($d['modul'] == 'MODUL 2: Kesehatan dan Gizi') ? 'selected' : '' ?>>MODUL 2: Kesehatan dan Gizi</option>
                                <option value="MODUL 3: Ekonomi" <?= ($d['modul'] == 'MODUL 3: Ekonomi') ? 'selected' : '' ?>>MODUL 3: Ekonomi</option>
                                <option value="MODUL 4: Pencegahan dan Penanganan Stunting" <?= ($d['modul'] == 'MODUL 4: Pencegahan dan Penanganan Stunting') ? 'selected' : '' ?>>MODUL 4: Pencegahan dan Penanganan Stunting</option>
                                <option value="MODUL 5: Perlindungan Anak" <?= ($d['modul'] == 'MODUL 5: Perlindungan Anak') ? 'selected' : '' ?>>MODUL 5: Perlindungan Anak</option>
                                <option value="MODUL 6: Kesehatan Kesejahteraan Sosial" <?= ($d['modul'] == 'MODUL 6: Kesehatan Kesejahteraan Sosial') ? 'selected' : '' ?>>MODUL 6: Kesehatan Kesejahteraan Sosial</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Sesi</label>
                            <select name="sesi" class="form-control" id="sesi">
                                <option value="">-- Pilih Modul Terlebih Dahulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Data Pemateri P2K2 (Khusus RHK 2) -->
                    <div class="section-title" id="dataPemateriTitle">
                        <i class="bi bi-person-badge"></i> Data Pemateri P2K2
                    </div>
                    <div class="row g-3" id="dataPemateriSection">
                        <div class="col-md-6">
                            <label class="form-label">Pemateri 1</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="pemateri1" class="form-control" value="<?= htmlspecialchars($d['pemateri1']) ?>" placeholder="Nama pemateri 1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pemateri 2</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="pemateri2" class="form-control" value="<?= htmlspecialchars($d['pemateri2']) ?>" placeholder="Nama pemateri 2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pemateri 3</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="pemateri3" class="form-control" value="<?= htmlspecialchars($d['pemateri3']) ?>" placeholder="Nama pemateri 3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pemateri 4</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="pemateri4" class="form-control" value="<?= htmlspecialchars($d['pemateri4']) ?>" placeholder="Nama pemateri 4">
                            </div>
                        </div>
                    </div>

                    <!-- Dokumentasi (Selalu Tampil) -->
                    <div class="section-title">
                        <i class="bi bi-images"></i> Dokumentasi Kegiatan
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card card-dokumentasi h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-pen upload-icon"></i>
                                    <label class="form-label d-block">Foto Tanda Tangan</label>
                                    <?php if($d['fotottd']): ?>
                                        <div class="text-center mb-3">
                                            <img src="uploads/<?= htmlspecialchars($d['fotottd']) ?>" class="preview-image" alt="Foto TTD">
                                            <br>
                                            <span class="badge bg-success"><?= htmlspecialchars($d['fotottd']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotottd" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-dokumentasi h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-camera upload-icon"></i>
                                    <label class="form-label d-block">Foto Kegiatan 1</label>
                                    <?php if($d['fotokeg1']): ?>
                                        <div class="text-center mb-3">
                                            <img src="uploads/<?= htmlspecialchars($d['fotokeg1']) ?>" class="preview-image" alt="Foto Kegiatan 1">
                                            <br>
                                            <span class="badge bg-success"><?= htmlspecialchars($d['fotokeg1']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotokeg1" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-dokumentasi h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-camera2 upload-icon"></i>
                                    <label class="form-label d-block">Foto Kegiatan 2</label>
                                    <?php if($d['fotokeg2']): ?>
                                        <div class="text-center mb-3">
                                            <img src="uploads/<?= htmlspecialchars($d['fotokeg2']) ?>" class="preview-image" alt="Foto Kegiatan 2">
                                            <br>
                                            <span class="badge bg-success"><?= htmlspecialchars($d['fotokeg2']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotokeg2" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-dokumentasi h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-camera2 upload-icon"></i>
                                    <label class="form-label d-block">Foto Absen P2K2</label>
                                    <?php if($d['fotoabsen']): ?>
                                        <div class="text-center mb-3">
                                            <img src="uploads/<?= htmlspecialchars($d['fotoabsen']) ?>" class="preview-image" alt="Foto Absen">
                                            <br>
                                            <span class="badge bg-success"><?= htmlspecialchars($d['fotoabsen']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotoabsen" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Kosongkan jika tidak ingin mengubah</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Tombol Aksi -->
                    <div class="text-center">
                        <button type="submit" name="update" class="btn btn-update btn-lg">
                            <i class="bi bi-save me-2"></i>Update Data
                        </button>
                        <a href="index.php" class="btn btn-kembali btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                        <a href="home.php" class="btn btn-kembali btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Home
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Data sesi berdasarkan modul
        const dataSesi = {
            "MODUL 1: Pengasuhan dan Pendidikan Anak": [
                "SESI 1: Menjadi Orangtua Yang Lebih Baik",
                "SESI 2: Memahami Perkembangan Dan Perilaku Anak",
                "SESI 3: Memahami Cara Anak Usia Dini Belajar",
                "SESI 4: Membantu Anak Sukses Di Sekolah"
            ],
            "MODUL 2: Kesehatan dan Gizi": [
                "SESI 1: Pentingnya Gizi Dan Layanan Kesehatan Ibu Hamil",
                "SESI 2: Pentingnya Gizi Untuk Ibu Menyusui Dan Balita",
                "SESI 3: Kesakitan Pada Anak Dan Kesehatan Lingkungan"
            ],
            "MODUL 3: Ekonomi": [
                "SESI 1: Mengelola Keuangan Keluarga",
                "SESI 2: Cermat Meminjam Dan Menabung",
                "SESI 3: Memulai Usaha"
            ],
            "MODUL 4: Pencegahan dan Penanganan Stunting": [
                "SESI 1: Permasalahan Stunting",
                "SESI 2: Permasalahan Sosial",
                "SESI 3: Mendukung Ibu Hamil Mengakses Informasi yang tepat dan Layanan yang Tersedia di Masyarakat",
                "SESI 4: Mendukung Perawatan Sehari-hari Ibu Hamil",
                "SESI 5: Mendukung Ibu dan Ayah untuk Memberikan Stimulasi pada Janin",
                "SESI 6: Pencegahan dan Penanganan Stunting Melalui Pemenuhan Kesejahteraan Bayi Baru Lahir dan Ibu Menyusui",
                "SESI 7: Mendukung Pemberian Stimulasi pada Bayi baru lahir",
                "SESI 8: Mendukung Pemberian Stimulasi pada Bayi Usia 6 - 12 bulan",
                "SESI 9: Mendukung Pemberian Stimulasi pada Anak Usia 1-2 tahun",
                "SESI 10: Mendukung Pemberian Stimulasi pada Anak Usia 2-6 tahun",
                "SESI 11: Pemanfaatan Bantuan Sosial Dalam Pemenuhan Gizi Bagi Anak dan Ibu Hamil",
                "SESI 12: Mendukung Praktik Cuci Tangan Pakai Sabun (CTPS)",
                "SESI 13: Pemetaan Potensi Diri, Keluarga dan Lingkungan Sekitar",
                "SESI 14: Mendukung Keluarga Mengakses Sistem Rujukan untuk Penanganan Anak Stunting",
                "SESI 15: Komitmen Melaksanakan Rencana Tindak Lanjut"
            ],
            "MODUL 5: Perlindungan Anak": [
                "SESI 1: Upaya Pencegahan Kekerasan Dan Perilaku Salah Pada Anak",
                "SESI 2: Penelantaran Dan Eksploitasi Terhadap Anak"
            ],
            "MODUL 6: Kesehatan Kesejahteraan Sosial": [
                "SESI 1: Pelayanan Bagi Penyandang Disabilitas Berat",
                "SESI 2: Pentingnya Kesejahteraan Lanjut Usia"
            ]
        };

        function loadSesi() {
            const modul = document.getElementById('modul').value;
            const sesiSelect = document.getElementById('sesi');
            
            sesiSelect.innerHTML = '';
            
            if (modul && dataSesi[modul]) {
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Pilih Sesi --';
                sesiSelect.appendChild(defaultOption);
                
                dataSesi[modul].forEach(sesi => {
                    const option = document.createElement('option');
                    option.value = sesi;
                    option.textContent = sesi;
                    
                    // Set selected jika sesuai dengan data dari database
                    if (sesi === "<?= htmlspecialchars($d['sesi']) ?>") {
                        option.selected = true;
                    }
                    
                    sesiSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-- Pilih Modul Terlebih Dahulu --';
                sesiSelect.appendChild(option);
            }
        }

        // Fungsi untuk menampilkan/menyembunyikan section berdasarkan jenis RHK
        function toggleSections() {
            const jenisLaporan = document.getElementById('jenis_laporan').value;
            
            // Dapatkan semua section
            const dataKegiatanTitle = document.getElementById('dataKegiatanTitle');
            const dataKegiatanSection = document.getElementById('dataKegiatanSection');
            const dataKPMTitle = document.getElementById('dataKPMTitle');
            const dataKPMSection = document.getElementById('dataKPMSection');
            const dataKelompokTitle = document.getElementById('dataKelompokTitle');
            const dataKelompokSection = document.getElementById('dataKelompokSection');
            const dataPemateriTitle = document.getElementById('dataPemateriTitle');
            const dataPemateriSection = document.getElementById('dataPemateriSection');
            
            // Field khusus RHK 8
            const agendaRapat = document.querySelector('input[name="jeniskegiatan"]').closest('.col-md-6');
            const pimpinanRapat = document.querySelector('input[name="pemateri"]').closest('.col-md-6');
            const pesertaRapat = document.querySelector('input[name="peserta"]').closest('.col-md-6');
            
            // Field umum di Data Kegiatan
            const hariField = document.querySelector('select[name="hari"]').closest('.col-md-4');
            const waktuField = document.querySelector('input[name="waktupel"]').closest('.col-md-4');
            const tglKegField = document.querySelector('input[name="tglkeg"]').closest('.col-md-4');
            const tglLaporanField = document.querySelector('input[name="tgllaporan"]').closest('.col-md-4');
            const lokasiField = document.querySelector('textarea[name="lokasikeg"]').closest('.col-12');
            
            // Sembunyikan semua section terlebih dahulu
            dataKegiatanTitle.style.display = 'none';
            dataKegiatanSection.style.display = 'none';
            dataKPMTitle.style.display = 'none';
            dataKPMSection.style.display = 'none';
            dataKelompokTitle.style.display = 'none';
            dataKelompokSection.style.display = 'none';
            dataPemateriTitle.style.display = 'none';
            dataPemateriSection.style.display = 'none';
            
            // Sembunyikan field khusus RHK 8
            if (agendaRapat) agendaRapat.style.display = 'none';
            if (pimpinanRapat) pimpinanRapat.style.display = 'none';
            if (pesertaRapat) pesertaRapat.style.display = 'none';
            
            // Tampilkan field berdasarkan jenis RHK
            if (jenisLaporan.startsWith('1.') || jenisLaporan.startsWith('3.') || 
                jenisLaporan.startsWith('4.') || jenisLaporan.startsWith('5.') || 
                jenisLaporan.startsWith('6.')) { // RHK 1,3,4,5,6    //|| jenisLaporan.startsWith('9.')) { // RHK 1,3,4,5,6,9
                
                // Tampilkan Data Kegiatan (dengan field umum)
                dataKegiatanTitle.style.display = 'block';
                dataKegiatanSection.style.display = 'flex';
                if (hariField) hariField.style.display = 'block';
                if (waktuField) waktuField.style.display = 'block';
                if (tglKegField) tglKegField.style.display = 'block';
                if (tglLaporanField) tglLaporanField.style.display = 'block';
                if (lokasiField) lokasiField.style.display = 'block';
                
                // Tampilkan Data KPM
                dataKPMTitle.style.display = 'block';
                dataKPMSection.style.display = 'flex';
            } 
            else if (jenisLaporan.startsWith('2.')) { // RHK 2
                // Tampilkan Data Kegiatan (dengan field umum)
                dataKegiatanTitle.style.display = 'block';
                dataKegiatanSection.style.display = 'flex';
                if (hariField) hariField.style.display = 'block';
                if (waktuField) waktuField.style.display = 'block';
                if (tglKegField) tglKegField.style.display = 'block';
                if (tglLaporanField) tglLaporanField.style.display = 'block';
                if (lokasiField) lokasiField.style.display = 'block';
                
                // Tampilkan Data Kelompok dan Modul P2K2
                dataKelompokTitle.style.display = 'block';
                dataKelompokSection.style.display = 'flex';
                
                // Tampilkan Data Pemateri P2K2
                dataPemateriTitle.style.display = 'block';
                dataPemateriSection.style.display = 'flex';
            } 
            else if (jenisLaporan.startsWith('7.')) { // RHK 7
                // Tampilkan hanya Tanggal Laporan dari Data Kegiatan
                dataKegiatanTitle.style.display = 'block';
                dataKegiatanSection.style.display = 'flex';
                
                // Sembunyikan field yang tidak diperlukan
                if (hariField) hariField.style.display = 'none';
                if (waktuField) waktuField.style.display = 'none';
                if (tglKegField) tglKegField.style.display = 'none';
                if (lokasiField) lokasiField.style.display = 'none';
                
                // Pastikan Tanggal Laporan tetap tampil
                if (tglLaporanField) tglLaporanField.style.display = 'block';
            } 
            else if (jenisLaporan.startsWith('8.')) { // RHK 8
                // Tampilkan Data Kegiatan
                dataKegiatanTitle.style.display = 'block';
                dataKegiatanSection.style.display = 'flex';
                
                // Sembunyikan field umum
                if (hariField) hariField.style.display = 'none';
                if (waktuField) waktuField.style.display = 'none';
                if (tglKegField) tglKegField.style.display = 'none';
                if (lokasiField) lokasiField.style.display = 'none';
                if (tglLaporanField) tglLaporanField.style.display = 'block';
                
                // Tampilkan field khusus RHK 8
                if (agendaRapat) {
                    agendaRapat.style.display = 'block';
                    agendaRapat.classList.add('col-md-6');
                }
                if (pimpinanRapat) {
                    pimpinanRapat.style.display = 'block';
                    pimpinanRapat.classList.add('col-md-6');
                }
                if (pesertaRapat) {
                    pesertaRapat.style.display = 'block';
                    pesertaRapat.classList.add('col-md-6');
                }
                
                // Tampilkan Data KPM
              //  dataKPMTitle.style.display = 'block';
              //  dataKPMSection.style.display = 'flex';
            }
            
            else if (jenisLaporan.startsWith('9.')) { // RHK 9 - REVISI SESUAI PERMINTAAN
        // Tampilkan Data Kegiatan (dengan field umum)
        dataKegiatanTitle.style.display = 'block';
        dataKegiatanSection.style.display = 'flex';
        if (hariField) hariField.style.display = 'block';
        if (waktuField) waktuField.style.display = 'block';
        if (tglKegField) tglKegField.style.display = 'block';
        if (tglLaporanField) tglLaporanField.style.display = 'block';
        if (lokasiField) lokasiField.style.display = 'block';
        
        // TIDAK MENAMPILKAN Data KPM (sesuai permintaan)
        // dataKPMTitle tetap disembunyikan
    }
 }

        // Event listener untuk perubahan jenis laporan
        document.addEventListener('DOMContentLoaded', function() {
            const jenisLaporanSelect = document.getElementById('jenis_laporan');
            if (jenisLaporanSelect) {
                jenisLaporanSelect.addEventListener('change', toggleSections);
                // Jalankan sekali saat halaman dimuat
                toggleSections();
            }
            
            // Panggil loadSesi jika ada modul yang sudah dipilih
            const modul = document.getElementById('modul');
            if (modul && modul.value) {
                loadSesi();
            }
        });

        // NOTIFIKASI SWEETALERT
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const msg = urlParams.get('msg');

        if(status && msg) {
            Swal.fire({
                icon: status,
                title: status == 'success' ? 'Berhasil!' : 'Gagal!',
                text: decodeURIComponent(msg),
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                timerProgressBar: true,
                background: status == 'success' ? '#10b981' : '#ef4444',
                color: '#ffffff'
            });
            
            // Hapus parameter dari URL
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            url.searchParams.delete('msg');
            window.history.replaceState({}, document.title, url.toString());
        }

        // Update activity setiap 30 detik
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