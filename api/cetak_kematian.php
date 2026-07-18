<?php
include 'koneksi.php';

// 1. Ambil data dengan aman
$nomor_ambil = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';
if (empty($nomor_ambil)) { die("Error: Parameter nomor surat tidak ditemukan."); }

$nomor_ambil = mysqli_real_escape_string($koneksi, $nomor_ambil);
$query = "SELECT * FROM surat_kematian WHERE nomor_surat = '$nomor_ambil'";
$hasil = mysqli_query($koneksi, $query);

if (mysqli_num_rows($hasil) == 0) {
    die("Data surat dengan nomor <strong>$nomor_ambil</strong> tidak ditemukan.");
}

$data = mysqli_fetch_assoc($hasil);

// 2. Fungsi Tanggal Indonesia
function tgl_indo($tanggal){
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $pecahkan = explode('-', $tanggal);
    return ($pecahkan[2] ?? '') . ' ' . ($bulan[(int)($pecahkan[1] ?? 0)] ?? '') . ' ' . ($pecahkan[0] ?? '');
}

// 3. Mapping Variabel
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
    <title>Cetak Surat Keterangan Kematian - <?php echo $data['nama_jenazah']; ?></title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            margin: 30px 50px; /* Diperkecil agar menghemat ruang atas dan bawah */
            color: #000; 
            line-height: 1.4; /* Jarak antar baris teks sedikit dirapatkan */
        }
        
        /* Layout Kop Surat Resmi */
        .header-container { 
            position: relative; /* Menjadi acuan patokan untuk posisi logo */
            display: flex; 
            flex-direction: column;
            align-items: center; /* Mengunci teks agar benar-benar berada di tengah */
            justify-content: center; 
            border-bottom: 4px solid #000; 
            padding-bottom: 5px; 
            margin-bottom: 15px; 
            min-height: 90px; /* Menjaga tinggi kop surat agar pas dengan logo */
        }
        .logo-pemkot { 
            position: absolute; /* Membuat logo mengambang tanpa mendorong teks */
            left: 75px; /* Mengatur posisi logo di sebelah kiri kop surat */
            top: 45%;
            transform: translateY(-50%); /* Membuat logo pas di tengah secara vertikal */
            width: 85px; 
            height: auto; 
            display: block;
        }
        .kop-teks { 
            text-align: center; 
            width: 100%; /* Memastikan area teks memanfaatkan seluruh lebar tengah */
            margin-right: 0; /* Menghapus semua sisa dorongan margin */
            margin-left: 0;
        }
        .kop-teks h2 { margin: 0; font-size: 16px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2; }
        .kop-teks h1 { margin: 0; font-size: 19px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.3; }
        .kop-teks p { margin: 1px 0; font-size: 11px; }
        
        /* Judul Dokumen */
        .judul-surat { 
            text-align: center; 
            margin-top: 15px; 
            margin-bottom: 15px; 
        }
        .judul-surat h3 { margin: 0; font-size: 16px; text-transform: uppercase; text-decoration: underline; font-weight: bold; }
        .judul-surat p { margin: 3px 0 0 0; font-size: 14px; font-weight: bold; }

        /* Isi Konten */
        .isi-surat { font-size: 15px; text-align: justify; }
        .isi-surat p { margin-bottom: 10px; text-indent: 45px; }
        
        /* Tabel Identitas */
        .table-data { 
            margin-left: 45px; 
            margin-top: 5px; 
            margin-bottom: 10px; 
            font-size: 15px; 
            border-collapse: collapse; 
        }
        .table-data td { padding: 2px 5px; vertical-align: top; } 
        .table-data td.label-kolom { width: 160px; }
        .table-data td.titik-dua { width: 15px; text-align: center; }

        /* Bagian Tanda Tangan */
        .footer-container { 
            margin-top: 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; /* Ubah ke flex-start agar sejajar atas */
            font-size: 15px; 
            page-break-inside: avoid; 
        }
        
        .ttd-box { 
            text-align: left; 
            width: 320px;
            margin-right: -50px; /* Tambahkan ini: Semakin besar angkanya, semakin ke kiri */
            /* ATAU jika ingin digeser ke kanan, gunakan margin-left */
            margin-left: 100px; /* UBAH ANGKA INI: Tambahkan margin-left untuk menggeser ke kanan */
        }
        .qrcode-box { text-align: center; margin-left: 50px; }
        .ttd-space { height: 60px; } 
        
        .ttd-box p { margin: 0; line-height: 1.2; }
        
        /* Memaksa nama dan SIP dalam satu blok yang aman */
        .ttd-container {
            display: block;
            width: 100%;
        }
        
        .nama-dokter { 
            font-weight: bold; 
            text-decoration: underline; 
            display: block; /* Memastikan baris baru */
            white-space: nowrap; 
        }
        
        .sip-dokter {
            display: block; /* Memastikan berada di baris baru di bawah nama */
            white-space: nowrap;
        }

        /* Tombol Sistem Navigasi */
        .tombol-aksi { margin-bottom: 20px; background: #e9ecef; padding: 10px; border-radius: 6px; border: 1px solid #ced4da; }
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 4px; font-family: Arial, sans-serif; font-size: 13px; display: inline-block; margin-right: 10px; font-weight: bold; border: none; cursor: pointer; }
        .btn-print { background: #007bff; }
        .btn-kembali { background: #6c757d; }
        
        @media print {
            .tombol-aksi { display: none; }
            body { margin: 5px 15px; }
        }
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
        <p>Nomor : <?php echo $data['nomor_surat']; ?></p>
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
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=http://localhost/e-surat/cetak_kematian.php?nomor=<?php echo urlencode($nomor_surat); ?>" alt="QR">
            <p>E-Verifikasi Sah</p>
        </div>
        <div class="ttd-box">
    <p>Surabaya, <?php echo $tanggal_sekarang; ?></p>
    <p>Dokter Pemeriksa,</p>
    <div class="ttd-space"></div>
    <div class="ttd-container">
        <span class="nama-dokter" style="display:block; font-weight:bold; text-decoration:underline;">
            <?php echo $data['nama_dokter']; ?>
        </span>
        <span style="display:block;">SIP. <?php echo $data['sip_dokter']; ?></span>
    </div>
</div>
</body>
</html>