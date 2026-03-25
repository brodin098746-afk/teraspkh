<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION['generate_result'])){
    header("Location: generate_pdf_filter.php");
    exit;
}

$result = $_SESSION['generate_result'];
$success_files = $result['success'];
$errors = $result['errors'];
$total = $result['total'];
$output_dir = $result['dir'];
$waktu_generate = $result['tanggal'];

// Kelompokkan file berdasarkan jenis laporan
$grouped_files = [];
foreach($success_files as $file) {
    $grouped_files[$file['jenis']][] = $file;
}

// Hapus session setelah diambil
unset($_SESSION['generate_result']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Hasil Generate PDF</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f8f9fa;
        padding: 30px 0;
    }
    .result-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 25px 30px;
    }
    .card-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .stats-container {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    .stat-box {
        flex: 1;
        min-width: 200px;
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    .stat-box.success {
    background: linear-gradient(135deg, #d1f7e0 0%, #a8e6cf 100%);
    border-color: #28a745;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.4);
    animation: glowPulse 2s infinite alternate;
}

@keyframes glowPulse {
    from {
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    }
    to {
        box-shadow: 0 0 25px rgba(40, 167, 69, 0.6);
    }
}
    .stat-box.warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        border-color: #ffc107;
    }
    .stat-box.primary {
    background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
    border-color: #dc3545;
}
    .success-icon i {
    font-size: 50px;
    color: #28a745;
    opacity: 0;
    transform: scale(0.5);
    animation: popCheck 0.6s ease-out forwards;
    animation-fill-mode: forwards;
}

@keyframes popCheck {
    0% {
        opacity: 0;
        transform: scale(0.5);
    }
    70% {
        opacity: 1;
        transform: scale(1.2);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
    .stat-number {
        font-size: 42px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }
    .jenis-section {
        background: white;
        border-radius: 15px;
        border: 2px solid #e9ecef;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .jenis-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px 20px;
        border-bottom: 2px solid #dee2e6;
        cursor: pointer;
        transition: all 0.3s;
    }
    .jenis-header:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }
    .jenis-header h5 {
        margin: 0;
        font-weight: 600;
        color: #495057;
    }
    .jenis-badge {
        background: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .file-item {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        transition: all 0.3s;
    }
    .file-item:last-child {
        border-bottom: none;
    }
    .file-item:hover {
        background: #e9ecef;
    }
    .btn-download {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-block;
    }
    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        color: white;
    }
    .btn-preview {
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-block;
    }
    .btn-preview:hover {
        background: #5a6268;
        color: white;
    }
    .btn-zip {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #212529;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
    }
    .error-list {
        background: #f8d7da;
        border: 2px solid #f5c6cb;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .file-info {
        font-size: 12px;
        color: #6c757d;
    }
    .file-info i {
        margin-right: 3px;
    }
    .time-badge {
        background: #17a2b8;
        color: white;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 11px;
    }
</style>
</head>
<body>

<div class="container">
    <div class="result-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="bi bi-check-circle-fill me-2"></i>Hasil Generate Laporan</h4>
                    <p class="mb-0 small opacity-75">Waktu Generate: <?= $waktu_generate ?></p>
                </div>
                <span class="time-badge">
                    <i class="bi bi-files me-1"></i><?= $total ?> File
                </span>
            </div>
        </div>
        
        <div class="card-body p-4">
            <!-- Statistics -->
            <div class="stats-container">
                <div class="stat-box success text-center">
    <div class="success-icon mb-2">
        <i class="bi bi-check-circle-fill"></i>
    </div>
    <div class="stat-number text-success"><?= count($success_files) ?></div>
    <div class="stat-label">Total File Berhasil</div>
    <small class="text-muted">Laporan tergenerate</small>
</div>
                <div class="stat-box warning">
                    <div class="stat-number text-warning"><?= count($grouped_files) ?></div>
                    <div class="stat-label">Jenis Laporan</div>
                    <small class="text-muted">Dengan Rencana Hasil Kerja berbeda</small>
                </div>
                <div class="stat-box primary">
                    
                    <div class="stat-number text-danger"><?= count($errors) ?></div>
                    <div class="stat-label">Error</div>
                    <small class="text-muted">Gagal generate</small>
                </div>
            </div>
            
            <!-- Error List dengan Tampilan Lebih Baik -->
<?php if(!empty($errors)): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
        <div>
            <h5 class="alert-heading mb-2">
                <i class="bi bi-info-circle me-2"></i>Informasi Generate
            </h5>
            <p class="mb-2">Berhasil generate <strong><?= count($success_files) ?></strong> dari <strong><?= count($success_files) ?></strong> jenis laporan yang dipilih.</p>
            
            <?php if(!empty($errors)): ?>
            <hr>
            <h6 class="mb-2">Detail Error:</h6>
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                    <li class="mb-1"><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <p class="mt-2 mb-0 small">
                <i class="bi bi-lightbulb me-1"></i>
                Tips: Pastikan ada data di database untuk jenis laporan yang dipilih.
            </p>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
            
            <!-- Success Files Grouped by Jenis -->
            <h5 class="mb-3"><i class="bi bi-files me-2"></i>Daftar File Berhasil:</h5>
            
            <?php foreach($grouped_files as $jenis => $files): ?>
            <div class="jenis-section">
                <div class="jenis-header" onclick="toggleJenis('<?= str_replace(' ', '_', $jenis) ?>')">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-caret-down-fill me-2" id="icon_<?= str_replace(' ', '_', $jenis) ?>"></i>
                            <?= htmlspecialchars($jenis) ?>
                        </h5>
                        <span class="jenis-badge">
                            <i class="bi bi-file-earmark-text me-1"></i><?= count($files) ?> File
                        </span>
                    </div>
                </div>
                
                <div class="jenis-content" id="content_<?= str_replace(' ', '_', $jenis) ?>">
                    <?php foreach($files as $index => $file): ?>
                    <div class="file-item">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-word-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <strong class="d-block"><?= htmlspecialchars($file['nama']) ?></strong>
                                        <div class="file-info">
                                            <i class="bi bi-person-fill"></i> <?= htmlspecialchars($file['kpm']) ?>
                                            | <i class="bi bi-calendar-fill"></i> <?= date('d/m/Y', strtotime($file['tanggal'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="file-info">
                                    <i class="bi bi-hdd-stack-fill"></i> 
                                    <?php 
                                    if(file_exists($file['path'])) {
                                        $size = filesize($file['path']);
                                        echo round($size / 1024, 2) . ' KB';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end mt-2 mt-md-0">
                                <a href="<?= $file['path'] ?>" class="btn-download me-2" download>
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                                <button class="btn-preview" onclick="window.open('<?= $file['path'] ?>')">
                                    <i class="bi bi-eye me-1"></i>Preview
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <a href="generate_pdf_filter.php" class="btn-back">
                        <i class="bi bi-arrow-left me-2"></i>Generate Lagi
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="home.php" class="btn-back" style="background: #17a2b8;">
                        <i class="bi bi-house-fill me-2"></i>Ke Halaman Utama
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn-zip" onclick="window.location.href='download_all.php?dir=<?= basename($output_dir) ?>'">
                        <i class="bi bi-file-zip-fill me-2"></i>Download Semua (ZIP)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleJenis(id) {
    var content = document.getElementById('content_' + id);
    var icon = document.getElementById('icon_' + id);
    
    if(content.style.display === 'none') {
        content.style.display = 'block';
        icon.className = 'bi bi-caret-down-fill me-2';
    } else {
        content.style.display = 'none';
        icon.className = 'bi bi-caret-right-fill me-2';
    }
}

// Set all content visible by default
document.addEventListener('DOMContentLoaded', function() {
    var contents = document.querySelectorAll('[id^="content_"]');
    contents.forEach(function(content) {
        content.style.display = 'block';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    
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