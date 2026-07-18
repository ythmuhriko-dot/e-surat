<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_surat   = $_POST['tanggal_surat'] ?? null;
    $nomor_surat     = $_POST['nomor_surat'] ?? '';
    $perihal         = $_POST['perihal'] ?? '';
    $pj_surat        = $_POST['pj_surat'] ?? '';
    $keterangan      = $_POST['keterangan'] ?? '';
    $kode_klasifikasi = $_POST['kode_klasifikasi'] ?? '';

    $nama_file     = $_FILES['file_surat']['name'] ?? '';
    $tmp_file      = $_FILES['file_surat']['tmp_name'] ?? '';
    $ekstensi_file = pathinfo($nama_file, PATHINFO_EXTENSION);

    $nama_file_baru = "NON_PELAYANAN_" . time() . "." . $ekstensi_file;
    $path_target   = "uploads/" . $nama_file_baru;

    if (move_uploaded_file($tmp_file, $path_target)) {
        try {
            $query = "INSERT INTO surat_non_pelayanan (nomor_surat, tanggal_surat, perihal, pj_surat, keterangan, file_surat) 
                      VALUES (:nomor_surat, :tanggal_surat, :perihal, :pj_surat, :keterangan, :file_surat)";
            
            $stmt = $koneksi->prepare($query);
            $simpan = $stmt->execute([
                ':nomor_surat'   => $nomor_surat,
                ':tanggal_surat' => $tanggal_surat ? $tanggal_surat : null,
                ':perihal'       => $perihal,
                ':pj_surat'      => $pj_surat,
                ':keterangan'    => $keterangan,
                ':file_surat'    => $nama_file_baru
            ]);

            if ($simpan) {
                echo "<script>alert('Data Berhasil Disimpan!'); window.location.href='index.php?menu=rekap';</script>";
            }
        } catch (PDOException $e) {
            echo "Gagal menyimpan database: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Gagal mengunggah berkas! Pastikan folder uploads sudah dibuat.'); window.history.back();</script>";
    }
}
?>