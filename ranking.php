<?php
/**
 * Sınav Takip Sistemi - Kurum Geneli Sıralama Raporu
 */
require_once 'includes/functions.php';
if (!isLoggedIn()) redirect('login.php');

$db = dbConnect();
$selected_exam = filter_input(INPUT_GET, 'exam_id', FILTER_VALIDATE_INT);

// Sınav listesini dropdown için çekelim
$exams = $db->query("SELECT id, exam_name, exam_type FROM exams ORDER BY exam_date DESC")->fetchAll();

$rankings = [];
if ($selected_exam) {
    // SQL: Puanları sıralar ve öğrenci/sınıf bilgilerini getirir
    $query = "SELECT u.name, c.class_name, r.total_score, r.scores_json,
              RANK() OVER (ORDER BY r.total_score DESC) as genel_sira
              FROM exam_results r
              JOIN users u ON r.student_id = u.id
              LEFT JOIN classes c ON u.class_id = c.id
              WHERE r.exam_id = ?
              ORDER BY r.total_score DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$selected_exam]);
    $rankings = $stmt->fetchAll();
    
    $total_students = count($rankings);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sıralama ve Başarı Raporu - Sınav Takip</title>
<link rel="stylesheet" href="/SinavTakipSistemi/assets/style.css">
    <style>
        /* Sadece bu sayfaya özel, ilk 3'e girenleri vurgulayan küçük bir dokunuş */
        .top-rank { background-color: #fff9c4 !important; font-weight: bold; }
        .percentile { font-size: 0.85rem; color: #7f8c8d; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" style="text-decoration: none; color: var(--primary); font-size: 0.9rem;">← Panele Dön</a>
    <h2 style="margin-top: 15px;">🏆 Kurum İçi Başarı Sıralaması</h2>
    <hr>

    <div class="card" style="background: #f8f9fa; margin-bottom: 25px;">
        <form method="GET">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Sınav Seçiniz:</label>
            <select name="exam_id" onchange="this.form.submit()" style="max-width: 400px;">
                <option value="">-- Listelemek için sınav seçin --</option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $selected_exam == $e['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['exam_name']) ?> (<?= $e['exam_type'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($selected_exam && !empty($rankings)): ?>
        <table>
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Öğrenci Adı</th>
                    <th>Sınıf</th>
                    <th>Toplam Puan</th>
                    <th>Yüzdelik Dilim</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankings as $r): 
                    $percentile = ($r['genel_sira'] / $total_students) * 100;
                ?>
                    <tr class="<?= $r['genel_sira'] <= 3 ? 'top-rank' : '' ?>">
                        <td style="width: 60px; text-align: center;">
                            <?php 
                                if($r['genel_sira'] == 1) echo "🥇";
                                elseif($r['genel_sira'] == 2) echo "🥈";
                                elseif($r['genel_sira'] == 3) echo "🥉";
                                else echo $r['genel_sira'];
                            ?>
                        </td>
                        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                        <td><?= htmlspecialchars($r['class_name'] ?? 'Sınıfsız') ?></td>
                        <td><?= number_format($r['total_score'], 2) ?></td>
                        <td class="percentile">%<?= number_format($percentile, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($selected_exam): ?>
        <div class="alert alert-error">Bu sınava ait henüz veri girişi yapılmamış.</div>
    <?php else: ?>
        <p style="text-align: center; color: #7f8c8d; padding: 40px;">Sıralamayı görmek için yukarıdan bir sınav seçin.</p>
    <?php endif; ?>
</div>

</body>
</html>