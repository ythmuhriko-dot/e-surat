<?php
include 'koneksi.php';

// Terima baik 'nomor' maupun 'nomor_surat' untuk antisipasi
$nomor_ambil = $_GET['nomor'] ?? $_GET['nomor_surat'] ?? '';

if (empty($nomor_ambil)) { 
    die("Error: Parameter nomor surat tidak ditemukan di URL."); 
}

$nomor_ambil = mysqli_real_escape_string($koneksi, $nomor_ambil);

// Lanjutkan query ke database
$query = "SELECT * FROM surat_sehat WHERE nomor_surat = '$nomor_ambil'";
$hasil = mysqli_query($koneksi, $query);

if (mysqli_num_rows($hasil) == 0) {
    die("Data surat dengan nomor <strong>$nomor_ambil</strong> tidak ditemukan di database.");
}
// Tambahkan fungsi ini untuk format tanggal Indonesia
function tgl_indo($tanggal){
    $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

// Ambil tanggal hari ini dan ubah ke format Indonesia
$tanggal_sekarang = tgl_indo(date('Y-m-d'));
$data = mysqli_fetch_assoc($hasil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Sehat - <?php echo $data['nama_pasien']; ?></title>
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
            left: 70px; /* Mengatur posisi logo di sebelah kiri kop surat */
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
        <h3>SURAT KETERANGAN SEHAT</h3>
        <p>Nomor : <?php echo $data['nomor_surat']; ?></p>
    </div>

<div class="isi-surat">
 <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>
        <table class="table-data">
        <tr>
            <td class="label-kolom">Nama Pasien</td>
            <td>:</td>
            <td><?php echo $data['nama_pasien']; ?></td>
        </tr>
        <tr>
            <td class="label-kolom">Jenis Kelamin</td>
            <td>:</td>
            <td><?php echo $data['jenis_kelamin']; ?></td>
        </tr>
        <tr>
            <td class="label-kolom">Umur</td>
            <td>:</td>
            <td><?php echo $data['umur']; ?> Tahun</td>
        </tr>
        <tr>
            <td class="label-kolom">Pekerjaan</td>
            <td>:</td>
            <td><?php echo $data['pekerjaan']; ?></td>
        </tr>
        <tr>
            <td class="label-kolom">Alamat</td>
            <td>:</td>
            <td><?php echo $data['alamat_domisili']; ?></td>
        </tr>
    </table>

    <div class="pemeriksaan">
        <p>Telah dilakukan pemeriksaan kesehatan dengan hasil:</p>
        <table class="table-data">
            <tr>
                <td>Tensi</td>
                <td>:</td>
                <td><?php echo $data['tensi']; ?> mmHg</td>
            </tr>
            <tr>
                <td>Nadi</td>
                <td>:</td>
                <td><?php echo $data['nadi']; ?> x/menit</td>
            </tr>
            <tr>
                <td>Suhu</td>
                <td>:</td>
                <td><?php echo $data['suhu']; ?> °C</td>
            </tr>
            <tr>
                <td>Berat Badan</td>
                <td>:</td>
                <td><?php echo $data['berat_badan']; ?> kg</td>
            </tr>
            <tr>
                <td>Tinggi Badan</td>
                <td>:</td>
                <td><?php echo $data['tinggi_badan']; ?> cm</td>
            </tr>
            <tr>
                <td>Golongan Darah</td>
                <td>:</td>
                <td><?php echo $data['gol_darah']; ?></td>
            </tr>
            <tr>
                <td>Penglihatan</td>
                <td>:</td>
                <td><?php echo $data['visus_kanan']; ?></td>
            </tr>
            <tr>
                <td>Buta Warna</td>
                <td>:</td>
                <td><?php echo $data['buta_warna']; ?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td><?php echo $data['visus_kiri']; ?></td>
            </tr>
        </table>
    </div>

    <p>Berdasarkan hasil pemeriksaan tersebut, yang bersangkutan dinyatakan dalam keadaan <strong>SEHAT</strong>. Surat keterangan ini dipergunakan untuk : <strong><?php echo $data['keperluan']; ?></strong>.</p>
    
    <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
</div>

<div class="footer-container">
    <div class="qrcode-box">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=http://localhost/e-surat/cetak_sehat.php?nomor=<?php echo urlencode($data['nomor_surat']); ?>" alt="QR Verification">
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