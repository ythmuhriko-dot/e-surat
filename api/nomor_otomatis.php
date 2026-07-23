<?php
include 'koneksi.php';

// Helper function untuk merapikan Nama, Alamat, dll. (Huruf Depan Saja Yang Kapital)
if (!function_exists('format_teks_rapi')) {
    function format_teks_rapi($teks) {
        if (empty($teks) || trim($teks) === '') return '-';
        
        // Hapus spasi berlebih di awal/akhir dan spasi ganda di tengah
        $teks_clean = preg_replace('/\s+/', ' ', trim($teks));
        
        // Ubah huruf depan setiap kata menjadi KAPITAL (Title Case)
        return mb_convert_case($teks_clean, MB_CASE_TITLE, "UTF-8");
    }
}

function buat_nomor_surat_otomatis($kode_klasifikasi = "400.7.22.1") {
    global $koneksi;
    
    // Tahun dinamis mengikuti sistem berjalan
    $tahun = date('Y');
    
    // Master query gabungan untuk mengambil porsi angka nomor surat saja dari semua jenis surat
    $query_max = "
        SELECT MAX(CAST(split_part(nomor_surat, '/', 2) AS INTEGER)) as nomor_tertinggi
        FROM (
            SELECT nomor_surat FROM surat_sakit WHERE nomor_surat LIKE :tahun
            UNION ALL
            SELECT nomor_surat FROM surat_sehat WHERE nomor_surat LIKE :tahun
            UNION ALL
            SELECT nomor_surat FROM surat_kematian WHERE nomor_surat LIKE :tahun
            UNION ALL
            SELECT nomor_surat FROM surat_non_pelayanan WHERE nomor_surat LIKE :tahun
        ) AS gabungan_surat
    ";
    
    try {
        $stmt = $koneksi->prepare($query_max);
        $stmt->execute([':tahun' => "%/$tahun"]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nomor_terakhir = isset($row['nomor_tertinggi']) ? (int)$row['nomor_tertinggi'] : 0;
        $nomor_baru_angka = $nomor_terakhir + 1;
        
        $nomor_baru_format = str_pad($nomor_baru_angka, 4, "0", STR_PAD_LEFT);
        
        // Menggunakan parameter $kode_klasifikasi untuk bagian depan nomor surat
        $nomor_surat_final = $kode_klasifikasi . "/" . $nomor_baru_format . "/436.7.2.3.55/" . $tahun;
        
        return $nomor_surat_final;
        
    } catch (PDOException $e) {
        return $kode_klasifikasi . "/0001/436.7.2.3.55/" . $tahun;
    }
}
?>