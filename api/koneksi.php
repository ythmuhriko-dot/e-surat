<?php
// Ambil variabel dari Environment Variables di Vercel demi keamanan
// Jika variabel di Vercel belum disetel, dia otomatis memakai string di sebelah kanan sebagai default (fallback)
$host     = getenv('DB_HOST') ?: 'db.ervxtpptbgejbvwiohrd.supabase.co'; // Ganti dengan Host URI Supabase Anda
$port     = getenv('DB_PORT') ?: '6543';
$database = getenv('DB_NAME') ?: 'postgres';
$user     = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '@26040Riko1'; // Ganti dengan password database Supabase Anda

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