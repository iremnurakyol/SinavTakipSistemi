<?php
require_once 'includes/functions.php';
if (!isLoggedIn()) redirect('login.php');

$db = dbConnect();

// Kullanıcı bilgilerini güvenli bir şekilde çekiyoruz
$user = currentUser();
if (!$user) {
    // Eğer oturum var ama veritabanında kullanıcı yoksa (nadir bir durum)
    redirect('logout.php');
}

$u_id = $user['id'];
$u_name = $user['name'] ?? 'Kullanıcı';
$u_role = $_SESSION['user_role'] ?? $user['role']; // Hem session hem DB kontrolü

// İstatistikleri Çekelim
if (isAdmin()) {
    $total_students = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $total_exams = $db->query("SELECT COUNT(*) FROM exams")->fetchColumn();
    
    $genel_ort_res = $db->query("SELECT AVG(total_score) FROM exam_results")->fetchColumn();
    $genel_ort = $genel_ort_res ? $genel_ort_res : 0;
} else {
    $stmt = $db->prepare("SELECT COUNT(*) FROM exam_results WHERE student_id = ?");
    $stmt->execute([$u_id]);
    $my_exams_count = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT AVG(total_score) FROM exam_results WHERE student_id = ?");
    $stmt->execute([$u_id]);
    $my_avg_res = $stmt->fetchColumn();
    $my_avg = $my_avg_res ? $my_avg_res : 0;

    $stmt = $db->prepare("SELECT total_score FROM exam_results WHERE student_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$u_id]);
    $last_score = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Sınav Takip Sistemi</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="sidebar">
    <h2>🎯 Sınav Takip</h2>
    <ul>
        <li><a href="index.php">🏠 Ana Sayfa</a></li>
        
        <?php if (isAdmin()): ?>
            <li><a href="student_management.php">👥 Öğrenci Yönetimi</a></li>
            <li><a href="classes.php">🏫 Sınıf Yönetimi</a></li>
            <li><a href="exams.php">📝 Sınav Tanımla</a></li>
            <li><a href="results_entry.php">📊 Not Girişi</a></li>
            <li><a href="ranking.php">🏆 Genel Sıralama</a></li>
        <?php else: ?>
            <li><a href="reports.php">📈 Gelişim Grafiğim</a></li>
            <li><a href="ranking.php">🏆 Kurum Sıralaması</a></li>
        <?php endif; ?>
        
        <li style="margin-top: 20px;"><a href="logout.php" style="color: #e74c3c;">🚪 Çıkış Yap</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="container">
        <h1>Hoş geldin, <?= htmlspecialchars($u_name) ?>! 👋</h1>
        <p>Sınav Takip Sistemi üzerinden güncel sınav sonuçlarını görebilir ve gelişimini takip edebilirsin.</p>
        
        <div class="stats-grid">
            <?php if (isAdmin()): ?>
                <div class="stat-card">
                    <h4>Toplam Öğrenci</h4>
                    <p><?= $total_students ?> Kişi</p>
                </div>
                <div class="stat-card" style="border-left-color: #27ae60;">
                    <h4>Tanımlı Sınavlar</h4>
                    <p><?= $total_exams ?> Adet</p>
                </div>
                <div class="stat-card" style="border-left-color: #f1c40f;">
                    <h4>Kurum Ortalaması</h4>
                    <p><?= number_format((float)$genel_ort, 2) ?></p>
                </div>
            <?php else: ?>
                <div class="stat-card">
                    <h4>Girdiğim Sınavlar</h4>
                    <p><?= $my_exams_count ?> Sınav</p>
                </div>
                <div class="stat-card" style="border-left-color: #27ae60;">
                    <h4>Genel Ortalamam</h4>
                    <p><?= number_format((float)$my_avg, 2) ?></p>
                </div>
                <div class="stat-card" style="border-left-color: #e67e22;">
                    <h4>Son Sınav Puanım</h4>
                    <p><?= $last_score ? number_format((float)$last_score, 2) : '0.00' ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 40px;">
            <h3>Hızlı İşlemler</h3>
            <div class="stats-grid">
                <?php if (isAdmin()): ?>
                    <a href="results_entry.php" class="card" style="text-decoration: none; text-align: center; color: var(--primary);">
                        <h3>➕ Not Gir</h3>
                        <p>Yeni bir sınav sonucu ekle</p>
                    </a>
                    <a href="ranking.php" class="card" style="text-decoration: none; text-align: center; color: var(--primary);">
                        <h3>🏆 Sıralama</h3>
                        <p>Genel başarıyı görüntüle</p>
                    </a>
                <?php else: ?>
                    <a href="reports.php" class="card" style="text-decoration: none; text-align: center; color: var(--primary);">
                        <h3>📈 Grafiğim</h3>
                        <p>Gelişimini incele</p>
                    </a>
                    <a href="export_pdf.php" target="_blank" class="card" style="text-decoration: none; text-align: center; color: var(--primary);">
                        <h3>📄 Karne Al</h3>
                        <p>Sonuçlarını PDF olarak indir</p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>