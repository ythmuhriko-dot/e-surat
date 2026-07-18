<?php
include 'koneksi.php';

function buat_nomor_surat_otomatis() {
    global $koneksi;
    
    // Tahun dinamis mengikuti sistem berjalan
    $tahun = date('Y');
    
    // Master query gabungan untuk mengambil porsi angka nomor surat saja dari semua jenis surat
    // split_part(nomor_surat, '/', 2) memotong string berdasarkan '/' dan mengambil bagian kedua (counter nomor)
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
        // Memastikan pencarian counter nomor di-filter hanya untuk tahun berjalan
        $stmt->execute([':tahun' => "%/$tahun"]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Jika data ditemukan, ambil nomor tertinggi lalu tambah 1. Jika kosong, mulai dari 1.
        $nomor_terakhir = isset($row['nomor_tertinggi']) ? (int)$row['nomor_tertinggi'] : 0;
        $nomor_baru_angka = $nomor_terakhir + 1;
        
        // Format angka dengan padding 4 digit (misal: 1 -> 0001, 1873 -> 1873)
        $nomor_baru_format = str_pad($nomor_baru_angka, 4, "0", STR_PAD_LEFT);
        
        // Susun kembali pola instansi Puskesmas Bangkingan secara utuh
        $nomor_surat_final = "400.7.22.1/" . $nomor_baru_format . "/436.7.2.3.55/" . $tahun;
        
        return $nomor_surat_final;
        
    } catch (PDOException $e) {
        // Jika database bermasalah, kembalikan format default agar aplikasi tidak crash
        return "400.7.22.1/0001/436.7.2.3.55/" . $tahun;
    }
}
?>