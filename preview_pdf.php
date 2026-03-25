<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";

if(!isset($_SESSION['filter_data'])) {
    header("Location: generate_pdf_filter.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];
$filter = $_SESSION['filter_data'];
$where = $filter['query'];

// Hitung jumlah data per jenis laporan
$query_count = $conn->query("SELECT jenis_laporan, COUNT(*) as total FROM kegiatan $where GROUP BY jenis_laporan");
$data_count = [];
while($row = $query_count->fetch_assoc()) {
    $data_count[$row['jenis_laporan']] = $row['total'];
}

$total_data = array_sum($data_count);

// Di bagian atas setelah hitung data
if($total_data == 0) {
    $_SESSION['generate_error'] = "Tidak ada data dengan filter yang dipilih!";
    header("Location: generate_pdf_filter.php");
    exit;
}

// Tampilkan peringatan jika ada jenis laporan tanpa data
$empty_jenis = [];
// Cari jenis laporan yang tidak ada datanya (opsional)

// Ambil informasi user yang difilter (untuk admin)
$filter_user_name = 'Semua User';
if($role == 'admin' && isset($filter['filter_user']) && $filter['filter_user'] > 0) {
    $user_filter = $conn->query("SELECT username FROM users WHERE id=" . $filter['filter_user'])->fetch_assoc();
    $filter_user_name = $user_filter['username'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Preview Data Laporan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f8f9fa;
        padding: 30px 0;
    }
    .preview-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 20px 25px;
    }
    .summary-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .jenis-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 20px;
        transition: all 0.3s;
        cursor: pointer;
        height: 100%;
    }
    .jenis-card:hover {
        border-color: #28a745;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(40,167,69,0.2);
    }
    .jenis-card.selected {
        border-color: #28a745;
        background: #f0fff4;
    }
    .jenis-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .jenis-icon i {
        font-size: 30px;
        color: white;
    }
    .btn-generate {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
    }
    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(40,167,69,0.4);
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
    }
    .badge-count {
        background: rgba(40,167,69,0.2);
        color: #28a745;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    .role-badge {
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
    }
    .role-admin {
        background: #dc3545;
        color: white;
    }
    .role-user {
        background: #28a745;
        color: white;
    }
</style>
</head>
<body>

<div class="container">
    <div class="preview-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="bi bi-eye-fill me-2"></i>Preview Data Laporan</h4>
            </div>
            <div>
                <?php if($role == 'admin'): ?>
                    <span class="role-badge role-admin">
                        <i class="bi bi-shield-lock me-1"></i>Administrator
                    </span>
                <?php else: ?>
                    <span class="role-badge role-user">
                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($_SESSION['username']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body p-4">
            <!-- Summary -->
            <div class="summary-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-3">Ringkasan Filter:</h5>
                        
                        <?php if($role == 'admin'): ?>
                        <p class="mb-2">
                            <i class="bi bi-people-fill me-2"></i>
                            User: <strong><?= $filter_user_name ?></strong>
                        </p>
                        <?php endif; ?>
                        
                        <p class="mb-2">
                            <i class="bi bi-tag-fill me-2"></i>
                            Jenis Laporan: <strong><?= $filter['jenis_laporan'] ?: 'Semua' ?></strong>
                        </p>
                        
                        <p class="mb-0">
                            <i class="bi bi-calendar-fill me-2"></i>
                            Periode: <strong>
                                <?= $filter['tanggal_awal'] ? date('d/m/Y', strtotime($filter['tanggal_awal'])) : 'Semua' ?> 
                                s/d 
                                <?= $filter['tanggal_akhir'] ? date('d/m/Y', strtotime($filter['tanggal_akhir'])) : 'Semua' ?>
                            </strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="badge-count d-inline-block">
                            <i class="bi bi-database me-1"></i>
                            Total: <?= $total_data ?> Data
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pilih Jenis Laporan untuk Generate -->
            <h5 class="mb-3">Pilih Jenis Laporan yang Akan Digenerate:</h5>
            <p class="text-muted mb-4">Setiap jenis laporan akan menghasilkan file Word terpisah</p>
            
            <form method="POST" action="proses_generate_pdf.php" id="generateForm">
                <div class="row mb-4">
                    <?php 
                    $no = 1;
                    foreach($data_count as $jenis => $total): 
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="jenis-card" onclick="selectJenis('<?= $jenis ?>', this)">
                            <div class="jenis-icon">
                                <i class="bi bi-file-word-fill"></i>
                            </div>
                            <h6 class="mb-2"><?= htmlspecialchars($jenis) ?></h6>
                            <p class="mb-2 text-muted small">Jumlah Data: <?= $total ?></p>
                            <div class="form-check">
                                <input class="form-check-input jenis-checkbox" 
                                       type="checkbox" 
                                       name="jenis_laporan[]" 
                                       value="<?= htmlspecialchars($jenis) ?>" 
                                       id="jenis<?= $no ?>">
                                <label class="form-check-label small" for="jenis<?= $no ?>">
                                    Pilih untuk generate
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $no++;
                    endforeach; 
                    ?>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="generate_pdf_filter.php" class="btn-back w-100 text-center">
                            <i class="bi bi-arrow-left me-2"></i>Ubah Filter
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="proses_generate" class="btn-generate w-100">
                            <i class="bi bi-file-earmark-word-fill me-2"></i>
                            Generate Word Terpilih ( <span id="selectedCount">0</span> )
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Preview Data Tabel -->
            <div class="mt-5">
                <h5 class="mb-3">Preview Data (10 Data Terbaru):</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <?php if($role == 'admin'): ?>
                                <th>User</th>
                                <?php endif; ?>
                                <th>Petugas</th>
                                <th>Jenis Laporan</th>
                                <th>KPM</th>
                                <th>Desa</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $preview = $conn->query("SELECT k.*, u.username as pembuat 
                                                     FROM kegiatan k 
                                                     LEFT JOIN users u ON k.user_id = u.id 
                                                     $where 
                                                     ORDER BY k.id DESC LIMIT 10");
                            $no = 1;
                            while($d = $preview->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <?php if($role == 'admin'): ?>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($d['pembuat']) ?></span>
                                </td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($d['namapetugas']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($d['jenis_laporan']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($d['namakpm']) ?></td>
                                <td><?= htmlspecialchars($d['desa']) ?></td>
                                <td><?= date('d/m/Y', strtotime($d['tglkeg'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php if($total_data > 10): ?>
                        <p class="text-muted small">... dan <?= $total_data - 10 ?> data lainnya</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectJenis(jenis, element) {
    element.classList.toggle('selected');
    var checkbox = element.querySelector('.jenis-checkbox');
    checkbox.checked = !checkbox.checked;
    updateSelectedCount();
}

function updateSelectedCount() {
    var checkboxes = document.querySelectorAll('.jenis-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
}

document.getElementById('generateForm').onsubmit = function(e) {
    var checkboxes = document.querySelectorAll('.jenis-checkbox:checked');
    if(checkboxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu jenis laporan untuk digenerate!');
    }
}

updateSelectedCount();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    
    // Update last activity setiap 2 menit
setInterval(function() {
    fetch('update_activity.php?ajax=1', {
        method: 'GET',
        cache: 'no-cache'
    }).then(response => response.json())
    .then(data => {
        console.log('Activity updated');
    }).catch(error => {
        console.error('Error:', error);
    });
}, 120000); // 2 menit

// Set online status offline saat user menutup tab/browser
window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('update_activity.php?offline=1');
});
    </script>
</body>
</html>