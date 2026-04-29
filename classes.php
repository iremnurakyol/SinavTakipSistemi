<?php
require_once 'includes/functions.php';

// Güvenlik: Sadece Admin girebilir!
if (!isAdmin()) {
    header("HTTP/1.1 403 Forbidden");
    die("Bu alana erişim yetkiniz bulunmamaktadır.");
}

$db = dbConnect();
$error = "";
$success = "";

// 1. Yeni Sınıf Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $class_name = trim($_POST['class_name'] ?? '');
    
    if (empty($class_name)) {
        $error = "Sınıf adı boş olamaz!";
    } else {
        $stmt = $db->prepare("INSERT INTO classes (class_name) VALUES (?)");
        if ($stmt->execute([$class_name])) {
            $success = "Sınıf başarıyla oluşturuldu.";
        }
    }
}

// 2. Sınıfları Listeleme
$classes = $db->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınıf Yönetimi - Sınav Takip</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="container">
    <h2>🏫 Sınıf Yönetimi</h2>
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Panele Dön</a>
    <hr>

    <?php if ($error): ?> 
        <div class="alert alert-error"><?php echo $error; ?></div> 
    <?php endif; ?>
    
    <?php if ($success): ?> 
        <div class="alert alert-success"><?php echo $success; ?></div> 
    <?php endif; ?>

    <form method="POST" style="margin-top: 20px;">
        <div style="display: flex; gap: 10px;">
            <input type="text" name="class_name" placeholder="Örn: 12-A Sayısal" required>
            <button type="submit" name="add_class" class="btn-success">Ekle</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sınıf Adı</th>
                <th>Öğrenci Sayısı</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['class_name']); ?></strong></td>
                    <td>
                        <?php 
                        $count = $db->prepare("SELECT COUNT(*) FROM users WHERE class_id = ?");
                        $count->execute([$c['id']]);
                        echo $count->fetchColumn();
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>