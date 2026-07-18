<?php 
include 'nomor_otomatis.php'; 
$nomor_surat_baru = buat_nomor_non_pelayanan_otomatis();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Surat Non Pelayanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- MENAMBAHKAN CSS SELECT2 UNTUK FITUR PENCARIAN -->
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
        
        .select2-container--default .select2-selection--single {
            height: 45px !important;
            padding: 8px 6px;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>
<body>

<div class="form-container">
    <a href="index.php?menu=rekap" class="back-link">&larr; Kembali ke Dashboard</a>
    <h2>💼 Input Surat Non-Pelayanan (Umum)</h2>

    <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 12px; border-radius: 8px; margin-bottom: 25px;">
        <label style="color: #856404; display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
            <input type="checkbox" id="switch_backdate"> ⚠️ Aktifkan Mode Backdate (Nomor Manual)
        </label>
    </div>

    <form action="simpan_non_pelayanan.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tanggal Surat:</label>
            <input type="date" name="tanggal_surat" id="tanggal_surat" value="<?php echo date('Y-m-d'); ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>Nomor Surat:</label>
            <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" style="font-weight: 600;" placeholder="Contoh: 400.7/0001/436.7.2.3.55/2026" required>
        </div>

        <div class="form-group">
            <label>Kode Klasifikasi Surat (400.7 Kesehatan):</label>
            <select name="kode_klasifikasi" id="kode_klasifikasi" required class="form-control">
                <option value="">-- Ketik kode / nama klasifikasi di sini --</option>
                <option value="400.7.1">400.7.1 Kebijakan di bidang Kesehatan yang dilakukan oleh Pemerintah Daerah</option>
                <option value="400.7.2.1">400.7.2.1 Pelayanan kedokteran keluarga</option>
                <option value="400.7.2.2">400.7.2.2 Praktek klinis dokter di pelayanan kesehatan primer</option>
                <option value="400.7.2.3">400.7.2.3 Pelaksanaan kesehatan primer</option>
                <option value="400.7.2.4">400.7.2.4 Kesehatan gigi dan mulut di puskesmas</option>
                <option value="400.7.2.5">400.7.2.5 Kesehatan gigi dan mulut di rumah sakit</option>
                <option value="400.7.2.6">400.7.2.6 ICD 10, Destistry & Stomatology</option>
                <option value="400.7.2.7">400.7.2.7 Infeksi menular lewat transfusi darah</option>
                <option value="400.7.2.8">400.7.2.8 Penyakit mulut di tingkat primer</option>
                <option value="400.7.2.9">400.7.2.9 Pembiayaan darah</option>
                <option value="400.7.2.10">400.7.2.10 Penggunaan darah rasional</option>
                <option value="400.7.2.11">400.7.2.11 Unit transfusi darah, bank darah rumah sakit dan jejaring pelayanan darah</option>
                <option value="400.7.2.12">400.7.2.12 Pelayanan kesehatan di daerah terpencil, sangat terpencil dan kepulauan</option>
                <option value="400.7.2.13">400.7.2.13 Akreditasi puskesmas</option>
                <option value="400.7.2.14">400.7.2.14 Puskesmas berprestasi</option>
                <option value="400.7.3.1">400.7.3.1 Pelayanan kesehatan rujukan</option>
                <option value="400.7.3.2">400.7.3.2 Pelayanan kedokteran, organisasi profesi dan konsorsium upaya kesehatan (KUK)</option>
                <option value="400.7.3.3">400.7.3.3 Pelayanan rumah sakit privat</option>
                <option value="400.7.3.4">400.7.3.4 Pelayanan kesehatan rumah sakit khusus dan fasilitas pelayanan kesehatan lainnya</option>
                <option value="400.7.3.5">400.7.3.5 Pelayanan kesehatan rumah sakit pendidikan</option>
                <option value="400.7.3.6">400.7.3.6 Pelayanan jaminan pasien kesehatan</option>
                <option value="400.7.3.7">400.7.3.7 Fasilitas pelayanan kesehatan asing dan perdagangan jasa</option>
                <option value="400.7.3.8">400.7.3.8 Badan pengawas rumah sakit</option>
                <option value="400.7.3.9">400.7.3.9 Perizinan dan penetapan kelas rumah sakit kelas A dan Penanam Modal Asing (PMA)</option>
                <option value="400.7.3.10">400.7.3.10 Akreditasi rumah sakit dan fasilitas kesehatan lainnya</option>
                <option value="400.7.4.1">400.7.4.1 Pelayanan Keperawatan Dasar</option>
                <option value="400.7.4.2">400.7.4.2 Pelayanan keperawatan profesional di rumah sakit</option>
                <option value="400.7.4.3">400.7.4.3 Pelayanan Keperawatan di Rumah Sakit Umum</option>
                <option value="400.7.4.4">400.7.4.4 Pelayanan Keperawatan di Rumah Sakit Khusus</option>
                <option value="400.7.4.5">400.7.4.5 Bina pelayanan kebidanan</option>
                <option value="400.7.5.1">400.7.5.1 Mikrobiologi dan imunologi</option>
                <option value="400.7.5.2">400.7.5.2 Patologi dan toksilogi</option>
                <option value="400.7.5.3">400.7.5.3 Radiologi</option>
                <option value="400.7.5.4">400.7.5.4 Perizinan dan sertifikasi</option>
                <option value="400.7.5.5">400.7.5.5 Sarana dan prasarana kesehatan</option>
                <option value="400.7.5.6">400.7.5.6 Peralatan medis di fasilitas pelayanan kesehatan</option>
                <option value="400.7.5.7">400.7.5.7 Aplikasi sarana dan prasarana alat kesehatan</option>
                <option value="400.7.6.1">400.7.6.1 Kesehatan jiwa di non fasilitas pelayanan kesehatan</option>
                <option value="400.7.6.2">400.7.6.2 Bina kesehatan jiwa di fasilitas pelayanan kesehatan</option>
                <option value="400.7.6.3">400.7.6.3 Etikolegal dan asesmen</option>
                <option value="400.7.6.4">400.7.6.4 Pencegahan dan penanggulangan narkotika dan sejenisnya</option>
                <option value="400.7.6.5">400.7.6.5 Etikolegal dan asesmen (2)</option>
                <option value="400.7.6.6">400.7.6.6 Kesehatan jiwa kelompok beresiko</option>
                <option value="400.7.7.1">400.7.7.1 Surveilans dan respon kejadian luar biasa</option>
                <option value="400.7.7.2">400.7.7.2 Imunisasi</option>
                <option value="400.7.7.3">400.7.7.3 Karantina kesehatan dan kesehatan di pelabuhan</option>
                <option value="400.7.7.4">400.7.7.4 Kesehatan matra</option>
                <option value="400.7.8.1">400.7.8.1 Pengendalian tuberkolosis</option>
                <option value="400.7.8.2">400.7.8.2 Pengendalian AIDS dan penyakit menular seksual</option>
                <option value="400.7.8.3">400.7.8.3 Pengendalian infeksi saluran pernafasan akut</option>
                <option value="400.7.8.4">400.7.8.4 Pengendalian diare dan infeksi saluran pencernaan</option>
                <option value="400.7.8.5">400.7.8.5 Pengendalian kusta dan frambusia</option>
                <option value="400.7.9.1">400.7.9.1 Pengendalian malaria</option>
                <option value="400.7.9.2">400.7.9.2 Pengendalian arbovirosis</option>
                <option value="400.7.9.3">400.7.9.3 Pengendalian zoonosis</option>
                <option value="400.7.9.4">400.7.9.4 Pengendalian filariasis dan kecacingan</option>
                <option value="400.7.10.1">400.7.10.1 Pengendalian penyakit jantung dan pembuluh darah</option>
                <option value="400.7.10.2">400.7.10.2 Pengendalian penyakit diabetes melitus dan penyakit metabolik</option>
                <option value="400.7.10.3">400.7.10.3 Penyakit kanker</option>
                <option value="400.7.10.4">400.7.10.4 Penyakit kronis dan generatif</option>
                <option value="400.7.10.5">400.7.10.5 Gangguan akibat kecelakaan dan tindak kekerasan</option>
                <option value="400.7.11.1">400.7.11.1 Penyehatan air dan sanitasi dasar</option>
                <option value="400.7.11.2">400.7.11.2 Pemukiman dan tempat umum</option>
                <option value="400.7.11.3">400.7.11.3 Kawasan dan sanitasi darurat</option>
                <option value="400.7.11.4">400.7.11.4 Higien sanitasi pangan</option>
                <option value="400.7.11.5">400.7.11.5 Pengamanan limbah, udara, radiasi</option>
                <option value="400.7.12">400.7.12 Pengembangan dan penapisan teknologi pengendalian penyakit dan pengendalian lingkungan</option>
                <option value="400.7.13.1">400.7.13.1 Gizi makro</option>
                <option value="400.7.13.2">400.7.13.2 Gizi mikro</option>
                <option value="400.7.13.3">400.7.13.3 Gizi klinik dan diatetik</option>
                <option value="400.7.13.4">400.7.13.4 Konsumsi makanan</option>
                <option value="400.7.13.5">400.7.13.5 Kewaspadaan gizi makanan dan jasa</option>
                <option value="400.7.14.1">400.7.14.1 Kesehatan ibu hamil</option>
                <option value="400.7.14.2">400.7.14.2 Kesehatan ibu bersalin dan nifas</option>
                <option value="400.7.14.3">400.7.14.3 Kesehatan maternal dengan pencegahan komplikasi</option>
                <option value="400.7.14.4">400.7.14.4 Keluarga berencana</option>
                <option value="400.7.14.5">400.7.14.5 Perlindungan kesehatan reproduksi</option>
                <option value="400.7.15.1">400.7.15.1 Kelangsungan hidup bayi</option>
                <option value="400.7.15.2">400.7.15.2 Kelangsungan anak balita dan pra sekolah</option>
                <option value="400.7.15.3">400.7.15.3 Kewaspadaan penanganan balita beresiko</option>
                <option value="400.7.15.4">400.7.15.4 Kualitas hidup anak usia sekolah</option>
                <option value="400.7.15.5">400.7.15.5 Perlindungan kesehatan anak dan remaja</option>
                <option value="400.7.16.1">400.7.16.1 Kesehatan tradisional keterampilan</option>
                <option value="400.7.16.2">400.7.16.2 Kesehatan tradisional ramuan</option>
                <option value="400.7.16.3">400.7.16.3 Kesehatan alternatif komplementer</option>
                <option value="400.7.16.4">400.7.16.4 Penapisan dan kemitraan</option>
                <option value="400.7.17.1">400.7.17.1 Pelayanan kesehatan kerja</option>
                <option value="400.7.17.2">400.7.17.2 Kapasitas kerja</option>
                <option value="400.7.17.3">400.7.17.3 Lingkungan kerja</option>
                <option value="400.7.17.4">400.7.17.4 Kemitraan kesehatan kerja</option>
                <option value="400.7.17.5">400.7.17.5 Kesehatan perkotaan</option>
                <option value="400.7.17.6">400.7.17.6 Kesehatan olahraga</option>
                <option value="400.7.18.1">400.7.18.1 Harga obat publik</option>
                <option value="400.7.18.2">400.7.18.2 Pengadaan obat</option>
                <option value="400.7.18.3">400.7.18.3 Perbekalan kesehatan</option>
                <option value="400.7.19.1">400.7.19.1 Alat kesehatan</option>
                <option value="400.7.19.2">400.7.19.2 Produsen dan distributor alat kesehatan dan obat</option>
                <option value="400.7.19.3">400.7.19.3 Produk diagnostik in vitro dan perbekalan kesehatan rumah tangga</option>
                <option value="400.7.20.1">400.7.20.1 Pelayanan kefarmasian</option>
                <option value="400.7.20.2">400.7.20.2 Farmasi klinis</option>
                <option value="400.7.20.3">400.7.20.3 Farmasi Komunitas</option>
                <option value="400.7.20.4">400.7.20.4 Penggunaan obat rasional</option>
                <option value="400.7.21.1">400.7.21.1 Obat tradisional</option>
                <option value="400.7.21.2">400.7.21.2 Kosmetik dan makanan</option>
                <option value="400.7.21.3">400.7.21.3 Narkotika, psikotropika, prekursor farmasi dan sediaan farmasi khusus</option>
                <option value="400.7.21.4">400.7.21.4 Kemandirian obat dan bahan baku obat</option>
                <option value="400.7.22.1">400.7.22.1 Surat keterangan</option>
                <option value="400.7.22.2">400.7.22.2 Sertifikasi dan perijinan</option>
                <option value="400.7.23.1">400.7.23.1 Pencegahan, mitigasi dan kesiapsiagaan</option>
                <option value="400.7.23.2">400.7.23.2 Tanggap darurat dan pemulihan</option>
                <option value="400.7.23.3">400.7.23.3 Pemantauan dan informasi</option>
                <option value="400.7.23.4">400.7.23.4 Penanggulangan krisis kesehatan dalam bidang pengendalian penyakit dan penyehatan</option>
                <option value="400.7.23.5">400.7.23.5 Pelayanan kesehatan reproduksi situasi bencana</option>
                <option value="400.7.24.1">400.7.24.1 Tersedianya data NHA setiap tahun</option>
                <option value="400.7.24.2">400.7.24.2 Tersedianya dokumen teknis penguatan pelaksanaan JKN</option>
                <option value="400.7.25.1">400.7.25.1 Pemeliharaan dan peningkatan kemampuan inteligensia kesehatan</option>
                <option value="400.7.25.2">400.7.25.2 Penanggulangan masalah inteligensia kesehatan</option>
                <option value="400.7.26.1">400.7.26.1 Pelayanan dan pendayagunaan sumber daya kesehatan haji</option>
                <option value="400.7.26.2">400.7.26.2 Peningkatan kesehatan dan pengendalian faktor risiko kesehatan haji</option>
                <option value="400.7.27.1">400.7.27.1 Sarana Promosi Kesehatan</option>
                <option value="400.7.27.2">400.7.27.2 Pembinaan advokasi dan kemitraan serta pemberdayaan peran</option>
                <option value="400.7.27.3">400.7.27.3 Pengembangan pesan promosi kesehatan</option>
                <option value="400.7.27.4">400.7.27.4 Hari Kesehatan</option>
                <option value="400.7.28.1">400.7.28.1 Statistik kesehatan</option>
                <option value="400.7.28.2">400.7.28.2 Analisis dan diseminasi informasi</option>
                <option value="400.7.28.3">400.7.28.3 Pengembangan sistem informasi dan bank data kesehatan</option>
                <option value="400.7.29.1">400.7.29.1 Penilaian obat tradisional, suplemen makanan dan kosmetik</option>
                <option value="400.7.29.2">400.7.29.2 Standardisasi obat tradisional, kosmetik dan produk komplimen</option>
                <option value="400.7.29.3">400.7.29.3 Inspeksi dan sertifikasi obat tradisional, kosmetik dan produk komplimen</option>
                <option value="400.7.29.4">400.7.29.4 Obat Asli Indonesia</option>
                <option value="400.7.30.1">400.7.30.1 Penilaian keamanan pangan</option>
                <option value="400.7.30.2">400.7.30.2 Standardisasi produk pangan</option>
                <option value="400.7.30.3">400.7.30.3 Inspeksi dan sertifikasi produk pangan</option>
                <option value="400.7.30.4">400.7.30.4 Surveilan dan penyuluhan keamanan pangan</option>
                <option value="400.7.30.5">400.7.30.5 Pengawasan produk dan bahan berbahaya</option>
                <option value="400.7.31">400.7.31 Rekam Medis</option>
            </select>
        </div>

        <div class="form-group">
            <label>Perihal Surat:</label>
            <input type="text" name="perihal" required class="form-control" placeholder="Contoh: Undangan Rapat / Nota Dinas Eksternal">
        </div>

        <div class="form-group">
            <label>PJ Surat (Penanggung Jawab / Pengirim):</label>
            <input type="text" name="pj_surat" required class="form-control" placeholder="Nama Pejabat / Bagian TU">
        </div>

        <div class="form-group">
            <label>Keterangan:</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan singkat isi surat..."></textarea>
        </div>

        <div class="form-group">
            <label>Upload File Surat (PDF / Gambar):</label>
            <input type="file" name="file_surat" required class="form-control" accept="application/pdf, image/*">
        </div>

        <button type="submit" class="btn-submit">💾 Simpan Data Surat</button>
    </form>
</div>

<!-- MENAMBAHKAN JQUERY & JAVASCRIPT SELECT2 SEBELUM SCRIPT UTAMA -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#kode_klasifikasi').select2();

    function aturBlokNomorUrut() {
        var kodeKlasifikasi = $('#kode_klasifikasi').val();
        var inputSurat = document.getElementById('nomor_surat');

        if (kodeKlasifikasi && inputSurat.value !== "") {
            var startPos = kodeKlasifikasi.length + 1; 
            var sisaTeks = inputSurat.value.substring(startPos);
            var endPos = startPos + sisaTeks.indexOf('/'); 

            if (endPos > startPos) {
                inputSurat.focus();
                inputSurat.setSelectionRange(startPos, endPos);
            }
        }
    }

    $('#kode_klasifikasi').on('change', function() {
        var kodeKlasifikasi = $(this).val();
        var formatPuskesmas = "/436.7.2.3.55/2026";
        var defaultNomorUrut = "0001";
        
        if (kodeKlasifikasi !== "") {
            var textLengkap = kodeKlasifikasi + "/" + defaultNomorUrut + formatPuskesmas;
            $('#nomor_surat').val(textLengkap);
            setTimeout(aturBlokNomorUrut, 50);
        }
    });

    $('#nomor_surat').on('click focus', function(e) {
        if (!$('#switch_backdate').is(':checked')) {
            e.preventDefault();
            aturBlokNomorUrut();
        }
    });
});

document.getElementById('switch_backdate').addEventListener('change', function() {
    var inputNomor = document.getElementById('nomor_surat');
    if (this.checked) {
        inputNomor.removeAttribute('readonly');
        inputNomor.style.backgroundColor = '#fff3cd';
        inputNomor.focus();
    } else {
        inputNomor.setAttribute('readonly', 'readonly');
        inputNomor.style.backgroundColor = '#ffffff';
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_nomor_ajak.php?tanggal=' + document.getElementById('tanggal_surat').value + '&jenis=non_pelayanan', true);
        xhr.onload = function() {
            if (xhr.status === 200) { 
                inputNomor.value = xhr.responseText; 
                $('#kode_klasifikasi').trigger('change');
            }
        };
        xhr.send();
    }
});

document.getElementById('tanggal_surat').addEventListener('change', function() {
    var switchBackdate = document.getElementById('switch_backdate');
    var inputNomor = document.getElementById('nomor_surat');
    
    if (!switchBackdate.checked) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_nomor_ajak.php?tanggal=' + this.value + '&jenis=non_pelayanan', true);
        xhr.onload = function() {
            if (xhr.status === 200) { 
                inputNomor.value = xhr.responseText; 
                $('#kode_klasifikasi').trigger('change');
            }
        };
        xhr.send();
    }
});
</script>
</body>
</html>