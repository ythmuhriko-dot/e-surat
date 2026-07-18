<?php
include 'koneksi.php';

$nomor = $_GET['nomor'] ?? '';
$jenis = $_GET['jenis'] ?? '';

if (!empty($nomor) && !empty($jenis)) {
    $tabel = "";
    if ($jenis == "Surat Sakit") $tabel = "surat_sakit";
    elseif ($jenis == "Surat Sehat") $tabel = "surat_sehat";
    elseif ($jenis == "Surat Kematian") $tabel = "surat_kematian";
    elseif ($jenis == "Non-Pelayanan") $tabel = "surat_non_pelayanan";

    if ($tabel != "") {
        try {
            $query = "DELETE FROM $tabel WHERE nomor_surat = :nomor";
            $stmt = $koneksi->prepare($query);
            $stmt->execute([':nomor' => $nomor]);
        } catch (PDOException $e) {
            // Bisa tambahkan log jika diperlukan, namun tetap alihkan halaman
        }
    }
}

header("Location: index.php?menu=rekap");
exit();
?>