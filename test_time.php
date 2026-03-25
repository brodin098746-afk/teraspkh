<?php
include "koneksi.php";

echo "<h3>Informasi Waktu</h3>";
echo "<pre>";
echo "PHP Timezone: " . date_default_timezone_get() . "\n";
echo "PHP Current Time: " . date('Y-m-d H:i:s') . "\n\n";

$db_time = $conn->query("SELECT NOW() as waktu")->fetch_assoc();
echo "Database Time (NOW()): " . $db_time['waktu'] . "\n";

$php_time = date('Y-m-d H:i:s');
echo "PHP Time: " . $php_time . "\n\n";

echo "Selisih: " . (strtotime($db_time['waktu']) - strtotime($php_time)) . " detik\n";
echo "</pre>";
?>