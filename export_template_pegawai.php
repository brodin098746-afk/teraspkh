<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

include "koneksi.php";
require_once "autoload_phpspreadsheet_final.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

try {
    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Pegawai');

    // HEADER KOLOM (Sesuai permintaan - nama yang mudah dibaca)
    $headers = [
        'A1' => 'no',
        'B1' => 'no_idpegawai',
        'C1' => 'nip',
        'D1' => 'nik',
        'E1' => 'nokk',
        'F1' => 'nama',
        'G1' => 'tmttugas',
        'H1' => 'kecamatantugas',
        'I1' => 'desadampingan',
        'J1' => 'jmlkpm',
        'K1' => 'alamat',
        'L1' => 'kabupaten',
        'M1' => 'kecamatan',
        'N1' => 'desa',
        'O1' => 'jeniskelamin',
        'P1' => 'tmplahir',
        'Q1' => 'tgllahir',
        'R1' => 'agama',
        'S1' => 'usia',
        'T1' => 'statusnikah',
        'U1' => 'jmlanak',
        'V1' => 'pekerjaan',
        'W1' => 'jabatan',
        'X1' => 'nohp',
        'Y1' => 'userzimbra',
        'Z1' => 'emailzimbra',
        'AA1' => 'email',
        'AB1' => 'universitas',
        'AC1' => 'jurusan',
        'AD1' => 'noijazah',
        'AE1' => 'tgllulus',
        'AF1' => 'tahunlulus',
        'AG1' => 'jenjang',
        'AH1' => 'norekbtn',
        'AI1' => 'norekjatim',
        'AJ1' => 'norekbni',
        'AK1' => 'norekbri',
        'AL1' => 'namarekening',
        'AM1' => 'ibukandung',
        'AN1' => 'npwp',
        'AO1' => 'bpjskes',
        'AP1' => 'bpjstk'
    ];

    // Set header
    foreach($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }

    // Style header
    $sheet->getStyle('A1:AP1')->getFont()->setBold(true);
    $sheet->getStyle('A1:AP1')->getFont()->getColor()->setARGB('FFFFFFFF');
    $sheet->getStyle('A1:AP1')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FF28A745'); // Warna hijau untuk template
    $sheet->getStyle('A1:AP1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:AP1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Contoh data (baris 2) - sebagai panduan
    $sheet->setCellValue('A2', '1'); // No
    $sheet->setCellValueExplicit('B2', 'P001', DataType::TYPE_STRING); // No ID Pegawai
    $sheet->setCellValueExplicit('C2', '198001012010011001', DataType::TYPE_STRING); // NIP
    $sheet->setCellValueExplicit('D2', '3201010101010001', DataType::TYPE_STRING); // NIK
    $sheet->setCellValueExplicit('E2', '3201010101', DataType::TYPE_STRING); // No KK
    $sheet->setCellValue('F2', 'Budi Santoso'); // Nama
    $sheet->setCellValue('G2', '2020-01-01'); // TMT Tugas
    $sheet->setCellValue('H2', 'Kecamatan A'); // Kecamatan Tugas
    $sheet->setCellValue('I2', 'Desa A'); // Desa Dampingan
    $sheet->setCellValue('J2', 50); // Jml KPM
    $sheet->setCellValue('K2', 'Jl. Merdeka No. 1'); // Alamat
    $sheet->setCellValue('L2', 'Kabupaten A'); // Kabupaten
    $sheet->setCellValue('M2', 'Kecamatan A'); // Kecamatan
    $sheet->setCellValue('N2', 'Desa A'); // Desa
    $sheet->setCellValue('O2', 'Laki-laki'); // Jenis Kelamin
    $sheet->setCellValue('P2', 'Jakarta'); // Tempat Lahir
    $sheet->setCellValue('Q2', '1980-01-01'); // Tanggal Lahir
    $sheet->setCellValue('R2', 44); // Usia
    $sheet->setCellValue('S2', 'Islam'); // Agama
    $sheet->setCellValue('T2', 'Kawin'); // Status Nikah
    $sheet->setCellValue('U2', 2); // Jml Anak
    $sheet->setCellValue('V2', 'PNS'); // Pekerjaan
    $sheet->setCellValue('W2', 'Staff'); // Jabatan
    $sheet->setCellValueExplicit('X2', '081234567890', DataType::TYPE_STRING); // No HP
    $sheet->setCellValue('Y2', 'budi.santoso'); // User Zimbra
    $sheet->setCellValue('Z2', 'budi.santoso@email.com'); // Email Zimbra
    $sheet->setCellValue('AA2', 'budi@gmail.com'); // Email
    $sheet->setCellValue('AB2', 'Universitas Indonesia'); // Universitas
    $sheet->setCellValue('AC2', 'Ekonomi'); // Jurusan
    $sheet->setCellValueExplicit('AD2', 'IJZ001', DataType::TYPE_STRING); // No Ijazah
    $sheet->setCellValue('AE2', '2005-06-15'); // Tgl Lulus
    $sheet->setCellValue('AF2', 2005); // Tahun Lulus
    $sheet->setCellValue('AG2', 'S1'); // Jenjang
    $sheet->setCellValueExplicit('AH2', '1234567890', DataType::TYPE_STRING); // No Rek BTN
    $sheet->setCellValueExplicit('AI2', '1234567890', DataType::TYPE_STRING); // No Rek JATIM
    $sheet->setCellValueExplicit('AJ2', '1234567890', DataType::TYPE_STRING); // No Rek BNI
    $sheet->setCellValueExplicit('AK2', '1234567890', DataType::TYPE_STRING); // No Rek BRI
    $sheet->setCellValue('AL2', 'Budi Santoso'); // Nama Rekening
    $sheet->setCellValue('AM2', 'Siti Aminah'); // Ibu Kandung
    $sheet->setCellValueExplicit('AN2', '123456789012345', DataType::TYPE_STRING); // NPWP
    $sheet->setCellValueExplicit('AO2', '1234567890', DataType::TYPE_STRING); // BPJS Kesehatan
    $sheet->setCellValueExplicit('AP2', '1234567890', DataType::TYPE_STRING); // BPJS TK

    // Style untuk contoh data
    $sheet->getStyle('A2:AP2')->getFont()->setItalic(true);
    $sheet->getStyle('A2:AP2')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FFF8F9FA');

    // Catatan (baris 4-12)
    $sheet->setCellValue('A4', 'PETUNJUK PENGISIAN:');
    $sheet->setCellValue('A5', '1. Baris pertama (header) WAJIB ADA dengan nama kolom seperti di atas');
    $sheet->setCellValue('A6', '2. Kolom NIK (D) WAJIB DIISI sebagai identifikasi data (untuk update/insert)');
    $sheet->setCellValue('A7', '3. Format tanggal: YYYY-MM-DD (contoh: 2024-01-31)');
    $sheet->setCellValue('A8', '4. Kolom No, No ID Pegawai, NIP, NIK, No KK, No HP, No Ijazah, No Rekening, NPWP, BPJS: biarkan sebagai teks (tidak usah diformat angka)');
    $sheet->setCellValue('A9', '5. Baris 2 adalah CONTOH, HARAP DIHAPUS saat mengisi data sebenarnya');
    $sheet->setCellValue('A10', '6. LOGIKA IMPORT:');
    $sheet->setCellValue('A11', '   - Jika NIK sudah ada di database → Data akan DIUPDATE');
    $sheet->setCellValue('A12', '   - Jika NIK belum ada di database → Data akan DITAMBAH (INSERT)');

    // Merge cells untuk catatan
    $sheet->mergeCells('A4:AO4');
    $sheet->mergeCells('A5:AO5');
    $sheet->mergeCells('A6:AO6');
    $sheet->mergeCells('A7:AO7');
    $sheet->mergeCells('A8:AO8');
    $sheet->mergeCells('A9:AO9');
    $sheet->mergeCells('A10:AO10');
    $sheet->mergeCells('A11:AO11');
    $sheet->mergeCells('A12:AO12');

    // Style catatan
    $sheet->getStyle('A4:AO12')->getFont()->setBold(true);
    $sheet->getStyle('A4')->getFont()->setSize(14);
    $sheet->getStyle('A4:AO12')->getFont()->getColor()->setARGB('FFDC3545'); // Warna merah
    $sheet->getStyle('A4:AO12')->getAlignment()->setWrapText(true);

    // Auto size kolom
    foreach(range('A','Z') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    foreach(range('AA','AP') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Download file
    $writer = new Xlsx($spreadsheet);
    $filename = 'template_import_pegawai.xlsx';

    // Hapus semua output buffer
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>