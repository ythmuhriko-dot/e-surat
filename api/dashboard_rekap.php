<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Filter Jenis Surat (default: semua)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil kata kunci pencarian
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = "";
if ($cari != "") {
    $where = " WHERE nomor_surat ILIKE :cari OR subjek ILIKE :cari OR nama_dokter ILIKE :cari";
}

// Set Master Query Berdasarkan Tombol Filter yang Dipilih
if ($filter == 'pelayanan') {
    // Khusus Surat Pelayanan
    $query_master = "
        SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, '' AS file_upload FROM surat_sakit
        UNION ALL
        SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, '' AS file_upload FROM surat_sehat
        UNION ALL
        SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, '' AS file_upload FROM surat_kematian
    ";
} elseif ($filter == 'non_pelayanan') {
    // Khusus Surat Non Pelayanan
    $query_master = "
        SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, pj_surat AS nama_dokter, '' AS link, file_surat FROM surat_non_pelayanan
    ";
} else {
    // Semua Surat
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

// Fetch Data Tampil
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

<!-- ================= TAMPILAN ELEMENT TABEL & FILTER ================= -->

<div class="card shadow-sm mt-4">
    <div class="card-body">
        
        <!-- ROW FILTER BUTTONS DI BAWAH REKAP STATISTIK -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
            <div>
                <span class="mr-2 font-weight-bold">Filter Tampilan Surat:</span>
                <a href="dashboard_rekap.php?filter=semua" 
                   class="btn btn-sm <?= ($filter == 'semua') ? 'btn-dark' : 'btn-outline-dark'; ?> mr-1">
                   📋 Semua Surat (<?= $total_keseluruhan; ?>)
                </a>
                <a href="dashboard_rekap.php?filter=pelayanan" 
                   class="btn btn-sm <?= ($filter == 'pelayanan') ? 'btn-primary' : 'btn-outline-primary'; ?> mr-1">
                   🩺 Surat Pelayanan (<?= $count_sakit + $count_sehat + $count_kematian; ?>)
                </a>
                <a href="dashboard_rekap.php?filter=non_pelayanan" 
                   class="btn btn-sm <?= ($filter == 'non_pelayanan') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?>">
                   📑 Surat Non Pelayanan (<?= $count_non_pelayanan; ?>)
                </a>
            </div>

            <!-- FORM PENCARIAN -->
            <form method="GET" class="form-inline">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter); ?>">
                <input type="text" name="cari" class="form-control form-control-sm mr-2" placeholder="Cari nomor/nama..." value="<?= htmlspecialchars($cari); ?>">
                <button type="submit" class="btn btn-sm btn-dark">Cari</button>
                <a href="dashboard_rekap.php?filter=<?= $filter; ?>" class="btn btn-sm btn-link text-danger">Reset</a>
            </form>
        </div>

        <!-- TABEL DATA SURAT -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                                <td>
                                    <?php if ($row['jenis'] == 'Non-Pelayanan'): ?>
                                        <span class="badge badge-warning text-dark"><?= $row['jenis']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?= $row['jenis']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['subjek']); ?></td>
                                <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                                <td>
                                    <?php if (!empty($row['link'])): ?>
                                        <a href="<?= $row['link']; ?>?no=<?= $row['nomor_surat']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">🖨️ Cetak</a>
                                    <?php endif; ?>
                                    <a href="hapus_surat.php?no=<?= $row['nomor_surat']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus surat ini?')">🗑️ Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Data surat tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>