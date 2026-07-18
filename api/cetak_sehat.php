<?php
include 'koneksi.php';

// Terima baik 'nomor' maupun 'nomor_surat' untuk antisipasi
$nomor_ambil = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';[cite: 8]
if (empty($nomor_ambil)) { 
    die("Error: Parameter nomor surat tidak ditemukan di URL."); [cite: 8]
}

try {
    // Menggunakan Prepared Statement PDO
    $query = "SELECT * FROM surat_sehat WHERE nomor_surat = :nomor";[cite: 8]
    $stmt = $koneksi->prepare($query);
    $stmt->execute([':nomor' => $nomor_ambil]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data surat dengan nomor <strong>" . htmlspecialchars($nomor_ambil) . "</strong> tidak ditemukan di database.");[cite: 8]
    }
} catch (PDOException $e) {
    die("Gagal mengambil data database: " . $e->getMessage());
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal){
        $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');[cite: 8]
        $pecahkan = explode('-', $tanggal);[cite: 8]
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];[cite: 8]
    }
}

// Ambil tanggal hari ini dan ubah ke format Indonesia
$tanggal_sekarang = tgl_indo(date('Y-m-d'));[cite: 8]
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Sehat - <?php echo htmlspecialchars($data['nama_pasien']); ?></title>[cite: 8]
    <style>
        body { font-family: "Times New Roman", Times, serif; margin: 30px 50px; color: #000; line-height: 1.4; }[cite: 8]
        .header-container { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; border-bottom: 4px solid #000; padding-bottom: 5px; margin-bottom: 15px; min-height: 90px; }[cite: 8]
        .logo-pemkot { position: absolute; left: 70px; top: 45%; transform: translateY(-50%); width: 85px; height: auto; display: block; }[cite: 8]
        .kop-teks { text-align: center; width: 100%; margin-right: 0; margin-left: 0; }[cite: 8]
        .kop-teks h2 { margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2; }[cite: 8]
        .kop-teks h1 { margin: 0; font-size: 19px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.3; }[cite: 8]
        .kop-teks p { margin: 1px 0; font-size: 11px; }[cite: 8]
        .judul-surat { text-align: center; margin-top: 15px; margin-bottom: 15px; }[cite: 8]
        .judul-surat h3 { margin: 0; font-size: 16px; text-transform: uppercase; text-decoration: underline; font-weight: bold; }[cite: 8]
        .judul-surat p { margin: 3px 0 0 0; font-size: 14px; font-weight: bold; }[cite: 8]
        .isi-surat { font-size: 15px; text-align: justify; }[cite: 8]
        .isi-surat p { margin-bottom: 10px; text-indent: 45px; }[cite: 8]
        .table-data { margin-left: 45px; margin-top: 5px; margin-bottom: 10px; font-size: 15px; border-collapse: collapse; }[cite: 8]
        .table-data td { padding: 2px 5px; vertical-align: top; }[cite: 8]
        .table-data td.label-kolom { width: 160px; }[cite: 8]
        .footer-container { margin-top: 25px; display: flex; justify-content: space-between; align-items: flex-start; font-size: 15px; page-break-inside: avoid; }[cite: 8]
        .ttd-box { text-align: left; width: 320px; margin-left: 100px; }[cite: 8]
        .qrcode-box { text-align: center; margin-left: 50px; }[cite: 8]
        .ttd-space { height: 60px; }[cite: 8]
        .ttd-box p { margin: 0; line-height: 1.2; }[cite: 8]
        .ttd-container { display: block; width: 100%; }[cite: 8]
        .nama-dokter { font-weight: bold; text-decoration: underline; display: block; white-space: nowrap; }[cite: 8]
        .tombol-aksi { margin-bottom: 20px; background: #e9ecef; padding: 10px; border-radius: 6px; border: 1px solid #ced4da; }[cite: 8]
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 4px; font-family: Arial, sans-serif; font-size: 13px; display: inline-block; margin-right: 10px; font-weight: bold; border: none; cursor: pointer; }[cite: 8]
        .btn-print { background: #007bff; }[cite: 8]
        .btn-kembali { background: #6c757d; }[cite: 8]
        @media print { .tombol-aksi { display: none; } body { margin: 5px 15px; } }[cite: 8]
    </style>
</head>
<body>
    <div class="tombol-aksi">
        <button onclick="window.print();" class="btn btn-print">🖨️ Cetak Surat ke PDF / Kertas</button>[cite: 8]
        <a href="index.php?menu=rekap" class="btn btn-kembali">🏠 Kembali</a>[cite: 8]
    </div>

    <div class="header-container">
        <img class="logo-pemkot" src="logo_surabaya.png" alt="Logo Pemkot Surabaya">[cite: 8]
        <div class="kop-teks">
            <h2>PEMERINTAH KOTA SURABAYA</h2>[cite: 8]
            <h2>DINAS KESEHATAN</h2>[cite: 8]
            <h1>UPTD PUSKESMAS BANGKINGAN</h1>[cite: 8]
            <p>Jl. Bangkingan Pesarean No. 3-4 Surabaya 60214</p>[cite: 8]
            <p>Telp. (031) 7665218</p>[cite: 8]
            <p>Surabaya.go.id, Pos-el : pkmbangkingan@gmail.com</p>[cite: 8]
        </div>
    </div>

    <div class="judul-surat">
        <h3>SURAT KETERANGAN SEHAT</h3>[cite: 8]
        <p>Nomor : <?php echo htmlspecialchars($data['nomor_surat']); ?></p>[cite: 8]
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>[cite: 8]
        <table class="table-data">
            <tr><td class="label-kolom">Nama Pasien</td><td>:</td><td><?php echo htmlspecialchars($data['nama_pasien']); ?></td></tr>[cite: 8]
            <tr><td class="label-kolom">Jenis Kelamin</td><td>:</td><td><?php echo htmlspecialchars($data['jenis_kelamin']); ?></td></tr>[cite: 8]
            <tr><td class="label-kolom">Umur</td><td>:</td><td><?php echo htmlspecialchars($data['umur']); ?> Tahun</td></tr>[cite: 8]
            <tr><td class="label-kolom">Pekerjaan</td><td>:</td><td><?php echo htmlspecialchars($data['pekerjaan']); ?></td></tr>[cite: 8]
            <tr><td class="label-kolom">Alamat</td><td>:</td><td><?php echo htmlspecialchars($data['alamat_domisili']); ?></td></tr>[cite: 8]
        </table>

        <div class="pemeriksaan">
            <p>Telah dilakukan pemeriksaan kesehatan dengan hasil:</p>[cite: 8]
            <table class="table-data">
                <tr><td>Tensi</td><td>:</td><td><?php echo htmlspecialchars($data['tensi']); ?> mmHg</td></tr>[cite: 8]
                <tr><td>Nadi</td><td>:</td><td><?php echo htmlspecialchars($data['nadi']); ?> x/menit</td></tr>[cite: 8]
                <tr><td>Suhu</td><td>:</td><td><?php echo htmlspecialchars($data['suhu']); ?> °C</td></tr>[cite: 8]
                <tr><td>Berat Badan</td><td>:</td><td><?php echo htmlspecialchars($data['berat_badan']); ?> kg</td></tr>[cite: 8]
                <tr><td>Tinggi Badan</td><td>:</td><td><?php echo htmlspecialchars($data['tinggi_badan']); ?> cm</td></tr>[cite: 8]
                <tr><td>Golongan Darah</td><td>:</td><td><?php echo htmlspecialchars($data['gol_darah']); ?></td></tr>[cite: 8]
                <tr><td>Penglihatan</td><td>:</td><td><?php echo htmlspecialchars($data['visus_kanan']); ?></td></tr>[cite: 8]
                <tr><td>Buta Warna</td><td>:</td><td><?php echo htmlspecialchars($data['buta_warna']); ?></td></tr>[cite: 8]
                <tr><td>Keterangan</td><td>:</td><td><?php echo htmlspecialchars($data['visus_kiri']); ?></td></tr>[cite: 8]
            </table>
        </div>

        <p>Berdasarkan hasil pemeriksaan tersebut, yang bersangkutan dinyatakan dalam keadaan <strong>SEHAT</strong>. Surat keterangan ini dipergunakan untuk : <strong><?php echo htmlspecialchars($data['keperluan']); ?></strong>.</p>[cite: 8]
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>[cite: 8]
    </div>

    <div class="footer-container">
        <div class="qrcode-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=http://localhost/e-surat/cetak_sehat.php?nomor=<?php echo urlencode($data['nomor_surat']); ?>" alt="QR Verification">[cite: 8]
            <p>E-Verifikasi Sah</p>[cite: 8]
        </div>
        <div class="ttd-box">
            <p>Surabaya, <?php echo htmlspecialchars($tanggal_sekarang); ?></p>[cite: 8]
            <p>Dokter Pemeriksa,</p>[cite: 8]
            <div class="ttd-space"></div>[cite: 8]
            <div class="ttd-container">
                <span class="nama-dokter"><?php echo htmlspecialchars($data['nama_dokter']); ?></span>[cite: 8]
                <span>SIP. <?php echo htmlspecialchars($data['sip_dokter']); ?></span>[cite: 8]
            </div>
        </div>
    </div>
</body>
</html>