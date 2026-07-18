<?php
include 'nomor_otomatis.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$jenis   = $_GET['jenis'] ?? 'pelayanan';

if ($jenis == 'non_pelayanan') {
    echo buat_nomor_non_pelayanan_otomatis($tanggal);
} else {
    echo buat_nomor_surat_otomatis($tanggal);
}
?>