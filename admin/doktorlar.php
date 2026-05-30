<?php
session_start();
require_once __DIR__ . '/../bl/admin_bl.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../giris.php"); exit;
}

$adminService = new admin_bl();
$hata_mesaji = "";
$basari_mesaji = "";

if (isset($_POST['doktor_ekle'])) {
    try {
        $adminService->doktorEkle($_POST['ad'], $_POST['soyad'], $_POST['bolum_id']);
        $basari_mesaji = "Doktor başarıyla eklendi.";
    } catch (Exception $e) {
        $hata_mesaji = "Hata: " . $e->getMessage();
    }
}

if (isset($_GET['doktor_sil'])) {
    try {
        $adminService->doktorSil($_GET['doktor_sil']);
        $basari_mesaji = "Doktor başarıyla silindi.";
    } catch (Exception $e) {
        $hata_mesaji = $e->getMessage();
    }
}

$doktorlar = $adminService->tumDoktorlarDetayli();
$bolumler  = $adminService->tumBolumler();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Doktor Yönetimi</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { padding: 40px; background: #f1f5f9; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { color: #35508b; margin-bottom: 20px; border-left: 5px solid #5957cc; padding-left: 10px; font-size: 24px; }
        .form-row { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
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
    <h1>Doktor Yönetimi</h1>

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

    <form method="POST" class="form-row" action="doktorlar.php">
        <input type="text" name="ad" placeholder="Doktor Adı" required style="padding:10px; border:1px solid #ddd; border-radius:8px;">
        <input type="text" name="soyad" placeholder="Doktor Soyadı" required style="padding:10px; border:1px solid #ddd; border-radius:8px;">
        <select name="bolum_id" required style="flex: 1; padding:10px; border:1px solid #ddd; border-radius:8px;">
            <option value="">Uzmanlık Alanı Seçin</option>
            <?php foreach($bolumler as $b): ?>
                <option value="<?= $b['bolum_id'] ?>"><?= htmlspecialchars($b['bolum_adi']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="doktor_ekle" class="btn btn-add" style="padding:10px 20px; background:#5957cc; color:white; border:none; border-radius:8px; cursor:pointer;">Doktor Ekle</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Doktor Ad Soyad</th>
                <th>Uzmanlık Alanı (Bölüm)</th>
                <th style="width: 100px;">İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($doktorlar as $d): ?>
            <tr>
                <td><strong><?= htmlspecialchars($d['Doktor Ad Soyad'] ?? '') ?></strong></td>
                <td><?= htmlspecialchars($d['Uzmanlık Alanı'] ?? '') ?></td>
                <td><a href="?doktor_sil=<?= $d['doktor_id'] ?>" style="background:#ef4444; color:white; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:12px;" onclick="return confirm('Doktor kaydını silmek istediğinize emin misiniz?')">Sil</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>