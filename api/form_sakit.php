<?php 
include 'nomor_otomatis.php'; 
$nomor_surat_baru = buat_nomor_surat_otomatis();
?>
<?php
// Menyertakan file koneksi database di bagian paling atas
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
    <title>Input Surat Keterangan Sakit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: Arial, sans-serif; }
        button { background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #218838; }
        .back-link { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <div class="form-container">
        <a href="index.php?menu=rekap" class="back-link">&larr; Kembali ke Menu</a>
        <h2>Form Surat Keterangan Sakit</h2>
        
        <form action="simpan_sakit.php" method="POST">
            <div class="form-group">
                <label>Nomor Surat</label>
                <input type="text" name="nomor_surat" value="<?php echo htmlspecialchars($nomor_surat_baru); ?>" readonly class="form-control">
            </div>
            <div class="form-group">
                <label>Nama Pasien</label>
                <input type="text" name="nama_pasien" required>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="Laki-laki">Laki-Laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Umur (Tahun)</label>
                <input type="number" name="umur" required>
            </div>
            <div class="form-group">
                <label>Alamat Domisili</label>
                <textarea name="alamat_domisili" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" required>
            </div>
            <div class="form-group">
                <label>Lama Istirahat (Sebutkan, misal: 3 (tiga) hari)</label>
                <select name="lama_istirahat_teks" required>
                    <option value="1 Hari">1 hari</option>
                    <option value="2 Hari">2 Hari</option>
                    <option value="3 Hari">3 Hari</option>
                </select>
            </div>
            <div class="form-group">
                <label>Lama Istirahat (Angka Hari untuk Hitung Otomatis)</label>
                <input type="number" name="lama_istirahat_angka" placeholder="Contoh: 3" required>
            </div>
            <div class="form-group">
                <label>Tanggal Mulai Istirahat</label>
                <input type="date" name="tanggal_mulai" required>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">
            
            <div class="form-group">
                <label>Nama Dokter Pemeriksa</label>
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
            
            <button type="submit">Simpan & Cetak Surat</button>
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