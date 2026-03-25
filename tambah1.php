<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
 
include "koneksi.php";

if(isset($_POST['simpan'])){

    $uploadDir = "uploads/";
    
    // Buat folder jika belum ada
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload file dengan pengecekan error
    $fotoabsen = '';
    $fotottd = '';
    $fotokeg1 = '';
    $fotokeg2 = '';

    // Foto absen
    if(isset($_FILES['fotoabsen']) && $_FILES['fotoabsen']['error'] == 0){
        $fotoabsen = time() . '_absen_' . $_FILES['fotoabsen']['name'];
        move_uploaded_file($_FILES['fotoabsen']['tmp_name'], $uploadDir . $fotoabsen);
    }

    // Foto tanda tangan
    if(isset($_FILES['fotottd']) && $_FILES['fotottd']['error'] == 0){
        $fotottd = time() . '_ttd_' . $_FILES['fotottd']['name'];
        move_uploaded_file($_FILES['fotottd']['tmp_name'], $uploadDir . $fotottd);
    }

    // Foto kegiatan 1
    if(isset($_FILES['fotokeg1']) && $_FILES['fotokeg1']['error'] == 0){
        $fotokeg1 = time() . '_keg1_' . $_FILES['fotokeg1']['name'];
        move_uploaded_file($_FILES['fotokeg1']['tmp_name'], $uploadDir . $fotokeg1);
    }

    // Foto kegiatan 2
    if(isset($_FILES['fotokeg2']) && $_FILES['fotokeg2']['error'] == 0){
        $fotokeg2 = time() . '_keg2_' . $_FILES['fotokeg2']['name'];
        move_uploaded_file($_FILES['fotokeg2']['tmp_name'], $uploadDir . $fotokeg2);
    }

    // ambil user login
    $user_id = $_SESSION['user_id'];

    // HITUNG JUMLAH KOLOM: Ada 33 kolom
    $stmt = $conn->prepare("INSERT INTO kegiatan (
        user_id,
        namapetugas, nip, jabatan, unitkerja, wilayahtugas, 
        jeniskegiatan, pemateri, peserta, hari, waktupel, 
        lokasikeg, namakelompok, modul, sesi, pemateri1, pemateri2, pemateri3, pemateri4,
        namakpm, nik, nokk, alamat, desa, kecamatan, 
        kabupaten, tglkeg, tgllaporan, jenis_laporan,
        fotottd, fotokeg1, fotokeg2, fotoabsen
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error prepare statement: " . $conn->error);
    }

    // PERBAIKAN: String bind_param harus 33 karakter (1 i + 32 s)
    $stmt->bind_param("issssssssssssssssssssssssssssssss", // 33 karakter
        $user_id,                                           // i
        $_POST['namapetugas'],                              // s
        $_POST['nip'],                                      // s
        $_POST['jabatan'],                                  // s
        $_POST['unitkerja'],                                // s
        $_POST['wilayahtugas'],                             // s
        $_POST['jeniskegiatan'],                            // s
        $_POST['pemateri'],                                 // s
        $_POST['peserta'],                                  // s
        $_POST['hari'],                                     // s
        $_POST['waktupel'],                                 // s
        $_POST['lokasikeg'],                                // s
        $_POST['namakelompok'],                             // s
        $_POST['modul'],                                    // s
        $_POST['sesi'],                                     // s
        $_POST['pemateri1'],                                // s
        $_POST['pemateri2'],                                // s
        $_POST['pemateri3'],                                // s
        $_POST['pemateri4'],                                // s
        $_POST['namakpm'],                                  // s
        $_POST['nik'],                                      // s
        $_POST['nokk'],                                     // s
        $_POST['alamat'],                                   // s
        $_POST['desa'],                                     // s
        $_POST['kecamatan'],                                // s
        $_POST['kabupaten'],                                // s
        $_POST['tglkeg'],                                   // s
        $_POST['tgllaporan'],                               // s
        $_POST['jenis_laporan'],                            // s
        $fotottd,                                           // s
        $fotokeg1,                                          // s
        $fotokeg2,                                          // s
        $fotoabsen                                          // s
    );

    if ($stmt->execute()) {
        header("Location: index.php?status=success&msg=Data berhasil ditambahkan");
        exit;
    } else {
        die("Error execute: " . $stmt->error);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Kegiatan</title>
    <!-- [rest of your HTML code remains the same] -->
</head>
<body>
    <!-- [your HTML form remains the same] -->
</body>
</html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
            background: linear-gradient(45deg, #28a745, #20c997);
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
            border-left: 5px solid #28a745;
            border-radius: 0 10px 10px 0;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .section-title i {
            margin-right: 10px;
            color: #28a745;
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
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .input-group-text {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 12px 0 0 12px;
        }
        
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 10px;
        }
        
        .file-upload-wrapper .form-control {
            padding: 8px 12px;
        }
        
        .btn-simpan {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-simpan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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
        
        hr {
            border: 2px solid #e9ecef;
            border-radius: 2px;
            opacity: 0.5;
            margin: 25px 0;
        }
        
        .card-footer {
            background: transparent;
            border-top: 2px dashed #e9ecef;
            padding: 25px 30px;
        }
        
        .upload-icon {
            font-size: 2rem;
            color: #28a745;
            margin-right: 10px;
        }
        
        .file-info {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .input-group {
            margin-bottom: 15px;
        }
        
        /* Style untuk select */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2328a745' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
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
            color: #28a745;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 20px !important;
            }
            
            .btn-simpan, .btn-kembali {
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
                <h4><i class="bi bi-plus-circle me-2"></i>TAMBAH DATA RENCANA HASIL KERJA</h4>
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
                            <select name="jenis_laporan" class="form-control" required>
                                <option value="">-- Pilih Jenis Laporan --</option>
                                
                                <!-- Kategori 1: Edukasi dan Sosialisasi -->
                                <optgroup label="Kategori RHK 1: Edukasi dan Sosialisasi">
                                    <option value="1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai">1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai</option>
                                    <option value="1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial">1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial</option>
                                    <option value="1. Laporan Penelitian penyaluran bantuan Sosial">1. Laporan Penelitian penyaluran bantuan Sosial</option>
                                    <option value="1. Laporan Supervisi Permasalahan Bantuan Sosial">1. Laporan Supervisi Permasalahan Bantuan Sosial</option>
                                </optgroup>
                                
                                <!-- Kategori 2: P2K2 -->
                                <optgroup label="Kategori RHK 2: P2K2">
                                    <option value="2. Laporan Pelaksanaan P2K2">2. Laporan Pelaksanaan P2K2</option>
                                </optgroup>
                                
                                <!-- Kategori 3: Pendampingan dan Verifikasi -->
                                <optgroup label="Kategori RHK 3: Pendampingan dan Verifikasi">
                                    <option value="3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif">3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif</option>
                                    <option value="3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial">3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial</option>
                                </optgroup>
                                
                                <!-- Kategori 4: Graduasi -->
                                <optgroup label="Kategori RHK 4: Graduasi">
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)">4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)</option>
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)">4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)</option>
                                    <option value="4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE">4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE</option>
                                </optgroup>
                                
                                <!-- Kategori 5: Pemutakhiran Data -->
                                <optgroup label="Kategori RHK 5: Pemutakhiran Data">
                                    <option value="5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)">5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)</option>
                                    <option value="5. Laporan Pemutakhiran Data KPM PKH">5. Laporan Pemutakhiran Data KPM PKH</option>
                                    <option value="5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial">5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial</option>
                                </optgroup>
                                
                                <!-- Kategori 6: Pengaduan -->
                                <optgroup label="Kategori RHK 6: Pengaduan">
                                    <option value="6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan">6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan</option>
                                </optgroup>
                                
                                <!-- Kategori 7: Laporan Bulanan -->
                                <optgroup label="Kategori RHK 7: Laporan Bulanan">
                                    <option value="7. LAPORAN BULANAN ASN PPPK">7. LAPORAN BULANAN ASN PPPK</option>
                                </optgroup>
                                
                                <!-- Kategori 8: Rapat Koordinasi dan Sosialisasi -->
                                <optgroup label="Kategori RHK 8: Rapat Koordinasi dan Sosialisasi">
                                    <option value="8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)">8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)</option>
                                    <option value="8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)">8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)</option>
                                    <option value="8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll">8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll</option>
                                    <option value="8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)">8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)</option>
                                </optgroup>
                                
                                <!-- Kategori 9: Media Sosial -->
                                <optgroup label="Kategori RHK 9: Media Sosial">
                                    <option value="9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial">9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial</option>
                                </optgroup>
                            </select>
                            <small class="text-muted">Pilih jenis laporan sesuai dengan RHK</small>
                        </div>
                	</div>
                                 
                    <!-- Data Petugas -->
                    <div class="section-title">
                        <i class="bi bi-person-badge"></i> Data Petugas
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Petugas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="namapetugas" class="form-control" placeholder="Masukkan nama lengkap petugas" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
    						<label class="form-label">Jabatan</label>
   						 <div class="input-group">
        					<span class="input-group-text"><i class="bi bi-briefcase"></i></span>
       						 <select name="jabatan" class="form-control" required>
           						 <option value="">-- Pilih Jabatan --</option>
            					<option value="Penata Layanan Operasional">Penata Layanan Operasional</option>
            					<option value="Pengelola Layanan Operasional">Pengelola Layanan Operasional</option>
            					<option value="Operator Layanan Operasional">Operator Layanan Operasional</option>
        					</select>
    					</div>
					</div>
                        
                        <div class="col-md-6">
    						<label class="form-label">Unit Kerja</label>
    					<div class="input-group">
        				<span class="input-group-text"><i class="bi bi-building"></i></span>
        					<select name="unitkerja" class="form-control" required>
            			<option value="">-- Pilih Unit Kerja --</option>
           			 <option value="Direktorat Perlindungan Sosial Non Kebencanaan">Direktorat Perlindungan Sosial Non Kebencanaan</option>
       				 </select>
   				 </div>
				</div>
                        
                        

                        <div class="col-md-6">
                            <label class="form-label">Wilayah Tugas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="wilayahtugas" class="form-control" placeholder="Masukkan wilayah tugas">
                            </div>
                        </div>
                    </div>

                    <!-- Data Kegiatan -->
                    <div class="section-title mt-4">
                        <i class="bi bi-calendar-event"></i> Data Kegiatan
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Agenda Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" name="jeniskegiatan" class="form-control" placeholder="Masukkan agenda rapat (untuk point 8)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pimpinan Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-mic"></i></span>
                                <input type="text" name="pemateri" class="form-control" placeholder="Masukkan nama pemimpin rapat (untuk point 8)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Peserta Rapat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <input type="text" name="peserta" class="form-control" placeholder="Masukkan peserta rapat (untuk point 8)">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
    						<label class="form-label">Hari</label>
   					    <div class="input-group">
       						 <span class="input-group-text"><i class="bi bi-sun"></i></span>
       						 <select name="hari" class="form-control" required>
            					<option value="">-- Pilih Hari --</option>
            					<option value="Senin">Senin</option>
            					<option value="Selasa">Selasa</option>
            					<option value="Rabu">Rabu</option>
            					<option value="Kamis">Kamis</option>
            					<option value="Jumat">Jumat</option>
            					<option value="Sabtu">Sabtu</option>
            					<option value="Minggu">Minggu</option>
        					</select>
    					</div>
					</div>
                        
                        <div class="col-md-4">
    						<label class="form-label">Waktu Pelaksanaan Kegiatan</label>
    						<div class="input-group">
        						<span class="input-group-text"><i class="bi bi-clock"></i></span>
        						<input type="time" name="waktupel" class="form-control" step="60" value="07:30">
    						</div>
   								 <small class="text-muted">Format 24 jam (Contoh: 08:00 atau 14:30)</small>
							</div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" name="tglkeg" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Laporan Dibuat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="tgllaporan" class="form-control">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Lokasi Kegiatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="lokasikeg" class="form-control" rows="2" placeholder="Masukkan lokasi kegiatan secara lengkap"></textarea>
                            </div>
                        </div>
                        
                        

                    <!-- Data KPM -->
                    <div class="section-title mt-4">
                        <i class="bi bi-people-fill"></i> Data KPM (Keluarga Penerima Manfaat)
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama KPM</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                <input type="text" name="namakpm" class="form-control" placeholder="Nama lengkap KPM">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" name="nik" class="form-control" placeholder="Nomor NIK KPM">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No KK</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-folder"></i></span>
                                <input type="text" name="nokk" class="form-control" placeholder="Nomor Kartu Keluarga">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-house"></i></span>
                                <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Desa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tree"></i></span>
                                <input type="text" name="desa" class="form-control" placeholder="Desa">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kecamatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <input type="text" name="kecamatan" class="form-control" placeholder="Kecamatan">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kabupaten</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="kabupaten" class="form-control" placeholder="Kabupaten">
                            </div>
                        </div>

                    </div>

                    <!-- Data Kelompok dan Modul P2K2-->
<div class="section-title mt-4">
    <i class="bi bi-people-fill"></i> Data Kelompok dan Modul P2K2
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Kelompok</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-people"></i></span>
            <input type="text" name="namakelompok" class="form-control" placeholder="Masukkan nama kelompok">
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Modul</label>
        <select name="modul" class="form-control" id="modul" onchange="loadSesi()">
            <option value="">-- Pilih Modul --</option>
            <option value="MODUL 1: Pengasuhan dan Pendidikan Anak">MODUL 1: Pengasuhan dan Pendidikan Anak</option>
            <option value="MODUL 2: Kesehatan dan Gizi">MODUL 2: Kesehatan dan Gizi</option>
            <option value="MODUL 3: Ekonomi">MODUL 3: Ekonomi</option>
            <option value="MODUL 4: Pencegahan dan Penanganan Stunting">MODUL 4: Pencegahan dan Penanganan Stunting</option>
            <option value="MODUL 5: Perlindungan Anak">MODUL 5: Perlindungan Anak</option>
            <option value="MODUL 6: Kesehatan Kesejahteraan Sosial">MODUL 6: Kesehatan Kesejahteraan Sosial</option>
        </select>
    </div>
    
    <div class="col-md-12">
        <label class="form-label">Sesi</label>
        <select name="sesi" class="form-control" id="sesi">
            <option value="">-- Pilih Modul Terlebih Dahulu --</option>
        </select>
    </div>
</div>

<!-- Data Pemateri P2K2-->
<div class="section-title mt-4">
    <i class="bi bi-person-badge"></i> Data Pemateri P2K2
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Pemateri 1</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="pemateri1" class="form-control" placeholder="Nama pemateri 1">
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Pemateri 2</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="pemateri2" class="form-control" placeholder="Nama pemateri 2">
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Pemateri 3</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="pemateri3" class="form-control" placeholder="Nama pemateri 3">
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Pemateri 4</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="pemateri4" class="form-control" placeholder="Nama pemateri 4">
        </div>
    </div>
</div>
                    
                    <!-- Dokumentasi -->
                    <div class="section-title mt-4">
                        <i class="bi bi-images"></i> Dokumentasi Kegiatan
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-pen upload-icon"></i>
                                    <label class="form-label d-block">Foto Tanda Tangan</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotottd" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Maks: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-camera upload-icon"></i>
                                    <label class="form-label d-block">Foto Kegiatan 1</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotokeg1" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Maks: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-camera2 upload-icon"></i>
                                    <label class="form-label d-block">Foto Kegiatan 2</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="fotokeg2" class="form-control" accept="image/*">
                                    </div>
                                    <small class="file-info">Format: JPG, PNG. Maks: 2MB</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Foto Absen -->
<div class="col-md-4">
    <div class="card h-100 border-0 shadow-sm">
        <div class="card-body text-center">
            <i class="bi bi-camera2 upload-icon"></i>
            <label class="form-label d-block">Foto Absen P2K2</label>
            <div class="file-upload-wrapper">
                <input type="file" name="fotoabsen" class="form-control" accept="image/*">
            </div>
            <small class="file-info">Format: JPG, PNG. Maks: 2MB</small>
        </div>
    </div>
</div>
                    </div>

                    <hr>

                    <!-- Tombol Aksi -->
                    <div class="text-center">
                        <button type="submit" name="simpan" class="btn btn-simpan btn-lg">
                            <i class="bi bi-save me-2"></i>Simpan Data
                        </button>
                        <a href="index.php" class="btn btn-kembali btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Lihat Data
                        </a>
                        <a href="home.php" class="btn btn-kembali btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Home
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
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
    const sesiSelected = document.getElementById('sesi_selected')?.value || '';
    
    // Kosongkan option
    sesiSelect.innerHTML = '';
    
    if (modul && dataSesi[modul]) {
        // Tambahkan option default
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Pilih Sesi --';
        sesiSelect.appendChild(defaultOption);
        
        // Tambahkan sesi-sesi
        dataSesi[modul].forEach(sesi => {
            const option = document.createElement('option');
            option.value = sesi;
            option.textContent = sesi;
            if (sesi === sesiSelected) {
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

// Panggil saat halaman dimuat (untuk edit)
document.addEventListener('DOMContentLoaded', function() {
    const modul = document.getElementById('modul');
    if (modul && modul.value) {
        loadSesi();
    }
});
        
// Update last activity setiap 30 detik (lebih sering)
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
}, 30000); // Update setiap 30 detik, bukan 2 menit

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

// Update saat user melakukan klik (opsional)
document.addEventListener('click', function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).catch(error => console.error('Error:', error));
});
</script>
</body>
</html>