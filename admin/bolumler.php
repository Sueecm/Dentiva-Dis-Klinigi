<?php
session_start();
require_once __DIR__ . '/../bl/admin_bl.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../giris.php"); exit;
}

$adminService = new admin_bl();
$hata_mesaji = "";
$basari_mesaji = "";

if (isset($_POST['bolum_ekle'])) {
    try {
        $adminService->bolumEkle($_POST['bolum_adi']);
        $basari_mesaji = "Bölüm başarıyla eklendi.";
    } catch (Exception $e) {
        $hata_mesaji = "Hata: " . $e->getMessage();
    }
}

if (isset($_GET['bolum_sil'])) {
    try {
        $adminService->bolumSil($_GET['bolum_sil']);
        $basari_mesaji = "Bölüm başarıyla silindi.";
    } catch (Exception $e) {
        $hata_mesaji = $e->getMessage();
    }
}

$bolumler = $adminService->tumBolumler();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Bölüm Yönetimi</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { padding: 40px; background: #f1f5f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { color: #35508b; margin-bottom: 20px; border-left: 5px solid #5957cc; padding-left: 10px; font-size: 24px; }
        .form-row { display: flex; gap: 10px; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f8fafc; color: #5957cc; border-bottom: 2px solid #edf2f7; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .geri-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #35508b; font-weight: bold; }
        
        .mesaj-kutusu { padding: 12px; margin-bottom: 20px; border-radius: 10px; font-size: 14px; font-weight: 500; line-height: 1.5; }
        .hata { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
        .basari { background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; }
    </style>
</head>
<body>
<div class="container">
    <a href="panel.php" class="geri-link">← Panele Dön</a>
    <h1>Bölüm Yönetimi</h1>

    <?php if(!empty($hata_mesaji)): ?>
        <div class="mesaj-kutusu hata">
            <strong>Sistem Uyarısı:</strong> <?= htmlspecialchars($hata_mesaji) ?>
        </div>
    <?php endif; ?>
    <?php if(!empty($basari_mesaji)): ?>
        <div class="mesaj-kutusu basari">
            <strong>Başarılı:</strong> <?= htmlspecialchars($basari_mesaji) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="form-row" action="bolumler.php">
        <input type="text" name="bolum_adi" placeholder="Yeni Bölüm Adı" required style="flex:1; padding:10px; border:1px solid #ddd; border-radius:8px;">
        <button type="submit" name="bolum_ekle" class="btn btn-add" style="padding:10px 20px; background:#5957cc; color:white; border:none; border-radius:8px; cursor:pointer;">Bölüm Ekle</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Bölüm Adı</th>
                <th style="width: 100px;">İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($bolumler as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['bolum_adi']) ?></td>
                <td><a href="?bolum_sil=<?= $b['bolum_id'] ?>" style="background:#ef4444; color:white; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:12px;" onclick="return confirm('Bölümü silmek istiyor musunuz?')">Sil</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>