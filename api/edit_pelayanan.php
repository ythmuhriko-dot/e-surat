<?php
// include 'koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

// Data Dummy untuk simulasi Surat Pelayanan
$data = [
    'tanggal_surat' => '2026-07-06',
    'nomor_surat'   => '400.7.22.1/0006/436.7.2.3.55/2026',
    'jenis_surat'   => 'Surat Sehat',
    'nama_pasien'   => 'Deni Prananata',
    'nama_dokter'   => 'dr. Dhedhy Tryantono',
    'keterangan'    => 'Tekanan Darah: 120/80 mmHg, Kondisi: Sehat Fisik.'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_surat = $_POST['tanggal_surat'];
    $nomor_surat   = $_POST['nomor_surat'];
    $jenis_surat   = $_POST['jenis_surat'];
    $nama_pasien   = $_POST['nama_pasien'];
    $nama_dokter   = $_POST['nama_dokter'];
    $keterangan    = $_POST['keterangan'];

    /*
    $update = mysqli_query($koneksi, "UPDATE surat_pelayanan SET 
        tanggal_surat='$tanggal_surat', nomor_surat='$nomor_surat', jenis_surat='$jenis_surat', 
        nama_pasien='$nama_pasien', nama_dokter='$nama_dokter', keterangan='$keterangan' WHERE id='$id'");
    */
    echo "<script>alert('Data Pelayanan Berhasil Diperbarui!'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Surat Pelayanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px 20px; }
        .form-container { max-width: 600px; margin: auto; background: white; padding: 35px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        h2 { margin-bottom: 25px; color: #1e293b; font-weight: 700; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        .btn-submit { background: #3b82f6; color: white; border: none; padding: 14px 20px; border-radius: 8px; font-weight: 600; width: 100%; cursor: pointer; font-size: 15px; }
        .btn-submit:hover { background: #1d4ed8; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-container">
    <a href="index.php?menu=rekap" class="back-link">&larr; Batal & Kembali</a>
    <h2>🏥 Edit Surat Pelayanan (Klinis)</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>Tanggal Surat:</label>
            <input type="date" name="tanggal_surat" value="<?php echo $data['tanggal_surat']; ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Nomor Surat:</label>
            <input type="text" name="nomor_surat" value="<?php echo $data['nomor_surat']; ?>" class="form-control" style="font-weight: 600;">
        </div>

        <div class="form-group">
            <label>Jenis Surat Pelayanan:</label>
            <select name="jenis_surat" required class="form-control">
                <option value="Surat Sehat" <?php echo ($data['jenis_surat'] == 'Surat Sehat') ? 'selected' : ''; ?>>Surat Sehat</option>
                <option value="Surat Sakit" <?php echo ($data['jenis_surat'] == 'Surat Sakit') ? 'selected' : ''; ?>>Surat Sakit</option>
                <option value="Surat Kematian" <?php echo ($data['jenis_surat'] == 'Surat Kematian') ? 'selected' : ''; ?>>Surat Kematian</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Pasien / Keperluan:</label>
            <input type="text" name="nama_pasien" value="<?php echo $data['nama_pasien']; ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label>Nama Dokter / PJ Pemeriksa:</label>
            <input type="text" name="nama_dokter" value="<?php echo $data['nama_dokter']; ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label>Keterangan Klinis:</label>
            <textarea name="keterangan" class="form-control" rows="4"><?php echo $data['keterangan']; ?></textarea>
        </div>

        <button type="submit" class="btn-submit">💾 Perbarui Data Pelayanan</button>
    </form>
</div>

</body>
</html>