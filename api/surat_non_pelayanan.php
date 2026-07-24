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
    $where = " WHERE nomor_surat ILIKE :cari OR perihal ILIKE :cari OR pj_surat ILIKE :cari";
}

// Query khusus Surat Non-Pelayanan
$query_master = "
    SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, pj_surat AS nama_dokter, '' AS link, file_surat 
    FROM surat_non_pelayanan
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

// Hitung Statistik Card Summary
$count_non_pelayanan = $koneksi->query("SELECT COUNT(*) FROM surat_non_pelayanan")->fetchColumn();
?>

<!-- HTML / Tampilan Header & Cards -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Statistik & Rekap Surat Non-Pelayanan</h2>
    <a href="export_excel_non_pelayanan.php" class="btn btn-success">Download Excel Non Pelayanan</a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <h6>TOTAL SURAT NON-PELAYANAN</h6>
                <h3><?= $count_non_pelayanan; ?></h3>
            </div>
        </div>
    </div>
</div>