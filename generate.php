<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';
require_once 'vendor/phpword/PhpWord/Autoloader.php';

\PhpOffice\PhpWord\Autoloader::register();

use PhpOffice\PhpWord\TemplateProcessor;

$id = (int)$_GET['id'];

$query = $conn->query("SELECT * FROM kegiatan WHERE id='$id'");
$data = $query->fetch_assoc();

if(!$data){
    die("Data tidak ditemukan");
}

// Dapatkan template berdasarkan parameter atau default
$template = isset($_GET['template']) ? $_GET['template'] : 'template_edukasi';

$templateFiles = [
    'template_rhk1_1' => 'templates/1. Laporan Monitoring Bansos PKH.docx',
    'template_edukasi' => 'templates/1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai.docx',
    'template_homevisit' => 'templates/1. Laporan Home Visit PKH.docx'
];

$templateFile = $templateFiles[$template] ?? $templateFiles['template_edukasi'];

if (!file_exists($templateFile)) {
    die("Template tidak ditemukan: " . $templateFile);
}

$templateProcessor = new TemplateProcessor($templateFile);

// Mapping data dari database ke placeholder di template
$replacements = [
    'namapetugas' => $data['namapetugas'],
    'nip' => $data['nip'],
    'jabatan' => $data['jabatan'],
    'unitkerja' => $data['unitkerja'],
    'wilayahtugas' => $data['wilayahtugas'],
    'hari' => $data['hari'],
    'tglkeg' => $data['tglkeg'],
    'waktupel' => $data['waktupel'],
    'lokasikeg' => $data['lokasikeg'],
    'namakpm' => $data['namakpm'],
    'nik' => $data['nik'],
    'nokk' => $data['nokk'],
    'alamat' => $data['alamat'],
    'desa' => $data['desa'],
    'kecamatan' => $data['kecamatan'],
    'kabupaten' => $data['kabupaten'],
    'tgllaporan' => $data['tgllaporan']
    'namakelompok' => $data['namakelompok']
    'modul' => $data['modul']
    'sesi' => $data['sesi']
    'pemateri1' => $data['pemateri1']
    'pemateri2' => $data['pemateri2']
    'pemateri3' => $data['pemateri3']
    'pemateri4' => $data['pemateri4']
 
];

// Set nilai untuk semua placeholder
foreach ($replacements as $key => $value) {
    $templateProcessor->setValue($key, $value);
}

// Handle gambar dengan pengecekan yang lebih baik
if (!empty($data['fotoabsen']) && file_exists('uploads/' . $data['fotoabsen'])) {
    $templateProcessor->setImageValue('fotoabsen', [
        'path' => 'uploads/' . $data['fotoabsen'],
        'width' => 120,
        'height' => 60,
        'ratio' => true
    ]);
}
if (!empty($data['fotottd']) && file_exists('uploads/' . $data['fotottd'])) {
    $templateProcessor->setImageValue('fotottd', [
        'path' => 'uploads/' . $data['fotottd'],
        'width' => 120,
        'height' => 60,
        'ratio' => true
    ]);
}

if (!empty($data['fotokeg1']) && file_exists('uploads/' . $data['fotokeg1'])) {
    $templateProcessor->setImageValue('fotokeg1', [
        'path' => 'uploads/' . $data['fotokeg1'],
        'width' => 300,
        'height' => 200,
        'ratio' => true
    ]);
}

if (!empty($data['fotokeg2']) && file_exists('uploads/' . $data['fotokeg2'])) {
    $templateProcessor->setImageValue('fotokeg2', [
        'path' => 'uploads/' . $data['fotokeg2'],
        'width' => 300,
        'height' => 200,
        'ratio' => true
    ]);
}

// Buat direktori output jika belum ada
if (!file_exists('output')) {
    mkdir('output', 0777, true);
}

$outputFile = "output/laporan_{$template}_{$id}.docx";
$templateProcessor->saveAs($outputFile);

echo "Berhasil dibuat: <a href='$outputFile' target='_blank'>Download File</a>";
echo "<br><a href='javascript:history.back()'>Kembali</a>";
?>