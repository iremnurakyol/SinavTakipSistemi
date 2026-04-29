<?php
// functions.php
require_once 'db.php';
require_once 'config.php';

// 1. Yönlendirme Fonksiyonu
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// 2. Kullanıcı Giriş Kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 3. Giriş yapan kullanıcının bilgilerini döndüren fonksiyon
function currentUser() {
    if(!isLoggedIn()) return null;
    $db = dbConnect();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. Admin Kontrolü
function isAdmin() {
    // index.php'deki user_role session anahtarı ile eşleşti
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

// 5. E-posta Kontrolü
function emailExists($email) {
    $db = dbConnect();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// 6. Başarı Sırası Hesaplama
function getInstitutionRank($exam_id, $student_score) {
    $db = dbConnect();
    $stmt = $db->prepare("SELECT COUNT(*) + 1 as rank FROM exam_results WHERE exam_id = ? AND total_score > ?");
    $stmt->execute([$exam_id, $student_score]);
    $result = $stmt->fetch();
    return $result['rank'] ?? '-';
}
?>