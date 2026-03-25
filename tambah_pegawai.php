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
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil semua data dari form
    $no_idpegawai = $conn->real_escape_string($_POST['no_idpegawai']);
    $nip = $conn->real_escape_string($_POST['nip']);
    $nik = $conn->real_escape_string($_POST['nik']);
    $nokk = $conn->real_escape_string($_POST['nokk']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $tmttugas = $_POST['tmttugas'] ?: NULL;
    $kecamatantugas = $conn->real_escape_string($_POST['kecamatantugas']);
    $desadampingan = $conn->real_escape_string($_POST['desadampingan']);
    $jmlkpm = (int)$_POST['jmlkpm'];
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $kabupaten = $conn->real_escape_string($_POST['kabupaten']);
    $kecamatan = $conn->real_escape_string($_POST['kecamatan']);
    $desa = $conn->real_escape_string($_POST['desa']);
    $jeniskelamin = $conn->real_escape_string($_POST['jeniskelamin']);
    $tmplahir = $conn->real_escape_string($_POST['tmplahir']);
    $tgllahir = $_POST['tgllahir'] ?: NULL;
    $usia = hitungUsia($tgllahir);
    $agama = $conn->real_escape_string($_POST['agama']);
    $statusnikah = $conn->real_escape_string($_POST['statusnikah']);
    $jmlanak = (int)$_POST['jmlanak'];
    $pekerjaan = $conn->real_escape_string($_POST['pekerjaan']);
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $nohp = $conn->real_escape_string($_POST['nohp']);
    $userzimbra = $conn->real_escape_string($_POST['userzimbra']);
    $emailzimbra = $conn->real_escape_string($_POST['emailzimbra']);
    $email = $conn->real_escape_string($_POST['email']);
    $universitas = $conn->real_escape_string($_POST['universitas']);
    $jurusan = $conn->real_escape_string($_POST['jurusan']);
    $noijazah = $conn->real_escape_string($_POST['noijazah']);
    $tgllulus = $_POST['tgllulus'] ?: NULL;
    $tahunlulus = (int)$_POST['tahunlulus'];
    $jenjang = $conn->real_escape_string($_POST['jenjang']);
    $norekbtn = $conn->real_escape_string($_POST['norekbtn']);
    $norekjatim = $conn->real_escape_string($_POST['norekjatim']);
    $norekbni = $conn->real_escape_string($_POST['norekbni']);
    $norekbri = $conn->real_escape_string($_POST['norekbri']);
    $namarekening = $conn->real_escape_string($_POST['namarekening']);
    $ibukandung = $conn->real_escape_string($_POST['ibukandung']);
    $npwp = $conn->real_escape_string($_POST['npwp']);
    $bpjskes = $conn->real_escape_string($_POST['bpjskes']);
    $bpjstk = $conn->real_escape_string($_POST['bpjstk']);
    
    // Upload foto
    $foto = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $foto = time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $foto;
        
        // Check file size (max 2MB)
        if($_FILES["foto"]["size"] > 2000000) {
            $error = "Ukuran file terlalu besar (max 2MB)";
        } else {
            // Allow certain file formats
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array(strtolower($file_extension), $allowed_types)) {
                if(!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $error = "Gagal upload file";
                    $foto = '';
                }
            } else {
                $error = "Format file tidak didukung (jpg, jpeg, png, gif)";
                $foto = '';
            }
        }
    }
    
    if(empty($error)) {

    // cek nip dulu
    if(!empty($nip)){
        $cek_nip = $conn->query("SELECT nip FROM pegawai WHERE nip='$nip'");
        if($cek_nip && $cek_nip->num_rows > 0){
            $error = "NIP sudah terdaftar. Silakan gunakan NIP yang berbeda.";
        }
    }

    // jika tidak ada error baru insert
    if(empty($error)){

        $query = "INSERT INTO pegawai (
            no_idpegawai, nip, nik, nokk, nama, tmttugas, kecamatantugas, desadampingan,
            jmlkpm, alamat, kabupaten, kecamatan, desa, jeniskelamin, tmplahir, tgllahir,
            usia, agama, statusnikah, jmlanak, pekerjaan, jabatan, nohp, userzimbra,
            emailzimbra, email, universitas, jurusan, noijazah, tgllulus, tahunlulus,
            jenjang, norekbtn, norekjatim, norekbni, norekbri, namarekening, ibukandung, npwp,
            bpjskes, bpjstk, foto, user_id
        ) VALUES (
            '$no_idpegawai', '$nip', '$nik', '$nokk', '$nama', " . ($tmttugas ? "'$tmttugas'" : "NULL") . ",
            '$kecamatantugas', '$desadampingan', $jmlkpm, '$alamat', '$kabupaten', '$kecamatan',
            '$desa', '$jeniskelamin', '$tmplahir', " . ($tgllahir ? "'$tgllahir'" : "NULL") . ",
            $usia, '$agama', '$statusnikah', $jmlanak, '$pekerjaan', '$jabatan', '$nohp',
            '$userzimbra', '$emailzimbra', '$email', '$universitas', '$jurusan', '$noijazah',
            " . ($tgllulus ? "'$tgllulus'" : "NULL") . ", $tahunlulus, '$jenjang', '$norekbtn', '$norekjatim',
            '$norekbni', '$norekbri', '$namarekening', '$ibukandung', '$npwp', '$bpjskes',
            '$bpjstk', '$foto', '$user_id'
        )";

        if($conn->query($query)) {
            $success = "Data pegawai berhasil ditambahkan";
            header("refresh:2;url=master_pegawai.php");
        } else {
            $error = "Gagal menambahkan data: " . $conn->error;
        }

    }
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
        }
        .form-header h3 {
            margin: 0;
            font-size: 1.8rem;
        }
        .form-body {
            padding: 30px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #28a745;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            outline: none;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex justify-content-between align-items-center">
            <h3><i class="bi bi-person-plus-fill"></i> Tambah Data Pegawai</h3>
            <a href="master_pegawai.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        
        <div class="form-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Data Pribadi -->
                <h4 class="section-title"><i class="bi bi-person-badge"></i> Data Pribadi</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">No ID Pegawai <span class="text-danger">*</span></label>
                        <input type="text" name="no_idpegawai" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No KK</label>
                        <input type="text" name="nokk" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>

                <!-- Tempat & Tanggal Lahir -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tmplahir" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgllahir" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jeniskelamin" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Agama & Status -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Budha">Budha</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Nikah</label>
                        <select name="statusnikah" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Menikah</option>
                            <option value="Kawin">Menikah</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Anak</label>
                        <input type="number" name="jmlanak" class="form-control" value="0" min="0">
                    </div>
                </div>

                <!-- Alamat -->
                <h4 class="section-title"><i class="bi bi-house"></i> Alamat</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                   
                    <div class="col-md-4">
                        <label class="form-label">Kabupaten</label>
                        <select name="kabupaten" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Sumenep">Sumenep</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kecamatan</label>
                        <select name="kecamatan" class="form-select">
                            <option value="">Pilih</option>
                            <option value="AMBUNTEN">AMBUNTEN</option>
                            <option value="ARJASA">ARJASA</option>
                            <option value="BATANG BATANG">BATANG BATANG</option>
                            <option value="BATUAN">BATUAN</option>
                            <option value="BATUPUTIH">BATUPUTIH</option>
                            <option value="BLUTO">BLUTO</option>
                            <option value="DASUK">DASUK</option>
                            <option value="DUNGKOK">DUNGKOK</option>
                            <option value="GANDING">GANDING</option>
                            <option value="GAPURA">GAPURA</option>
                            <option value="GAYAM">GAYAM</option>
                            <option value="GILIGENTING">GILIGENTING</option>
                            <option value="GULUK GULUK">GULUK GULUK</option>
                            <option value="KALIANGET">KALIANGET</option>
                            <option value="KANGAYAN">KANGAYAN</option>
                            <option value="KOTA SUMENEP">KOTA SUMENEP</option>
                            <option value="LENTENG">LENTENG</option>
                            <option value="MANDING">MANDING</option>
                            <option value="MASALEMBU">MASALEMBU</option>
                            <option value="NONGGUNONG">NONGGUNONG</option>
                            <option value="PASONGSONGAN">PASONGSONGAN</option>
                            <option value="PRAGAAN">PRAGAAN</option>
                            <option value="RAAS">RAAS</option>
                            <option value="RUBARU">RUBARU</option>
                            <option value="SAPEKEN">SAPEKEN</option>
                            <option value="SARONGGI">SARONGGI</option>
                            <option value="TALANGO">TALANGO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Desa</label>
                        <input type="text" name="desa" class="form-control">
                    </div>
                </div>

                <!-- Wilayah Tugas -->
                <h4 class="section-title"><i class="bi bi-geo-alt"></i> Wilayah Tugas</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">TMT Tugas</label>
                        <input type="date" name="tmttugas" class="form-control">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Kecamatan Tugas</label>
                        <select name="kecamatantugas" class="form-select">
                            <option value="">Pilih</option>
                            <option value="AMBUNTEN">AMBUNTEN</option>
                            <option value="ARJASA">ARJASA</option>
                            <option value="BATANG BATANG">BATANG BATANG</option>
                            <option value="BATUAN">BATUAN</option>
                            <option value="BATUPUTIH">BATUPUTIH</option>
                            <option value="BLUTO">BLUTO</option>
                            <option value="DASUK">DASUK</option>
                            <option value="DUNGKOK">DUNGKOK</option>
                            <option value="GANDING">GANDING</option>
                            <option value="GAPURA">GAPURA</option>
                            <option value="GAYAM">GAYAM</option>
                            <option value="GILIGENTING">GILIGENTING</option>
                            <option value="GULUK GULUK">GULUK GULUK</option>
                            <option value="KALIANGET">KALIANGET</option>
                            <option value="KANGAYAN">KANGAYAN</option>
                            <option value="KOTA SUMENEP">KOTA SUMENEP</option>
                            <option value="LENTENG">LENTENG</option>
                            <option value="MANDING">MANDING</option>
                            <option value="MASALEMBU">MASALEMBU</option>
                            <option value="NONGGUNONG">NONGGUNONG</option>
                            <option value="PASONGSONGAN">PASONGSONGAN</option>
                            <option value="PRAGAAN">PRAGAAN</option>
                            <option value="RAAS">RAAS</option>
                            <option value="RUBARU">RUBARU</option>
                            <option value="SAPEKEN">SAPEKEN</option>
                            <option value="SARONGGI">SARONGGI</option>
                            <option value="TALANGO">TALANGO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Desa Dampingan</label>
                        <input type="text" name="desadampingan" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah KPM</label>
                        <input type="number" name="jmlkpm" class="form-control" value="0" min="0">
                    </div>
                </div>

                <!-- Pekerjaan -->
                <h4 class="section-title"><i class="bi bi-briefcase"></i> Pekerjaan & Jabatan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control">
                    </div>
                </div>

                <!-- Kontak -->
                <h4 class="section-title"><i class="bi bi-telephone"></i> Kontak</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="nohp" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Zimbra</label>
                        <input type="text" name="userzimbra" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Zimbra</label>
                        <input type="email" name="emailzimbra" class="form-control">
                    </div>
                </div>

                <!-- Pendidikan -->
                <h4 class="section-title"><i class="bi bi-mortarboard"></i> Pendidikan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Universitas</label>
                        <input type="text" name="universitas" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Ijazah</label>
                        <input type="text" name="noijazah" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lulus</label>
                        <input type="date" name="tgllulus" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="tahunlulus" class="form-control" min="1900" max="2099">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenjang</label>
                        <select name="jenjang" class="form-select">
                            <option value="">Pilih</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                </div>

                <!-- Data Keuangan -->
                <h4 class="section-title"><i class="bi bi-wallet2"></i> Data Keuangan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BTN</label>
                        <input type="text" name="norekbtn" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening JATIM</label>
                        <input type="text" name="norekjatim" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BNI</label>
                        <input type="text" name="norekbni" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BRI</label>
                        <input type="text" name="norekbri" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Rekening</label>
                        <input type="text" name="namarekening" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NPWP</label>
                        <input type="text" name="npwp" class="form-control">
                    </div>
                </div>

                <!-- BPJS -->
                <h4 class="section-title"><i class="bi bi-heart-pulse"></i> BPJS</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">BPJS Kesehatan</label>
                        <input type="text" name="bpjskes" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BPJS Ketenagakerjaan</label>
                        <input type="text" name="bpjstk" class="form-control">
                    </div>
                </div>

                <!-- Data Keluarga -->
                <h4 class="section-title"><i class="bi bi-people"></i> Data Keluarga</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Ibu Kandung</label>
                        <input type="text" name="ibukandung" class="form-control">
                    </div>
                </div>

                <!-- Foto -->
                <h4 class="section-title"><i class="bi bi-camera"></i> Foto</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Upload Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                    </div>
                </div>

                <hr class="my-4">
                
                <div class="text-end">
                    <button type="reset" class="btn btn-secondary me-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>