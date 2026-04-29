<?php
/**
 * Sınav Takip Sistemi - Öğrenci Yönetim ve Sınıf Atama Paneli
 * Yetki: Sadece Yönetici (Admin)
 */
require_once 'includes/functions.php';
// Güvenlik Kontrolü
if (!isAdmin()) {
    header("HTTP/1.1 403 Forbidden");
    die("Bu sayfaya erişim yetkiniz bulunmamaktadır.");
}

$db = dbConnect();
$message = "";
$messageClass = "";

// 1. Sınıf Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_class'])) {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $class_id   = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

    if ($student_id) {
        try {
            $stmt = $db->prepare("UPDATE users SET class_id = ? WHERE id = ?");
            $target_class = ($class_id === 0) ? null : $class_id;
            
            if ($stmt->execute([$target_class, $student_id])) {
                $message = "Öğrenci sınıf bilgisi başarıyla güncellendi.";
                $messageClass = "alert-success";
            }
        } catch (PDOException $e) {
            $message = "Güncelleme hatası: " . $e->getMessage();
            $messageClass = "alert-error";
        }
    }
}

// 2. Verileri Çekme
try {
    $query = "SELECT u.id, u.name, u.email, c.class_name, u.class_id 
              FROM users u 
              LEFT JOIN classes c ON u.class_id = c.id 
              WHERE u.role = 'student' 
              ORDER BY u.name ASC";
    $students = $db->query($query)->fetchAll();
    $classes = $db->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Yönetimi - Sınav Takip</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="container">
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Yönetim Paneline Dön</a>
    <h2 style="margin-top: 15px;">👥 Öğrenci Listesi ve Sınıf Atama</h2>
    <hr>
    
    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Öğrenci Adı</th>
                <th>E-posta</th>
                <th>Mevcut Sınıf</th>
                <th>Sınıf Ata / Değiştir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($student['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td>
                        <?php echo $student['class_name'] ? htmlspecialchars($student['class_name']) : '<span style="color:#999">Sınıf Atanmamış</span>'; ?>
                    </td>
                    <td>
                        <form method="POST" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                            <select name="class_id" style="margin: 0; width: auto;">
                                <option value="0">Sınıf Seçiniz</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>" <?php echo ($student['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_class" class="btn-primary" style="padding: 8px 15px; font-size: 0.85rem;">Güncelle</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if (empty($students)): ?>
        <p style="text-align: center; padding: 40px; color: #999;">Henüz kayıtlı öğrenci bulunamadı.</p>
    <?php endif; ?>
</div>

</body>
</html>