<?php
session_start();
require_once __DIR__ . '/../bl/admin_bl.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../giris.php"); exit;
}

$adminService = new admin_bl();

if (isset($_GET['randevu_onayla'])) {
    $adminService->randevuOnayla($_GET['randevu_onayla']);
    header("Location: randevular.php"); exit;
}
if (isset($_GET['randevu_sil'])) {
    $adminService->randevuSil($_GET['randevu_sil']);
    header("Location: randevular.php"); exit;
}

$randevular = $adminService->tumRandevulariDetayli();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Randevu Kontrolü</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { padding: 40px; background: #f1f5f9; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { color: #35508b; margin-bottom: 20px; border-left: 5px solid #5957cc; padding-left: 10px; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #5957cc; font-size: 14px; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .geri-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #35508b; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; }
        .badge-waiting { background: #fef3c7; color: #92400e; }
        .badge-done { background: #dcfce7; color: #166534; }
        .btn { padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-success { background: #22c55e; color: white; }
    </style>
</head>
<body>
<div class="container">
    <a href="panel.php" class="geri-link">← Panele Dön</a>
    <h1>Randevu Onay ve Kontrol Paneli</h1>
    <table>
        <thead>
            <tr>
                <th>Hasta Ad Soyad</th>
                <th>Doktor / Bölüm</th>
                <th>Yapılacak İşlem</th>
                <th>Tarih / Saat</th>
                <th>Hasta Mesajı & Sağlık Bilgisi</th>
                <th>Durum</th>
                <th>İşlem </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($randevular as $r): ?>
            <tr>
                <td><strong><?= htmlspecialchars($r['Hasta Ad Soyad'] ?? '') ?></strong></td>
                <td><?= htmlspecialchars($r['Doktor Ad Soyad'] ?? '') ?> (<?= htmlspecialchars($r['Bölüm'] ?? '') ?>)</td>
                <td><?= htmlspecialchars($r['İşlem Türü'] ?? $r['islem_turu'] ?? '') ?></td>
                <td><?= isset($r['Randevu Tarihi']) ? date("d.m.Y", strtotime($r['Randevu Tarihi'])) : '' ?> - <?= htmlspecialchars($r['Randevu Saati'] ?? '') ?></td>
                <td style="font-size: 12px; color: #475569; max-width: 200px;">
                    <div style="background: #f8fafc; padding: 8px; border-radius: 6px;">
                        <strong>Not:</strong> <?= htmlspecialchars($r['hasta_notu'] ?? 'Yok') ?><br>
                        <strong>Alerji:</strong> <?= htmlspecialchars($r['ilac_alerjisi'] ?? 'Yok') ?><br>
                        <strong>Kronik:</strong> <?= htmlspecialchars($r['kronik_hastalik'] ?? 'Yok') ?>
                    </div>
                </td>
                <td>
                    <span class="badge <?= ($r['Durum'] ?? '') == 'Onaylandı' ? 'badge-done' : 'badge-waiting' ?>">
                        <?= htmlspecialchars($r['Durum'] ?? '') ?>
                    </span>
                </td>
                <td>
                    <?php if(($r['Durum'] ?? '') != 'Onaylandı'): ?>
                        <a href="?randevu_onayla=<?= $r['randevu_id'] ?>" class="btn btn-success">Onayla</a>
                    <?php endif; ?>
                    <a href="?randevu_sil=<?= $r['randevu_id'] ?>" class="btn btn-delete" onclick="return confirm('Silinsin mi?')">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>