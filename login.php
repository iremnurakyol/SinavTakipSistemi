<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Yolun doğruluğundan emin olalım (login.php ana dizinde, functions includes içinde)
if (file_exists('includes/functions.php')) {
    require_once 'includes/functions.php';
} else {
    die("Hata: includes/functions.php dosyası bulunamadı! Lütfen dosya yolunu kontrol et.");
}

// 1. OTURUM KONTROLÜ
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// 1. OTURUM KONTROLÜ
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = "";

// 2. GİRİŞ İŞLEMİ (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $db = dbConnect();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Veritabanındaki hash ile girilen şifreyi karşılaştırıyoruz
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        header("Location: index.php");
        exit();
    } else {
        $error = "E-posta adresi veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Sınav Takip Sistemi</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* login.php için daha önce verdiğim o şık CSS kodlarını buraya yapıştır */
        :root { --primary-color: #3498db; --secondary-color: #2c3e50; --bg-color: #f4f7f6; }
        body { display: flex; align-items: center; justify-content: center; height: 100vh; background: linear-gradient(135deg, #f4f7f6 0%, #e0eafc 100%); font-family: 'Poppins', sans-serif; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 420px; }
        .brand-header { text-align: center; margin-bottom: 35px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; }
        .input-group input { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; }
        .btn-login { width: 100%; padding: 12px; background: var(--primary-color); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .error-alert { background: #fdf2f2; color: #e74c3c; padding: 10px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <h1>🎯 Sınav Takip</h1>
        <p>Lütfen giriş yapın</p>
    </div>

    <?php if ($error): ?>
        <div class="error-alert">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>E-posta Adresi</label>
            <div class="input-group">
                <input type="email" name="email" placeholder="ornek@test.com" required>
            </div>
        </div>
        <div class="form-group">
            <label>Şifre</label>
            <div class="input-group">
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" class="btn-login">Sisteme Giriş Yap</button>
    </form>
</div>

</body>
</html>