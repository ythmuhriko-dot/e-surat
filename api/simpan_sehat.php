<?php
include 'koneksi.php';
include 'nomor_otomatis.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Generate nomor baru secara real-time
        $nomor_surat = buat_nomor_surat_otomatis("400.7.22.1");

        $nama_pasien   = $_POST['nama_pasien'] ?? '';
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
        $umur          = $_POST['umur'] ?? 0;
        $pekerjaan     = $_POST['pekerjaan'] ?? '';
        $alamat        = $_POST['alamat_domisili'] ?? '';
        $tensi         = $_POST['tensi'] ?? '';
        $nadi          = $_POST['nadi'] ?? 0;
        $suhu          = $_POST['suhu'] ?? '';
        $bb            = $_POST['berat_badan'] ?? 0;
        $tb            = $_POST['tinggi_badan'] ?? 0;
        $gol_darah     = $_POST['gol_darah'] ?? '';
        $visus_kanan   = $_POST['visus_kanan'] ?? '';
        $visus_kiri    = $_POST['visus_kiri'] ?? '';
        $buta_warna    = $_POST['buta_warna'] ?? '';
        $keperluan     = $_POST['keperluan'] ?? '';
        $nama_dokter   = $_POST['nama_dokter'] ?? '';
        $sip_dokter    = $_POST['sip_dokter'] ?? '';

        $query = "INSERT INTO surat_sehat (nomor_surat, nama_pasien, jenis_kelamin, umur, pekerjaan, alamat_domisili, tensi, nadi, suhu, berat_badan, tinggi_badan, gol_darah, visus_kanan, visus_kiri, buta_warna, keperluan, nama_dokter, sip_dokter) 
                  VALUES (:nomor_surat, :nama_pasien, :jenis_kelamin, :umur, :pekerjaan, :alamat, :tensi, :nadi, :suhu, :bb, :tb, :gol_darah, :visus_kanan, :visus_kiri, :buta_warna, :keperluan, :nama_dokter, :sip_dokter)";

        $stmt_insert = $koneksi->prepare($query);
        $simpan = $stmt_insert->execute([
            ':nomor_surat'   => $nomor_surat,
            ':nama_pasien'   => $nama_pasien,
            ':jenis_kelamin' => $jenis_kelamin,
            ':umur'          => (int)$umur,
            ':pekerjaan'     => $pekerjaan,
            ':alamat'        => $alamat,
            ':tensi'         => $tensi,
            ':nadi'          => (int)$nadi,
            ':suhu'          => $suhu,
            ':bb'            => (int)$bb,
            ':tb'            => (int)$tb,
            ':gol_darah'     => $gol_darah,
            ':visus_kanan'   => $visus_kanan,
            ':visus_kiri'    => $visus_kiri,
            ':buta_warna'    => $buta_warna,
            ':keperluan'     => $keperluan,
            ':nama_dokter'   => $nama_dokter,
            ':sip_dokter'    => $sip_dokter
        ]);

        if ($simpan) {
            echo "<script>
                    alert('Data Surat Sehat Berhasil Disimpan!');
                    window.location.href = 'cetak_sehat.php?nomor=' + encodeURIComponent('" . $nomor_surat . "');
                  </script>";
        }
    } catch (PDOException $e) {
        echo "Gagal menyimpan data ke Supabase: " . $e->getMessage();
    }
} 
?>