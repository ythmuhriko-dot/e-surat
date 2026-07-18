<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-Surat</title>
    <style>
        body {
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            background: white;
            padding: 40px;
            width: 320px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 { 
            color: #1e293b; 
            margin-bottom: 25px; 
            font-weight: 600; 
            font-size: 20px; 
        }
        .error-message {
            color: #ef4444; 
            font-size: 14px; 
            margin-bottom: 20px; 
            font-weight: 500;
            background-color: #fef2f2;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #fee2e2;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border 0.3s;
        }
        input:focus { border-color: #3b82f6; outline: none; }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background-color: #2563eb; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>E-Surat Puskesmas Bangkingan</h2>
    
    <?php 
    // Menampilkan pesan error jika URL mendeteksi parameter error=1
    if (isset($_GET['error'])): 
    ?>
        <div class="error-message">
            ❌ Username atau Password salah!
        </div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>