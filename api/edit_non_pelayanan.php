<?php
// Koneksi ke database (sesuaikan dengan file koneksi Anda)
// include 'koneksi.php'; 

// Simulasi mengambil ID dari URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// ─── CONTOH QUERY AMBIL DATA LAMA (Silakan aktifkan jika koneksi database sudah ada) ───
/*
$query = mysqli_query($koneksi, "SELECT * FROM surat_non_pelayanan WHERE id = '$id'");
$data  = mysqli_fetch_assoc($query);
*/

// Data Dummy untuk simulasi tampilan form agar tidak error saat dicoba
$data = [
    'tanggal_surat'    => '2026-07-06',
    'nomor_surat'      => '400.7.2.13/0001/436.7.2.3.55/2026',
    'kode_klasifikasi' => '400.7.2.13',
    'perihal'          => 'Undangan Koordinasi Internal Restrukturisasi Jaringan',
    'pj_surat'         => 'Riko (Tata Usaha)',
    'keterangan'       => 'Rapat bertempat di Aula Puskesmas Bangkingan pukul 09.00 WIB.',
    'file_surat'       => 'surat_undangan.pdf'
];

// Proses Update ketika tombol simpan diklik
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari inputan form
    $tanggal_surat    = $_POST['tanggal_surat'];
    $nomor_surat      = $_POST['nomor_surat'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $perihal          = $_POST['perihal'];
    $pj_surat         = $_POST['pj_surat'];
    $keterangan       = $_POST['keterangan'];
    
    // Logika upload file baru jika ada
    if ($_FILES['file_surat']['name'] != '') {
        $file_name = $_FILES['file_surat']['name'];
        $tmp_name  = $_FILES['file_surat']['tmp_name'];
        move_uploaded_path($tmp_name, "uploads/" . $file_name);
    } else {
        $file_name = $_POST['file_lama']; // Pakai file lama jika tidak upload baru
    }

    /* 
    // Jalankan query update ke database
    $update = mysqli_query($koneksi, "UPDATE surat_non_pelayanan SET 
        tanggal_surat='$tanggal_surat', nomor_surat='$nomor_surat', kode_klasifikasi='$kode_klasifikasi', 
        perihal='$perihal', pj_surat='$pj_surat', keterangan='$keterangan', file_surat='$file_name' WHERE id='$id'");
    
    if($update) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='index.php';</script>";
    }
    */
    echo "<script>alert('Simpan Berhasil (Mode Simulasi)!'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Surat Non Pelayanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 40px 20px; }
        .form-container { max-width: 600px; margin: auto; background: white; padding: 35px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        h2 { margin-bottom: 25px; color: #1e293b; font-weight: 700; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        .btn-submit { background: #d97706; color: white; border: none; padding: 14px 20px; border-radius: 8px; font-weight: 600; width: 100%; cursor: pointer; font-size: 15px; }
        .btn-submit:hover { background: #b45309; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #64748b; font-size: 14px; }
        .select2-container--default .select2-selection--single { height: 45px !important; padding: 8px 6px; border: 1px solid #cbd5e1 !important; border-radius: 8px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }
        .select2-container { width: 100% !important; }
    </style>
</head>
<body>

<div class="form-container">
    <a href="index.php" class="back-link">&larr; Batal & Kembali</a>
    <h2>📝 Edit Surat Non-Pelayanan (Umum)</h2>

    <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 12px; border-radius: 8px; margin-bottom: 25px;">
        <label style="color: #856404; display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
            <input type="checkbox" id="switch_backdate"> ⚠️ Aktifkan Mode Edit Nomor Manual (Backdate)
        </label>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="file_lama" value="<?php echo $data['file_surat']; ?>">

        <div class="form-group">
            <label>Tanggal Surat:</label>
            <input type="date" name="tanggal_surat" id="tanggal_surat" value="<?php echo $data['tanggal_surat']; ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Nomor Surat:</label>
            <input type="text" name="nomor_surat" id="nomor_surat" value="<?php echo $data['nomor_surat']; ?>" readonly class="form-control" style="font-weight: 600; background-color: #f1f5f9;">
        </div>

        <div class="form-group">
            <label>Kode Klasifikasi Surat:</label>
            <select name="kode_klasifikasi" id="kode_klasifikasi" required class="form-control">
                <option value="400.7.2.13" <?php echo ($data['kode_klasifikasi'] == '400.7.2.13') ? 'selected' : ''; ?>>400.7.2.13 Akreditasi puskesmas</option>
                <option value="400.7.14.4" <?php echo ($data['kode_klasifikasi'] == '400.7.14.4') ? 'selected' : ''; ?>>400.7.14.4 Keluarga berencana</option>
                <option value="400.7.31" <?php echo ($data['kode_klasifikasi'] == '400.7.31') ? 'selected' : ''; ?>>400.7.31 Rekam Medis</option>
                <!-- Silakan tambahkan opsi kode klasifikasi lengkap lainnya di sini -->
            </select>
        </div>

        <div class="form-group">
            <label>Perihal Surat:</label>
            <input type="text" name="perihal" value="<?php echo $data['perihal']; ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label>PJ Surat (Penanggung Jawab / Pengirim):</label>
            <input type="text" name="pj_surat" value="<?php echo $data['pj_surat']; ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label>Keterangan:</label>
            <textarea name="keterangan" class="form-control" rows="3"><?php echo $data['keterangan']; ?></textarea>
        </div>

        <div class="form-group">
            <label>File Surat Saat Ini:</label>
            <p style="font-size: 13px; color: #0284c7;"><a href="uploads/<?php echo $data['file_surat']; ?>" target="_blank">📄 <?php echo $data['file_surat']; ?></a></p>
            <label style="margin-top: 10px;">Ganti File Baru (Kosongkan jika tidak ingin diubah):</label>
            <input type="file" name="file_surat" class="form-control" accept="application/pdf, image/*">
        </div>

        <button type="submit" class="btn-submit">💾 Perbarui Data Surat</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#kode_klasifikasi').select2();

    // Otomatis ubah kode depan nomor surat saat dropdown diganti (hanya jika mode backdate mati)
    $('#kode_klasifikasi').on('change', function() {
        var kodeBaru = $(this).val();
        var inputNomor = $('#nomor_surat');
        var switchBackdate = $('#switch_backdate');

        if (!switchBackdate.is(':checked') && kodeBaru !== "") {
            var nomorSekarang = inputNomor.val();
            var bagianNomor = nomorSekarang.split('/');
            if (bagianNomor.length >= 4) {
                bagianNomor[0] = kodeBaru;
                inputNomor.val(bagianNomor.join('/'));
            }
        }
    });
});

document.getElementById('switch_backdate').addEventListener('change', function() {
    var inputNomor = document.getElementById('nomor_surat');
    if (this.checked) {
        inputNomor.removeAttribute('readonly');
        inputNomor.style.backgroundColor = '#fff3cd';
    } else {
        inputNomor.setAttribute('readonly', 'readonly');
        inputNomor.style.backgroundColor = '#f1f5f9';
    }
});
</script>
</body>
</html>