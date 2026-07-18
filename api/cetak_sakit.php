<?php
include 'koneksi.php';

// 1. Cek apakah parameter nomor ada
if (!isset($_GET['nomor']) || empty($_GET['nomor'])) {
    die("Error: Parameter nomor surat tidak ditemukan.");
}

// 2. Ambil data dengan aman dari URL
$nomor_ambil = mysqli_real_escape_string($koneksi, $_GET['nomor']);

// 3. Query pencarian
$query = "SELECT * FROM surat_sakit WHERE nomor_surat = '$nomor_ambil'";
$hasil = mysqli_query($koneksi, $query);

// 4. Cek apakah hasil ditemukan
if (mysqli_num_rows($hasil) == 0) {
    die("Data surat dengan nomor <strong>$nomor_ambil</strong> tidak ditemukan di database.");
}

$data = mysqli_fetch_assoc($hasil);

// ... sisa kode Anda di bawah ...

// Menghitung tanggal selesai otomatis
$tanggal_mulai_raw = $data['tanggal_mulai'];
$days_to_add = (int)$data['lama_istirahat'] - 1;
$tanggal_selesai_raw = date('Y-m-d', strtotime($tanggal_mulai_raw . " + " . $days_to_add . " days"));

function tgl_indo($tanggal){
    $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$tanggal_buat_surat = tgl_indo(date('Y-m-d', strtotime($data['tanggal_dibuat'])));
$tanggal_mulai = tgl_indo($tanggal_mulai_raw);
$tanggal_selesai = tgl_indo($tanggal_selesai_raw);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Keterangan Sakit - <?php echo $data['nama_pasien']; ?></title>
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
        .judul-surat p { margin: 0px 0 0 0; font-size: 14px; font-weight: bold; }

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
        <h3>SURAT KETERANGAN SAKIT</h3>
        <p>Nomor : <?php echo $data['nomor_surat']; ?></p>
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan dibawah ini, Dokter Puskesmas Bangkingan Dinas Kesehatan Kota Surabaya, menerangkan bahwa :</p>
        
        <table class="table-data">
            <tr>
                <td class="label-kolom">Nama</td>
                <td class="titik-dua">:</td>
                <td><?php echo $data['nama_pasien']; ?></td>
            </tr>
            <tr>
                <td class="label-kolom">Jenis Kelamin</td>
                <td class="titik-dua">:</td>
                <td><?php echo $data['jenis_kelamin']; ?></td>
            </tr>
            <tr>
                <td class="label-kolom">Umur</td>
                <td class="titik-dua">:</td>
                <td><?php echo $data['umur']; ?> Tahun</td>
            </tr>
            <tr>
                <td class="label-kolom">Alamat Domisili</td>
                <td class="titik-dua">:</td>
                <td><?php echo $data['alamat_domisili']; ?></td>
            </tr>
            <tr>
                <td class="label-kolom">Pekerjaan</td>
                <td class="titik-dua">:</td>
                <td><?php echo $data['pekerjaan']; ?></td>
            </tr>
        </table>

        <p>Berdasarkan hasil pemeriksaan menunjukkan bahwa yang bersangkutan dalam kondisi sakit dan membutuhkan istirahat selama <strong><?php echo $data['alasan_sakit']; ?></strong>, pada tanggal <strong><?php echo $tanggal_mulai; ?></strong> sampai dengan tanggal <strong><?php echo $tanggal_selesai; ?></strong>.</p>
        
        <p>Demikian surat keterangan ini kami buat, supaya digunakan sebagaimana mestinya.</p>
    </div>

   <div class="footer-container">
        <div class="qrcode-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=http://localhost/e-surat/cetak_sakit.php?nomor=<?php echo urlencode($data['nomor_surat']); ?>" alt="QR Verification">
            <p>E-Verifikasi Sah</p>
        </div>

        <div class="ttd-box">
            <p>Surabaya, <?php echo $tanggal_buat_surat; ?></p>
            <p>Dokter Pemeriksa,</p>
            <div class="ttd-space"></div>
            <div class="ttd-container">
                <span class="nama-dokter"><?php echo $data['nama_dokter']; ?></span>
                <span>SIP. <?php echo $data['sip_dokter']; ?></span>
            </div>
        </div>
    </div>
</body>
</html>