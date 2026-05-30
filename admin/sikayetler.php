<?php
session_start();
require_once __DIR__ . '/../bl/admin_bl.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../giris.php"); exit;
}

$adminService = new admin_bl();

if (isset($_GET['talep_sil'])) {
    $adminService->talepSil($_GET['talep_sil']);
    header("Location: sikayetler.php"); exit;
}

$talepler = $adminService->tumTalepler();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Şikayetler & Talepler</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { padding: 40px; background: #f1f5f9; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { color: #35508b; margin-bottom: 20px; border-left: 5px solid #5957cc; padding-left: 10px; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #5957cc; font-size: 14px; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .geri-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #35508b; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <a href="panel.php" class="geri-link">← Panele Dön</a>
    <h1>Şikayet ve Bilgi Talepleri</h1>
    <table>
        <thead>
            <tr>
                <th>Gönderen</th>
                <th>Bölüm / Şikayet Türü</th>
                <th>Açıklama / Mesaj</th>
                <th style="width: 100px;">İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($talepler as $t): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars(($t['ad'] ?? '') . " " . ($t['soyad'] ?? '')) ?></strong><br>
                    <small style="color: #64748b;"><?= htmlspecialchars($t['email'] ?? '') ?></small>
                </td>
                <td><?= htmlspecialchars($t['ilgili_bolum'] ?? '') ?> / <?= htmlspecialchars($t['sikayet_turu'] ?? '') ?></td>
                <td><?= htmlspecialchars($t['aciklama'] ?? '') ?></td>
                <td><a href="?talep_sil=<?= $t['id'] ?>" class="btn btn-delete" onclick="return confirm('Talebi silmek istiyor musunuz?')">Sil</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>