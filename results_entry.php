<?php
/**
 * Sınav Takip Sistemi - Sınav Sonuç Giriş Sayfası
 * Yetki: Sadece Yönetici (Admin)
 */
require_once 'includes/functions.php';

// Yetki Kontrolü
if (!isAdmin()) {
    header("HTTP/1.1 403 Forbidden");
    die("Bu alana erişim yetkiniz bulunmamaktadır.");
}

$db = dbConnect();
$message = "";
$messageClass = "";

// Sınavları ve Öğrencileri Veritabanından Çekelim
try {
    $exams = $db->query("SELECT id, exam_name, exam_type FROM exams ORDER BY exam_date DESC")->fetchAll();
    $students = $db->query("SELECT id, name FROM users WHERE role = 'student' ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Veri çekme hatası: " . $e->getMessage());
}

// Form Gönderimi İşleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $exam_id    = filter_input(INPUT_POST, 'exam_id', FILTER_VALIDATE_INT);
    
    // Ders Verilerini Alalım
    $t_d = (float)($_POST['tur_d'] ?? 0); $t_y = (float)($_POST['tur_y'] ?? 0);
    $m_d = (float)($_POST['mat_d'] ?? 0); $m_y = (float)($_POST['mat_y'] ?? 0);
    
    // Net Hesaplama (4 Yanlış 1 Doğruyu Götürür Kuralı)
    $tur_net = max(0, $t_d - ($t_y * 0.25));
    $mat_net = max(0, $m_d - ($m_y * 0.25));
    
    // Temsili Puan Hesaplama Modeli
    $total_score = ($tur_net + $mat_net) * 5;

    // Verilerin JSON Olarak Hazırlanması
    $scores_array = [
        'turkce' => ['dogru' => $t_d, 'yanlis' => $t_y, 'net' => $tur_net],
        'matematik' => ['dogru' => $m_d, 'yanlis' => $m_y, 'net' => $mat_net]
    ];
    $scores_json = json_encode($scores_array);

    if ($student_id && $exam_id) {
        try {
            $stmt = $db->prepare("INSERT INTO exam_results (student_id, exam_id, scores_json, total_score) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $exam_id, $scores_json, $total_score]);
            $message = "Sınav sonucu başarıyla kaydedildi.";
            $messageClass = "alert-success"; // style.css ile uyumlu hale getirildi
        } catch (PDOException $e) {
            $message = "Veritabanı hatası: " . $e->getMessage();
            $messageClass = "alert-error"; // style.css ile uyumlu hale getirildi
        }
    } else {
        $message = "Lütfen geçerli bir öğrenci ve sınav seçiniz.";
        $messageClass = "alert-error";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sonuç Girişi - Yönetim Paneli</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="container">
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Panele Dön</a>
    <h2>📊 Sınav Sonucu Giriş Paneli</h2>
    <hr>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" style="margin-top: 20px;">
        <div class="form-group">
            <label for="student_id">Öğrenci Seçimi</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- Öğrenci Seçiniz --</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="exam_id">Sınav Seçimi</label>
            <select name="exam_id" id="exam_id" required>
                <option value="">-- Sınav Seçiniz --</option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['exam_name']); ?> (<?php echo $e['exam_type']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Ders Verileri (Doğru / Yanlış)</label>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div class="subject-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: center;">
                    <span>Türkçe</span>
                    <input type="number" name="tur_d" placeholder="D" min="0" max="40" required>
                    <input type="number" name="tur_y" placeholder="Y" min="0" max="40" required>
                </div>
                <div class="subject-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: center;">
                    <span>Matematik</span>
                    <input type="number" name="mat_d" placeholder="D" min="0" max="40" required>
                    <input type="number" name="mat_y" placeholder="Y" min="0" max="40" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 20px;">Sonucu Kaydet</button>
    </form>
</div>

</body>
</html>