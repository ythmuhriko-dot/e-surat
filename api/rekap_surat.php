<?php
include 'koneksi.php';

try {
    // Ambil data gabungan dari database menggunakan sintaks PostgreSQL & PDO
    $query = "
        SELECT nomor_surat, nama_pasien AS nama_subjek, 'Surat Sakit' AS jenis_surat, tanggal_mulai AS tanggal_surat, nama_dokter FROM surat_sakit
        UNION ALL
        SELECT nomor_surat, nama_pasien AS nama_subjek, 'Surat Sehat' AS jenis_surat, CURRENT_DATE AS tanggal_surat, nama_dokter FROM surat_sehat
        UNION ALL
        SELECT nomor_surat, nama_jenazah AS nama_subjek, 'Surat Kematian' AS jenis_surat, tanggal_meninggal AS tanggal_surat, nama_dokter FROM surat_kematian
        ORDER BY tanggal_surat DESC
    ";
    
    // Eksekusi query dengan PDO
    $stmt = $koneksi->query($query);
?>

<div class="container mt-4">
    <h2>REKAPITULASI PENOMORAN SURAT - UPTD PUSKESMAS BANGKINGAN</h2>
    
    <!-- Tombol Ekspor jika diperlukan di halaman rekap -->
    <a href="ekspor_pelayanan.php" class="btn btn-success mb-3">Export to Excel</a>
    
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Surat</th>
                <th>Nomor Surat</th>
                <th>Nama Pasien / Jenazah</th>
                <th>Tanggal Terbit</th>
                <th>Dokter Pemeriksa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            // Menggunakan fetch(PDO::FETCH_ASSOC) untuk menggantikan mysqli_fetch_assoc
            while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>".$no++."</td>";
                echo "<td>".htmlspecialchars($data['jenis_surat'])."</td>";
                echo "<td>".htmlspecialchars($data['nomor_surat'])."</td>";
                echo "<td>".htmlspecialchars(strtoupper($data['nama_subjek']))."</td>";
                echo "<td>".htmlspecialchars($data['tanggal_surat'])."</td>";
                echo "<td>".htmlspecialchars($data['nama_dokter'])."</td>";
                echo "<td>
                        <a href='hapus_surat.php?nomor=".urlencode($data['nomor_surat'])."&jenis=".urlencode($data['jenis_surat'])." ' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Gagal memuat data rekap: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>