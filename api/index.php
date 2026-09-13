<?php
// ==============================================================================
// 1. ROUTER SERVERLESS VERCEL
// ==============================================================================
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requested_file = basename($request_uri);

if (!empty($requested_file) && $requested_file !== 'index.php') {
    $target_file = __DIR__ . '/' . $requested_file;
    if (!file_exists($target_file)) {
        $target_file = dirname(__DIR__) . '/' . $requested_file;
    }

    if (file_exists($target_file) && is_file($target_file)) {
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $mime_types = [
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
            'css'  => 'text/css',
            'js'   => 'application/javascript'
        ];

        if (array_key_exists($ext, $mime_types)) {
            header('Content-Type: ' . $mime_types[$ext]);
            header('Content-Length: ' . filesize($target_file));
            readfile($target_file);
            exit();
        }

        require $target_file;
        exit();
    }
}

// ==============================================================================
// 2. LOGIKA DASHBOARD & REKAP (INDEX.PHP)
// ==============================================================================

ob_start();

include 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) && !empty($_COOKIE['user_login'])) {
    $_SESSION['login'] = true;
    $_SESSION['username'] = $_COOKIE['user_login'];
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

$menu = isset($_GET['menu']) ? $_GET['menu'] : '';

if ($menu == 'rekap') {
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

    $where = "";
    $params = [];
    if ($cari != "") {
        $where = " WHERE nomor_surat ILIKE :cari1 OR subjek ILIKE :cari2 OR nama_dokter ILIKE :cari3";
        $params[':cari1'] = "%$cari%";
        $params[':cari2'] = "%$cari%";
        $params[':cari3'] = "%$cari%";
    }

    if ($filter == 'pelayanan') {
        $sub_query = "
            SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, NULL AS file_upload FROM surat_sakit
            UNION ALL
            SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, NULL AS file_upload FROM surat_sehat
            UNION ALL
            SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, NULL AS file_upload FROM surat_kematian
        ";
    } elseif ($filter == 'non_pelayanan') {
        $sub_query = "
            SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, CAST('-' AS VARCHAR) AS nama_dokter, CAST('#' AS VARCHAR) AS link, file_surat AS file_upload FROM surat_non_pelayanan
        ";
    } else {
        $sub_query = "
            SELECT nomor_surat, 'Surat Sakit' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sakit.php' AS link, NULL AS file_upload FROM surat_sakit
            UNION ALL
            SELECT nomor_surat, 'Surat Sehat' AS jenis, nama_pasien AS subjek, nama_dokter, 'cetak_sehat.php' AS link, NULL AS file_upload FROM surat_sehat
            UNION ALL
            SELECT nomor_surat, 'Surat Kematian' AS jenis, nama_jenazah AS subjek, nama_dokter, 'cetak_kematian.php' AS link, NULL AS file_upload FROM surat_kematian
            UNION ALL
            SELECT nomor_surat, 'Non-Pelayanan' AS jenis, perihal AS subjek, CAST('-' AS VARCHAR) AS nama_dokter, CAST('#' AS VARCHAR) AS link, file_surat AS file_upload FROM surat_non_pelayanan
        ";
    }

    $query_gabungan = "SELECT * FROM ($sub_query) AS gabungan $where";

    try {
        $stmt_total = $koneksi->prepare("SELECT COUNT(*) as total FROM ($query_gabungan) as sub");
        $stmt_total->execute($params);
        $total_data = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $total_pages = ceil($total_data / $limit);
        if ($total_pages < 1) $total_pages = 1;

        $query_tampil = $query_gabungan . " 
            ORDER BY 
                CASE 
                    WHEN split_part(nomor_surat, '/', 2) ~ '^[0-9]+$' 
                    THEN CAST(split_part(nomor_surat, '/', 2) AS INTEGER)
                    ELSE 0 
                END DESC 
            LIMIT :limit OFFSET :offset";
            
        $stmt_tampil = $koneksi->prepare($query_tampil);
        
        $stmt_tampil->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt_tampil->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt_tampil->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt_tampil->execute();

        $count_sakit = $koneksi->query("SELECT COUNT(*) FROM surat_sakit")->fetchColumn();
        $count_sehat = $koneksi->query("SELECT COUNT(*) FROM surat_sehat")->fetchColumn();
        $count_kematian = $koneksi->query("SELECT COUNT(*) FROM surat_kematian")->fetchColumn();
        $count_non_pelayanan = $koneksi->query("SELECT COUNT(*) FROM surat_non_pelayanan")->fetchColumn();
        
        $total_pelayanan = $count_sakit + $count_sehat + $count_kematian;
        $total_keseluruhan = $total_pelayanan + $count_non_pelayanan;
    } catch (PDOException $e) {
        die("Gagal memuat rekap database: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard E-Surat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/logo_surabaya.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', sans-serif; }
        body { display: flex; height: 100vh; background: #f4f7f6; color: #334155; }
        
        .sidebar { 
            width: 260px; 
            background: #36454F; 
            color: white; 
            padding: 20px; 
            flex-shrink: 0; 
            height: 100vh; 
            position: sticky; 
            top: 0; 
        }

        .sidebar h2 { font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid #a83d42; padding-bottom: 10px; }
        .menu-item { display: block; padding: 10px 12px; color: #ffccd0; text-decoration: none; margin-bottom: 5px; border-radius: 4px; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: #7a151b; color: white; }
        .logout-btn { margin-top: 15px; color: #ff9999; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; background-color: #f8fafc; }
        .card-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h1 { color: #333; margin-bottom: 20px; }
        .welcome-box { border-left: 5px solid #8e1b21; padding: 15px; background: #fff5f5; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #64748b; background: #ffffff; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; border: 1px solid #e2e8f0; transition: all 0.2s; }
        .back-btn:hover { color: #1e293b; background: #f1f5f9; border-color: #cbd5e1; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: #ffffff; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%; }
        .card h3 { font-size: 13px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; font-weight: 600; }
        .card p { font-size: 32px; font-weight: 700; color: #1e293b; margin-top: 12px; }
        .card.total::before { background: #7c3aed; }
        .card.sakit::before { background: #10b981; }
        .card.sehat::before { background: #3b82f6; }
        .card.kematian::before { background: #ef4444; }
        .card.non_pelayanan::before { background: #FFFF33; }
        .table-wrapper { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden; padding: 10px 0; }
        .table-title { padding: 15px 24px; font-size: 16px; font-weight: 700; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        table th { background: #f8fafc; color: #64748b; font-size: 13px; font-weight: 600; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 16px 24px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        table tr:hover td { background-color: #f8fafc; }

        .btn-filter { text-decoration: none; padding: 8px 14px; font-size: 13px; font-weight: 600; border-radius: 6px; border: 1px solid #cbd5e1; color: #475569; background: #ffffff; transition: 0.2s; }
        .btn-filter:hover { background: #f1f5f9; }
        .btn-filter.active-semua { background: #334155; color: white; border-color: #334155; }
        .btn-filter.active-pelayanan { background: #2563eb; color: white; border-color: #2563eb; }
        .btn-filter.active-non { background: #d97706; color: white; border-color: #d97706; }

        /* KONTROL WIDGET DI BAWAH KIRI (DIPINDAHKAN DARI KANAN ATAS) */
        .widget-controls-left {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }

        .widget-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            font-size: 20px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, background-color 0.2s;
        }

        .widget-btn:hover {
            transform: scale(1.1);
        }

        .btn-add { background: #ff7700; }
        .btn-destroy { background: #ef4444; display: none; }

        /* CONTAINER TEMPAT KARAKTER YANG DI-SPAWN (KANAN BAWAH) */
        #narutoContainer {
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 9997;
            display: flex;
            flex-direction: row-reverse;
            gap: 20px;
            align-items: flex-end;
            pointer-events: none;
        }

        .spawned-char-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: auto;
            animation: popIn 0.3s ease-out;
        }

        .spawned-speech-bubble {
            background: #ffffff;
            color: #1e293b;
            padding: 8px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            margin-bottom: 8px;
            max-width: 180px;
            text-align: center;
            position: relative;
            word-wrap: break-word;
        }

        .spawned-speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #ffffff;
        }

        .spawned-char-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));
            animation: charBounce 0.8s ease-in-out infinite alternate;
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes charBounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-5px); }
        }

        /* MODAL DIALOG POP-UP DI BAWAH KIRI */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.3);
            z-index: 10000;
            display: none;
            align-items: flex-end;
            justify-content: flex-start;
            padding: 75px 20px;
        }

        .modal-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            width: 300px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: popIn 0.2s ease-out;
        }

        .modal-card h4 {
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .modal-card textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            resize: none;
            outline: none;
            margin-bottom: 12px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-modal {
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-modal-cancel { background: #e2e8f0; color: #475569; }
        .btn-modal-submit { background: #ff7700; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>E-Surat Bangkingan</h2>
    <a href="index.php" class="menu-item <?php echo $menu == '' ? 'active' : ''; ?>">🏠 Dashboard</a>
    <a href="index.php?menu=rekap" class="menu-item <?php echo $menu == 'rekap' ? 'active' : ''; ?>">📊 Rekap & Statistik</a>
    <hr style="border:0; border-top:1px solid #a83d42; margin: 15px 0;">
    <p style="padding: 6px 10px; font-size: 14px; font-weight: 600; color: #cbd5e1;">SURAT PELAYANAN</p>
    <a href="form_sakit.php" class="menu-item">🛏️ Surat Sakit</a>
    <a href="form_sehat.php" class="menu-item">📧 Surat Sehat</a>
    <a href="form_kematian.php" class="menu-item">🪦 Surat Kematian</a>
    <p style="padding: 6px 10px; font-size: 14px; font-weight: 600; color: #cbd5e1; margin-top: 10px;">NON PELAYANAN</p>
    <a href="form_non_pelayanan.php" class="menu-item">📝 Input Agenda</a>
    <a href="logout.php" class="menu-item logout-btn">🚪 Keluar / Logout</a>
</div>

<div class="main-content">

    <?php if ($menu == 'rekap') : ?>
        <div class="header-section">
            <div>
                <h2>Statistik &amp; Rekap Penomoran</h2>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="ekspor_pelayanan.php" class="back-btn" style="background-color: #10b981; color: white; border-color: #10b981;">📥 Download Excel Pelayanan</a>
                <a href="ekspor_non_pelayanan.php" class="back-btn" style="background-color: #10b981; color: white; border-color: #10b981;">📥 Download Excel Non Pelayanan</a>
            </div>
        </div>

        <div class="card-grid">
            <div class="card total"><h3>Total Semua Surat</h3><p><?php echo htmlspecialchars($total_keseluruhan); ?></p></div>
            <div class="card sakit"><h3>Surat Sakit</h3><p><?php echo htmlspecialchars($count_sakit); ?></p></div>
            <div class="card sehat"><h3>Surat Sehat</h3><p><?php echo htmlspecialchars($count_sehat); ?></p></div>
            <div class="card kematian"><h3>Surat Kematian</h3><p><?php echo htmlspecialchars($count_kematian); ?></p></div>
            <div class="card non_pelayanan"><h3>Surat Non Pelayanan</h3><p><?php echo htmlspecialchars($count_non_pelayanan); ?></p></div>
        </div>

        <div class="table-wrapper">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 24px; border-bottom: 1px solid #f1f5f9;">
                <div class="table-title" style="padding: 0; border-bottom: none;">📋 Riwayat Sinkronisasi Log Surat</div>
                <div style="background: #f8fafc; padding: 8px 15px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 13px; font-weight: 600; color: #475569;">📥 Import Data (.CSV):</span>
                    <form action="proses_import.php" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 8px; margin: 0;">
                        <input type="file" name="file_csv" accept=".csv" required style="font-size: 13px;">
                        <button type="submit" name="import" style="padding: 5px 12px; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">Mulai Import</button>
                    </form>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 24px; background: #fafafa; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; gap: 8px;">
                    <a href="index.php?menu=rekap&filter=semua" 
                       class="btn-filter <?php echo ($filter == 'semua') ? 'active-semua' : ''; ?>">
                       📋 Semua Surat (<?php echo $total_keseluruhan; ?>)
                    </a>
                    <a href="index.php?menu=rekap&filter=pelayanan" 
                       class="btn-filter <?php echo ($filter == 'pelayanan') ? 'active-pelayanan' : ''; ?>">
                       🩺 Surat Pelayanan (<?php echo $total_pelayanan; ?>)
                    </a>
                    <a href="index.php?menu=rekap&filter=non_pelayanan" 
                       class="btn-filter <?php echo ($filter == 'non_pelayanan') ? 'active-non' : ''; ?>">
                       📑 Surat Non Pelayanan (<?php echo $count_non_pelayanan; ?>)
                    </a>
                </div>

                <div>
                    <form method="GET" action="index.php" style="display: inline-block;">
                        <input type="hidden" name="menu" value="rekap">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <input type="text" name="cari" placeholder="Cari nomor/nama pasien..." value="<?php echo htmlspecialchars($cari); ?>" style="padding: 8px 12px; width: 220px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        <button type="submit" style="padding: 8px 14px; background: #334155; color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer;">Cari</button>
                        <a href="index.php?menu=rekap&filter=<?php echo $filter; ?>" style="padding: 8px; text-decoration: none; color: #ef4444; font-size: 13px; font-weight: 500;">Reset</a>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No Surat</th>
                        <th>Jenis Surat</th>
                        <th>Nama Pasien/Keperluan</th>
                        <th>Nama Dokter/Pj</th>
                        <th>Aksi</th>  
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($stmt_tampil->rowCount() > 0) {
                        while ($row = $stmt_tampil->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['nomor_surat'])."</td>";
                            echo "<td>".htmlspecialchars($row['jenis'])."</td>";
                            echo "<td>".htmlspecialchars($row['subjek'])."</td>";
                            echo "<td>".htmlspecialchars($row['nama_dokter'])."</td>";
                            echo "<td>";
                            
                            if ($row['jenis'] == 'Non-Pelayanan') {
                                echo "<a href='uploads/".htmlspecialchars($row['file_upload'])."' target='_blank'>📂 Buka</a> ";
                            } else {
                                echo "<a href='".htmlspecialchars($row['link'])."?nomor=".urlencode($row['nomor_surat'])."' target='_blank'>🖨️ Cetak</a> ";
                            }

                            echo "<a href='hapus_surat.php?nomor=".urlencode($row['nomor_surat'])."&jenis=".urlencode($row['jenis'])."' onclick=\"return confirm('Yakin ingin menghapus data ini?');\" style='color: red; margin-left: 10px;'>🗑️ Hapus</a>";
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#94a3b8;'>Data tidak ditemukan untuk kategori filter ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table> 

            <div style="text-align: center; margin-top: 20px; padding-bottom: 10px;">
                <?php 
                $param_filter = "&filter=" . urlencode($filter);
                $param_cari = $cari != "" ? "&cari=" . urlencode($cari) : "";
                if($page > 1): ?>
                    <a href="?menu=rekap<?php echo $param_filter . $param_cari; ?>&page=<?php echo $page - 1; ?>">« Sebelumnya</a>
                <?php endif; ?>

                <span style="margin: 0 15px;">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></span>

                <?php if($page < $total_pages): ?>
                    <a href="?menu=rekap<?php echo $param_filter . $param_cari; ?>&page=<?php echo $page + 1; ?>">Selanjutnya »</a>
                <?php endif; ?>
            </div>
        </div>

    <?php else : ?>
        <div class="card-box">
            <h1>Dashboard</h1>
            <div class="welcome-box">
                <p>Selamat datang <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong> di sistem aplikasi E-Surat Puskesmas Bangkingan.</p>
            </div>
            <p style="margin-top:20px; color:#666;">Gunakan menu di samping untuk mengelola data surat atau melihat rekapitulasi data.</p>
        </div>
    <?php endif; ?>

</div>

<div class="widget-controls-left">
    <button class="widget-btn btn-add" onclick="openInputModal()" title="Tambah Karakter Naruto">+</button>
    <button class="widget-btn btn-destroy" id="btnDestroy" onclick="destroyAllCharacters()" title="Destroy All">💥</button>
</div>

<div id="narutoContainer"></div>

<div class="modal-overlay" id="inputModal">
    <div class="modal-card">
        <h4>Panggil Karakter Naruto 🍃</h4>
        <textarea id="narutoText" placeholder="Ketik kalimat dialog karakter di sini..."></textarea>
        <div class="modal-actions">
            <button class="btn-modal btn-modal-cancel" onclick="closeInputModal()">Batal</button>
            <button class="btn-modal btn-modal-submit" onclick="spawnCharacter()">Kirim Dattebayo!</button>
        </div>
    </div>
</div>

<script>
    // --------------------------------------------------------------------------
    // LOGIKA KARAKTER NARUTO (RANDOM CHARACTER SPAWN, LEFT POPUP & CONTROLS)
    // --------------------------------------------------------------------------
    const narutoCharacters = [
        { name: 'Naruto', img: '/naruto.gif' },
        { name: 'Sasuke', img: '/sasuke.gif' },
        { name: 'Kakashi', img: '/kakashi.gif' },
        { name: 'Kisame', img: '/kisame.gif' },
        { name: 'Itachi', img: '/itachi.gif' },
        { name: 'Jiraya', img: '/jiraya.gif' },
        { name: 'Hokage', img: '/hokage.gif' },

    ];

    function openInputModal() {
        document.getElementById('inputModal').style.display = 'flex';
        document.getElementById('narutoText').focus();
    }

    function closeInputModal() {
        document.getElementById('inputModal').style.display = 'none';
        document.getElementById('narutoText').value = '';
    }

    function spawnCharacter() {
        const textInput = document.getElementById('narutoText').value.trim();
        if (!textInput) return;

        // Memilih karakter secara RANDOM dari daftar GIF
        const randomIndex = Math.floor(Math.random() * narutoCharacters.length);
        const randomChar = narutoCharacters[randomIndex];
        
        const charWrapper = document.createElement('div');
        charWrapper.className = 'spawned-char-item';
        
        charWrapper.innerHTML = `
            <div class="spawned-speech-bubble">${escapeHtml(textInput)}</div>
            <img src="${randomChar.img}" alt="${randomChar.name}" class="spawned-char-img" onerror="this.src='/logo_surabaya.png'">
        `;

        document.getElementById('narutoContainer').appendChild(charWrapper);

        document.getElementById('btnDestroy').style.display = 'flex';

        closeInputModal();
    }

    function destroyAllCharacters() {
        const container = document.getElementById('narutoContainer');
        container.innerHTML = '';
        document.getElementById('btnDestroy').style.display = 'none';
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>

<?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
<script>
    alert("Selamat! Anda berhasil login.");
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }
</script>
<?php endif; ?>

</body>
</html>
<?php 
ob_end_flush(); 
?>