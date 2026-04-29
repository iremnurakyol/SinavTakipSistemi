<?php
// config ve functions'ı dahil etmeyi asla unutma knk
require_once 'includes/functions.php';

// Eğer zaten giriş yapmışsa direkt ana sayfaya yolla, burada işi yok
if (isLoggedIn()) {
    redirect('index.php');
}

$error = "";
$success = "";

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verileri temizleyerek alalım (Girdi Doğrulama - Temizlik)
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Kritik İşlem: Boş alan kontrolü
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Tüm alanları doldurman lazım knk!";
    } 
    // 2. Kritik İşlem: Geçerli e-posta kontrolü
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Bu e-posta biraz tuhaf duruyor, kontrol et.";
    }
    // 3. Kritik İşlem: Aynı e-posta var mı? (functions.php'deki fonksiyonu kullanıyoruz)
    elseif (emailExists($email)) {
        $error = "Bu e-posta adresi zaten sisteme kayıtlı.";
    } 
    else {
        // Her şey temizse kaydı yapalım
        $db = dbConnect();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                $success = "Kayıt başarılı! Şimdi giriş yapabilirsin.";
            } else {
                $error = "Kayıt sırasında teknik bir aksilik oldu.";
            }
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sınav Takip - Kayıt Ol</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="register-card">
    <h3>Kayıt Ol</h3>
    <?php if ($error): ?> <p class="error"><?php echo $error; ?></p> <?php endif; ?>
    <?php if ($success): ?> <p class="success"><?php echo $success; ?></p> <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Ad Soyad" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
        <input type="email" name="email" placeholder="E-posta Adresi">
        <input type="password" name="password" placeholder="Şifre">
        <button type="submit">Sisteme Katıl</button>
    </form>
    <p style="font-size: 0.8rem; text-align: center;">Zaten üye misin? <a href="login.php">Giriş Yap</a></p>
</div>

</body>
</html>