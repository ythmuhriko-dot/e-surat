<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// 1. Ambil Parameter Tab Menu (default: semua)
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'semua';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Ambil Parameter Pencarian
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = "";
if ($cari != "") {
    $where = " WHERE nomor_surat ILIKE :cari OR subjek ILIKE :cari OR nama_dokter ILIKE :cari";
}

// 3. Tentukan Query Master Berdasarkan Tab yang Dipilih
if ($tab == 'pelayanan') {
    // Khusus Surat Pelayanan (Sakit, Sehat, Kematian)
    $query_master = "
        SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, '' AS file_upload FROM surat_sakit
        UNION ALL
        SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, '' AS file_upload FROM surat_sehat
        UNION ALL
        SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, '' AS file_upload FROM surat_kematian
    ";
} elseif ($tab == 'non_pelayanan') {
    // Khusus Surat Non-Pelayanan
    $query_master = "
        SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, pj_surat AS nama_dokter, '' AS link, file_surat FROM surat_non_pelayanan
    ";
} else {
    // Rekap Semua Surat
    $query_master = "
        SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, '' AS file_upload FROM surat_sakit
        UNION ALL
        SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, '' AS file_upload FROM surat_sehat
        UNION ALL
        SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, '' AS file_upload FROM surat_kematian
        UNION ALL
        SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, pj_surat AS nama_dokter, '' AS link, file_surat FROM surat_non_pelayanan
    ";
}

// Urutkan berdasarkan nomor urut secara eksplisit
$query_gabungan = "SELECT * FROM ($query_master) AS gabungan $where 
                   ORDER BY CAST(split_part(nomor_surat, '/', 2) AS INTEGER) DESC";

// Eksekusi Hitung Total Paginasi
if ($cari != "") {
    $stmt_total = $koneksi->prepare("SELECT COUNT(*) as total FROM ($query_master) as sub $where");
    $stmt_total->execute([':cari' => "%$cari%"]);
    $total_data = $stmt_total->fetch()['total'];
} else {
    $sql_total = $koneksi->query("SELECT COUNT(*) as total FROM ($query_master) as sub");
    $total_data = $sql_total->fetch()['total'];
}
$total_pages = ceil($total_data / $limit);

// Eksekusi Tampil Data Per Halaman
$query_tampil = $query_gabungan . " LIMIT :limit OFFSET :offset";
$stmt_tampil = $koneksi->prepare($query_tampil);
$stmt_tampil->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_tampil->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($cari != "") {
    $stmt_tampil->bindValue(':cari', "%$cari%", PDO::PARAM_STR);
}
$stmt_tampil->execute();
$data_surat = $stmt_tampil->fetchAll();

// Hitung Statistik Card
$count_sakit = $koneksi->query("SELECT COUNT(*) FROM surat_sakit")->fetchColumn();
$count_sehat = $koneksi->query("SELECT COUNT(*) FROM surat_sehat")->fetchColumn();
$count_kematian = $koneksi->query("SELECT COUNT(*) FROM surat_kematian")->fetchColumn();
$count_non_pelayanan = $koneksi->query("SELECT COUNT(*) FROM surat_non_pelayanan")->fetchColumn();

$total_pelayanan = $count_sakit + $count_sehat + $count_kematian;
$total_keseluruhan = $total_pelayanan + $count_non_pelayanan;
?>

<!-- HTML TEMPLATE -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Surat Bangkingan - Rekap & Statistik</title>
    <!-- CSS Bootstrap / Style Anda -->
</head>
<body>

<div class="d-flex">
    <!-- SIDEBAR NAVIGASI -->
    <div class="sidebar p-3 bg-dark text-white" style="width: 250px; min-height: 100vh;">
        <h4>E-Surat Bangkingan</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="index.php">🏠 Dashboard</a>
            </li>
            
            <!-- 3 MENU REKAP SESUAI PERMINTAAN -->
            <li class="nav-item">
                <a class="nav-link text-white <?= ($tab == 'semua') ? 'active bg-primary rounded' : ''; ?>" 
                   href="dashboard_rekap.php?tab=semua">📊 Rekap Semua Surat</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= ($tab == 'pelayanan') ? 'active bg-primary rounded' : ''; ?>" 
                   href="dashboard_rekap.php?tab=pelayanan">📋 Rekap Pelayanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= ($tab == 'non_pelayanan') ? 'active bg-primary rounded' : ''; ?>" 
                   href="dashboard_rekap.php?tab=non_pelayanan">📑 Rekap Non Pelayanan</a>
            </li>

            <li class="nav-header mt-3 text-muted">SURAT PELAYANAN</li>
            <li class="nav-item"><a class="nav-link text-white-50" href="form_sakit.php">Surat Sakit</a></li>
            <li class="nav-item"><a class="nav-link text-white-50" href="form_sehat.php">Surat Sehat</a></li>
            <li class="nav-item"><a class="nav-link text-white-50" href="form_kematian.php">Surat Kematian</a></li>

            <li class="nav-header mt-3 text-muted">NON PELAYANAN</li>
            <li class="nav-item"><a class="nav-link text-white-50" href="form_non_pelayanan.php">Input Agenda</a></li>
            
            <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php">Keluar / Logout</a></li>
        </ul>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content flex-grow-1 p-4 bg-light">
        
        <!-- HEADER KONTEN DENGAN DYNAMIC TITLE & TOMBOL DOWNLOAD -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <?php if ($tab == 'pelayanan'): ?>
                <h2>Statistik & Rekap Surat Pelayanan</h2>
                <a href="ekspor_pelayanan.php" class="btn btn-success">Download Excel Pelayanan</a>
            <?php elseif ($tab == 'non_pelayanan'): ?>
                <h2>Statistik & Rekap Surat Non-Pelayanan</h2>
                <a href="ekspor_non_pelayanan.php" class="btn btn-success">Download Excel Non Pelayanan</a>
            <?php else: ?>
                <h2>Statistik & Rekap Penomoran (Semua Surat)</h2>
                <div>
                    <a href="ekspor_pelayanan.php" class="btn btn-success btn-sm">Download Excel Pelayanan</a>
                    <a href="ekspor_non_pelayanan.php" class="btn btn-success btn-sm">Download Excel Non Pelayanan</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- SUMMARY CARDS DINAMIS -->
        <div class="row mb-4">
            <?php if ($tab == 'pelayanan'): ?>
                <div class="col"><div class="card border-primary"><div class="card-body"><h6>TOTAL PELAYANAN</h6><h3><?= $total_pelayanan; ?></h3></div></div></div>
                <div class="col"><div class="card border-success"><div class="card-body"><h6>SURAT SAKIT</h6><h3><?= $count_sakit; ?></h3></div></div></div>
                <div class="col"><div class="card border-info"><div class="card-body"><h6>SURAT SEHAT</h6><h3><?= $count_sehat; ?></h3></div></div></div>
                <div class="col"><div class="card border-danger"><div class="card-body"><h6>SURAT KEMATIAN</h6><h3><?= $count_kematian; ?></h3></div></div></div>
            <?php elseif ($tab == 'non_pelayanan'): ?>
                <div class="col-md-4"><div class="card border-warning"><div class="card-body"><h6>TOTAL SURAT NON PELAYANAN</h6><h3><?= $count_non_pelayanan; ?></h3></div></div></div>
            <?php else: ?>
                <!-- Tampilan Tab Semua -->
                <div class="col"><div class="card border-primary"><div class="card-body"><h6>TOTAL SEMUA SURAT</h6><h3><?= $total_keseluruhan; ?></h3></div></div></div>
                <div class="col"><div class="card border-success"><div class="card-body"><h6>SURAT SAKIT</h6><h3><?= $count_sakit; ?></h3></div></div></div>
                <div class="col"><div class="card border-info"><div class="card-body"><h6>SURAT SEHAT</h6><h3><?= $count_sehat; ?></h3></div></div></div>
                <div class="col"><div class="card border-danger"><div class="card-body"><h6>SURAT KEMATIAN</h6><h3><?= $count_kematian; ?></h3></div></div></div>
                <div class="col"><div class="card border-warning"><div class="card-body"><h6>SURAT NON PELAYANAN</h6><h3><?= $count_non_pelayanan; ?></h3></div></div></div>
            <?php endif; ?>
        </div>

        <!-- TABEL REKAP SURAT -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="m-0">📋 Riwayat Log Surat</h5>
                <form method="GET" class="form-inline">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab); ?>">
                    <input type="text" name="cari" class="form-control form-control-sm mr-2" placeholder="Cari nomor/nama..." value="<?= htmlspecialchars($cari); ?>">
                    <button type="submit" class="btn btn-sm btn-dark">Cari</button>
                    <a href="dashboard_rekap.php?tab=<?= $tab; ?>" class="btn btn-sm btn-link text-danger">Reset</a>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>NO SURAT</th>
                            <th>JENIS SURAT</th>
                            <th>NAMA PASIEN/KEPERLUAN</th>
                            <th>NAMA DOKTER/PJ</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_surat) > 0): ?>
                            <?php foreach ($data_surat as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nomor_surat']); ?></td>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($row['jenis']); ?></span></td>
                                    <td><?= htmlspecialchars($row['subjek']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                                    <td>
                                        <?php if (!empty($row['link'])): ?>
                                            <a href="<?= $row['link']; ?>?no=<?= $row['nomor_surat']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">🖨️ Cetak</a>
                                        <?php elseif (!empty($row['file_upload'])): ?>
                                            <a href="uploads/<?= $row['file_upload']; ?>" class="btn btn-sm btn-outline-info" target="_blank">👁️ Lihat File</a>
                                        <?php endif; ?>
                                        <a href="hapus_surat.php?no=<?= $row['nomor_surat']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus surat ini?')">🗑️ Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Data surat tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>