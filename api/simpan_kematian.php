<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';
include 'nomor_otomatis.php';

$nomor_surat_form = $_POST['nomor_surat'] ?? $_GET['nomor_surat'] ?? '-';

try {
    $stmt_kematian = $koneksi->prepare("SELECT nomor_surat FROM surat_kematian WHERE nomor_surat = :nomor");
    $stmt_kematian->execute([':nomor' => $nomor_surat_form]);
    
    $stmt_sakit = $koneksi->prepare("SELECT nomor_surat FROM surat_sakit WHERE nomor_surat = :nomor");
    $stmt_sakit->execute([':nomor' => $nomor_surat_form]);
    
    $stmt_sehat = $koneksi->prepare("SELECT nomor_surat FROM surat_sehat WHERE nomor_surat = :nomor");
    $stmt_sehat->execute([':nomor' => $nomor_surat_form]);

    if ($stmt_kematian->fetch() || $stmt_sakit->fetch() || $stmt_sehat->fetch() || $nomor_surat_form == '-') {
        $nomor_surat = buat_nomor_surat_otomatis();
    } else {
        $nomor_surat = $nomor_surat_form;
    }

    $nama_jenazah      = $_POST['nama_jenazah'] ?? $_GET['nama_jenazah'] ?? '-';
    $jenis_kelamin     = $_POST['jenis_kelamin'] ?? $_GET['jenis_kelamin'] ?? '-';
    $umur              = $_POST['umur'] ?? $_GET['umur'] ?? '-'; 
    $alamat_jenazah    = $_POST['alamat_jenazah'] ?? $_GET['alamat_jenazah'] ?? '-';
    $hari_meninggal    = $_POST['hari_meninggal'] ?? $_GET['hari_meninggal'] ?? '-';
    $tanggal_meninggal = $_POST['tanggal_meninggal'] ?? $_GET['tanggal_meninggal'] ?? '2026-01-01';
    $jam_meninggal     = $_POST['jam_meninggal'] ?? $_GET['jam_meninggal'] ?? '-';
    $tempat_meninggal  = $_POST['tempat_meninggal'] ?? $_GET['tempat_meninggal'] ?? '-';
    $penyebab          = $_POST['penyebab'] ?? $_GET['penyebab'] ?? '-';
    $nama_pelapor      = $_POST['nama_pelapor'] ?? $_GET['nama_pelapor'] ?? '-';
    $hubungan_pelapor  = $_POST['hubungan_pelapor'] ?? $_GET['hubungan_pelapor'] ?? '-';
    $nama_dokter       = $_POST['nama_dokter'] ?? $_GET['nama_dokter'] ?? '-';
    $sip_dokter        = $_POST['sip_dokter'] ?? $_GET['sip_dokter'] ?? '-';

    $query = "INSERT INTO surat_kematian (nomor_surat, nama_jenazah, jenis_kelamin, umur, alamat_jenazah, hari_meninggal, tanggal_meninggal, jam_meninggal, tempat_meninggal, penyebab, nama_pelapor, hubungan_pelapor, nama_dokter, sip_dokter) 
              VALUES (:nomor_surat, :nama_jenazah, :jenis_kelamin, :umur, :alamat_jenazah, :hari_meninggal, :tanggal_meninggal, :jam_meninggal, :tempat_meninggal, :penyebab, :nama_pelapor, :hubungan_pelapor, :nama_dokter, :sip_dokter)";
    
    $stmt_insert = $koneksi->prepare($query);
    $simpan = $stmt_insert->execute([
        ':nomor_surat'       => $nomor_surat,
        ':nama_jenazah'      => $nama_jenazah,
        ':jenis_kelamin'     => $jenis_kelamin,
        ':umur'              => $umur,
        ':alamat_jenazah'    => $alamat_jenazah,
        ':hari_meninggal'    => $hari_meninggal,
        ':tanggal_meninggal' => $tanggal_meninggal,
        ':jam_meninggal'     => $jam_meninggal,
        ':tempat_meninggal'  => $tempat_meninggal,
        ':penyebab'          => $penyebab,
        ':nama_pelapor'      => $nama_pelapor,
        ':hubungan_pelapor'  => $hubungan_pelapor,
        ':nama_dokter'       => $nama_dokter,
        ':sip_dokter'        => $sip_dokter
    ]);

    // ... [Kode bagian atas tetap sama] ...

    if ($simpan) {
        echo "<script>
                alert('Data Surat Kematian Sesuai Format Word Berhasil Disimpan!');
                window.location.href = 'cetak_kematian.php?nomor=' + encodeURIComponent('" . $nomor_surat . "');
              </script>";
    }
} catch (PDOException $e) {
// ... [Kode bagian bawah tetap sama] ...
    echo "Gagal menyimpan data ke Supabase: " . $e->getMessage();
}
?>