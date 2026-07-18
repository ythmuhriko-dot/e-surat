<?php
session_start();
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
            $_SESSION['login'] = true;
            $_SESSION['username'] = $username;
            
            echo "<script>
                    alert('Login Berhasil! Selamat Datang, " . htmlspecialchars($username) . "');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Username atau Password salah!');
                    window.location.href = 'login.php';
                  </script>";
        }
    } catch (PDOException $e) {
        echo "Error Login: " . $e->getMessage();
    }
}
?>