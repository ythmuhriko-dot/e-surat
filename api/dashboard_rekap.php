<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil kata kunci pencarian secara aman (PostgreSQL ramah ILIKE untuk case-insensitive)
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = "";
if ($cari != "") {
    // ILIKE digunakan di Postgres agar pencarian huruf besar/kecil sama saja
    $where = " WHERE nomor_surat ILIKE :cari OR subjek ILIKE :cari OR nama_dokter ILIKE :cari";
}

// Master Query Gabungan
$query_master = "
    SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, '' AS file_upload FROM surat_sakit
    UNION ALL
    SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, '' AS file_upload FROM surat_sehat
    UNION ALL
    SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, '' AS file_upload FROM surat_kematian
    UNION ALL
    SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, pj_surat AS nama_dokter, '' AS link, file_surat FROM surat_non_pelayanan
";

/**
 * PERBAIKAN UTAMA:
 * split_part(nomor_surat, '/', 2) digunakan untuk mengambil bagian nomor urut (contoh: 1873).
 * CAST (... AS INTEGER) mengubah teks nomor tersebut menjadi angka agar urutannya matematis dan sempurna.
 */
$query_gabungan = "SELECT * FROM ($query_master) AS gabungan $where 
                   ORDER BY CAST(split_part(nomor_surat, '/', 2) AS INTEGER) DESC";

// Eksekusi Hitung Total dengan PDO (Disederhanakan untuk menghindari duplikasi query ORDER BY yang berat)
if ($cari != "") {
    $stmt_total = $koneksi->prepare("SELECT COUNT(*) as total FROM ($query_master) as sub 
                                     WHERE nomor_surat ILIKE :cari OR subjek ILIKE :cari OR nama_dokter ILIKE :cari");
    $stmt_total->execute([':cari' => "%$cari%"]);
    $total_data = $stmt_total->fetch()['total'];
} else {
    $sql_total = $koneksi->query("SELECT COUNT(*) as total FROM ($query_master) as sub");
    $total_data = $sql_total->fetch()['total'];
}
$total_pages = ceil($total_data / $limit);

// Query Tampil Data Halaman Aktif dengan PDO
$query_tampil = $query_gabungan . " LIMIT :limit OFFSET :offset";
$stmt_tampil = $koneksi->prepare($query_tampil);
$stmt_tampil->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_tampil->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($cari != "") {
    $stmt_tampil->bindValue(':cari', "%$cari%", PDO::PARAM_STR);
}
$stmt_tampil->execute();
$data_surat = $stmt_tampil->fetchAll();

// Hitung Statistik Card Summary
$count_sakit = $koneksi->query("SELECT COUNT(*) FROM surat_sakit")->fetchColumn();
$count_sehat = $koneksi->query("SELECT COUNT(*) FROM surat_sehat")->fetchColumn();
$count_kematian = $koneksi->query("SELECT COUNT(*) FROM surat_kematian")->fetchColumn();
$count_non_pelayanan = $koneksi->query("SELECT COUNT(*) FROM surat_non_pelayanan")->fetchColumn();
$total_keseluruhan = $count_sakit + $count_sehat + $count_kematian + $count_non_pelayanan;
?>