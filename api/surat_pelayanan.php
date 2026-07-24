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

// Ambil kata kunci pencarian
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = "";
if ($cari != "") {
    $where = " WHERE nomor_surat ILIKE :cari OR subjek ILIKE :cari OR nama_dokter ILIKE :cari";
}

// Master Query khusus Surat Pelayanan
$query_master = "
    SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, '' AS file_upload FROM surat_sakit
    UNION ALL
    SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, '' AS file_upload FROM surat_sehat
    UNION ALL
    SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, '' AS file_upload FROM surat_kematian
";

$query_gabungan = "SELECT * FROM ($query_master) AS gabungan $where 
                   ORDER BY CAST(split_part(nomor_surat, '/', 2) AS INTEGER) DESC";

// Hitung Total Data untuk Paginasi
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

// Query Tampil Data
$query_tampil = $query_gabungan . " LIMIT :limit OFFSET :offset";
$stmt_tampil = $koneksi->prepare($query_tampil);
$stmt_tampil->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_tampil->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($cari != "") {
    $stmt_tampil->bindValue(':cari', "%$cari%", PDO::PARAM_STR);
}
$stmt_tampil->execute();
$data_surat = $stmt_tampil->fetchAll();

// Hitung Statistik Card Summary Khusus Pelayanan
$count_sakit = $koneksi->query("SELECT COUNT(*) FROM surat_sakit")->fetchColumn();
$count_sehat = $koneksi->query("SELECT COUNT(*) FROM surat_sehat")->fetchColumn();
$count_kematian = $koneksi->query("SELECT COUNT(*) FROM surat_kematian")->fetchColumn();
$total_pelayanan = $count_sakit + $count_sehat + $count_kematian;
?>

<!-- HTML / Tampilan Header & Cards -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Statistik & Rekap Surat Pelayanan</h2>
    <a href="export_excel_pelayanan.php" class="btn btn-success">Download Excel Pelayanan</a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6>TOTAL SURAT PELAYANAN</h6>
                <h3><?= $total_pelayanan; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <h6>SURAT SAKIT</h6>
                <h3><?= $count_sakit; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6>SURAT SEHAT</h6>
                <h3><?= $count_sehat; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body">
                <h6>SURAT KEMATIAN</h6>
                <h3><?= $count_kematian; ?></h3>
            </div>
        </div>
    </div>
</div>