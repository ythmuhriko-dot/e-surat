<?php 
include 'nomor_otomatis.php'; 
// Kirim kode klasifikasi khusus untuk kematian
$nomor_surat_baru = buat_nomor_surat_otomatis("400.12.3.1"); 
?>
<?php
include 'koneksi.php';

try {
    // Mengambil data dokter dari database menggunakan PDO
    $query_dokter = "SELECT * FROM dokter ORDER BY nama_dokter ASC";
    $stmt_dokter = $koneksi->prepare($query_dokter);
    $stmt_dokter->execute();
    $data_dokter = $stmt_dokter->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal memuat data dokter: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Surat Keterangan Kematian</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 600px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: Arial, sans-serif; }
        .row-flex { display: flex; gap: 10px; }
        .row-flex .form-group { flex: 1; }
        .section-title { margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #007bff; color: #007bff; font-size: 16px; font-weight: bold; }
        button { background: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #bd2130; }
        .back-link { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <div class="form-container">
        <a href="index.php?menu=rekap" class="back-link">&larr; Kembali ke Menu</a>
        <h2>Form Surat Keterangan Kematian</h2>
        
        <form action="simpan_kematian.php" method="POST">
            <div class="form-group">
                <label>Nomor Surat Kematian</label>
                <input type="text" name="nomor_surat" value="<?php echo htmlspecialchars($nomor_surat_baru); ?>" readonly class="form-control">
            </div>
            <div class="section-title">Data Jenazah (Mendiang)</div>
            <div class="form-group">
                <label>Nama Lengkap Jenazah</label>
                <input type="text" name="nama_jenazah" required>
            </div>
            <div class="row-flex">
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Umur saat Meninggal</label>
                    <input type="text" name="umur" placeholder="Contoh: 65 Tahun / 3 Bulan" required>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat Rumah / Domisili Mendiang</label>
                <textarea name="alamat_jenazah" rows="2" required></textarea>
            </div>

            <div class="section-title">Waktu & Tempat Kejadian</div>
            <div class="row-flex">
                <div class="form-group">
                    <label>Hari Kematian</label>
                    <select name="hari_meninggal" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Kematian</label>
                    <input type="date" name="tanggal_meninggal" required>
                </div>
                <div class="form-group">
                    <label>Jam Kematian</label>
                    <input type="text" name="jam_meninggal" placeholder="Contoh: 08:30 WIB" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tempat Meninggal</label>
                <input type="text" name="tempat_meninggal" placeholder="Contoh: Puskesmas Bangkingan / Rumah Duka" required>
            </div>
            <div class="form-group">
                <label>Penyebab Kematian (Kondisi Medis/Penyakit)</label>
                <input type="text" name="penyebab" placeholder="Contoh: Gagal Jantung Kronis / Sakit Tua" required>
            </div>

            <div class="section-title">Data Pelapor (Keluarga/Ahli Waris)</div>
            <div class="form-group">
                <label>Nama Lengkap Pelapor</label>
                <input type="text" name="nama_pelapor" required>
            </div>
            <div class="form-group">
                <label>Hubungan dengan Jenazah</label>
                <input type="text" name="hubungan_pelapor" placeholder="Contoh: Anak Kandung / Istri / Suami" required>
            </div>

            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">
            
            <div class="form-group">
                <label>Nama Dokter Pemeriksa / Yang Menyatakan</label>
                <select name="nama_dokter" id="nama_dokter" onchange="updateSip()" required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php 
                    foreach ($data_dokter as $row) {
                        echo "<option value='".htmlspecialchars($row['nama_dokter'])."' data-sip='".htmlspecialchars($row['sip_dokter'])."'>".htmlspecialchars($row['nama_dokter'])."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>SIP Dokter</label>
                <select name="sip_dokter" id="sip_dokter" required>
                    <option value="">-- Pilih SIP --</option>
                    <?php
                    foreach ($data_dokter as $row) {
                        echo "<option value='".htmlspecialchars($row['sip_dokter'])."'>".htmlspecialchars($row['sip_dokter'])."</option>";
                    }
                    ?>
                </select>
            </div>
            
            <button type="submit">Buka & Cetak Surat Kematian</button>
        </form>
    </div>

    <script>
    function updateSip() {
        var dokterSelect = document.getElementById("nama_dokter");
        var sipSelect = document.getElementById("sip_dokter");
        
        var selectedOption = dokterSelect.options[dokterSelect.selectedIndex];
        var sipValue = selectedOption.getAttribute("data-sip");
        
        if(sipValue) {
            sipSelect.value = sipValue;
        } else {
            sipSelect.value = "";
        }
    }
    </script>
</body>
</html>