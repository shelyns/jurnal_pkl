<?php
// login.php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php'); 
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ubah penangkapan 'email' menjadi 'username'
    $username = trim($_POST['username'] ?? ''); 
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Query tetap mencari di kolom 'email' (karena isinya sekarang username)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email']; // Berisi username sekarang
            $_SESSION['role']    = $user['role'];
            $_SESSION['photo']   = $user['photo'];
            redirect('dashboard.php');
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Mohon isi username dan password.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .login-box { max-width: 400px; margin: 80px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .login-box h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body style="background: #edf2f7;">
    <div class="login-box">
        <h2>Login <?= APP_NAME ?></h2>
        
        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <!-- Ubah label & input name menjadi username, type text (bukan email) -->
            <label>Username</label>
            <input type="text" name="username" required placeholder="anggota1" value="<?= e($_POST['username'] ?? '') ?>">
            
            <label>Password</label>
            <input type="password" name="password" required placeholder="password">
            
            <button type="submit" style="width: 100%; padding: 12px; font-size: 16px;">Masuk</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px; font-size: 14px;">
            <a href="<?= BASE_URL ?>/index.php">← Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>