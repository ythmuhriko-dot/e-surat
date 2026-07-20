<?php
include 'koneksi.php';

// Konfigurasi API Supabase (Ganti dengan kredensial proyek Supabase Anda)
$supabase_url = "https://zapcouvzxhijqalbcpzq.supabase.co"; // URL Supabase Anda
$supabase_key = "sb_publishable_m0dOUlMj3R5gfJfNHt2VSg_KL8_vrz5"; // API Key Supabase
$bucket_name  = "surat-bucket"; // Nama Bucket yang Anda buat di Supabase Storage

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_surat   = $_POST['tanggal_surat'] ?? null;
    $nomor_surat     = $_POST['nomor_surat'] ?? '';
    $perihal         = $_POST['perihal'] ?? '';
    $pj_surat        = $_POST['pj_surat'] ?? '';
    $keterangan      = $_POST['keterangan'] ?? '';
    $kode_klasifikasi = $_POST['kode_klasifikasi'] ?? '';

    $nama_file     = $_FILES['file_surat']['name'] ?? '';
    $tmp_file      = $_FILES['file_surat']['tmp_name'] ?? '';
    $file_type     = $_FILES['file_surat']['type'] ?? 'application/octet-stream';
    $ekstensi_file = pathinfo($nama_file, PATHINFO_EXTENSION);

    // Membuat nama file unik agar tidak bertabrakan di Storage
    $nama_file_baru = "NON_PELAYANAN_" . time() . "." . $ekstensi_file;

    if (!empty($tmp_file) && is_uploaded_file($tmp_file)) {
        
        // Membaca file biner berkas yang diunggah
        $file_data = file_get_contents($tmp_file);
        
        // Endpoint API Supabase Storage untuk unggah berkas
        $upload_url = $supabase_url . "/storage/v1/object/" . $bucket_name . "/" . $nama_file_baru;

        // Proses unggah menggunakan cURL (karena di lingkungan Vercel serverless)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $supabase_key,
            "ApiKey: " . $supabase_key,
            "Content-Type: " . $file_type
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // HTTP Code 200 berarti berkas sukses masuk ke Supabase Storage
        if ($http_code == 200) {
            // Membuat tautan publik berkas
            $public_url = $supabase_url . "/storage/v1/object/public/" . $bucket_name . "/" . $nama_file_baru;

            try {
                // Menyimpan URL teks publik ke database, bukan file fisiknya
                $query = "INSERT INTO surat_non_pelayanan (nomor_surat, tanggal_surat, perihal, pj_surat, keterangan, file_surat) 
                          VALUES (:nomor_surat, :tanggal_surat, :perihal, :pj_surat, :keterangan, :file_surat)";
                
                $stmt = $koneksi->prepare($query);
                $simpan = $stmt->execute([
                    ':nomor_surat'   => $nomor_surat,
                    ':tanggal_surat' => $tanggal_surat ? $tanggal_surat : null,
                    ':perihal'       => $perihal,
                    ':pj_surat'      => $pj_surat,
                    ':keterangan'    => $keterangan,
                    ':file_surat'    => $public_url // Menyimpan Teks URL
                ]);

                if ($simpan) {
                    echo "<script>alert('Data Berhasil Disimpan ke Cloud Storage!'); window.location.href='index.php?menu=rekap';</script>";
                }
            } catch (PDOException $e) {
                echo "Gagal menyimpan database: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('Gagal mengunggah berkas ke Supabase Cloud! Kode Error: " . $http_code . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Berkas tidak ditemukan atau tidak valid.'); window.history.back();</script>";
    }
}
?>