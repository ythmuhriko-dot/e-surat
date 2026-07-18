<?php
// Ambil variabel dari Environment Variables di Vercel demi keamanan
// Jika variabel di Vercel belum disetel, dia otomatis memakai string di sebelah kanan sebagai default (fallback)
$host     = getenv('DB_HOST') ?: 'aws-0-ap-northeast-1.pooler.supabase.com';
$port     = getenv('DB_PORT') ?: '6543'; // Ubah dari 6543 ke 5432 jika pakai host pooler diatas
$database = getenv('DB_NAME') ?: 'postgres';
$user     = getenv('DB_USER') ?: 'postgres.zapcouvzxhijqalbcpzq'; // Tambahkan ID project-mu
$password = getenv('DB_PASSWORD') ?: 'RIKO260402@1r';

try {
    // Membuat koneksi ke PostgreSQL via PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;";
    $koneksi = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Menampilkan pesan error jika koneksi gagal
    echo "Koneksi database gagal: " . $e->getMessage();
    exit;
}
?>