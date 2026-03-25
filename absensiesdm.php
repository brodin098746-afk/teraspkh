
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KonSen</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            overflow: hidden;
            border: 1px solid #e0e0e0;
            width: 100%;
        }
        
        .header {
            background: #ffffff;
            color: #333333;
            padding: 16px 20px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header h1 {
            font-size: 1.6em;
            margin-bottom: 4px;
            font-weight: 400;
            color: #222222;
            letter-spacing: -0.02em;
        }
        
        .header p {
            font-size: 0.85em;
            color: #777777;
        }
        
        .content {
            padding: 16px;
            background: #ffffff;
        }
        
        /* Upload Section */
        .upload-section {
            background: #fafafa;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e0e0e0;
        }
        
        .upload-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .upload-header-icon {
            width: 36px;
            height: 36px;
            background: #eaeaea;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #555555;
            border: none;
        }
        
        .upload-header-title {
            flex: 1;
        }
        
        .upload-header-title h3 {
            font-size: 1.1em;
            color: #333333;
            margin-bottom: 4px;
            font-weight: 400;
        }
        
        .upload-header-title p {
            color: #777777;
            font-size: 0.8em;
        }
        
        .upload-header-title p span {
            background: #eaeaea;
            padding: 3px 10px;
            border-radius: 20px;
            color: #555555;
            font-size: 0.75em;
            margin-right: 8px;
            border: none;
        }
        
        .file-input-wrapper {
            position: relative;
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .file-input {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }
        
        .file-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 32px;
            background: #ffffff;
            color: #333333;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 400;
            font-size: 0.9em;
            transition: all 0.2s ease;
            border: 1px solid #cccccc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        
        .file-label:hover {
            background: #f5f5f5;
            border-color: #aaaaaa;
        }
        
        .file-label:active {
            background: #e8e8e8;
        }
        
        .supported-formats {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .format-badge {
            background: #f0f0f0;
            padding: 4px 12px;
            border-radius: 30px;
            color: #666666;
            font-size: 0.75em;
            border: none;
            font-weight: 400;
        }
        
        /* Progress indicator */
        .progress-elegant {
            background: #fafafa;
            border-radius: 8px;
            padding: 14px;
            margin-top: 16px;
            border: 1px solid #e0e0e0;
        }
        
        .progress-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .progress-icon {
            width: 32px;
            height: 32px;
            background: #eaeaea;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #555555;
            border: none;
        }
        
        .progress-title {
            flex: 1;
        }
        
        .progress-title h4 {
            color: #444444;
            margin-bottom: 2px;
            font-size: 0.85em;
            font-weight: 400;
        }
        
        .progress-title p {
            color: #777777;
            font-size: 0.7em;
        }
        
        .progress-bar-elegant {
            height: 4px;
            background: #e0e0e0;
            border-radius: 30px;
            overflow: hidden;
            margin: 12px 0;
        }
        
        .progress-fill-elegant {
            height: 100%;
            background: #666666;
            border-radius: 30px;
            transition: width 0.3s ease;
        }
        
        .progress-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #777777;
            font-size: 0.75em;
        }
        
        .progress-percent {
            font-weight: 500;
            color: #333333;
        }
        
        /* Info Bar - Abu & Putih */
        .info-bar {
            display: flex;
            gap: 24px;
            background: #fafafa;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 16px;
            border: 1px solid #e0e0e0;
            justify-content: center;
            font-size: 0.85em;
        }
        
        .info-item {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        
        .info-label {
            font-size: 0.8em;
            color: #777777;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .info-value {
            font-size: 0.95em;
            color: #333333;
            font-weight: 500;
            border-bottom: 2px dotted #aaaaaa;
            padding-bottom: 2px;
        }
        
        /* Info Panel - DISEMBUNYIKAN */
        .info-panel {
            display: none;
        }
        
        /* Warning Info */
        .warning-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #fef9e7;
            border: 1px solid #f0e5c9;
            border-radius: 8px;
            padding: 14px 20px;
            margin: 16px 0 8px 0;
            color: #8a6d3b;
            font-size: 0.9em;
            line-height: 1.5;
            text-align: center;
        }
        
        .warning-info .icon {
            font-size: 1.6em;
            flex-shrink: 0;
        }
        
        .warning-info .message {
            flex: 1;
        }
        
        .warning-info .message strong {
            font-weight: 600;
            color: #6d4c2e;
        }
        
        /* Buttons - Abu & Putih */
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 30px;
            font-size: 0.85em;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #ffffff;
            color: #333333;
            border: 1px solid #cccccc;
        }
        
        .btn-primary {
            background: #666666;
            color: #ffffff;
            border: 1px solid #555555;
        }
        
        .btn-danger {
            background: #888888;
            color: #ffffff;
            border: 1px solid #777777;
        }
        
        .btn:hover {
            background: #f5f5f5;
        }
        
        .btn-primary:hover {
            background: #555555;
        }
        
        .btn-danger:hover {
            background: #777777;
        }
        
        .btn:active {
            background: #e0e0e0;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Table - Clean */
        .table-container {
            background: #ffffff;
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid #e0e0e0;
            margin: 16px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8em;
        }
        
        th {
            background: #f5f5f5;
            color: #555555;
            padding: 10px 8px;
            font-weight: 500;
            text-align: left;
            border-bottom: 2px solid #dddddd;
            border-right: none;
            font-size: 0.75em;
            letter-spacing: 0.3px;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #eeeeee;
            border-right: none;
            color: #444444;
        }
        
        tr:hover {
            background: #fafafa;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        /* Action Bar - Bottom */
        .action-bar-bottom {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e0e0e0;
        }
        
        /* Modal - Abu & Putih */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }
        
        .modal-content {
            background-color: #ffffff;
            margin: 15% auto;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 380px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 1px solid #dddddd;
        }
        
        .modal-content h3 {
            margin-bottom: 12px;
            font-weight: 400;
            color: #333333;
            font-size: 1.2em;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 8px;
        }
        
        .modal-content p {
            color: #777777;
            margin-bottom: 20px;
            font-size: 0.9em;
            line-height: 1.5;
        }
        
        /* Toast - Abu & Putih */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 40px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
            display: none;
            border-left: 4px solid #666666;
            z-index: 1001;
            font-size: 0.85em;
            color: #333333;
            border: 1px solid #dddddd;
            font-weight: 400;
        }
        
        /* History Section */
        #historyContainer h3 {
            font-family: inherit;
            color: #333333;
            font-weight: 400;
            font-size: 1.1em;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
            margin-bottom: 0;
            background: #fafafa;
            padding: 14px;
        }
        
        /* Footer Style - Abu & Putih */
        .app-footer {
            text-align: center;
            margin-top: 16px;
            padding: 16px 10px;
            color: #777777;
            font-size: 0.8em;
            border-top: 1px solid #e0e0e0;
            background: #ffffff;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .app-footer span {
            font-weight: 400;
            letter-spacing: 0.3px;
            color: #555555;
        }
        
        .app-footer .footer-heart {
            color: #999999;
            margin: 0 4px;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            body {
                padding: 8px;
            }
            
            .header h1 {
                font-size: 1.4em;
            }
            
            .content {
                padding: 10px;
            }
            
            .upload-section {
                padding: 12px;
            }
            
            .info-bar {
                flex-direction: column;
                gap: 8px;
                align-items: center;
            }
            
            .info-item {
                width: 100%;
                justify-content: center;
            }
            
            .action-bar-bottom {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 250px;
            }
            
            .warning-info {
                flex-direction: column;
                gap: 8px;
                padding: 12px;
                font-size: 0.85em;
            }
            
            .app-footer {
                margin-top: 10px;
                padding: 12px 8px;
            }
        }

        /* Scrollbar custom - grayscale */
        .table-container::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 8px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #999999;
            border-radius: 8px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #777777;
        }

        /* Additional grayscale touches */
        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 12px 0;
        }

        input, select, textarea {
            border: 1px solid #dddddd;
            border-radius: 6px;
            padding: 8px;
            background: #ffffff;
            color: #333333;
            font-size: 0.85em;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #999999;
        }
    </style>
    
    <!-- Library untuk membaca Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Library untuk menyimpan file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
</head>
<body>
    <div class="container">
        
        
        <div class="content">
            <!-- Upload Section -->
            <div class="upload-section">
                              
                <div class="file-input-wrapper">
                    <input type="file" id="fileInput" class="file-input" accept=".xls,.xlsx,.xlsm,.csv">
                    <label for="fileInput" class="file-label" id="fileLabel">
                        Pilih File
                    </label>
                    
                    <div class="supported-formats">
                        <span class="format-badge">XLS</span>
                        <span class="format-badge">XLSX</span>
                        <span class="format-badge">XLSM</span>
                        <span class="format-badge">CSV</span>
                    </div>
                </div>
                
                <!-- File Info disembunyikan -->
                <div id="fileInfo" class="file-info" style="display: none;"></div>
                
                <!-- Auto Load Progress Indicator -->
                <div id="autoLoadProgress" class="progress-elegant" style="display: none;">
                    <div class="progress-header">
                       
                        <div class="progress-title">
                            <h4>Memproses File</h4>
                            <p id="autoLoadStatus">Memuat 0 dari 0 sheet...</p>
                        </div>
                    </div>
                    
                    <div class="progress-bar-elegant">
                        <div class="progress-fill-elegant" id="autoLoadProgressFill"></div>
                    </div>
                    
                    <div class="progress-stats">
                        <span>Progress</span>
                        <span class="progress-percent" id="progressPercent">0%</span>
                    </div>
                </div>
            </div>
            
            <!-- Info Bar - NIP dan Periode -->
            <div class="info-bar" id="infoBar" style="display: none;">
                <div class="info-item">
                    <span class="info-label">NIP</span>
                    <span class="info-value" id="nipDisplay">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Periode</span>
                    <span class="info-value" id="periodeDisplay">-</span>
                </div>
            </div>
            
            <!-- Info Panel - DISEMBUNYIKAN (Total Masuk, Total Pulang, Hari Kerja) -->
            <div class="info-panel" id="infoPanel" style="display: none;">
                <!-- Konten disembunyikan melalui CSS -->
            </div>
            
            <!-- Data Table -->
            <div class="table-container" id="tableContainer" style="display: none;">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Waktu</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>
            
            <!-- Warning Info - Tampil setelah data diproses -->
            <div class="warning-info" id="warningInfo" style="display: none;">
                <div class="message">
                    Pastikan jam Masuk/Pulang pada kolom waktu terisi semua, jika ada yang kosong kemungkinan Anda lupa untuk Check-in/Check-out. Silahkan input manual setelah berhasil disimpan.
                </div>
            </div>
            
            <!-- Action Bar - Bottom -->
            <div class="action-bar-bottom" id="actionBar" style="display: none;">
                <button class="btn btn-primary" onclick="exportToExcel()">
                    Simpan
                </button>
                <button class="btn btn-danger" onclick="clearData()">
                    Hapus
                </button>
            </div>
            
            <!-- History Section -->
            <div class="table-container" id="historyContainer" style="display: none; margin-top: 20px;">
                <h3 style="margin: 0; background: #fafafa;">Riwayat Konversi</h3>
                <table id="historyTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>NIP</th>
                            <th>Periode</th>
                            <th>Jml</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Konfirmasi -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Konfirmasi</h3>
            <p id="modalMessage"></p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn" onclick="hideModal()">Batal</button>
                <button class="btn btn-primary" id="modalConfirmBtn">Ya</button>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>
    
    <!-- Footer -->
    <div class="app-footer">
        <span>La_Paste © <span id="currentYear"></span></span>        
    </div>
    
    <script>
        // Global variables
        let workbookData = null;
        let convertedData = [];
        let historyData = [];
        let loadedSheets = [];
        let currentNIP = '';
        let currentNama = '';
        let currentPeriode = '';
        let currentBulan = 2;
        let currentTahun = 2026;
        
        // Daftar nama bulan dalam Bahasa Indonesia
        const bulanIndonesia = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        // Mapping untuk konversi dari bahasa Inggris ke Indonesia
        const bulanInggrisKeIndonesia = {
            'january': 'Januari', 
            'february': 'Februari', 
            'march': 'Maret', 
            'april': 'April',
            'may': 'Mei', 
            'june': 'Juni', 
            'july': 'Juli', 
            'august': 'Agustus',
            'september': 'September', 
            'october': 'Oktober', 
            'november': 'November', 
            'december': 'Desember',
            'jan': 'Januari', 
            'feb': 'Februari', 
            'mar': 'Maret', 
            'apr': 'April',
            'jun': 'Juni', 
            'jul': 'Juli', 
            'aug': 'Agustus', 
            'sep': 'September',
            'oct': 'Oktober', 
            'nov': 'November', 
            'dec': 'Desember'
        };
        
        // Initialize localStorage data
        function loadHistory() {
            const saved = localStorage.getItem('absensiHistory');
            if (saved) {
                historyData = JSON.parse(saved);
                displayHistory();
            }
        }
        
        // Load from history
        function loadFromHistory(item) {
            showModal('Konfirmasi', 'Muat data dari riwayat?', () => {
                convertedData = item.data;
                currentNIP = item.nip;
                currentNama = item.nama || '';
                currentPeriode = item.periode;
                currentBulan = item.bulan;
                currentTahun = item.tahun;
                
                displayConvertedData();
                updateInfoBar();
                showWarningInfo(); // Tampilkan warning
                showToast('Data berhasil dimuat!', 'success');
                hideModal();
            });
        }
        
        // Delete from history
        function deleteHistory(id) {
            showModal('Konfirmasi', 'Hapus item dari riwayat?', () => {
                historyData = historyData.filter(item => item.id !== id);
                localStorage.setItem('absensiHistory', JSON.stringify(historyData));
                displayHistory();
                showToast('Item dihapus dari riwayat', 'info');
                hideModal();
            });
        }
        
        // File upload handler
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Reset data
            workbookData = null;
            convertedData = [];
            loadedSheets = [];
            currentNama = '';
            
            // Read Excel file
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    workbookData = XLSX.read(data, { type: 'array' });
                    
                    // Auto load semua sheet
                    autoLoadAllSheets();
                    
                } catch (error) {
                    showToast('Error membaca file: ' + error.message, 'error');
                }
            };
            reader.readAsArrayBuffer(file);
        });
        
        // Auto load all sheets
        function autoLoadAllSheets() {
            if (!workbookData || workbookData.SheetNames.length === 0) {
                showToast('Tidak ada sheet untuk diload!', 'warning');
                return;
            }
            
            // Tampilkan progress
            document.getElementById('autoLoadProgress').style.display = 'block';
            document.getElementById('autoLoadProgressFill').style.width = '0%';
            document.getElementById('progressPercent').textContent = '0%';
            document.getElementById('autoLoadStatus').textContent = `Memuat 0 dari ${workbookData.SheetNames.length} sheet...`;
            
            // Reset data untuk load baru
            convertedData = [];
            loadedSheets = [];
            
            let processedCount = 0;
            const totalSheets = workbookData.SheetNames.length;
            
            // Proses setiap sheet
            workbookData.SheetNames.forEach((sheetName, index) => {
                setTimeout(() => {
                    try {
                        const sheet = workbookData.Sheets[sheetName];
                        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });
                        
                        // Detect NIP, Nama, periode dari sheet pertama
                        if (index === 0) {
                            detectInfo(jsonData);
                        }
                        
                        // Convert data dari sheet ini
                        const sheetData = convertSheetData(jsonData);
                        convertedData = [...convertedData, ...sheetData];
                        
                        // Catat sheet yang sudah diproses
                        if (sheetData.length > 0) {
                            loadedSheets.push(sheetName);
                        }
                        
                    } catch (error) {
                        console.error(`Error loading sheet ${sheetName}:`, error);
                    }
                    
                    processedCount++;
                    
                    // Update progress
                    const progress = (processedCount / totalSheets) * 100;
                    document.getElementById('autoLoadProgressFill').style.width = progress + '%';
                    document.getElementById('progressPercent').textContent = Math.round(progress) + '%';
                    document.getElementById('autoLoadStatus').textContent = `Memuat ${processedCount} dari ${totalSheets} sheet...`;
                    
                    // Jika semua sudah diproses
                    if (processedCount === totalSheets) {
                        setTimeout(() => {
                            document.getElementById('autoLoadProgress').style.display = 'none';
                            
                            if (convertedData.length > 0) {
                                displayConvertedData();
                                updateInfoBar();
                                showWarningInfo(); // Tampilkan warning
                                
                                showToast(`${convertedData.length} data berhasil dimuat dari ${loadedSheets.length} sheet!`, 'success');
                            } else {
                                showToast('Tidak ada data yang ditemukan!', 'warning');
                            }
                        }, 500);
                    }
                }, index * 50);
            });
        }
        
        // Fungsi untuk menampilkan warning info
        function showWarningInfo() {
            document.getElementById('warningInfo').style.display = 'flex';
        }
        
        // Fungsi untuk menyembunyikan warning info
        function hideWarningInfo() {
            document.getElementById('warningInfo').style.display = 'none';
        }
        
        // Convert data dari satu sheet
        function convertSheetData(data) {
            let sheetData = [];
            let foundData = false;
            let headerRow = -1;
            
            // Find header row
            for (let i = 0; i < data.length; i++) {
                if (data[i] && data[i][0] && String(data[i][0]).toLowerCase() === 'tanggal') {
                    headerRow = i;
                    break;
                }
            }
            
            if (headerRow === -1) return sheetData;
            
            // Process data
            for (let i = headerRow + 1; i < data.length; i++) {
                const row = data[i];
                if (!row || !row[0]) {
                    if (foundData) break;
                    continue;
                }
                
                const tanggal = row[0];
                if (!isNaN(tanggal) && tanggal >= 1 && tanggal <= 31) {
                    foundData = true;
                    
                    // Create date
                    const date = new Date(currentTahun, currentBulan - 1, parseInt(tanggal));
                    
                    // Get keterangan
                    let keterangan = row[4] || 'Fleksibel Bekerja Secara Lokasi';
                    
                    // KONDISI 1: Jam masuk ada, jam pulang kosong
                    if (row[2] && !row[3]) {
                        // Hanya tambahkan data pulang dengan jam default 16:30
                        sheetData.push({
                            nip: currentNIP,
                            tanggal: formatDate(date),
                            kategori: 'Pulang',
                            jenis: keterangan,
                            waktu: '16:30',
                            keterangan: 'Absen pulang sore (default)'
                        });
                    }
                    
                    // KONDISI 2: Jam pulang ada, jam masuk kosong
                    else if (!row[2] && row[3]) {
                        // Hanya tambahkan data masuk dengan jam default 07:30
                        sheetData.push({
                            nip: currentNIP,
                            tanggal: formatDate(date),
                            kategori: 'Masuk',
                            jenis: keterangan,
                            waktu: '07:30',
                            keterangan: 'Absen masuk pagi (default)'
                        });
                    }
                    
                    // KONDISI 3: Kedua jam ada (normal)
                    else if (row[2] && row[3]) {
                        // Tambahkan data masuk
                        sheetData.push({
                            nip: currentNIP,
                            tanggal: formatDate(date),
                            kategori: 'Masuk',
                            jenis: keterangan,
                            waktu: formatTime(row[2]),
                            keterangan: 'Absen masuk pagi'
                        });
                        
                        // Tambahkan data pulang
                        sheetData.push({
                            nip: currentNIP,
                            tanggal: formatDate(date),
                            kategori: 'Pulang',
                            jenis: keterangan,
                            waktu: formatTime(row[3]),
                            keterangan: 'Absen pulang sore'
                        });
                    }
                    
                    // KONDISI 4: Kedua jam kosong - tidak menambahkan data apapun
                }
            }
            
            return sheetData;
        }
        
        // Detect NIP, Nama, and period
        function detectInfo(data) {
            for (let i = 0; i < Math.min(20, data.length); i++) {
                const row = data[i];
                if (!row || !row[0]) continue;
                
                const cellValue = String(row[0]).toLowerCase();
                
                // Detect NIP
                if (cellValue.includes('nip')) {
                    let nip = cellValue.replace(/nip\s*:?\s*/i, '').trim();
                    nip = nip.replace(/[^0-9]/g, '');
                    if (nip) currentNIP = nip;
                }
                
                // Detect Nama (disimpan tapi tidak ditampilkan)
                if (cellValue.includes('nama') || cellValue.includes('name')) {
                    let nama = cellValue.replace(/(nama|name)\s*:?\s*/i, '').trim();
                    nama = nama.replace(/[^a-zA-Z\s]/g, '');
                    if (nama && nama.length > 0) currentNama = nama;
                }
                
                // Detect periode
                if (cellValue.includes('absensi')) {
                    let periodeText = cellValue.replace(/absensi\s*:?\s*/i, '').trim();
                    // Konversi ke Indonesia sebelum disimpan
                    currentPeriode = konversiBulanKeIndonesia(periodeText);
                    parsePeriode(periodeText); // Parse dari teks asli untuk dapat bulan dan tahun
                }
                
                // Juga cek di kolom lain jika perlu
                for (let j = 1; j < row.length; j++) {
                    if (!row[j]) continue;
                    const colValue = String(row[j]).toLowerCase();
                    
                    if (!currentNama && (colValue.includes('nama') || colValue.includes('name'))) {
                        let nama = colValue.replace(/(nama|name)\s*:?\s*/i, '').trim();
                        nama = nama.replace(/[^a-zA-Z\s]/g, '');
                        if (nama && nama.length > 0) currentNama = nama;
                    }
                }
            }
            
            // Jika nama tidak ditemukan, gunakan default
            if (!currentNama) {
                currentNama = 'Tidak Diketahui';
            }
        }
        
        // Fungsi baru untuk konversi bulan Inggris ke Indonesia tanpa mengubah teks lain
        function konversiBulanKeIndonesia(teks) {
            let teksLower = teks.toLowerCase();
            let teksHasil = teks; // Mulai dengan teks asli
            
            // Cari dan ganti nama bulan Inggris dengan Indonesia
            for (const [eng, ind] of Object.entries(bulanInggrisKeIndonesia)) {
                // Gunakan regex dengan flag 'i' untuk case insensitive
                const regex = new RegExp('\\b' + eng + '\\b', 'gi');
                if (regex.test(teksLower)) {
                    teksHasil = teksHasil.replace(regex, ind);
                }
            }
            
            return teksHasil;
        }
        
        // Parse period - FIXED: sekarang tidak mengubah currentPeriode
        function parsePeriode(periode) {
            const months = {
                'januari': 1, 'jan': 1,
                'februari': 2, 'feb': 2,
                'maret': 3, 'mar': 3,
                'april': 4, 'apr': 4,
                'mei': 5,
                'juni': 6, 'jun': 6,
                'juli': 7, 'jul': 7,
                'agustus': 8, 'agu': 8, 'ags': 8, 'aug': 8, 'august': 8,
                'september': 9, 'sep': 9,
                'oktober': 10, 'okt': 10, 'oct': 10, 'october': 10,
                'november': 11, 'nov': 11,
                'desember': 12, 'des': 12, 'dec': 12, 'december': 12
            };
            
            const parts = periode.toLowerCase().split(' ');
            
            for (const part of parts) {
                const cleanPart = part.replace(/[^a-zA-Z0-9]/g, '');
                
                // Check for year (4 digits)
                if (cleanPart.length === 4 && !isNaN(cleanPart)) {
                    currentTahun = parseInt(cleanPart);
                }
                
                // Check for month
                for (const [key, value] of Object.entries(months)) {
                    if (cleanPart.includes(key)) {
                        currentBulan = value;
                        break;
                    }
                }
            }
        }
        
        // Helper untuk mendapatkan nama bulan
        function getBulanIndonesia(bulanAngka) {
            return bulanIndonesia[bulanAngka - 1] || 'Januari';
        }
        
        // Format periode - FIXED: hanya format ulang jika perlu
        function formatPeriodeDisplay() {
            if (currentPeriode && currentPeriode !== '-') {
                return currentPeriode;
            }
            return `${getBulanIndonesia(currentBulan)} ${currentTahun}`;
        }
        
        // Update info bar dengan NIP dan Periode
        function updateInfoBar() {
            document.getElementById('nipDisplay').textContent = currentNIP || '-';
            document.getElementById('periodeDisplay').textContent = formatPeriodeDisplay();
            
            document.getElementById('infoBar').style.display = 'flex';
        }
        
        // Display converted data
        function displayConvertedData() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            
            // Sort data by date and category
            const sorted = [...convertedData].sort((a, b) => {
                if (a.tanggal < b.tanggal) return -1;
                if (a.tanggal > b.tanggal) return 1;
                return a.kategori === 'Masuk' ? -1 : 1;
            });
            
            sorted.forEach(item => {
                const row = tbody.insertRow();
                row.insertCell().textContent = item.nip;
                row.insertCell().textContent = item.tanggal;
                row.insertCell().textContent = item.kategori;
                row.insertCell().textContent = item.jenis;
                row.insertCell().textContent = item.waktu;
                row.insertCell().textContent = item.keterangan;
            });
            
            document.getElementById('tableContainer').style.display = 'block';
            // Info panel tidak ditampilkan
            document.getElementById('infoPanel').style.display = 'none';
            document.getElementById('actionBar').style.display = 'flex';
        }
        
        // Display history
        function displayHistory() {
            if (historyData.length === 0) {
                document.getElementById('historyContainer').style.display = 'none';
                return;
            }
            
            const tbody = document.getElementById('historyBody');
            tbody.innerHTML = '';
            
            historyData.forEach(item => {
                const row = tbody.insertRow();
                row.insertCell().textContent = item.tanggal;
                row.insertCell().textContent = item.nip;
                row.insertCell().textContent = item.periode;
                row.insertCell().textContent = item.jumlahData;
                
                const actionCell = row.insertCell();
                const loadBtn = document.createElement('button');
                loadBtn.className = 'btn btn-primary';
                loadBtn.style.padding = '4px 12px';
                loadBtn.style.marginRight = '6px';
                loadBtn.style.fontSize = '0.75em';
                loadBtn.innerHTML = '📂 Load';
                loadBtn.onclick = () => loadFromHistory(item);
                
                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-danger';
                deleteBtn.style.padding = '4px 12px';
                deleteBtn.style.fontSize = '0.75em';
                deleteBtn.innerHTML = '🗑️ Hapus';
                deleteBtn.onclick = () => deleteHistory(item.id);
                
                actionCell.appendChild(loadBtn);
                actionCell.appendChild(deleteBtn);
            });
            
            document.getElementById('historyContainer').style.display = 'block';
        }
        
        // Export to Excel with font size 10 and filename format: Absensi_NIP_Tahun_Bulan.xlsx
        function exportToExcel() {
            if (convertedData.length === 0) {
                showToast('Tidak ada data untuk diekspor!', 'warning');
                return;
            }

            // Prepare filename: Absensi_NIP_Tahun_Bulan.xlsx
            const bulanFormatted = String(currentBulan).padStart(2, '0');
            const fileName = `Absensi_${currentNIP}_${currentTahun}_${bulanFormatted}.xlsx`;

            // Create worksheet data
            const wsData = [
                ['NIP', 'Tanggal', 'Kategori', 'Jenis', 'Waktu', 'Keterangan'],
                ...convertedData.map(d => [d.nip, d.tanggal, d.kategori, d.jenis, d.waktu, d.keterangan])
            ];

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            
            // Set column widths (optional, for better appearance)
            ws['!cols'] = [
                { wch: 15 }, // NIP
                { wch: 12 }, // Tanggal
                { wch: 10 }, // Kategori
                { wch: 25 }, // Jenis
                { wch: 8 },  // Waktu
                { wch: 30 }  // Keterangan
            ];
            
            // Set worksheet name
            XLSX.utils.book_append_sheet(wb, ws, 'Template Import Absensi');
            
            // Download file
            XLSX.writeFile(wb, fileName);
            
            showToast(`File Excel berhasil didownload sebagai ${fileName}`, 'success');
        }
        
        // Clear data
        function clearData() {
            showModal('Konfirmasi', 'Hapus semua data yang sedang ditampilkan?', () => {
                convertedData = [];
                loadedSheets = [];
                currentNIP = '';
                currentNama = '';
                currentPeriode = '';
                
                document.getElementById('tableBody').innerHTML = '';
                document.getElementById('tableContainer').style.display = 'none';
                document.getElementById('infoBar').style.display = 'none';
                document.getElementById('infoPanel').style.display = 'none';
                document.getElementById('actionBar').style.display = 'none';
                hideWarningInfo(); // Sembunyikan warning
                showToast('Data dibersihkan', 'info');
                hideModal();
            });
        }
        
        // Helper functions
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
        
        function formatTime(time) {
            if (!time) return '';
            
            // Handle Excel time serial
            if (typeof time === 'number') {
                const totalSeconds = Math.floor(time * 86400);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
            }
            
            // Handle string time
            if (typeof time === 'string') {
                const match = time.match(/(\d{1,2}):(\d{2})/);
                if (match) {
                    return `${String(match[1]).padStart(2, '0')}:${match[2]}`;
                }
            }
            
            return String(time);
        }
        
        // Modal functions
        function showModal(title, message, onConfirm) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('confirmModal').style.display = 'block';
            
            const confirmBtn = document.getElementById('modalConfirmBtn');
            if (onConfirm) {
                confirmBtn.onclick = onConfirm;
            } else {
                confirmBtn.onclick = hideModal;
            }
        }
        
        function hideModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
        
        // Toast notification
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            
            const colors = {
                success: '#666666',
                error: '#888888',
                warning: '#999999',
                info: '#777777'
            };
            
            toast.style.borderLeftColor = colors[type] || colors.info;
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
        
        // Initialize on load
        window.onload = function() {
            loadHistory();
            // Set current year in footer dynamically
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        };
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target === modal) {
                hideModal();
            }
        };
    </script>
</body>
</html>
