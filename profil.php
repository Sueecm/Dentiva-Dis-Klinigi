<?php
session_start();
require_once __DIR__ . '/bl/randevu_bl.php';

if (!isset($_SESSION['hasta_id'])) {
    header("Location: giris.php");
    exit;
}

$randevuService = new randevu_bl();
try {
    $randevular = $randevuService->hastaninDetayliRandevulari($_SESSION['hasta_id']);
} catch(Exception $e) {
    die("Hata: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Profilim</title>
      <style>
    :root { 
        --mavi: #5576bd; 
        --mor: #5957cc; 
        --gradyan: linear-gradient(135deg, #6a88c9, #5957cc); 
    }

    body { 
        font-family: 'Segoe UI', sans-serif; 
        background: #f1f5f9; 
        padding: 40px; 
    }

    .konteynir { 
        max-width: 1000px; 
        margin: auto; 
        background: white; 
        padding: 30px; 
        border-radius: 20px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
    }

    h1 { 
        color: var(--mavi); 
        margin-bottom: 20px; 
        text-align: center; 
    }

    .profil-bilgi { 
        background: #f8fafc; 
        padding: 20px; 
        border-radius: 15px; 
        margin-bottom: 30px; 
        border-left: 5px solid var(--mor); 
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 20px; 
    }

    th { 
        text-align: left; 
        padding: 15px; 
        background: #f8fafc; 
        color: #475569; 
        font-size: 14px; 
    }

    td { 
        padding: 15px; 
        border-bottom: 1px solid #e2e8f0; 
        font-size: 14px; 
    }

    .durum { 
        padding: 5px 10px; 
        border-radius: 20px; 
        font-size: 12px; 
        font-weight: bold; 
    }

    
    .durum-bekliyor { 
        background: #fef3c7; 
        color: #92400e; 
    }

    .durum-onaylandi { 
        background: #dcfce7; 
        color: #166534; 
    }

    .durum-iptal { 
        background: #fee2e2; 
        color: #991b1b; 
    }

    .geri-link { 
        display: inline-block; 
        margin-bottom: 20px; 
        text-decoration: none; 
        color: var(--mavi); 
        font-weight: bold; 
    }

   
    .card-footer { margin-top: 20px; padding-top: 15px; border-top: 2px dashed #edf2f7; font-size: 12.5px; }
    .footer-stat { display: inline-block; font-weight: bold; color: #4338ca; background: #e0e7ff; padding: 5px 12px; border-radius: 6px; }

</style>
   
</head>
<body>
<div class="konteynir">
    <a href="index.php" class="geri-link">← Ana Sayfaya Dön</a>
    <h1>Randevularım</h1>
    <div class="profil-bilgi">
        <strong>Ad Soyad:</strong> <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?><br>
        <strong>T.C. Kimlik:</strong> <?php echo htmlspecialchars($_SESSION['tc_id']); ?><br>
        <strong>Telefon:</strong> <?php echo htmlspecialchars($_SESSION['telefon']); ?>
    </div>
    <table>
        <thead>
            <tr>
                <th>Bölüm</th>
                <th>Doktor</th>
                <th>İşlem</th>
                <th>Tarih / Saat</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($randevular as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['Bölüm'] ?? $r['bolum_adi']); ?></td>
                <td>Dt. <?php echo htmlspecialchars($r['Doktor Ad Soyad'] ?? ($r['doktor_ad'] . " " . $r['doktor_soyad'])); ?></td>
                <td><?php echo htmlspecialchars($r['İşlem Türü'] ?? $r['islem_turu']); ?></td>
                <td><?php echo date("d.m.Y", strtotime($r['Randevu Tarihi'] ?? $r['randevu_tarihi'])); ?> - <?php echo htmlspecialchars($r['Randevu Saati'] ?? $r['randevu_saati']); ?></td>
                <td>
                    <?php 
                    $db_durum = $r['Durum'] ?? $r['durum']; 
                    if ($db_durum == 'Onaylandı') {
                        $css_sinif = 'durum-onaylandi';
                        $ekran_metni = 'Onaylandı';
                    } else {
                        $css_sinif = 'durum-bekliyor';
                        $ekran_metni = 'Beklemede';
                    }
                    ?>
                    <span class="durum <?php echo $css_sinif; ?>"><?php echo $ekran_metni; ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="card-footer">
        <span class="footer-stat"> Randevu Sayısı: <?= count($randevular) ?></span>
    </div>
</div>
</body>
</html>