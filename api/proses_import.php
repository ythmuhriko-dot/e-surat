<?php
include 'koneksi.php';
session_start();

// Proteksi Sesi Dashboard
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['import'])) {
    $file_target = $_FILES['file_csv']['tmp_name'];
    
    // Buka file CSV
    if (($handle = fopen($file_target, "r")) !== FALSE) {
        
        // Mengambil baris pertama (Header) menggunakan titik koma (;)
        $header = fgetcsv($handle, 1000, ";");
        
        if (count($header) <= 1) {
            rewind($handle); // reset bacaan file ke awal
            $delimiter = ",";
            fgetcsv($handle, 1000, $delimiter); // lewati header lagi
        } else {
            $delimiter = ";";
        }
        
        $berhasil = 0;
        $gagal = 0;

        try {
            // Siapkan Prepared Statements di luar loop untuk efisiensi
            $stmt_sakit = $koneksi->prepare("INSERT INTO surat_sakit (nomor_surat, nama_pasien, jenis_kelamin, umur, alamat_domisili, pekerjaan, alasan_sakit, lama_istirahat, tanggal_mulai, tanggal_dibuat, nama_dokter, sip_dokter) VALUES (:nomor, :nama, '-', 0, '-', '-', '-', 0, '-', :tgl, :dokter, '-')");
            
            $stmt_sehat = $koneksi->prepare("INSERT INTO surat_sehat (nomor_surat, nama_pasien, jenis_kelamin, umur, pekerjaan, alamat_domisili, tensi, nadi, suhu, berat_badan, tinggi_badan, gol_darah, visus_kanan, visus_kiri, buta_warna, keperluan, tanggal_input, nama_dokter, sip_dokter) VALUES (:nomor, :nama, '-', 0, '-', '-', '-', 0, '-', 0, 0, '-', '-', '-', '-', '-', :tgl, :dokter, '-')");
            
            $stmt_kematian = $koneksi->prepare("INSERT INTO surat_kematian (nomor_surat, nama_jenazah, jenis_kelamin, umur, alamat_jenazah, hari_meninggal, tanggal_meninggal, jam_meninggal, tempat_meninggal, penyebab, nama_pelapor, hubungan_pelapor, tanggal_input, nama_dokter, sip_dokter) VALUES (:nomor, :nama, '-', 0, '-', '-', '-', '-', '-', '-', '-', '-', :tgl, :dokter, '-')");

            // Membaca data CSV baris demi baris secara dinamis
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                
                if (!isset($data[2]) || empty(trim($data[2]))) {
                    continue;
                }
                
                $jenis_surat   = trim($data[0]);
                $tanggal_surat = trim($data[1]);
                $nomor_surat   = trim($data[2]);
                $nama_subjek   = trim($data[3]);
                $nama_dokter   = trim($data[4]);

                $cek_jenis = strtolower($jenis_surat);
                $simpan = false;

                if ($cek_jenis == 'surat sakit') {
                    $simpan = $stmt_sakit->execute([':nomor' => $nomor_surat, ':nama' => $nama_subjek, ':tgl' => $tanggal_surat, ':dokter' => $nama_dokter]);
                } elseif ($cek_jenis == 'surat sehat') {
                    $simpan = $stmt_sehat->execute([':nomor' => $nomor_surat, ':nama' => $nama_subjek, ':tgl' => $tanggal_surat, ':dokter' => $nama_dokter]);
                } elseif ($cek_jenis == 'surat kematian') {
                    $simpan = $stmt_kematian->execute([':nomor' => $nomor_surat, ':nama' => $nama_subjek, ':tgl' => $tanggal_surat, ':dokter' => $nama_dokter]);
                } else {
                    continue;
                }

                if ($simpan) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            }
        } catch (PDOException $e) {
            // Tangkap error database jika ada kendala tipe data
            echo "<script>alert('Terjadi kesalahan database saat import: " . addslashes($e->getMessage()) . "');</script>";
        }
        
        fclose($handle);
        
        echo "<script>
                alert('Proses Import Selesai! Berhasil dimasukkan: $berhasil data. Gagal/Error: $gagal data.');
                window.location.href = 'index.php?menu=rekap';
              </script>";
    } else {
        echo "<script>alert('Gagal membuka berkas Excel CSV.'); window.history.back();</script>";
    }
}
?>