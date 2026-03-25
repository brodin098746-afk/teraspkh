<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Convert Word ke PDF</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root {
    --primary: #4361ee;
    --secondary: #3f37c9;
    --success: #4cc9f0;
    --danger: #f72585;
    --warning: #f8961e;
    --dark: #1e1b4b;
    --light: #f8f9fa;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}

/* Card Styles */
.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 25px 30px;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

.card-header h4 {
    font-weight: 700;
    font-size: 1.8rem;
    margin: 0;
    position: relative;
    z-index: 1;
}

/* Badge Styles */
.badge-custom {
    padding: 8px 16px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.badge-admin {
    background: linear-gradient(135deg, var(--danger), #b5179e);
    color: white;
    box-shadow: 0 4px 10px rgba(247, 37, 133, 0.3);
}

.badge-user {
    background: linear-gradient(135deg, var(--success), #4895ef);
    color: white;
    box-shadow: 0 4px 10px rgba(76, 201, 240, 0.3);
}

/* Converter Box */
.converter-box {
    background: white;
    border-radius: 20px;
    padding: 40px;
    margin: 30px 0;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 2px solid rgba(67, 97, 238, 0.1);
    transition: all 0.3s ease;
}

.converter-box:hover {
    border-color: var(--primary);
    box-shadow: 0 15px 40px rgba(67, 97, 238, 0.1);
}

/* Upload Area */
.upload-area {
    border: 3px dashed #e9ecef;
    border-radius: 20px;
    padding: 60px 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
    margin-bottom: 30px;
}

.upload-area:hover {
    border-color: var(--primary);
    background: linear-gradient(135deg, #f0f4ff, #e6ecfe);
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(67, 97, 238, 0.15);
}

.upload-area i {
    font-size: 5rem;
    color: var(--primary);
    margin-bottom: 20px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.upload-area h3 {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 10px;
}

.upload-area p {
    color: #6c757d;
    margin-bottom: 20px;
}

.upload-area .btn-browse {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    pointer-events: auto;
    position: relative;
    z-index: 10;
}

.upload-area .btn-browse:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
}

/* File Info */
.file-info {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 20px;
    margin: 20px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 2px solid #e9ecef;
    animation: slideIn 0.3s ease-out;
}

.file-details {
    display: flex;
    align-items: center;
    gap: 15px;
}

.file-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.8rem;
}

.file-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 5px;
}

.file-size {
    font-size: 0.85rem;
    color: #6c757d;
}

.btn-remove {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #fee2e2;
    color: var(--danger);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-remove:hover {
    background: linear-gradient(135deg, #ef476f, #f72585);
    color: white;
    transform: rotate(90deg);
}

/* Convert Button */
.btn-convert {
    background: linear-gradient(135deg, #06d6a0, #118ab2);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    width: 100%;
    margin: 20px 0;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.btn-convert::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
    z-index: 0;
}

.btn-convert:hover::before {
    width: 300px;
    height: 300px;
}

.btn-convert:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(4, 169, 109, 0.3);
}

.btn-convert i, .btn-convert span {
    position: relative;
    z-index: 1;
}

.btn-convert:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Progress Bar */
.progress-container {
    margin: 30px 0;
    display: none;
}

.progress {
    height: 12px;
    border-radius: 50px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(135deg, #06d6a0, #118ab2);
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-text {
    text-align: center;
    margin-top: 10px;
    font-weight: 600;
    color: var(--dark);
}

/* Result Card */
.result-card {
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    border-radius: 20px;
    padding: 30px;
    margin: 30px 0;
    text-align: center;
    animation: slideIn 0.5s ease-out;
    border: 2px solid var(--primary);
}

.result-card i {
    font-size: 4rem;
    color: #06d6a0;
    margin-bottom: 20px;
}

.result-card h4 {
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 10px;
}

.result-card p {
    color: #4b5563;
    margin-bottom: 20px;
}

.btn-download {
    background: linear-gradient(135deg, #06d6a0, #118ab2);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    margin: 0 5px;
    cursor: pointer;
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(4, 169, 109, 0.3);
    color: white;
}

.btn-new {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    margin: 0 5px;
    cursor: pointer;
}

.btn-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
    color: white;
}

/* Alert */
.alert-custom {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: none;
    border-radius: 16px;
    padding: 20px;
    color: #991b1b;
    font-weight: 500;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.alert-custom i {
    font-size: 1.5rem;
}

/* Navigation */
.nav-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.btn-nav {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    background: white;
    color: var(--dark);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    cursor: pointer;
}

.btn-nav:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
}

.btn-nav i {
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .upload-area {
        padding: 40px 20px;
    }
    
    .upload-area i {
        font-size: 4rem;
    }
    
    .file-info {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .file-details {
        flex-direction: column;
    }
}
</style>
</head>
<body>

<div class="container-fluid">
<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">
        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Convert Word ke PDF
        <?php if($role == 'admin'): ?>
            <span class="badge-custom badge-admin ms-3">
                <i class="bi bi-shield-fill-check"></i>Administrator
            </span>
        <?php endif; ?>
    </h4>
    <div class="d-flex align-items-center gap-3">
        <div class="text-white">
            <i class="bi bi-person-circle fs-5 me-2"></i>
            <span class="fw-semibold"><?= htmlspecialchars($username) ?></span>
        </div>
        <a href="logout.php" class="btn btn-light btn-sm rounded-pill px-4">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>

<div class="card-body">

<!-- Navigation Buttons -->
<div class="nav-buttons">
    <a href="index.php" class="btn-nav">
        <i class="bi bi-table"></i>
        <span>Data Kegiatan</span>
    </a>
    <a href="tambah.php" class="btn-nav">
        <i class="bi bi-plus-circle"></i>
        <span>Tambah Data</span>
    </a>
    <a href="home.php" class="btn-nav">
        <i class="bi bi-house"></i>
        <span>Home</span>
    </a>
</div>

<!-- Converter Box -->
<div class="converter-box">

    <!-- Alert Info -->
    <div class="alert alert-primary d-flex align-items-center gap-3 mb-4" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
        <i class="bi bi-info-circle-fill fs-3 text-primary"></i>
        <div>
            <strong>Informasi:</strong> Upload file Word (.doc atau .docx) untuk dikonversi ke PDF. Maksimal ukuran file 10MB.
        </div>
    </div>

    <!-- Upload Area -->
    <form id="uploadForm" enctype="multipart/form-data" method="POST" action="process_convert.php">
        <div class="upload-area" id="uploadArea">
            <i class="bi bi-cloud-upload-fill"></i>
            <h3>Pilih File Word</h3>
            <p>Drag & drop file Anda disini atau klik tombol browse</p>
            <button type="button" class="btn-browse" id="browseBtn">
                <i class="bi bi-folder2-open"></i>
                Browse Files
            </button>
            <input type="file" id="fileInput" name="file_word" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display: none;">
        </div>

        <!-- File Info (Hidden by default) -->
        <div class="file-info" id="fileInfo" style="display: none;">
            <div class="file-details">
                <div class="file-icon">
                    <i class="bi bi-file-earmark-word-fill"></i>
                </div>
                <div>
                    <div class="file-name" id="fileName"></div>
                    <div class="file-size" id="fileSize"></div>
                </div>
            </div>
            <button type="button" class="btn-remove" id="removeFile">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Convert Button -->
        <button type="submit" class="btn-convert" id="convertBtn" disabled>
            <i class="bi bi-arrow-repeat"></i>
            <span>Konversi ke PDF</span>
        </button>
    </form>

    <!-- Progress Bar -->
    <div class="progress-container" id="progressContainer">
        <div class="progress">
            <div class="progress-bar" id="progressBar" style="width: 0%"></div>
        </div>
        <div class="progress-text" id="progressText">0%</div>
    </div>

    <!-- Result Container (Hidden by default) -->
    <div id="resultContainer" style="display: none;"></div>

</div>

<!-- Informasi Tambahan -->
<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #fff5f5, #fee2e2); border-radius: 20px;">
            <div class="card-body text-center p-4">
                <i class="bi bi-file-earmark-word-fill fs-1" style="color: #2b5797;"></i>
                <h6 class="fw-bold mt-3">Format Didukung</h6>
                <p class="small text-muted mb-0">.doc dan .docx</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 20px;">
            <div class="card-body text-center p-4">
                <i class="bi bi-shield-check fs-1" style="color: #2e7d32;"></i>
                <h6 class="fw-bold mt-3">Aman & Terpercaya</h6>
                <p class="small text-muted mb-0">File Anda aman dan dihapus setelah konversi</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); border-radius: 20px;">
            <div class="card-body text-center p-4">
                <i class="bi bi-lightning-charge-fill fs-1" style="color: #1976d2;"></i>
                <h6 class="fw-bold mt-3">Cepat & Mudah</h6>
                <p class="small text-muted mb-0">Konversi dalam hitungan detik</p>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const browseBtn = document.getElementById('browseBtn');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const convertBtn = document.getElementById('convertBtn');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const resultContainer = document.getElementById('resultContainer');
    const uploadForm = document.getElementById('uploadForm');

    // Browse button click
    browseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });

    // Upload area click
    uploadArea.addEventListener('click', function(e) {
        // Only trigger if the click is directly on upload area, not on its children
        if(e.target === uploadArea || e.target.classList.contains('bi-cloud-upload-fill') || e.target.tagName === 'H3' || e.target.tagName === 'P') {
            fileInput.click();
        }
    });

    // Drag and drop events
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--primary)';
        this.style.background = 'linear-gradient(135deg, #f0f4ff, #e6ecfe)';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#e9ecef';
        this.style.background = '#f8f9fa';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#e9ecef';
        this.style.background = '#f8f9fa';
        
        const files = e.dataTransfer.files;
        if(files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function() {
        if(this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        // Validasi tipe file
        const validExtensions = ['doc', 'docx'];
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if(!validExtensions.includes(fileExt)) {
            showError('Hanya file Word (.doc, .docx) yang diperbolehkan!');
            fileInput.value = '';
            return;
        }

        // Validasi ukuran (max 10MB)
        if(file.size > 10 * 1024 * 1024) {
            showError('Ukuran file maksimal 10MB!');
            fileInput.value = '';
            return;
        }

        // Tampilkan info file
        fileName.textContent = file.name;
        const sizeInKB = (file.size / 1024).toFixed(2);
        fileSize.textContent = sizeInKB + ' KB';
        fileInfo.style.display = 'flex';
        convertBtn.disabled = false;
        
        // Sembunyikan result container jika ada
        resultContainer.style.display = 'none';
    }

    // Remove file
    removeFile.addEventListener('click', function() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        convertBtn.disabled = true;
        progressContainer.style.display = 'none';
        resultContainer.style.display = 'none';
    });

    // Show error
    function showError(message) {
        resultContainer.innerHTML = `
            <div class="alert-custom">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>${message}</div>
            </div>
        `;
        resultContainer.style.display = 'block';
        
        fileInput.value = '';
        fileInfo.style.display = 'none';
        convertBtn.disabled = true;
    }

    // Show success
    function showSuccess(data) {
        resultContainer.innerHTML = `
            <div class="result-card">
                <i class="bi bi-check-circle-fill"></i>
                <h4>Konversi Berhasil!</h4>
                <p>File PDF siap diunduh</p>
                <div>
                    <a href="${data.file_url}" class="btn-download" download>
                        <i class="bi bi-download"></i>
                        Download PDF
                    </a>
                    <button type="button" class="btn-new" onclick="location.reload()">
                        <i class="bi bi-arrow-repeat"></i>
                        Konversi Lagi
                    </button>
                </div>
            </div>
        `;
        resultContainer.style.display = 'block';
        
        fileInfo.style.display = 'none';
        convertBtn.disabled = true;
        progressContainer.style.display = 'none';
    }

    // Form submit
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        
        // Tampilkan progress bar
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';

        // Simulasi progress
        let progress = 0;
        const interval = setInterval(function() {
            progress += 5;
            if(progress <= 90) {
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '%';
            }
        }, 200);

        // Kirim data dengan fetch
        fetch('process_convert.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            
            setTimeout(function() {
                if(data.status === 'success') {
                    showSuccess(data);
                } else {
                    showError(data.message || 'Terjadi kesalahan saat konversi');
                }
                progressContainer.style.display = 'none';
            }, 500);
        })
        .catch(error => {
            clearInterval(interval);
            progressContainer.style.display = 'none';
            showError('Terjadi kesalahan saat mengupload file: ' + error.message);
        });
    });
});
</script>

</body>
</html>