<?php
require_once 'includes/functions.php';
if (!isLoggedIn()) redirect('login.php');

$db = dbConnect();

// Eğer öğrenciyse kendi id'sini, adminse seçilen öğrenciyi görelim
$target_student = isAdmin() ? (int)($_GET['student_id'] ?? $_SESSION['user_id']) : $_SESSION['user_id'];

// Öğrenci bilgilerini çekelim (Başlıkta isim göstermek için)
$userStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
$userStmt->execute([$target_student]);
$student_name = $userStmt->fetchColumn();

// Öğrencinin sınav geçmişini çekelim
$query = "SELECT e.exam_name, e.exam_date, r.total_score 
          FROM exam_results r 
          JOIN exams e ON r.exam_id = e.id 
          WHERE r.student_id = ? 
          ORDER BY e.exam_date ASC";
$stmt = $db->prepare($query);
$stmt->execute([$target_student]);
$history = $stmt->fetchAll();

// Grafik için verileri hazırlayalım
$labels = [];
$data = [];
foreach ($history as $h) {
    $labels[] = $h['exam_name'];
    $data[] = (float)$h['total_score'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gelişim Raporu - Sınav Takip</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
</head>
<body>

<div class="container" style="max-width: 900px;">
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Panele Dön</a>
    <h2 style="margin-top: 15px;">📈 Gelişim Grafiği: <span style="color: var(--success);"><?= htmlspecialchars($student_name) ?></span></h2>
    <hr>

    <?php if (empty($history)): ?>
        <div class="alert alert-error">Henüz girilmiş bir sınav sonucu bulunamadığı için grafik oluşturulamıyor.</div>
    <?php else: ?>
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <canvas id="growthChart" style="max-height: 400px;"></canvas>
        </div>
        
        <div style="margin-top: 30px;">
            <h3>Son Sınav Performansları</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sınav Adı</th>
                        <th>Tarih</th>
                        <th>Toplam Puan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($history) as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['exam_name']) ?></td>
                            <td><?= date('d.m.Y', strtotime($h['exam_date'])) ?></td>
                            <td><strong><?= number_format($h['total_score'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
const ctx = document.getElementById('growthChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Sınav Puanı',
            data: <?= json_encode($data) ?>,
            borderColor: '#2980b9',
            backgroundColor: 'rgba(41, 128, 185, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' }
        },
        scales: {
            y: { 
                beginAtZero: false,
                grid: { color: '#f0f0f0' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>

</body>
</html>