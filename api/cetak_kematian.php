<?php
include 'koneksi.php';

$nomor_raw = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';
$nomor_ambil = urldecode($nomor_raw);

if (empty($nomor_ambil)) { 
    die("<div style='padding:20px; font-family:Arial; color:red;'>Error: Parameter nomor surat tidak ditemukan di URL.</div>"); 
}

try {
    $query = "SELECT * FROM surat_kematian WHERE nomor_surat = :nomor";
    $stmt = $koneksi->prepare($query);
    $stmt->execute([':nomor' => $nomor_ambil]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<div style='padding:20px; font-family:Arial;'><h3>Data Tidak Ditemukan</h3>Surat dengan nomor <strong>" . htmlspecialchars($nomor_ambil) . "</strong> tidak terdaftar di database. <br><br><a href='index.php?menu=rekap'>Kembali ke Rekap</a></div>");
    }
} catch (PDOException $e) {
    die("<div style='padding:20px; font-family:Arial; color:red;'>Gagal mengambil data database: " . $e->getMessage() . "</div>");
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal){
        if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
        $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $pecahkan = explode('-', $tanggal);
        return ($pecahkan[2] ?? '') . ' ' . ($bulan[(int)($pecahkan[1] ?? 0)] ?? '') . ' ' . ($pecahkan[0] ?? '');
    }
}

$nomor_surat            = $data['nomor_surat'] ?? '-';
$nama_jenazah           = $data['nama_jenazah'] ?? '-';
$jenis_kelamin          = $data['jenis_kelamin'] ?? '-';
$umur                   = $data['umur'] ?? '-';
$alamat_jenazah         = $data['alamat_jenazah'] ?? '-';
$hari_meninggal         = $data['hari_meninggal'] ?? '-';
$tanggal_meninggal_indo = tgl_indo($data['tanggal_meninggal'] ?? '');
$jam_meninggal          = $data['jam_meninggal'] ?? '-';
$tempat_meninggal       = $data['tempat_meninggal'] ?? '-';
$penyebab               = $data['penyebab'] ?? '-';
$nama_pelapor           = $data['nama_pelapor'] ?? '-';
$hubungan_pelapor       = $data['hubungan_pelapor'] ?? '-';
$nama_dokter            = $data['nama_dokter'] ?? '-';
$sip_dokter             = $data['sip_dokter'] ?? '-';
$tanggal_sekarang       = tgl_indo(date('Y-m-d'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Kematian - <?php echo htmlspecialchars($nama_jenazah); ?></title>
    <style>
        body { font-family: "Times New Roman", Times, serif; margin: 30px 50px; color: #000; line-height: 1.4; }
        .header-container { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; border-bottom: 4px solid #000; padding-bottom: 5px; margin-bottom: 15px; min-height: 90px; }
        .logo-pemkot { position: absolute; left: 75px; top: 45%; transform: translateY(-50%); width: 85px; height: auto; display: block; }
        .kop-teks { text-align: center; width: 100%; margin-right: 0; margin-left: 0; }
        .kop-teks h2 { margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2; }
        .kop-teks h1 { margin: 0; font-size: 19px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.3; }
        .kop-teks p { margin: 1px 0; font-size: 11px; }
        .judul-surat { text-align: center; margin-top: 15px; margin-bottom: 15px; }
        .judul-surat h3 { margin: 0; font-size: 16px; text-transform: uppercase; text-decoration: underline; font-weight: bold; }
        .judul-surat p { margin: 3px 0 0 0; font-size: 14px; font-weight: bold; }
        .isi-surat { font-size: 15px; text-align: justify; }
        .isi-surat p { margin-bottom: 10px; text-indent: 45px; }
        .table-data { margin-left: 45px; margin-top: 5px; margin-bottom: 10px; font-size: 15px; border-collapse: collapse; }
        .table-data td { padding: 2px 5px; vertical-align: top; }
        .table-data td.label-kolom { width: 160px; }
        .footer-container { margin-top: 25px; display: flex; justify-content: space-between; align-items: flex-start; font-size: 15px; page-break-inside: avoid; }
        .ttd-box { text-align: left; width: 320px; margin-left: 100px; }
        .qrcode-box { text-align: center; margin-left: 50px; }
        .ttd-space { height: 60px; }
        .ttd-box p { margin: 0; line-height: 1.2; }
        .ttd-container { display: block; width: 100%; }
        .nama-dokter { font-weight: bold; text-decoration: underline; display: block; white-space: nowrap; }
        .tombol-aksi { margin-bottom: 20px; background: #e9ecef; padding: 10px; border-radius: 6px; border: 1px solid #ced4da; }
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 4px; font-family: Arial, sans-serif; font-size: 13px; display: inline-block; margin-right: 10px; font-weight: bold; border: none; cursor: pointer; }
        .btn-print { background: #007bff; }
        .btn-kembali { background: #6c757d; }
        @media print { .tombol-aksi { display: none; } body { margin: 5px 15px; } }
    </style>
</head>
<body>
    <div class="tombol-aksi">
        <button onclick="window.print();" class="btn btn-print">🖨️ Cetak Surat ke PDF / Kertas</button>
        <a href="index.php?menu=rekap" class="btn btn-kembali">🏠 Kembali</a>
    </div>

    <div class="header-container">
        <img class="logo-pemkot" src="logo_surabaya.png" alt="Logo Pemkot Surabaya">
        <div class="kop-teks">
            <h2>PEMERINTAH KOTA SURABAYA</h2>
            <h2>DINAS KESEHATAN</h2>
            <h1>UPTD PUSKESMAS BANGKINGAN</h1>
            <p>Jl. Bangkingan Pesarean No. 3-4 Surabaya 60214</p>
            <p>Telp. (031) 7665218</p>
            <p>Surabaya.go.id, Pos-el : pkmbangkingan@gmail.com</p>
        </div>
    </div>

    <div class="judul-surat">
        <h3>SURAT KETERANGAN KEMATIAN</h3>
        <p>Nomor : <?php echo htmlspecialchars($nomor_surat); ?></p>
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>
        <table class="table-data">
            <tr><td class="label-kolom">Nama Jenazah</td><td>:</td><td><strong><?php echo htmlspecialchars(strtoupper($nama_jenazah)); ?></strong></td></tr>
            <tr><td class="label-kolom">Jenis Kelamin</td><td>:</td><td><?php echo htmlspecialchars($jenis_kelamin); ?></td></tr>
            <tr><td class="label-kolom">Umur</td><td>:</td><td><?php echo htmlspecialchars($umur); ?></td></tr>
            <tr><td class="label-kolom">Alamat Domisili</td><td>:</td><td><?php echo htmlspecialchars($alamat_jenazah); ?></td></tr>
        </table>
        
        <p>Telah dinyatakan meninggal dunia pada :</p>
        <table class="table-data">
            <tr><td class="label-kolom">Hari / Tanggal</td><td>:</td><td><?php echo htmlspecialchars($hari_meninggal); ?>, <?php echo htmlspecialchars($tanggal_meninggal_indo); ?></td></tr>
            <tr><td class="label-kolom">Jam / Waktu</td><td>:</td><td><?php echo htmlspecialchars($jam_meninggal); ?></td></tr>
            <tr><td class="label-kolom">Tempat Meninggal</td><td>:</td><td><?php echo htmlspecialchars($tempat_meninggal); ?></td></tr>
            <tr><td class="label-kolom">Penyebab</td><td>:</td><td><?php echo htmlspecialchars($penyebab); ?></td></tr>
        </table>

        <p>Berdasarkan laporan yang disampaikan oleh pihak keluarga/ahli waris di bawah ini :</p>
        <table class="table-data">
            <tr><td class="label-kolom">Nama Pelapor</td><td>:</td><td><?php echo htmlspecialchars($nama_pelapor); ?></td></tr>
            <tr><td class="label-kolom">Hubungan Keluarga</td><td>:</td><td><?php echo htmlspecialchars($hubungan_pelapor); ?></td></tr>
        </table>
        
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer-container">
        <div class="qrcode-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?php echo urlencode('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI']); ?>" alt="QR">
            <p>E-Verifikasi Sah</p>
        </div>
        <div class="ttd-box">
            <p>Surabaya, <?php echo htmlspecialchars($tanggal_sekarang); ?></p>
            <p>Dokter Pemeriksa,</p>
            <div class="ttd-space"></div>
            <div class="ttd-container">
                <span class="nama-dokter"><?php echo htmlspecialchars($nama_dokter); ?></span>
                <span>SIP. <?php echo htmlspecialchars($sip_dokter); ?></span>
            </div>
        </div>
    </div>
</body>
</html>