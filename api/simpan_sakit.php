<?php
include 'koneksi.php';
include 'nomor_otomatis.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Generate nomor baru secara real-time
        $nomor_surat = buat_nomor_surat_otomatis("400.7.22.1");

        $nama_pasien          = $_POST['nama_pasien'] ?? '';
        $jenis_kelamin        = $_POST['jenis_kelamin'] ?? '';
        $umur                 = $_POST['umur'] ?? 0;
        $alamat_domisili      = $_POST['alamat_domisili'] ?? '';
        $pekerjaan            = $_POST['pekerjaan'] ?? '';
        $lama_istirahat_teks  = $_POST['lama_istirahat_teks'] ?? ''; 
        $lama_istirahat_angka = $_POST['lama_istirahat_angka'] ?? 0; 
        $tanggal_mulai        = $_POST['tanggal_mulai'] ?? null;
        $nama_dokter          = $_POST['nama_dokter'] ?? '';
        $sip_dokter           = $_POST['sip_dokter'] ?? '';

        $query = "INSERT INTO surat_sakit (nomor_surat, nama_pasien, jenis_kelamin, umur, alamat_domisili, pekerjaan, alasan_sakit, lama_istirahat, tanggal_mulai, nama_dokter, sip_dokter) 
                  VALUES (:nomor_surat, :nama_pasien, :jenis_kelamin, :umur, :alamat_domisili, :pekerjaan, :alasan_sakit, :lama_istirahat, :tanggal_mulai, :nama_dokter, :sip_dokter)";
        
        $stmt_insert = $koneksi->prepare($query);
        $simpan = $stmt_insert->execute([
            ':nomor_surat'         => $nomor_surat,
            ':nama_pasien'          => $nama_pasien,
            ':jenis_kelamin'        => $jenis_kelamin,
            ':umur'                 => (int)$umur,
            ':alamat_domisili'      => $alamat_domisili,
            ':pekerjaan'            => $pekerjaan,
            ':alasan_sakit'         => $lama_istirahat_teks,
            ':lama_istirahat'       => (int)$lama_istirahat_angka,
            ':tanggal_mulai'        => $tanggal_mulai ? $tanggal_mulai : null,
            ':nama_dokter'          => $nama_dokter,
            ':sip_dokter'           => $sip_dokter
        ]);

        if ($simpan) {
            echo "<script>
                    alert('Data Surat Sakit Berhasil Disimpan!');
                    window.location.href = 'cetak_sakit.php?nomor=' + encodeURIComponent('" . $nomor_surat . "');
                  </script>";
        }
    } catch (PDOException $e) {
        echo "Gagal menyimpan data ke Supabase: " . $e->getMessage();
    }
}
?>