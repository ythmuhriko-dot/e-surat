<?php
include 'koneksi.php';

// 1. Ambil data dengan aman
$nomor_ambil = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';[cite: 6]
if (empty($nomor_ambil)) { die("Error: Parameter nomor surat tidak ditemukan."); }[cite: 6]

try {
    // Menggunakan Prepared Statement PDO
    $query = "SELECT * FROM surat_kematian WHERE nomor_surat = :nomor";
    $stmt = $koneksi->prepare($query);
    $stmt->execute([':nomor' => $nomor_ambil]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data surat dengan nomor <strong>" . htmlspecialchars($nomor_ambil) . "</strong> tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal mengambil data database: " . $e->getMessage());
}

// 2. Fungsi Tanggal Indonesia (Proteksi Redeclaration)
if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal){
        if (empty($tanggal) || $tanggal == '0000-00-00') return '-';[cite: 6]
        $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');[cite: 6]
        $pecahkan = explode('-', $tanggal);[cite: 6]
        return ($pecahkan[2] ?? '') . ' ' . ($bulan[(int)($pecahkan[1] ?? 0)] ?? '') . ' ' . ($pecahkan[0] ?? '');[cite: 6]
    }
}

// 3. Mapping Variabel
$nomor_surat            = $data['nomor_surat'] ?? '-';[cite: 6]
$nama_jenazah           = $data['nama_jenazah'] ?? '-';[cite: 6]
$jenis_kelamin          = $data['jenis_kelamin'] ?? '-';[cite: 6]
$umur                   = $data['umur'] ?? '-';[cite: 6]
$alamat_jenazah         = $data['alamat_jenazah'] ?? '-';[cite: 6]
$hari_meninggal         = $data['hari_meninggal'] ?? '-';[cite: 6]
$tanggal_meninggal_indo = tgl_indo($data['tanggal_meninggal'] ?? '');[cite: 6]
$jam_meninggal          = $data['jam_meninggal'] ?? '-';[cite: 6]
$tempat_meninggal       = $data['tempat_meninggal'] ?? '-';[cite: 6]
$penyebab               = $data['penyebab'] ?? '-';[cite: 6]
$nama_pelapor           = $data['nama_pelapor'] ?? '-';[cite: 6]
$hubungan_pelapor       = $data['hubungan_pelapor'] ?? '-';[cite: 6]
$nama_dokter            = $data['nama_dokter'] ?? '-';[cite: 6]
$sip_dokter             = $data['sip_dokter'] ?? '-';[cite: 6]
$tanggal_sekarang       = tgl_indo(date('Y-m-d'));[cite: 6]
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Kematian - <?php echo htmlspecialchars($nama_jenazah); ?></title>
    <style>
        body { font-family: "Times New Roman", Times, serif; margin: 30px 50px; color: #000; line-height: 1.4; }[cite: 6]
        .header-container { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; border-bottom: 4px solid #000; padding-bottom: 5px; margin-bottom: 15px; min-height: 90px; }[cite: 6]
        .logo-pemkot { position: absolute; left: 75px; top: 45%; transform: translateY(-50%); width: 85px; height: auto; display: block; }[cite: 6]
        .kop-teks { text-align: center; width: 100%; margin-right: 0; margin-left: 0; }[cite: 6]
        .kop-teks h2 { margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2; }[cite: 6]
        .kop-teks h1 { margin: 0; font-size: 19px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.3; }[cite: 6]
        .kop-teks p { margin: 1px 0; font-size: 11px; }[cite: 6]
        .judul-surat { text-align: center; margin-top: 15px; margin-bottom: 15px; }[cite: 6]
        .judul-surat h3 { margin: 0; font-size: 16px; text-transform: uppercase; text-decoration: underline; font-weight: bold; }[cite: 6]
        .judul-surat p { margin: 3px 0 0 0; font-size: 14px; font-weight: bold; }[cite: 6]
        .isi-surat { font-size: 15px; text-align: justify; }[cite: 6]
        .isi-surat p { margin-bottom: 10px; text-indent: 45px; }[cite: 6]
        .table-data { margin-left: 45px; margin-top: 5px; margin-bottom: 10px; font-size: 15px; border-collapse: collapse; }[cite: 6]
        .table-data td { padding: 2px 5px; vertical-align: top; }[cite: 6]
        .table-data td.label-kolom { width: 160px; }[cite: 6]
        .footer-container { margin-top: 25px; display: flex; justify-content: space-between; align-items: flex-start; font-size: 15px; page-break-inside: avoid; }[cite: 6]
        .ttd-box { text-align: left; width: 320px; margin-left: 100px; }[cite: 6]
        .qrcode-box { text-align: center; margin-left: 50px; }[cite: 6]
        .ttd-space { height: 60px; }[cite: 6]
        .ttd-box p { margin: 0; line-height: 1.2; }[cite: 6]
        .ttd-container { display: block; width: 100%; }[cite: 6]
        .nama-dokter { font-weight: bold; text-decoration: underline; display: block; white-space: nowrap; }[cite: 6]
        .tombol-aksi { margin-bottom: 20px; background: #e9ecef; padding: 10px; border-radius: 6px; border: 1px solid #ced4da; }[cite: 6]
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 4px; font-family: Arial, sans-serif; font-size: 13px; display: inline-block; margin-right: 10px; font-weight: bold; border: none; cursor: pointer; }[cite: 6]
        .btn-print { background: #007bff; }[cite: 6]
        .btn-kembali { background: #6c757d; }
        @media print { .tombol-aksi { display: none; } body { margin: 5px 15px; } }[cite: 6]
    </style>
</head>
<body>
    <div class="tombol-aksi">
        <button onclick="window.print();" class="btn btn-print">🖨️ Cetak Surat ke PDF / Kertas</button>[cite: 6]
        <a href="index.php?menu=rekap" class="btn btn-kembali">🏠 Kembali</a>[cite: 6]
    </div>

    <div class="header-container">
        <img class="logo-pemkot" src="logo_surabaya.png" alt="Logo Pemkot Surabaya">[cite: 6]
        <div class="kop-teks">
            <h2>PEMERINTAH KOTA SURABAYA</h2>[cite: 6]
            <h2>DINAS KESEHATAN</h2>[cite: 6]
            <h1>UPTD PUSKESMAS BANGKINGAN</h1>[cite: 6]
            <p>Jl. Bangkingan Pesarean No. 3-4 Surabaya 60214</p>[cite: 6]
            <p>Telp. (031) 7665218</p>[cite: 6]
            <p>Surabaya.go.id, Pos-el : pkmbangkingan@gmail.com</p>[cite: 6]
        </div>
    </div>

    <div class="judul-surat">
        <h3>SURAT KETERANGAN KEMATIAN</h3>[cite: 6]
        <p>Nomor : <?php echo htmlspecialchars($nomor_surat); ?></p>[cite: 6]
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>[cite: 6]
        <table class="table-data">
            <tr><td class="label-kolom">Nama Jenazah</td><td>:</td><td><strong><?php echo htmlspecialchars(strtoupper($nama_jenazah)); ?></strong></td></tr>[cite: 6]
            <tr><td class="label-kolom">Jenis Kelamin</td><td>:</td><td><?php echo htmlspecialchars($jenis_kelamin); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Umur</td><td>:</td><td><?php echo htmlspecialchars($umur); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Alamat Domisili</td><td>:</td><td><?php echo htmlspecialchars($alamat_jenazah); ?></td></tr>[cite: 6]
        </table>
        
        <p>Telah dinyatakan meninggal dunia pada :</p>[cite: 6]
        <table class="table-data">
            <tr><td class="label-kolom">Hari / Tanggal</td><td>:</td><td><?php echo htmlspecialchars($hari_meninggal); ?>, <?php echo htmlspecialchars($tanggal_meninggal_indo); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Jam / Waktu</td><td>:</td><td><?php echo htmlspecialchars($jam_meninggal); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Tempat Meninggal</td><td>:</td><td><?php echo htmlspecialchars($tempat_meninggal); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Penyebab</td><td>:</td><td><?php echo htmlspecialchars($penyebab); ?></td></tr>[cite: 6]
        </table>

        <p>Berdasarkan laporan yang disampaikan oleh pihak keluarga/ahli waris di bawah ini :</p>[cite: 6]
        <table class="table-data">
            <tr><td class="label-kolom">Nama Pelapor</td><td>:</td><td><?php echo htmlspecialchars($nama_pelapor); ?></td></tr>[cite: 6]
            <tr><td class="label-kolom">Hubungan Keluarga</td><td>:</td><td><?php echo htmlspecialchars($hubungan_pelapor); ?></td></tr>[cite: 6]
        </table>
        
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>[cite: 6]
    </div>

    <div class="footer-container">
        <div class="qrcode-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=http://localhost/e-surat/cetak_kematian.php?nomor=<?php echo urlencode($nomor_surat); ?>" alt="QR">[cite: 6]
            <p>E-Verifikasi Sah</p>[cite: 6]
        </div>
        <div class="ttd-box">
            <p>Surabaya, <?php echo htmlspecialchars($tanggal_sekarang); ?></p>[cite: 6]
            <p>Dokter Pemeriksa,</p>[cite: 6]
            <div class="ttd-space"></div>[cite: 6]
            <div class="ttd-container">
                <span class="nama-dokter"><?php echo htmlspecialchars($nama_dokter); ?></span>[cite: 6]
                <span>SIP. <?php echo htmlspecialchars($sip_dokter); ?></span>[cite: 6]
            </div>
        </div>
    </div>
</body>
</html>