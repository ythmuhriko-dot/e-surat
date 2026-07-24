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
    // Simpan ke array agar bisa di-loop berkali-kali tanpa masalah pointer database
    $data_dokter = $stmt_dokter->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal memuat data dokter: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Surat Keterangan Sehat</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 550px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: Arial, sans-serif; }
        .row-flex { display: flex; gap: 10px; }
        .row-flex .form-group { flex: 1; }
        button { background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #0056b3; }
        .back-link { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <div class="form-container">
        <a href="index.php?menu=rekap" class="back-link">&larr; Kembali ke Menu</a>
        <h2>Form Surat Keterangan Sehat</h2>
        
        <form action="simpan_sehat.php" method="POST">
            <div class="form-group">
                <label>Nomor Surat Kesembuhan/Sehat</label>
                <input type="text" name="nomor_surat" value="<?php echo htmlspecialchars($nomor_surat_baru); ?>" readonly class="form-control">
            </div>
            <div class="form-group">
                <label>Nama Lengkap Pasien</label>
                <input type="text" name="nama_pasien" required>
            </div>
            <div class="row-flex">
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
            </div>
            <div class="form-group">
                <label>Pekerjaan / Status</label>
                <input type="text" name="pekerjaan" placeholder="Contoh: Swasta / Pelajar" required>
            </div>
            <div class="form-group">
                <label>Alamat Domisili</label>
                <textarea name="alamat_domisili" rows="2" required></textarea>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">
            
            <div class="row-flex">
                <div class="form-group">
                    <label>Tekanan Darah (mmHg)</label>
                    <input type="text" name="tensi" placeholder="120/80" required>
                </div>
                <div class="form-group">
                    <label>Nadi (x/menit)</label>
                    <input type="number" name="nadi" placeholder="80" required>
                </div>
                <div class="form-group">
                    <label>Suhu (°C)</label>
                    <input type="text" name="suhu" placeholder="36.5" required>
                </div>
            </div>

            <div class="row-flex">
                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" name="berat_badan" placeholder="65" required>
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi_badan" placeholder="170" required>
                </div>
                <div class="form-group">
                    <label>Golongan Darah</label>
                    <select name="gol_darah" required>
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
            </div>

            <div class="row-flex">
                <div class="form-group">
                    <label>Penglihatan</label>
                    <select name="visus_kanan" required>
                        <option value="-">-</option>
                        <option value="DBN-BERKACAMATA"> DBN - Berkacamata</option>
                        <option value="DBN-TIDAK BERKACAMATA">DBN - Tidak Berkacamata</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Buta Warna</label>
                    <select name="buta_warna" required>
                        <option value="-">-</option>
                        <option value="Tidak Buta Warna">Tidak Buta Warna (Normal)</option>
                        <option value="Buta Warna Parsial">Buta Warna Parsial</option>
                        <option value="Buta Warna Total">Buta Warna Total</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="visus_kiri" placeholder="Perlu Dirujuk/Berobat Lanjut" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Keperluan Surat</label>
                <input type="text" name="keperluan" placeholder="Contoh: Persyaratan Melamar Pekerjaan" required>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">
            
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
            
            <button type="submit">Simpan & Cetak Surat Sehat</button>
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