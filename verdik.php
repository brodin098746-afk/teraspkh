<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerDik</title>
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #10b981;
            --bg-body: #ffffff;
            --text-dark: #111827;
        }

        body { 
            font-family: 'Calibri', Roboto, Helvetica, Arial, sans-serif; 
            margin: 0; padding: 0; color: var(--text-dark); 
            background-color: var(--bg-body); 
        }

        /* --- UI DASHBOARD --- */
        .no-print { 
            background: #ffffff; padding: 40px; border-radius: 0; 
            margin: 0 auto 30px auto; max-width: 1000px;
            box-shadow: none; text-align: center;
        }

        h1.main-title { color: #000000; margin-bottom: 10px; font-size: 24px; font-weight: 600; letter-spacing: -0.5px; }
        .guide-text { color: #6b7280; font-size: 14px; margin-bottom: 30px; }

        .card-input {
            background: #ffffff; border: 1px solid #e5e7eb;
            padding: 20px; border-radius: 8px; margin-bottom: 25px;
        }

        .input-group { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .input-group input { 
            padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 6px; 
            width: 250px; font-size: 14px; outline: none; background: #ffffff;
        }
        .input-group input:focus { border-color: #000000; box-shadow: 0 0 0 1px #000000; }

        #dropZone { 
            border: 2px dashed #9ca3af; background: #f9fafb; padding: 35px; border-radius: 8px; 
            cursor: pointer; color: #1f2937; text-align: center; transition: 0.2s;
        }
        #dropZone:hover { background: #f3f4f6; border-color: #4b5563; }
        #dropZone p { margin: 10px 0 0 0; font-weight: 400; }

        .action-area { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 30px; }

        .btn { padding: 14px 32px; font-weight: 500; border: 1px solid #000000; border-radius: 6px; cursor: pointer; font-size: 15px; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; background: #ffffff; color: #000000; }
        .btn-generate { background: #000000; color: #ffffff; border: 1px solid #000000; }
        .btn-generate:hover { background: #333333; }
        .btn-save { background: #ffffff; color: #000000; margin-left: 10px; }
        .btn-save:hover { background: #f3f4f6; }

        /* --- PRINT LAYOUT --- */
        .page-container { 
            width: 28.7cm; margin: 20px auto; padding: 0; background: white;
            box-sizing: border-box; page-break-after: always;
        }
        .page-container:last-child { page-break-after: auto; }

        .kop-surat { display: flex; align-items: center; border-bottom: 2px solid #000000; padding-bottom: 8px; margin-bottom: 10px; }
        .logo-box { width: 55px; flex-shrink: 0; }
        .logo-box img { width: 100%; }
        .header-text { text-align: center; flex-grow: 1; }
        .header-text h2 { margin: 0; font-size: 13px; text-transform: uppercase; color: black; font-weight: 400; }
        .header-text .school-name { margin: 3px 0; font-size: 16px; font-weight: 700; text-transform: uppercase; color: black; }
        
        table { width: 100%; border-collapse: collapse; table-layout: auto; border: 1px solid #000000; }
        thead { display: table-header-group; } 
        th, td { border: 1px solid #000000; padding: 4px 6px; font-size: 8.5px; white-space: nowrap; color: black; }
        th { background-color: #ffffff !important; font-weight: 600; -webkit-print-color-adjust: exact; }
        tbody tr { background-color: #ffffff !important; -webkit-print-color-adjust: exact; }

        .col-auto { text-align: left; }
        .col-narrow { width: 32px; min-width: 32px; text-align: center; }
        .col-ket { width: 55px; min-width: 55px; }

        .signature-wrapper { page-break-inside: avoid; margin-top: 15px; }
        .footer-container { display: flex; justify-content: space-between; align-items: flex-end; }
        .signature-box { width: 320px; text-align: center; font-size: 10px; color: black; }
        .signature-box p { margin: 2px 0; } 
        .space { height: 45px; } 
        .date-line { margin-bottom: 5px !important; }

        /* Footer dengan teks yang jelas terlihat */
        .page-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            color: #000000;
            border-top: 1px solid #000000;
            padding-top: 8px;
            font-weight: 500;
            letter-spacing: 0.3px;
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

        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            @page { size: A4 landscape; margin: 0.5cm; }
            .page-container { box-shadow: none; border: none; width: 100%; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; color: black !important; }
            .page-footer { 
                color: #000000 !important; 
                border-top: 1px solid #000000 !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <h1 class="main-title"></h1>
    <p class="guide-text"></p>

    <div class="card-input">
        <div class="input-group">
            <input type="text" id="kabupatenInput" placeholder="Kabupaten / Kota" />
            <input type="text" id="namaPendampingInput" placeholder="Nama Pendamping" />
            <input type="text" id="nipPendampingInput" placeholder="NIP Pendamping" />
        </div>
    </div>

    <div id="dropZone">
        <div style="font-size: 40px;">📄</div>
        <p id="dropText">Silahkan upload file disini</p>
        <input type="file" id="csvFiles" accept=".csv" multiple style="display: none;" />
        <div id="fileStatus" style="margin-top:12px; font-size:14px; color:#000000; font-weight:normal;"></div>
    </div>

    <div class="action-area">
        <button onclick="processMassiveCSVs()" class="btn btn-generate">GENERATE</button>
        <button onclick="saveAsPDF()" class="btn btn-save">SIMPAN PDF / CETAK</button>
    </div>
</div>

<div id="outputArea"></div>

 <!-- Toast Notification -->
    <div id="toast" class="toast"></div>        
<!-- Footer -->
    <div class="app-footer">
        <span>La_Paste © 2026<span id="currentYear"></span></span>        
    </div>
<script>
    let selectedFiles = [];
    let processedData = [];

    const fileInput = document.getElementById('csvFiles');
    const dropZone = document.getElementById('dropZone');

    dropZone.addEventListener('click', () => fileInput.click());
    ['dragenter', 'dragover'].forEach(e => {
        dropZone.addEventListener(e, (a) => { a.preventDefault(); a.stopPropagation(); });
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); e.stopPropagation();
        handleFiles(e.dataTransfer.files);
    });
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

    function handleFiles(files) {
        selectedFiles = Array.from(files);
        if (selectedFiles.length > 0) {
            document.getElementById('fileStatus').innerText = "✓ " + selectedFiles.length + " file siap";
        }
    }

    async function processMassiveCSVs() {
        const outputArea = document.getElementById('outputArea');
        const kabupaten = document.getElementById('kabupatenInput').value.trim() || "....................";
        const namaP = document.getElementById('namaPendampingInput').value.trim() || "__________________________";
        const nipP = document.getElementById('nipPendampingInput').value.trim();
        
        if (selectedFiles.length === 0) return alert("Pilih file CSV!");
        
        outputArea.innerHTML = '<p style="text-align:center;" class="no-print">Memproses data...</p>'; 
        let finalHtml = '';
        processedData = [];
        
        for (let file of selectedFiles) {
            try {
                const data = await readFileData(file);
                processedData.push(data);
                finalHtml += generateSchoolPage(data, namaP, nipP, kabupaten);
            } catch (err) {
                console.error("Gagal memproses file:", file.name);
            }
        }
        outputArea.innerHTML = finalHtml;
    }

    function readFileData(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const lines = e.target.result.split(/\r?\n/);
                let info = { nama: "NAMA SEKOLAH", npsn: "0" };
                let students = [];
                let idx = { pgs: 2, nik: 3, nisn: 4, sis: 5, tkt: 7, pnd: -1 };
                
                lines.forEach(line => {
                    let cols = line.split(';');
                    if (cols.length < 2) return;
                    let s = line.toUpperCase();
                    if (s.includes("NPSN")) info.npsn = cols[1].replace(/[^0-9]/g, '');
                    if (s.includes("NAMA SEKOLAH")) info.nama = cols[1].replace(/[:"'\r\n]/g, '').trim();
                    if (s.includes("NIK PENGURUS")) {
                        idx.pgs = cols.findIndex(c => c.toUpperCase().includes("NAMA PENGURUS"));
                        idx.nik = cols.findIndex(c => c.toUpperCase().includes("NIK SISWA"));
                        idx.nisn = cols.findIndex(c => c.toUpperCase().includes("NISN"));
                        idx.sis = cols.findIndex(c => c.toUpperCase().includes("NAMA SISWA"));
                        idx.tkt = cols.findIndex(c => c.toUpperCase().includes("TINGKAT"));
                        idx.pnd = cols.findIndex(c => c.toUpperCase().includes("PENDAMPING"));
                    }
                    if (cols.length > 5 && !isNaN(parseInt(cols[0]))) {
                        students.push({
                            pengurus: (cols[idx.pgs] || "").replace(/"/g, '').trim(),
                            nik: (cols[idx.nik] || "").replace(/'/g, '').trim(),
                            nisn: (cols[idx.nisn] || "").replace(/'/g, '').trim(),
                            siswa: (cols[idx.sis] || "").replace(/"/g, '').trim(),
                            tingkat: (cols[idx.tkt] || "").replace(/"/g, '').trim(),
                            pendamping: (cols[idx.pnd !== -1 ? idx.pnd : cols.length-1] || "").replace(/"/g, '').trim()
                        });
                    }
                });
                students.sort((a, b) => {
                    let st = a.tingkat.localeCompare(b.tingkat, undefined, {numeric: true});
                    return st !== 0 ? st : a.siswa.localeCompare(b.siswa);
                });
                resolve({ info, students });
            };
            reader.readAsText(file, 'ISO-8859-1');
        });
    }

    function generateSchoolPage(data, namaP, nipP, kab) {
        let rows = data.students.map((s, i) => `
            <tr>
                <td style="text-align:center">${i + 1}</td>
                <td class="col-auto">${s.pengurus}</td>
                <td class="col-auto">${s.siswa}</td>
                <td style="text-align:center">${s.nik}</td>
                <td style="text-align:center">${s.nisn}</td>
                <td style="text-align:center">${s.tingkat}</td>
                <td class="col-narrow"></td><td class="col-narrow"></td><td class="col-narrow"></td><td class="col-narrow"></td>
                <td class="col-ket"></td>
                <td class="col-auto" style="font-size:7.5px;">${s.pendamping}</td>
            </tr>`).join('');

        const currentYear = new Date().getFullYear();

        return `
        <div class="page-container">
            <div class="kop-surat">
                <div class="logo-box"><img src="Logo1.png"></div>
                <div class="header-text">
                    <h2>Form Verifikasi Komitmen Pendidikan</h2>
                    <p class="school-name">${data.info.nama}</p>
                    <p style="font-size:11px; font-weight:normal;">NPSN: ${data.info.npsn}</p>
                </div>
                <div class="logo-box"><img src="Logo2.png"></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width:25px">NO</th><th rowspan="2">NAMA PENGURUS</th><th rowspan="2">NAMA SISWA</th>
                        <th rowspan="2">NIK SISWA</th><th rowspan="2">NISN</th><th rowspan="2">TINGKAT</th>
                        <th colspan="4">KEHADIRAN</th><th rowspan="2" class="col-ket">KET</th><th rowspan="2">PENDAMPING</th>
                    </tr>
                    <tr><th class="col-narrow">A</th><th class="col-narrow">I</th><th class="col-narrow">S</th><th class="col-narrow">%</th></tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            
            <div class="signature-wrapper">
                <div class="footer-container">
                    <div class="signature-box">
                        <p style="margin-bottom: 20px !important;">&nbsp;</p>
                        <p>Pendamping PKH</p>
                        <div class="space"></div>
                        <p><u><strong>${namaP.toUpperCase()}</strong></u></p>
                        <p>NIP. ${nipP || "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;"}</p>
                    </div>

                    <div class="signature-box">
                        <p class="date-line">${kab}, .........................................</p>
                        <p>Kepala Sekolah / Verifikator</p>
                        <div class="space"></div>
                        <p><strong>__________________________</strong></p>
                        <p>NIP.&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</p>
                    </div>
                </div>
            </div>

           
        </div>`;
    }

    function saveAsPDF() {
        if (processedData.length === 0) return alert("Generate data dahulu!");
        if (processedData.length === 1) {
            let npsn = processedData[0].info.npsn;
            let namaSekolah = processedData[0].info.nama.replace(/\s+/g, '_').toUpperCase();
            document.title = "VERDIK_" + npsn + "_" + namaSekolah;
        } else {
            document.title = "VERDIK_GABUNGAN";
        }
        window.print();
    }
</script>
</body>
</html>













