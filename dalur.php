
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DaLur</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            background: #f5f5f7;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            border: 1px solid #eaeaea;
        }
        h1 {
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 24px;
            font-weight: 500;
            text-align: center;
            letter-spacing: -0.01em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 32px;
            font-size: 14px;
            text-align: center;
            font-weight: 400;
        }
        .upload-area {
            border: 1.5px dashed #ccc;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background: #fafafa;
            transition: all 0.2s ease;
            cursor: pointer;
            margin-bottom: 24px;
        }
        .upload-area:hover, .upload-area.dragover {
            border-color: #888;
            background: #f5f5f5;
        }
        .upload-icon {
            font-size: 40px;
            color: #555;
            margin-bottom: 12px;
            line-height: 1;
        }
        .upload-area h3 {
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 16px;
        }
        .upload-area p {
            color: #777;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .file-info {
            background: #f0f0f0;
            padding: 10px 16px;
            border-radius: 30px;
            display: inline-block;
            color: #333;
            font-size: 14px;
            border: 1px solid #ddd;
        }
        .btn {
            background: #2c2c2c;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 8px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 280px;
        }
        .btn:hover {
            background: #1a1a1a;
        }
        .btn:disabled {
            background: #cccccc;
            color: #888;
            cursor: not-allowed;
            box-shadow: none;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 24px;
        }
        .loading.active {
            display: block;
        }
        .spinner {
            border: 2px solid #f0f0f0;
            border-top: 2px solid #333;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 14px 18px;
            border-radius: 12px;
            margin: 20px 0 0;
            display: none;
            text-align: center;
            font-size: 14px;
            border: 1px solid #fcc;
        }
        .error-message.active {
            display: block;
        }
        .success-message {
            background: #f0f0f0;
            color: #2c2c2c;
            padding: 14px 18px;
            border-radius: 12px;
            margin: 20px 0 0;
            display: none;
            text-align: center;
            font-size: 14px;
            border: 1px solid #ddd;
        }
        .success-message.active {
            display: block;
        }
        .hidden {
            display: none;
        }
        .button-container {
            display: flex;
            justify-content: center;
        }
        .footer-note {
            text-align: center;
            margin-top: 24px;
            color: #aaa;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">        
        <div class="subtitle">            
        </div>

        <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
            <div class="upload-icon">📄</div>
            <h3>Upload file CSV</h3>
            <p>Klik atau drag & drop file di sini</p>
            <input type="file" id="fileInput" accept=".csv,.txt" style="display: none;">
            <div id="fileInfo" class="file-info hidden">
                <span id="fileName"></span>
            </div>
        </div>

        <div class="button-container">
            <button class="btn" id="convertBtn" disabled>Konversi ke XLSX</button>
        </div>

        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="color: #666; font-size: 14px;">Memproses file...</p>
        </div>
        
        <div class="footer-note">
            La_Paste &copy; <span id="currentYear"></span>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let currentData = null;
        let headers = [];
        let fileName = '';

        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileNameSpan = document.getElementById('fileName');
        const convertBtn = document.getElementById('convertBtn');
        const loading = document.getElementById('loading');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        // Set current year in footer
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        // Drag & drop handlers
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('dragover');
        }

        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Reset states
            errorMessage.classList.remove('active');
            successMessage.classList.remove('active');
            
            if (!file.name.toLowerCase().endsWith('.csv') && !file.name.toLowerCase().endsWith('.txt')) {
                showError('Harap pilih file dengan format CSV atau TXT');
                return;
            }

            fileName = file.name;
            fileNameSpan.textContent = fileName;
            fileInfo.classList.remove('hidden');
            convertBtn.disabled = false;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const content = e.target.result;
                    parseCSV(content);
                } catch (err) {
                    showError('Gagal membaca file: ' + err.message);
                }
            };
            reader.readAsText(file, 'UTF-8');
        }

        function parseCSV(content) {
            try {
                // Split by lines and filter empty lines
                const lines = content.split('\n').filter(line => line.trim() !== '');
                if (lines.length === 0) {
                    showError('File kosong');
                    return;
                }

                // Parse headers (first line)
                headers = parseLine(lines[0]);
                
                // Parse data
                const data = [];
                for (let i = 1; i < lines.length; i++) {
                    const values = parseLine(lines[i]);
                    if (values.length === headers.length) {
                        const row = {};
                        headers.forEach((header, index) => {
                            row[header] = values[index];
                        });
                        data.push(row);
                    }
                }

                currentData = data;
                
                // Enable convert button
                convertBtn.disabled = false;
                
                showSuccess(`File siap: ${data.length} baris data`);
                
            } catch (err) {
                showError('Error parsing CSV: ' + err.message);
            }
        }

        function parseLine(line) {
            const result = [];
            let current = '';
            let inQuotes = false;
            
            for (let i = 0; i < line.length; i++) {
                const char = line[i];
                
                if (char === '"') {
                    inQuotes = !inQuotes;
                } else if (char === '|' && !inQuotes) {
                    result.push(current.trim());
                    current = '';
                } else {
                    current += char;
                }
            }
            
            // Push the last field
            result.push(current.trim());
            
            // Clean up quotes from fields
            return result.map(field => {
                if (field.startsWith('"') && field.endsWith('"')) {
                    return field.slice(1, -1);
                }
                return field;
            });
        }

        convertBtn.addEventListener('click', () => {
            if (!currentData || currentData.length === 0) {
                showError('Tidak ada data untuk dikonversi');
                return;
            }
            
            convertToXLSX();
        });

        function convertToXLSX() {
            loading.classList.add('active');
            convertBtn.disabled = true;
            
            setTimeout(() => {
                try {
                    // Prepare data for XLSX
                    const worksheetData = [];
                    
                    // Add headers
                    worksheetData.push(headers);
                    
                    // Add data rows
                    currentData.forEach(row => {
                        const rowData = headers.map(header => row[header] || '');
                        worksheetData.push(rowData);
                    });
                    
                    // Create worksheet
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(worksheetData);
                    
                    // Add to workbook
                    XLSX.utils.book_append_sheet(wb, ws, 'Data');
                    
                    // Generate filename
                    const outputFileName = fileName.replace(/\.(csv|txt)$/i, '_converted.xlsx');
                    
                    // Save file
                    XLSX.writeFile(wb, outputFileName);
                    
                    loading.classList.remove('active');
                    convertBtn.disabled = false;
                    
                    showSuccess('File berhasil dikonversi!');
                    
                } catch (err) {
                    loading.classList.remove('active');
                    convertBtn.disabled = false;
                    showError('Gagal mengkonversi: ' + err.message);
                }
            }, 100);
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.add('active');
            successMessage.classList.remove('active');
            setTimeout(() => {
                errorMessage.classList.remove('active');
            }, 4000);
        }

        function showSuccess(message) {
            successMessage.textContent = message;
            successMessage.classList.add('active');
            errorMessage.classList.remove('active');
            setTimeout(() => {
                successMessage.classList.remove('active');
            }, 3000);
        }
    </script>
</body>
</html>
