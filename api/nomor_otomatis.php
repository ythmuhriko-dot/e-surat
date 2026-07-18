<?php
include 'koneksi.php';

// JALUR 1: SURAT PELAYANAN (MEDIS)
function buat_nomor_surat_otomatis($tahun_pilihan = null) {
    global $koneksi;
    if ($tahun_pilihan == null || empty($tahun_pilihan)) { 
        $tahun_pilihan = date('Y'); 
    } else { 
        $tahun_pilihan = date('Y', strtotime($tahun_pilihan)); 
    }
    
    // Menggunakan dialek PostgreSQL: SPLIT_PART dan RIGHT() diganti dengan LIKE / SUBSTRING
    $query = "
        SELECT MAX(CAST(SPLIT_PART(nomor_surat, '/', 2) AS INTEGER)) AS nomor_terbesar 
        FROM (
            SELECT nomor_surat FROM surat_sakit WHERE nomor_surat LIKE :tahun1
            UNION ALL
            SELECT nomor_surat FROM surat_sehat WHERE nomor_surat LIKE :tahun2
            UNION ALL
            SELECT nomor_surat FROM surat_kematian WHERE nomor_surat LIKE :tahun3
        ) AS gabungan_surat
    ";
    
    try {
        $stmt = $koneksi->prepare($query);
        $param_tahun = "%/" . $tahun_pilihan;
        $stmt->execute([
            ':tahun1' => $param_tahun,
            ':tahun2' => $param_tahun,
            ':tahun3' => $param_tahun
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nomor_urut = ($data && $data['nomor_terbesar'] != NULL) ? $data['nomor_terbesar'] + 1 : 1;
        return "400.7.22.1/" . str_pad($nomor_urut, 4, "0", STR_PAD_LEFT) . "/436.7.2.3.55/" . $tahun_pilihan;
    } catch (PDOException $e) {
        return "400.7.22.1/0001/436.7.2.3.55/" . $tahun_pilihan;
    }
}

// JALUR 2: SURAT NON-PELAYANAN (UMUM / ADMINISTRASI TATA USAHA)
function buat_nomor_non_pelayanan_otomatis($tahun_pilihan = null) {
    global $koneksi;
    if ($tahun_pilihan == null || empty($tahun_pilihan)) { 
        $tahun_pilihan = date('Y'); 
    } else { 
        $tahun_pilihan = date('Y', strtotime($tahun_pilihan)); 
    }
    
    $query = "SELECT MAX(CAST(SPLIT_PART(nomor_surat, '/', 2) AS INTEGER)) AS nomor_terbesar 
              FROM surat_non_pelayanan WHERE nomor_surat LIKE :tahun";
              
    try {
        $stmt = $koneksi->prepare($query);
        $stmt->execute([':tahun' => "%/" . $tahun_pilihan]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nomor_urut = ($data && $data['nomor_terbesar'] != NULL) ? $data['nomor_terbesar'] + 1 : 1;
        return "100.1.1.1/" . str_pad($nomor_urut, 4, "0", STR_PAD_LEFT) . "/436.7.2.3.55/" . $tahun_pilihan;
    } catch (PDOException $e) {
        return "100.1.1.1/0001/436.7.2.3.55/" . $tahun_pilihan;
    }
}
?>