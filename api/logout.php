<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Hapus semua variabel session
$_SESSION = array();

// 2. Hancurkan session
session_destroy();

// 3. Hapus Cookie 'user_login' dari browser agar tidak otomatis re-login
if (isset($_COOKIE['user_login'])) {
    setcookie('user_login', '', time() - 3600, '/');
}

// 4. Notifikasi dan Redirect ke Login
echo "<script>
        alert('Anda berhasil logout. Sampai jumpa!');
        window.location.href = 'login.php';
      </script>";
exit();
?>