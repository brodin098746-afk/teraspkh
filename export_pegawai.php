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
use PhpOffice\PhpSpreadsheet\Cell\DataType; // Tambahkan ini

// Ambil semua data pegawai
$query = "SELECT * FROM pegawai ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error query: " . $conn->error);
}

try {
    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Pegawai');

    // Header kolom
    $headers = [
        'A1' => 'No',
        'B1' => 'No ID Pegawai',
        'C1' => 'NIP',
        'D1' => 'NIK',
        'E1' => 'No KK',
        'F1' => 'Nama',
        'G1' => 'TMT Tugas',
        'H1' => 'Kecamatan Tugas',
        'I1' => 'Desa Dampingan',
        'J1' => 'Jml KPM',
        'K1' => 'Alamat',
        'L1' => 'Kabupaten',
        'M1' => 'Kecamatan',
        'N1' => 'Desa',
        'O1' => 'Jenis Kelamin',
        'P1' => 'Tempat Lahir',
        'Q1' => 'Tanggal Lahir',
        'R1' => 'Usia',
        'S1' => 'Agama',
        'T1' => 'Status Nikah',
        'U1' => 'Jml Anak',
        'V1' => 'Pekerjaan',
        'W1' => 'Jabatan',
        'X1' => 'No HP',
        'Y1' => 'User Zimbra',
        'Z1' => 'Email Zimbra',
        'AA1' => 'Email',
        'AB1' => 'Universitas',
        'AC1' => 'Jurusan',
        'AD1' => 'No Ijazah',
        'AE1' => 'Tgl Lulus',
        'AF1' => 'Tahun Lulus',
        'AG1' => 'Jenjang',
        'AH1' => 'No Rek BTN',
        'AI1' => 'No Rek JATIM',
        'AJ1' => 'No Rek BNI',
        'AK1' => 'No Rek BRI',
        'AL1' => 'Nama Rekening',
        'AM1' => 'Ibu Kandung',
        'AN1' => 'NPWP',
        'AO1' => 'BPJS Kesehatan',
        'AP1' => 'BPJS TK'
    ];

    foreach($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }

    // Style header
    $sheet->getStyle('A1:AP1')->getFont()->setBold(true);
    $sheet->getStyle('A1:AP1')->getFont()->getColor()->setARGB('FFFFFFFF');
    $sheet->getStyle('A1:AP1')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FF4361EE');
    $sheet->getStyle('A1:AP1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:AP1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Isi data
    $rowNumber = 2;
    $no = 1;

    while($row = $result->fetch_assoc()) {
        // Nomor urut (angka biasa)
        $sheet->setCellValue('A' . $rowNumber, $no++);
        
        // Kolom-kolom yang berisi angka panjang (set sebagai teks)
        $sheet->setCellValueExplicit('B' . $rowNumber, $row['no_idpegawai'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C' . $rowNumber, $row['nip'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D' . $rowNumber, $row['nik'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E' . $rowNumber, $row['nokk'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValue('F' . $rowNumber, $row['nama'] ?? ''); // teks biasa
        $sheet->setCellValue('G' . $rowNumber, $row['tmttugas'] ?? ''); // tanggal
        $sheet->setCellValue('H' . $rowNumber, $row['kecamatantugas'] ?? '');
        $sheet->setCellValue('I' . $rowNumber, $row['desadampingan'] ?? '');
        $sheet->setCellValue('J' . $rowNumber, $row['jmlkpm'] ?? 0); // angka
        $sheet->setCellValue('K' . $rowNumber, $row['alamat'] ?? '');
        $sheet->setCellValue('L' . $rowNumber, $row['kabupaten'] ?? '');
        $sheet->setCellValue('M' . $rowNumber, $row['kecamatan'] ?? '');
        $sheet->setCellValue('N' . $rowNumber, $row['desa'] ?? '');
        $sheet->setCellValue('O' . $rowNumber, $row['jeniskelamin'] ?? '');
        $sheet->setCellValue('P' . $rowNumber, $row['tmplahir'] ?? '');
        $sheet->setCellValue('Q' . $rowNumber, $row['tgllahir'] ?? ''); // tanggal
        $sheet->setCellValue('R' . $rowNumber, $row['usia'] ?? 0); // angka
        $sheet->setCellValue('S' . $rowNumber, $row['agama'] ?? '');
        $sheet->setCellValue('T' . $rowNumber, $row['statusnikah'] ?? '');
        $sheet->setCellValue('U' . $rowNumber, $row['jmlanak'] ?? 0); // angka
        $sheet->setCellValue('V' . $rowNumber, $row['pekerjaan'] ?? '');
        $sheet->setCellValue('W' . $rowNumber, $row['jabatan'] ?? '');
        $sheet->setCellValueExplicit('X' . $rowNumber, $row['nohp'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValue('Y' . $rowNumber, $row['userzimbra'] ?? '');
        $sheet->setCellValue('Z' . $rowNumber, $row['emailzimbra'] ?? '');
        $sheet->setCellValue('AA' . $rowNumber, $row['email'] ?? '');
        $sheet->setCellValue('AB' . $rowNumber, $row['universitas'] ?? '');
        $sheet->setCellValue('AC' . $rowNumber, $row['jurusan'] ?? '');
        $sheet->setCellValueExplicit('AD' . $rowNumber, $row['noijazah'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValue('AE' . $rowNumber, $row['tgllulus'] ?? '');
        $sheet->setCellValue('AF' . $rowNumber, $row['tahunlulus'] ?? ''); // tahun
        $sheet->setCellValue('AG' . $rowNumber, $row['jenjang'] ?? '');
        $sheet->setCellValueExplicit('AH' . $rowNumber, $row['norekbtn'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AI' . $rowNumber, $row['norekjatim'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AJ' . $rowNumber, $row['norekbni'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AK' . $rowNumber, $row['norekbri'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValue('AL' . $rowNumber, $row['namarekening'] ?? '');
        $sheet->setCellValue('AM' . $rowNumber, $row['ibukandung'] ?? '');
        $sheet->setCellValueExplicit('AN' . $rowNumber, $row['npwp'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AO' . $rowNumber, $row['bpjskes'] ?? '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AP' . $rowNumber, $row['bpjstk'] ?? '', DataType::TYPE_STRING);
        
        $rowNumber++;
    }

    // Auto size kolom
    foreach(range('A','Z') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    foreach(range('AA','AP') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Border untuk data
    $lastRow = $rowNumber - 1;
    if($lastRow >= 2) {
        $sheet->getStyle('A2:AP' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    // Download file
    $writer = new Xlsx($spreadsheet);
    $filename = 'data_pegawai_' . date('Y-m-d_H-i-s') . '.xlsx';

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