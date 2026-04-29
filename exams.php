<?php
require_once 'includes/functions.php';

// Güvenlik: Yetki Kontrolü
if (!isAdmin()) {
    header("HTTP/1.1 403 Forbidden");
    die("Bu alana erişim yetkiniz bulunmamaktadır.");
}

$db = dbConnect();
$error = "";
$success = "";

// Sınav Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $name = trim($_POST['exam_name'] ?? '');
    $type = $_POST['exam_type'] ?? 'TYT';
    $date = $_POST['exam_date'] ?? '';

    if (empty($name) || empty($date)) {
        $error = "Sınav adı ve tarihi alanları zorunludur.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO exams (exam_name, exam_type, exam_date) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $type, $date])) {
                $success = "Sınav başarıyla sisteme kaydedildi.";
            }
        } catch (PDOException $e) {
            $error = "Kayıt hatası: " . $e->getMessage();
        }
    }
}

// Mevcut Sınavları Çekme
$exams = $db->query("SELECT * FROM exams ORDER BY exam_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Yönetimi - Sınav Takip</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="container">
    <h2>📝 Sınav Tanımlama</h2>
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Panele Dön</a>
    <hr>

    <?php if ($error): ?> 
        <div class="alert alert-error"><?php echo $error; ?></div> 
    <?php endif; ?>
    
    <?php if ($success): ?> 
        <div class="alert alert-success"><?php echo $success; ?></div> 
    <?php endif; ?>

    <form method="POST" style="margin-top: 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div>
                <label style="font-size: 0.8rem;">Sınav Adı</label>
                <input type="text" name="exam_name" placeholder="Örn: Özdebir TYT-1" required>
            </div>
            <div>
                <label style="font-size: 0.8rem;">Tür</label>
                <select name="exam_type">
                    <option value="TYT">TYT</option>
                    <option value="AYT">AYT</option>
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem;">Tarih</label>
                <input type="date" name="exam_date" required>
            </div>
            <button type="submit" name="add_exam" class="btn-primary" style="height: 42px;">Kaydet</button>
        </div>
    </form>

    <table style="margin-top: 30px;">
        <thead>
            <tr>
                <th>Sınav Adı</th>
                <th>Tür</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exams as $e): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($e['exam_name']); ?></strong></td>
                    <td><?php echo $e['exam_type']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($e['exam_date'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if (empty($exams)): ?>
        <p style="text-align: center; color: #999; padding: 20px;">Henüz tanımlanmış bir sınav bulunmuyor.</p>
    <?php endif; ?>
</div>

</body>
</html>