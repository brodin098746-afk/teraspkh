<?php
/**
 * Simple Excel Reader/Writer tanpa library eksternal
 * Hanya untuk file CSV dan Excel sederhana
 */

class SimpleSpreadsheet {
    
    /**
     * Membaca file CSV
     */
    public function readCSV($filename, $delimiter = ',') {
        $data = [];
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, $delimiter);
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $item = [];
                foreach ($header as $index => $key) {
                    $item[trim($key)] = isset($row[$index]) ? trim($row[$index]) : '';
                }
                $data[] = $item;
            }
            fclose($handle);
        }
        return $data;
    }
    
    /**
     * Menulis ke file CSV
     */
    public function writeCSV($filename, $data, $delimiter = ',') {
        $fp = fopen($filename, 'w');
        
        // Tulis header (ambil dari key array pertama)
        if (!empty($data)) {
            fputcsv($fp, array_keys($data[0]), $delimiter);
            
            // Tulis data
            foreach ($data as $row) {
                fputcsv($fp, array_values($row), $delimiter);
            }
        }
        
        fclose($fp);
        return true;
    }
    
    /**
     * Generate Excel download
     */
    public function downloadCSV($data, $filename = "data_export.csv") {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Tulis BOM untuk UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Tulis header
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            // Tulis data
            foreach ($data as $row) {
                fputcsv($output, array_values($row));
            }
        }
        
        fclose($output);
        exit;
    }
}
?>