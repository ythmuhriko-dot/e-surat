<?php
// Aktifkan output buffering untuk kelancaran redirect di Vercel
ob_start();

// Memulai session dengan aman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? ''; 
    $password = $_POST['password'] ?? ''; 

    try {
        $query = "SELECT * FROM users WHERE username = :username AND password = :password"; 
        $stmt = $koneksi->prepare($query); 
        $stmt->execute([
            ':username' => $username, 
            ':password' => $password 
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($user) {
            // Pasang session dasar
            $_SESSION['login'] = true; 
            $_SESSION['username'] = $username; 
            
            // [KUNCI SUKSES] Set Cookie login agar awet di lingkungan Serverless Vercel (aktif selama 1 hari)
            setcookie('user_login', $username, time() + (86400 * 1), "/");
            
            // Redirect dengan membawa parameter sukses login
            header("Location: index.php?login=success");
            exit();
        } else {
            // Jika gagal login, gunakan redirect PHP murni dan bawa status error
            header("Location: login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        die("Error Login: " . $e->getMessage()); 
    }
}
ob_end_flush();
?>