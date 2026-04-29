<?php
/**
 * export_pdf.php - Yazdırılabilir Karne Sayfası
 */
require_once 'includes/functions.php';
if (!isLoggedIn()) redirect('login.php');

$db = dbConnect();
$user = currentUser();
$u_id = $user['id'];

// Öğrencinin sınav sonuçlarını çekelim
$stmt = $db->prepare("SELECT e.exam_name, e.exam_date, r.total_score 
                      FROM exam_results r 
                      JOIN exams e ON r.exam_id = e.id 
                      WHERE r.student_id = ? 
                      ORDER BY e.exam_date DESC");
$stmt->execute([$u_id]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Karne - <?php echo htmlspecialchars($user['name']); ?></title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
    <style>
        body { font-family: sans-serif; padding: 40px; line-height: 1.6; }
        .header { text-align: center; border-bottom: 3px solid #3498db; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; }
        .no-print-btn { 
            background: #27ae60; color: white; padding: 10px 20px; 
            text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px;
        }
        /* Yazdırırken butonun görünmemesi için */
        @media print {
            .no-print-btn { display: none; }
        }
    </style>
</head>
<body>

    <a href="#" onclick="window.print();" class="no-print-btn">🖨️ PDF Olarak Kaydet / Yazdır</a>
    <a href="index.php" class="no-print-btn" style="background:#7f8c8d;">← Panele Dön</a>

    <div class="header">
        <h1>SINAV SONUÇ KARNESİ</h1>
        <p><strong>Öğrenci:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Tarih:</strong> <?php echo date('d.m.Y'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sınav Adı</th>
                <th>Sınav Tarihi</th>
                <th>Toplam Puan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['exam_name']); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($r['exam_date'])); ?></td>
                    <td><strong><?php echo number_format($r['total_score'], 2); ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>