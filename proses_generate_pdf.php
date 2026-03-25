<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login1.php");
    exit;
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set time and memory limits
set_time_limit(300);
ini_set('memory_limit', '512M');

require_once 'koneksi.php';
require_once 'vendor/phpword/PhpWord/Autoloader.php';

\PhpOffice\PhpWord\Autoloader::register();

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;

if(!isset($_POST['proses_generate']) || !isset($_SESSION['filter_data'])) {
    header("Location: generate_pdf_filter.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];
$filter = $_SESSION['filter_data'];
$jenis_terpilih = isset($_POST['jenis_laporan']) ? $_POST['jenis_laporan'] : [];

if(empty($jenis_terpilih)) {
    $_SESSION['generate_error'] = "Pilih minimal satu jenis laporan untuk digenerate!";
    header("Location: preview_pdf.php");
    exit;
}

// ========== SET TEMPORARY DIRECTORY ==========
$base_dir = __DIR__ . DIRECTORY_SEPARATOR;
$temp_dir = $base_dir . 'temp' . DIRECTORY_SEPARATOR;

// Buat folder temp jika belum ada
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0777, true);
}

// Set PHPWord temp directory
Settings::setTempDir($temp_dir);

// Set sistem temp directory
$old_temp = sys_get_temp_dir();
putenv("TMPDIR=$temp_dir");
putenv("TEMP=$temp_dir");
putenv("TMP=$temp_dir");

// Buat folder output
$output_dir = $base_dir . 'output' . DIRECTORY_SEPARATOR . date('Y-m-d') . DIRECTORY_SEPARATOR;
if (!file_exists($output_dir)) {
    mkdir($output_dir, 0777, true);
}

// Mapping template
$template_mapping = [
    '1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai' => 'templates/1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai.docx',
    '1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial' => 'templates/1. Laporan Monitoring Pemantauan Penyaluran Bantuan Sosial.docx',
    '1. Laporan Penelitian penyaluran bantuan Sosial' => 'templates/1. Laporan Penelitian penyaluran bantuan Sosial.docx',
    '1. Laporan Supervisi Permasalahan Bantuan Sosial' => 'templates/1. Laporan Supervisi Permasalahan Bantuan Sosial.docx',
    '2. Laporan Pelaksanaan P2K2' => 'templates/2. Laporan Pelaksanaan P2K2.docx',
    '3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH dalam proses perubahan perilaku, pola pikir yang mandiri dan produktif' => 'templates/3. Laporan pendampingan, mediasi, dan fasilitasi kepada KPM PKH.docx',
    '3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial' => 'templates/3. Laporan Verifikasi Komitmen Pendidikan,Kesehatan dan Kesejahteraan Sosial.docx',
    '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD Surat Pernyataan)' => 'templates/4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Sudah TTD).docx',
    '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi)' => 'templates/4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE (Usulan Graduasi).docx',
    '4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE' => 'templates/4. Laporan Kegiatan Usulan KPM Graduasi mandiri dan Pemberdayaan PPSE.docx',
    '5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck)' => 'templates/5. Laporan Pemutakhiran Data KPM PKH (Pembaruan DTSEN Groundcheck).docx',
    '5. Laporan Pemutakhiran Data KPM PKH' => 'templates/5. Laporan Pemutakhiran Data KPM PKH.docx',
    '5. Laporan Proses Bisnis PKH yang meliputi verifikasi validasi calon penerima bantuan sosial' => 'templates/5. Laporan Proses Bisnis PKH verifikasi validasi calon penerima.docx',
    '6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan' => 'templates/6. Laporan Respon Kasus Pengaduan Kebencanaan Kerentanan.docx',
    '7. LAPORAN BULANAN ASN PPPK' => 'templates/7. LAPORAN BULANAN ASN PPPK.docx',
    '8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM)' => 'templates/8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Penguatan Kapasitas SDM).docx',
    '8. Laporan Rapat Koordinasi,Sosialisasi Kebijakan Proses Bisnis PKH dan Penguatan Kapasitas SDM (Rapat Koordinasi)' => 'templates/8. Laporan Rapat Koordinasi (Rapat Koordinasi).docx',
    '8. Laporan sosialisasi kebijakan dan bisnis proses PKH kepada aparat pemerintah tingkat kecamatan, desa kelurahan, KPM PKH, dan masyarakat umum secara berkala melalui Pertemuan atau media sosial dll' => 'templates/8. Laporan sosialisasi kebijakan dan bisnis proses PKH.docx',
    '8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP)' => 'templates/8. Laporan Tindak Lanjut Hasil Pemeriksaan (TLHP).docx',
    '9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial' => 'templates/9. Laporan Berperan aktif dalam memanfaatkan, menggunakan, melibatkan dan menyebarkan Media Sosial untuk menyampaikan semua program di Kementerian Sosial.docx'
];

$default_template = 'templates/1. Laporan Melakukan edukasi dan sosialisasi pencairan secara tunai dan non tunai.docx';
$generated_files = [];
$errors = [];
$success_count = 0;

function cleanFileName($string) {
    $string = preg_replace('/^\d+\.\s+/', '', $string);
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    $string = preg_replace('/\s+/', '_', $string);
    return trim($string, '_');
}

// Fungsi untuk generate file
function generateDocument($template_file, $data, $output_file, $temp_dir) {
    try {
        // Copy template ke temp
        $temp_template = $temp_dir . 'template_' . uniqid() . '.docx';
        if (!copy($template_file, $temp_template)) {
            throw new Exception("Gagal copy template");
        }
        
        // Buat instance TemplateProcessor
        $template = new TemplateProcessor($temp_template);
        
        // Set values
        $template->setValue('namapetugas', $data['namapetugas']);
        $template->setValue('nip', $data['nip']);
        $template->setValue('jabatan', $data['jabatan']);
        $template->setValue('unitkerja', $data['unitkerja']);
        $template->setValue('wilayahtugas', $data['wilayahtugas']);
        $template->setValue('jeniskegiatan', $data['jeniskegiatan']);
        $template->setValue('pemateri', $data['pemateri']);
        $template->setValue('peserta', $data['peserta']);
        $template->setValue('hari', $data['hari']);
        $template->setValue('waktupel', $data['waktupel']);
        $template->setValue('lokasikeg', $data['lokasikeg']);
        $template->setValue('namakelompok', $data['namakelompok']);
        $template->setValue('modul', $data['modul']);
        $template->setValue('sesi', $data['sesi']);
        $template->setValue('pemateri1', $data['pemateri1']);
        $template->setValue('pemateri2', $data['pemateri2']);
        $template->setValue('pemateri3', $data['pemateri3']);
        $template->setValue('pemateri4', $data['pemateri4']);
        $template->setValue('namakpm', $data['namakpm']);
        $template->setValue('nik', $data['nik']);
        $template->setValue('nokk', $data['nokk']);
        $template->setValue('alamat', $data['alamat']);
        $template->setValue('desa', $data['desa']);
        $template->setValue('kecamatan', $data['kecamatan']);
        $template->setValue('kabupaten', $data['kabupaten']);
        $template->setValue('tglkeg', date('d-m-Y', strtotime($data['tglkeg'])));
        $template->setValue('tgllaporan', date('d-m-Y', strtotime($data['tgllaporan'])));
        
        // Konfigurasi ukuran per field
$image_sizes = [
    'fotottd' => ['width' => 200, 'height' => 150],    // Tanda Tangan (kecil)
    'fotokeg1' => ['width' => 690, 'height' => 375],  // Foto Kegiatan 1 (besar)
    'fotokeg2' => ['width' => 690, 'height' => 375],  // Foto Kegiatan 2 (besar)
    'fotoabsen' => ['width' => 581, 'height' => 839]  // Foto Absen (sedang)
];

// Handle images dengan ukuran masing-masing
foreach($image_sizes as $foto => $size) {
    if (!empty($data[$foto]) && file_exists('uploads/' . $data[$foto])) {
        try {
            $template->setImageValue($foto, [
                'path' => 'uploads/' . $data[$foto],
                'width' => $size['width'],
                'height' => $size['height'],
                'ratio' => true  // Maintain aspect ratio
            ]);
        } catch (Exception $e) {
            // Skip gambar error
        }
    }
}
        
        // Handle images - tanpa error
       // $foto_fields = ['fotottd', 'fotokeg1', 'fotokeg2', 'fotoabsen'];
       // foreach($foto_fields as $foto) {
         //   if (!empty($data[$foto]) && file_exists('uploads/' . $data[$foto])) {
             //   try {
               //     $template->setImageValue($foto, [
                 //       'path' => 'uploads/' . $data[$foto],
                   //     'width' => 200,
                   //     'height' => 150,
                   //     'ratio' => true
                //    ]);
             //  } catch (Exception $e) {
                    // Skip gambar error
            //    }
          //  }
      //  }
        
        // Simpan file
        $template->saveAs($output_file);
        
        // Hapus temp template
        @unlink($temp_template);
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

foreach($jenis_terpilih as $jenis) {
    $jenis_escaped = $conn->real_escape_string($jenis);
    
    $where = "WHERE jenis_laporan='$jenis_escaped'";
    
    if($role != 'admin') {
        $where .= " AND user_id=$user_id";
    } elseif(isset($filter['filter_user']) && $filter['filter_user'] > 0) {
        $where .= " AND user_id=" . (int)$filter['filter_user'];
    }
    
    if(!empty($filter['tanggal_awal'])) {
        $where .= " AND tglkeg >= '" . $filter['tanggal_awal'] . "'";
    }
    if(!empty($filter['tanggal_akhir'])) {
        $where .= " AND tglkeg <= '" . $filter['tanggal_akhir'] . "'";
    }
    
    $query = $conn->query("SELECT * FROM kegiatan $where ORDER BY id ASC");
    
    if($query->num_rows == 0) {
        $errors[] = "⚠️ Tidak ada data untuk jenis laporan: <strong>$jenis</strong>";
        continue;
    }
    
    $template_file = isset($template_mapping[$jenis]) ? $template_mapping[$jenis] : $default_template;
    
    if (!file_exists($template_file)) {
        $errors[] = "❌ Template tidak ditemukan untuk: $jenis";
        continue;
    }
    
    $no = 1;
    while($data = $query->fetch_assoc()) {
        $template_name = cleanFileName($jenis);
        $kpm_name = cleanFileName($data['namakpm']);
        $tanggal = date('Ymd', strtotime($data['tglkeg']));
        
        $nama_file = $template_name . '_' . $kpm_name . '_' . $tanggal . '.docx';
        $output_file = $output_dir . $nama_file;
        
        // Coba generate
        if (generateDocument($template_file, $data, $output_file, $temp_dir)) {
            if (file_exists($output_file) && filesize($output_file) > 0) {
                $generated_files[] = [
                    'nama' => $nama_file,
                    'path' => 'output/' . date('Y-m-d') . '/' . $nama_file,
                    'jenis' => $data['jenis_laporan'],
                    'kpm' => $data['namakpm'],
                    'tanggal' => $data['tglkeg']
                ];
                $success_count++;
            } else {
                $errors[] = "❌ File hasil generate kosong untuk {$data['namakpm']}";
            }
        } else {
            $errors[] = "❌ Gagal generate untuk {$data['namakpm']}";
        }
        
        $no++;
    }
}

$_SESSION['generate_result'] = [
    'success' => $generated_files,
    'errors' => $errors,
    'total_success' => $success_count,
    'total_selected' => count($jenis_terpilih),
    'dir' => 'output/' . date('Y-m-d') . '/',
    'tanggal' => date('Y-m-d H:i:s')
];

header("Location: hasil_generate_pdf.php");
exit;
?>