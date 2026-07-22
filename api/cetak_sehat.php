<?php
include 'koneksi.php';

$nomor_raw = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';
$nomor_ambil = urldecode($nomor_raw);

if (empty($nomor_ambil)) { 
    die("<div style='padding:20px; font-family:Arial; color:red;'>Error: Parameter nomor surat tidak ditemukan di URL.</div>"); 
}

try {
    $query = "SELECT * FROM surat_sehat WHERE nomor_surat = :nomor";
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

$tanggal_sekarang = tgl_indo(date('Y-m-d'));

// --- KONVERSI LOGO KE BASE64 ---
$path_logo = __DIR__ . '/logo_surabaya.png';
if (!file_exists($path_logo)) {
    $path_logo = __DIR__ . '/../logo_surabaya.png';
}

if (file_exists($path_logo)) {
    $type_logo = pathinfo($path_logo, PATHINFO_EXTENSION);
    $data_logo = file_get_contents($path_logo);
    $src_logo = 'data:image/' . $type_logo . ';base64,' . base64_encode($data_logo);
} else {
    $src_logo = '/logo_surabaya.png';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Sehat - <?php echo htmlspecialchars($data['nama_pasien'] ?? '-'); ?></title>
    <style>
        body { font-family: "Times New Roman", Times, serif; margin: 30px 50px; color: #000; line-height: 1.4; }
        .header-container { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; border-bottom: 4px solid #000; padding-bottom: 5px; margin-bottom: 15px; min-height: 90px; }
        .logo-pemkot { position: absolute; left: 70px; top: 45%; transform: translateY(-50%); width: 85px; height: auto; display: block; }
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
        <img class="logo-pemkot" src="<?php echo $src_logo; ?>" alt="Logo Pemkot Surabaya">
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
        <h3>SURAT KETERANGAN SEHAT</h3>
        <p>Nomor : <?php echo htmlspecialchars($data['nomor_surat'] ?? '-'); ?></p>
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>
        <table class="table-data">
            <tr><td class="label-kolom">Nama Pasien</td><td>:</td><td><?php echo htmlspecialchars($data['nama_pasien'] ?? '-'); ?></td></tr>
            <tr><td class="label-kolom">Jenis Kelamin</td><td>:</td><td><?php echo htmlspecialchars($data['jenis_kelamin'] ?? '-'); ?></td></tr>
            <tr><td class="label-kolom">Umur</td><td>:</td><td><?php echo htmlspecialchars($data['umur'] ?? '-'); ?> Tahun</td></tr>
            <tr><td class="label-kolom">Pekerjaan</td><td>:</td><td><?php echo htmlspecialchars($data['pekerjaan'] ?? '-'); ?></td></tr>
            <tr><td class="label-kolom">Alamat</td><td>:</td><td><?php echo htmlspecialchars($data['alamat_domisili'] ?? '-'); ?></td></tr>
        </table>

        <div class="pemeriksaan">
            <p>Telah dilakukan pemeriksaan kesehatan dengan hasil:</p>
            <table class="table-data">
                <tr><td>Tensi</td><td>:</td><td><?php echo htmlspecialchars($data['tensi'] ?? '-'); ?> mmHg</td></tr>
                <tr><td>Nadi</td><td>:</td><td><?php echo htmlspecialchars($data['nadi'] ?? '-'); ?> x/menit</td></tr>
                <tr><td>Suhu</td><td>:</td><td><?php echo htmlspecialchars($data['suhu'] ?? '-'); ?> °C</td></tr>
                <tr><td>Berat Badan</td><td>:</td><td><?php echo htmlspecialchars($data['berat_badan'] ?? '-'); ?> kg</td></tr>
                <tr><td>Tinggi Badan</td><td>:</td><td><?php echo htmlspecialchars($data['tinggi_badan'] ?? '-'); ?> cm</td></tr>
                <tr><td>Golongan Darah</td><td>:</td><td><?php echo htmlspecialchars($data['gol_darah'] ?? '-'); ?></td></tr>
                <tr><td>Penglihatan</td><td>:</td><td><?php echo htmlspecialchars($data['visus_kanan'] ?? '-'); ?></td></tr>
                <tr><td>Buta Warna</td><td>:</td><td><?php echo htmlspecialchars($data['buta_warna'] ?? '-'); ?></td></tr>
                <tr><td>Keterangan</td><td>:</td><td><?php echo htmlspecialchars($data['visus_kiri'] ?? '-'); ?></td></tr>
            </table>
        </div>

        <p>Berdasarkan hasil pemeriksaan tersebut, yang bersangkutan dinyatakan dalam keadaan <strong>SEHAT</strong>. Surat keterangan ini dipergunakan untuk : <strong><?php echo htmlspecialchars($data['keperluan'] ?? '-'); ?></strong>.</p>
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer-container">
        <div class="qrcode-box">
            <?php 
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $domainName = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $link_verifikasi = $protocol . $domainName . dirname($_SERVER['PHP_SELF']) . '/verifikasi.php?jenis=sehat&nomor=' . urlencode($data['nomor_surat'] ?? '');
            ?>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=<?php echo urlencode($link_verifikasi); ?>" alt="QR Verification">
            <p style="font-size: 12px; font-weight: bold; margin-top: 5px;">E-Verifikasi Sah</p>
        </div>
        <div class="ttd-box">
            <p>Surabaya, <?php echo htmlspecialchars($tanggal_sekarang); ?></p>
            <p>Dokter Pemeriksa,</p>
            <div class="ttd-space"></div>
            <div class="ttd-container">
                <span class="nama-dokter"><?php echo htmlspecialchars($data['nama_dokter'] ?? '-'); ?></span>
                <span>SIP. <?php echo htmlspecialchars($data['sip_dokter'] ?? '-'); ?></span>
            </div>
        </div>
    </div>
</body>
</html>