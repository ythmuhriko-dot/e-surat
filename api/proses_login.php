<?php
// Aktifkan output buffering untuk kelancaran redirect di Vercel
ob_start();

// Memulai session dengan aman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? ''; //[cite: 3]
    $password = $_POST['password'] ?? ''; //[cite: 3]

    try {
        $query = "SELECT * FROM users WHERE username = :username AND password = :password"; //[cite: 3]
        $stmt = $koneksi->prepare($query); //[cite: 3]
        $stmt->execute([
            ':username' => $username, //[cite: 3]
            ':password' => $password //[cite: 3]
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC); //[cite: 3]

        if ($user) {
            // Pasang session dasar
            $_SESSION['login'] = true; //[cite: 3]
            $_SESSION['username'] = $username; //[cite: 3]
            
            // [KUNCI SUKSES] Set Cookie login agar awet di lingkungan Serverless Vercel (aktif selama 1 hari)
            setcookie('user_login', $username, time() + (86400 * 1), "/");
            
            // Redirect langsung via PHP header (tanpa echo script agar session tidak rusak)
            header("Location: index.php");
            exit();
        } else {
            // Jika gagal login, gunakan redirect PHP murni dan bawa status error
            header("Location: login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        die("Error Login: " . $e->getMessage()); //[cite: 3]
    }
}
ob_end_flush();
?>