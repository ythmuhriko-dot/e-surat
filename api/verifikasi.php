<?php
include 'koneksi.php';

$jenis = $_GET['jenis'] ?? '';
$nomor_raw = $_GET['nomor'] ?? '';
$nomor_ambil = urldecode($nomor_raw);

$data_surat = null;
$terdaftar = false;
$nama_surat_label = "Surat Keterangan";

if (!empty($nomor_ambil) && !empty($jenis)) {
    try {
        // Seleksi tabel database berdasarkan parameter jenis
        if ($jenis === 'sehat') {
            $table = "surat_sehat";
            $nama_surat_label = "Surat Keterangan Sehat";
        } elseif ($jenis === 'sakit') {
            $table = "surat_sakit";
            $nama_surat_label = "Surat Keterangan Sakit";
        } elseif ($jenis === 'kematian') {
            $table = "surat_kematian";
            $nama_surat_label = "Surat Keterangan Kematian";
        } else {
            die("<div style='padding:20px; font-family:Arial; color:red;'>Error: Jenis surat tidak valid.</div>");
        }

        $query = "SELECT * FROM $table WHERE nomor_surat = :nomor";
        $stmt = $koneksi->prepare($query);
        $stmt->execute([':nomor' => $nomor_ambil]);
        $data_surat = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data_surat) {
            $terdaftar = true;
        }
    } catch (PDOException $e) {
        die("<div style='padding:20px; font-family:Arial; color:red;'>Error Sistem: " . $e->getMessage() . "</div>");
    }
} else {
    die("<div style='padding:20px; font-family:Arial; color:red;'>Error: Parameter verifikasi tidak lengkap.</div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Verifikasi Sah - UPTD Puskesmas Bangkingan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 500px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .logo { width: 70px; height: auto; margin-bottom: 15px; }
        .kop { font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #2c3e50; }
        .instansi { font-size: 16px; font-weight: bold; margin-bottom: 20px; color: #16a085; text-transform: uppercase; }
        .status-box { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; font-size: 16px; }
        .asli { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .palsu { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info-table { width: 100%; text-align: left; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; font-size: 14px; }
        .info-table td { padding: 10px 4px; vertical-align: top; border-bottom: 1px solid #f1f1f1; }
        .info-table td.label { font-weight: bold; color: #666; width: 38%; }
        .notif-stempel { background-color: #fff3cd; color: #856404; border-left: 5px solid #ffc107; padding: 12px; text-align: left; font-size: 13px; border-radius: 4px; line-height: 1.5; }
    </style>
</head>
<body>

<div class="container">
    <img class="logo" src="logo_surabaya.png" alt="Logo Pemkot">
    <div class="kop">DINAS KESEHATAN KOTA SURABAYA</div>
    <div class="instansi">UPTD PUSKESMAS BANGKINGAN</div>

    <?php if ($terdaftar): ?>
        <div class="status-box asli">
            🟢 <?php echo strtoupper($nama_surat_label); ?> ASLI & VALID
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nomor Surat</td>
                <td>: <?php echo htmlspecialchars($data_surat['nomor_surat'] ?? '-'); ?></td>
            </tr>
            
            <?php if ($jenis === 'kematian'): ?>
                <tr>
                    <td class="label">Nama Jenazah</td>
                    <td>: <strong><?php echo htmlspecialchars($data_surat['nama_jenazah'] ?? '-'); ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Penyebab Kematian</td>
                    <td>: <?php echo htmlspecialchars($data_surat['penyebab'] ?? '-'); ?></td>
                </tr>
            <?php else: // Untuk Surat Sakit dan Sehat ?>
                <tr>
                    <td class="label">Nama Pasien</td>
                    <td>: <strong><?php echo htmlspecialchars($data_surat['nama_pasien'] ?? '-'); ?></strong></td>
                </tr>
            <?php endif; ?>

            <?php if ($jenis === 'sehat'): ?>
                <tr>
                    <td class="label">Keperluan</td>
                    <td>: <?php echo htmlspecialchars($data_surat['keperluan'] ?? '-'); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($jenis === 'sakit'): ?>
                <tr>
                    <td class="label">Lama Istirahat</td>
                    <td>: <?php echo htmlspecialchars($data_surat['lama_istirahat'] ?? '-'); ?> Hari</td>
                </tr>
            <?php endif; ?>

            <tr>
                <td class="label">Dokter Pemeriksa</td>
                <td>: <?php echo htmlspecialchars($data_surat['nama_dokter'] ?? '-'); ?></td>
            </tr>
        </table>

        <div class="notif-stempel">
            <strong>⚠️ Perhatian Keamanan berkas:</strong><br>
            Pastikan lembar cetak fisik dokumen ini dibawa ke Kantor Tata Usaha <strong>UPTD Puskesmas Bangkingan</strong> untuk dibubuhi <strong>stempel basah kedinasan resmi</strong> agar validasi fisik berkas Anda sempurna.
        </div>

    <?php else: ?>
        <div class="status-box palsu">
            🔴 DOKUMEN TIDAK VALID / PALSU
        </div>
        <p style="font-size: 14px; color: #666;">
            Data berkas dengan nomor <strong><?php echo htmlspecialchars($nomor_ambil); ?></strong> tidak ditemukan/tidak cocok dengan arsip data digital UPTD Puskesmas Bangkingan.
        </p>
    <?php endif; ?>
</div>

<!-- SCRIPT ALERT OTOMATIS SAAT DI-SCAN -->
<script>
    window.onload = function() {
        <?php if ($terdaftar): ?>
            alert("✅ Verifikasi Berhasil!\nDokumen ini ASLI & VALID terdaftar di UPTD Puskesmas Bangkingan.");
        <?php else: ?>
            alert("❌ Peringatan Keamanan!\nDokumen TIDAK VALID atau PALSU. Nomor surat tidak ditemukan dalam arsip digital.");
        <?php endif; ?>
    };
</script>

</body>
</html>