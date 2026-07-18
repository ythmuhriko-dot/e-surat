<?php
include 'koneksi.php';

// Mengatur Header agar Browser Mendownload sebagai File Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Surat_Puskesmas_Bangkingan.xls");

try {
    // Ambil data gabungan dari database
    $query = "
        SELECT nomor_surat, nama_pasien AS nama_subjek, 'Surat Sakit' AS jenis_surat, tanggal_mulai AS tanggal_surat, nama_dokter FROM surat_sakit
        UNION ALL
        SELECT nomor_surat, nama_pasien AS nama_subjek, 'Surat Sehat' AS jenis_surat, CURRENT_DATE AS tanggal_surat, nama_dokter FROM surat_sehat
        UNION ALL
        SELECT nomor_surat, nama_jenazah AS nama_subjek, 'Surat Kematian' AS jenis_surat, tanggal_meninggal AS tanggal_surat, nama_dokter FROM surat_kematian
        ORDER BY tanggal_surat DESC
    ";
    $stmt = $koneksi->query($query);
?>

<h2>REKAPITULASI PENOMORAN SURAT - UPTD PUSKESMAS BANGKINGAN</h2>
<table border="1">
    <tr>
        <th>No</th>
        <th>Jenis Surat</th>
        <th>Nomor Surat</th>
        <th>Nama Pasien / Jenazah</th>
        <th>Tanggal Terbit</th>
        <th>Dokter Pemeriksa</th>
    </tr>
    <?php 
    $no = 1;
    while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>".$no++."</td>";
        echo "<td>".htmlspecialchars($data['jenis_surat'])."</td>";
        echo "<td>".htmlspecialchars($data['nomor_surat'])."</td>";
        echo "<td>".htmlspecialchars(strtoupper($data['nama_subjek']))."</td>";
        echo "<td>".htmlspecialchars($data['tanggal_surat'])."</td>";
        echo "<td>".htmlspecialchars($data['nama_dokter'])."</td>";
        echo "</tr>";
    }
    ?>
</table>
<?php
} catch (PDOException $e) {
    echo "Gagal mengekspor data: " . $e->getMessage();
}
?>