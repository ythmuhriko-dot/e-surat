<?php
include 'koneksi.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Non_Pelayanan.xls");

// Menambahkan header kolom "No" dan "File Surat"
echo "<table border='1'>
        <tr>
            <th>No</th>
            <th>Nomor Surat</th>
            <th>Perihal</th>
            <th>PJ Surat</th>
            <th>Keterangan</th>
            <th>File Surat</th>
        </tr>";

// Pastikan field nama file surat (misal: file_surat) ikut diambil dari database
$sql = "SELECT nomor_surat, perihal, pj_surat, keterangan, file_surat FROM surat_non_pelayanan";
$res = mysqli_query($koneksi, $sql);

$no = 1; 

while($r = mysqli_fetch_assoc($res)) {
    // Tentukan path folder tempat Anda menyimpan file upload (misal: folder 'uploads/')
    // Sesuaikan nama folder 'uploads/' ini dengan settingan asli di sistem Anda
    $url_file = "http://" . $_SERVER['HTTP_HOST'] . "/e-surat/uploads/" . $r['file_surat'];
    
    echo "<tr>
            <td>{$no}</td>
            <td>{$r['nomor_surat']}</td>
            <td>{$r['perihal']}</td>
            <td>{$r['pj_surat']}</td>
            <td>{$r['keterangan']}</td>
            <td><a href='{$url_file}' target='_blank'>Buka File</a></td>
          </tr>";
    
    $no++; 
}
echo "</table>";
?>