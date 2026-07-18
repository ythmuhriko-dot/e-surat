<?php
session_start();

// 1. Hapus semua data session
session_unset();
session_destroy();

// 2. Tampilkan pop-up notifikasi sebelum mengarahkan kembali ke login
echo "<script>
        alert('Anda berhasil logout. Sampai jumpa!');
        window.location.href = 'login.php';
      </script>";
exit();
?>