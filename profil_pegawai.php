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

$query = "SELECT * FROM pegawai WHERE id = $id";
$result = $conn->query($query);

if(!$result || $result->num_rows == 0) {
    header("Location: master_pegawai.php");
    exit;
}

$pegawai = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profil Pegawai - <?= htmlspecialchars($pegawai['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 20px;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .profile-name {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .profile-title {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .badge-info {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 5px;
        }
        .card-body {
            padding: 30px;
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #4361ee;
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 3px solid #4361ee;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 15px;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #4361ee;
        }
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
        }
        .info-value small {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: normal;
        }
        .btn-back {
            display: inline-block;
            padding: 12px 25px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateX(-5px);
        }
        .btn-edit {
            background: #ffc107;
            color: black;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-edit:hover {
            background: #e0a800;
            color: black;
            transform: translateY(-2px);
        }
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                text-align: center;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="master_pegawai.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Master Data</a>
    
    <div class="card">
        <div class="card-header">
            <?php if($pegawai['foto'] && file_exists("uploads/".$pegawai['foto'])): ?>
            <img src="uploads/<?= $pegawai['foto'] ?>" class="profile-img">
            <?php else: ?>
            <div class="profile-img bg-secondary d-flex align-items-center justify-content-center" style="font-size: 3rem;">
                📷
            </div>
            <?php endif; ?>
            <div>
                <h1 class="profile-name"><?= htmlspecialchars($pegawai['nama']) ?></h1>
                <div class="profile-title">
                    <i class="bi bi-briefcase"></i> <?= htmlspecialchars($pegawai['jabatan']) ?> - <?= htmlspecialchars($pegawai['pekerjaan']) ?>
                </div>
                <div class="mt-3">
                    <span class="badge-info"><i class="bi bi-person-vcard"></i> NIP: <?= htmlspecialchars($pegawai['nip']) ?: '-' ?></span>
                    <span class="badge-info"><i class="bi bi-qr-code"></i> NIK: <?= htmlspecialchars($pegawai['nik']) ?></span>
                    <span class="badge-info"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($pegawai['kecamatantugas']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Data Pribadi -->
            <h3 class="section-title"><i class="bi bi-person-badge"></i> Data Pribadi</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">No ID Pegawai</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['no_idpegawai']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIP</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['nip']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['nik']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No KK</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['nokk']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-value"><?= $pegawai['jeniskelamin'] ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tempat, Tgl Lahir</div>
                    <div class="info-value">
                        <?= htmlspecialchars($pegawai['tmplahir']) ?>, 
                        <?= $pegawai['tgllahir'] ? date('d/m/Y', strtotime($pegawai['tgllahir'])) : '-' ?>
                        <small>(<?= $pegawai['usia'] ?> th)</small>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Agama</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['agama']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Nikah</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['statusnikah']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jumlah Anak</div>
                    <div class="info-value"><?= $pegawai['jmlanak'] ?: 0 ?></div>
                </div>
            </div>

            <!-- Kontak -->
            <h3 class="section-title"><i class="bi bi-telephone"></i> Kontak</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">No HP</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['nohp']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['email']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Zimbra</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['emailzimbra']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">User Zimbra</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['userzimbra']) ?: '-' ?></div>
                </div>
            </div>

            <!-- Alamat -->
            <h3 class="section-title"><i class="bi bi-house"></i> Alamat</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Alamat</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($pegawai['alamat'])) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kabupaten</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['kabupaten']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kecamatan</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['kecamatan']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Desa</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['desa']) ?: '-' ?></div>
                </div>
            </div>

            <!-- Wilayah Tugas -->
            <h3 class="section-title"><i class="bi bi-geo-alt"></i> Wilayah Tugas</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">TMT Tugas</div>
                    <div class="info-value"><?= $pegawai['tmttugas'] ? date('d/m/Y', strtotime($pegawai['tmttugas'])) : '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kecamatan Tugas</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['kecamatantugas']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Desa Dampingan</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['desadampingan']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jumlah KPM</div>
                    <div class="info-value"><?= $pegawai['jmlkpm'] ?: 0 ?> Keluarga</div>
                </div>
            </div>

            <!-- Pendidikan -->
            <h3 class="section-title"><i class="bi bi-mortarboard"></i> Pendidikan</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Universitas</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['universitas']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jurusan</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['jurusan']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No Ijazah</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['noijazah']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Lulus</div>
                    <div class="info-value"><?= $pegawai['tgllulus'] ? date('d/m/Y', strtotime($pegawai['tgllulus'])) : '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tahun Lulus</div>
                    <div class="info-value"><?= $pegawai['tahunlulus'] ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenjang</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['jenjang']) ?: '-' ?></div>
                </div>
            </div>

            <!-- Data Keuangan -->
            <h3 class="section-title"><i class="bi bi-wallet2"></i> Data Keuangan</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">No Rekening BTN</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['norekbtn']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No Rekening BNI</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['norekbni']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No Rekening BRI</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['norekbri']?? '') ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Rekening</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['namarekening']?? '') ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">NPWP</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['npwp']) ?: '-' ?></div>
                </div>
            </div>

            <!-- BPJS -->
            <h3 class="section-title"><i class="bi bi-heart-pulse"></i> BPJS</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">BPJS Kesehatan</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['bpjskes']) ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">BPJS Ketenagakerjaan</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['bpjstk']) ?: '-' ?></div>
                </div>
            </div>

            <!-- Data Keluarga -->
            <h3 class="section-title"><i class="bi bi-people"></i> Data Keluarga</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Ibu Kandung</div>
                    <div class="info-value"><?= htmlspecialchars($pegawai['ibukandung']) ?: '-' ?></div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="edit_pegawai.php?id=<?= $pegawai['id'] ?>" class="btn-edit"><i class="bi bi-pencil"></i> Edit Data</a>
                <a href="hapus_pegawai.php?id=<?= $pegawai['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars($pegawai['nama']) ?>?')"><i class="bi bi-trash"></i> Hapus Data</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>