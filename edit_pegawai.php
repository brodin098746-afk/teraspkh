<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pegawai = $conn->query("SELECT * FROM pegawai WHERE id = $id")->fetch_assoc();

if(!$pegawai) {
    header("Location: master_pegawai.php");
    exit;
}

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
    
    $foto = $pegawai['foto'];
    
    // Upload foto baru jika ada
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $foto = time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $foto;
        
        if($_FILES["foto"]["size"] <= 2000000) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array(strtolower($file_extension), $allowed_types)) {
                if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    // Hapus foto lama
                    if($pegawai['foto'] && file_exists($target_dir . $pegawai['foto'])) {
                        unlink($target_dir . $pegawai['foto']);
                    }
                } else {
                    $error = "Gagal upload file";
                    $foto = $pegawai['foto'];
                }
            } else {
                $error = "Format file tidak didukung";
                $foto = $pegawai['foto'];
            }
        } else {
            $error = "Ukuran file terlalu besar";
            $foto = $pegawai['foto'];
        }
    }
    
    if(empty($error)) {
        $query = "UPDATE pegawai SET 
            no_idpegawai = '$no_idpegawai',
            nip = '$nip',
            nik = '$nik',
            nokk = '$nokk',
            nama = '$nama',
            tmttugas = " . ($tmttugas ? "'$tmttugas'" : "NULL") . ",
            kecamatantugas = '$kecamatantugas',
            desadampingan = '$desadampingan',
            jmlkpm = $jmlkpm,
            alamat = '$alamat',
            kabupaten = '$kabupaten',
            kecamatan = '$kecamatan',
            desa = '$desa',
            jeniskelamin = '$jeniskelamin',
            tmplahir = '$tmplahir',
            tgllahir = " . ($tgllahir ? "'$tgllahir'" : "NULL") . ",
            usia = $usia,
            agama = '$agama',
            statusnikah = '$statusnikah',
            jmlanak = $jmlanak,
            pekerjaan = '$pekerjaan',
            jabatan = '$jabatan',
            nohp = '$nohp',
            userzimbra = '$userzimbra',
            emailzimbra = '$emailzimbra',
            email = '$email',
            universitas = '$universitas',
            jurusan = '$jurusan',
            noijazah = '$noijazah',
            tgllulus = " . ($tgllulus ? "'$tgllulus'" : "NULL") . ",
            tahunlulus = $tahunlulus,
            jenjang = '$jenjang',
            norekbtn = '$norekbtn',
            norekjatim = '$norekjatim',
            norekbni = '$norekbni',
            norekbri = '$norekbri',
            namarekening = '$namarekening',
            ibukandung = '$ibukandung',
            npwp = '$npwp',
            bpjskes = '$bpjskes',
            bpjstk = '$bpjstk',
            foto = '$foto'
            WHERE id = $id";
        
        if($conn->query($query)) {
            $success = "Data pegawai berhasil diupdate";
            header("refresh:2;url=master_pegawai.php");
        } else {
            $error = "Gagal mengupdate data: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Pegawai - <?= htmlspecialchars($pegawai['nama']) ?></title>
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
            background: linear-gradient(135deg, #ffc107, #fd7e14);
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
            color: #fd7e14;
            margin: 20px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #fd7e14;
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
            border-color: #fd7e14;
            outline: none;
            box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(253, 126, 20, 0.3);
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
        .current-photo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #fd7e14;
            margin-bottom: 10px;
        }
        .text-danger {
            color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="form-header d-flex justify-content-between align-items-center">
            <h3><i class="bi bi-pencil-square"></i> Edit Data Pegawai</h3>
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
                        <input type="text" name="no_idpegawai" class="form-control" value="<?= htmlspecialchars($pegawai['no_idpegawai'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($pegawai['nip'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($pegawai['nik'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No KK</label>
                        <input type="text" name="nokk" class="form-control" value="<?= htmlspecialchars($pegawai['nokk'] ?? '') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($pegawai['nama'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Tempat & Tanggal Lahir -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tmplahir" class="form-control" value="<?= htmlspecialchars($pegawai['tmplahir'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgllahir" class="form-control" value="<?= $pegawai['tgllahir'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jeniskelamin" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Laki-laki" <?= ($pegawai['jeniskelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($pegawai['jeniskelamin'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Agama & Status -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Islam" <?= ($pegawai['agama'] ?? '') == 'Islam' ? 'selected' : '' ?>>Islam</option>
                            <option value="Kristen" <?= ($pegawai['agama'] ?? '') == 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                            <option value="Katolik" <?= ($pegawai['agama'] ?? '') == 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                            <option value="Hindu" <?= ($pegawai['agama'] ?? '') == 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                            <option value="Budha" <?= ($pegawai['agama'] ?? '') == 'Budha' ? 'selected' : '' ?>>Budha</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Nikah</label>
                        <select name="statusnikah" class="form-select">
                            <option value="">Pilih</option>
                            <option value="Belum Menikah" <?= ($pegawai['statusnikah'] ?? '') == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                            <option value="Menikah" <?= ($pegawai['statusnikah'] ?? '') == 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                            <option value="Cerai Hidup" <?= ($pegawai['statusnikah'] ?? '') == 'Cerai Hidup' ? 'selected' : '' ?>>Cerai Hidup</option>
                            <option value="Cerai Mati" <?= ($pegawai['statusnikah'] ?? '') == 'Cerai Mati' ? 'selected' : '' ?>>Cerai Mati</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Anak</label>
                        <input type="number" name="jmlanak" class="form-control" value="<?= $pegawai['jmlanak'] ?? 0 ?>" min="0">
                    </div>
                </div>

                <!-- Alamat -->
                <h4 class="section-title"><i class="bi bi-house"></i> Alamat</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($pegawai['alamat'] ?? '') ?></textarea>
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
                        <input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($pegawai['desa'] ?? '') ?>">
                    </div>
                </div>

                <!-- Wilayah Tugas -->
                <h4 class="section-title"><i class="bi bi-geo-alt"></i> Wilayah Tugas</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">TMT Tugas</label>
                        <input type="date" name="tmttugas" class="form-control" value="<?= $pegawai['tmttugas'] ?? '' ?>">
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
                        <input type="text" name="desadampingan" class="form-control" value="<?= htmlspecialchars($pegawai['desadampingan'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah KPM</label>
                        <input type="number" name="jmlkpm" class="form-control" value="<?= $pegawai['jmlkpm'] ?? 0 ?>" min="0">
                    </div>
                </div>

                <!-- Pekerjaan -->
                <h4 class="section-title"><i class="bi bi-briefcase"></i> Pekerjaan & Jabatan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control" value="<?= htmlspecialchars($pegawai['pekerjaan'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($pegawai['jabatan'] ?? '') ?>">
                    </div>
                </div>

                <!-- Kontak -->
                <h4 class="section-title"><i class="bi bi-telephone"></i> Kontak</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="nohp" class="form-control" value="<?= htmlspecialchars($pegawai['nohp'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($pegawai['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Zimbra</label>
                        <input type="text" name="userzimbra" class="form-control" value="<?= htmlspecialchars($pegawai['userzimbra'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Zimbra</label>
                        <input type="email" name="emailzimbra" class="form-control" value="<?= htmlspecialchars($pegawai['emailzimbra'] ?? '') ?>">
                    </div>
                </div>

                <!-- Pendidikan -->
                <h4 class="section-title"><i class="bi bi-mortarboard"></i> Pendidikan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Universitas</label>
                        <input type="text" name="universitas" class="form-control" value="<?= htmlspecialchars($pegawai['universitas'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($pegawai['jurusan'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Ijazah</label>
                        <input type="text" name="noijazah" class="form-control" value="<?= htmlspecialchars($pegawai['noijazah'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lulus</label>
                        <input type="date" name="tgllulus" class="form-control" value="<?= $pegawai['tgllulus'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="tahunlulus" class="form-control" value="<?= $pegawai['tahunlulus'] ?? '' ?>" min="1900" max="2099">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenjang</label>
                        <select name="jenjang" class="form-select">
                            <option value="">Pilih</option>
                            <option value="SD" <?= ($pegawai['jenjang'] ?? '') == 'SD' ? 'selected' : '' ?>>SD</option>
                            <option value="SMP" <?= ($pegawai['jenjang'] ?? '') == 'SMP' ? 'selected' : '' ?>>SMP</option>
                            <option value="SMA" <?= ($pegawai['jenjang'] ?? '') == 'SMA' ? 'selected' : '' ?>>SMA</option>
                            <option value="D3" <?= ($pegawai['jenjang'] ?? '') == 'D3' ? 'selected' : '' ?>>D3</option>
                            <option value="S1" <?= ($pegawai['jenjang'] ?? '') == 'S1' ? 'selected' : '' ?>>S1</option>
                            <option value="S2" <?= ($pegawai['jenjang'] ?? '') == 'S2' ? 'selected' : '' ?>>S2</option>
                            <option value="S3" <?= ($pegawai['jenjang'] ?? '') == 'S3' ? 'selected' : '' ?>>S3</option>
                        </select>
                    </div>
                </div>

                <!-- Data Keuangan -->
                <h4 class="section-title"><i class="bi bi-wallet2"></i> Data Keuangan</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BTN</label>
                        <input type="text" name="norekbtn" class="form-control" value="<?= htmlspecialchars($pegawai['norekbtn'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening JATIM</label>
                        <input type="text" name="norekjatim" class="form-control" value="<?= htmlspecialchars($pegawai['norekjatim'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BNI</label>
                        <input type="text" name="norekbni" class="form-control" value="<?= htmlspecialchars($pegawai['norekbni'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No Rekening BRI</label>
                        <input type="text" name="norekbri" class="form-control" value="<?= htmlspecialchars($pegawai['norekbri'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Rekening</label>
                        <input type="text" name="namarekening" class="form-control" value="<?= htmlspecialchars($pegawai['namarekening'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NPWP</label>
                        <input type="text" name="npwp" class="form-control" value="<?= htmlspecialchars($pegawai['npwp'] ?? '') ?>">
                    </div>
                </div>

                <!-- BPJS -->
                <h4 class="section-title"><i class="bi bi-heart-pulse"></i> BPJS</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">BPJS Kesehatan</label>
                        <input type="text" name="bpjskes" class="form-control" value="<?= htmlspecialchars($pegawai['bpjskes'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BPJS Ketenagakerjaan</label>
                        <input type="text" name="bpjstk" class="form-control" value="<?= htmlspecialchars($pegawai['bpjstk'] ?? '') ?>">
                    </div>
                </div>

                <!-- Data Keluarga -->
                <h4 class="section-title"><i class="bi bi-people"></i> Data Keluarga</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Ibu Kandung</label>
                        <input type="text" name="ibukandung" class="form-control" value="<?= htmlspecialchars($pegawai['ibukandung'] ?? '') ?>">
                    </div>
                </div>

                <!-- Foto -->
                <h4 class="section-title"><i class="bi bi-camera"></i> Foto</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <?php if($pegawai['foto'] && file_exists("uploads/".$pegawai['foto'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Foto Saat Ini</label><br>
                            <img src="uploads/<?= $pegawai['foto'] ?>" class="current-photo">
                        </div>
                        <?php endif; ?>
                        <label class="form-label">Upload Foto Baru (Kosongkan jika tidak diganti)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                    </div>
                </div>

                <hr class="my-4">
                
                <div class="text-end">
                    <button type="reset" class="btn btn-secondary me-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>