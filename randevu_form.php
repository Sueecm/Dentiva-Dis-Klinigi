<?php
ob_start();
session_start();
require_once __DIR__ . '/bl/randevu_bl.php';

$hata_mesaji = "";
$basari_mesaji = "";

if (!isset($_SESSION['hasta_id'])) {
    header("Location: giris.php");
    exit;
}

$randevuService = new randevu_bl();
$bolum_adi = $_GET['klinik'] ?? 'Genel Diş Hekimliği';
$current_bolum_id = null;
$doktorlar = [];

$islem_listesi = [];
switch($bolum_adi) {
    case 'Ağız ve Çene Cerrahisi':
        $islem_listesi = ["Gömülü Diş Çekimi (20’lik Diş)", "Cerrahi Diş Çekimi", "İmplant Muayenesi", "İmplant Operasyonu", "Kist Operasyonu", "Çene Ağrısı / Travma Muayenesi"];
        break;
    case 'Endodonti':
        $islem_listesi = ["Kanal Tedavisi Muayenesi", "Kanal Tedavisi (Başlangıç)", "Kanal Tedavisi Yenileme (Retreatment)", "Diş İçi Enfeksiyon / Apse Tedavisi", "Acil Diş Ağrısı (Sinir İltihabı)"];
        break;
    case 'Ortodonti':
        $islem_listesi = ["Ortodontik Muayene", "Braket (Tel) Tedavisi Başlangıç", "Tel Ayarlama / Kontrol", "Şeffaf Plak (Invisalign) Muayenesi", "Retainer (Pekiştirme Plağı) Kontrolü"];
        break;
    case 'Periodontoloji':
        $islem_listesi = ["Diş Eti Muayenesi", "Diş Eti İltihabı (Gingivitis) Tedavisi", "Periodontitis Tedavisi", "Küretaj (Derin Temizlik)", "Diş Eti Çekilmesi Tedavisi", "Diş Eti Estetik İşlemleri"];
        break;
    case 'Estetik Diş Hekimliği':
        $islem_listesi = ["Diş Beyazlatma (Bleaching)", "Laminate Veneer (Yaprak Porselen)", "Porselen Kaplama", "Bonding (Kompozit Estetik Dolgu)", "Gülüş Tasarımı (Smile Design)"];
        break;
    default:
        $islem_listesi = ["Genel Muayene & Kontrol", "Diş Dolgusu (Kompozit / Amalgam)", "Diş Taşı Temizliği (Detertraj)", "Diş Çekimi", "Röntgen Çekimi", "Acil Diş Ağrısı Tedavisi"];
}

try {
    $klinikVerisi = $randevuService->klinikVerileriniGetir($bolum_adi);
    $current_bolum_id = $klinikVerisi['bolum_id'];
    $doktorlar = $klinikVerisi['doktorlar'];
} catch (Exception $e) {
    $hata_mesaji = "Klinik bilgileri yüklenemedi: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['randevu_kaydet'])) {
    try {
        $randevuService->randevuOlustur(
            $_SESSION['hasta_id'],
            $_POST['doktor_id'] ?? '',
            $current_bolum_id,
            $_POST['islem_turu'] ?? '',
            $_POST['randevu_tarihi'] ?? '',
            $_POST['randevu_saati'] ?? '',
            $_POST['hasta_notu'] ?? '',
            $_POST['ilac_alerjisi'] ?? 'Yok',
            $_POST['kronik_hastalik'] ?? 'Yok'
        );
        $basari_mesaji = "Randevunuz başarıyla alınmıştır! Ana sayfaya yönlendiriliyorsunuz...";
        header("Refresh: 2; url=index.php");
    } catch (Exception $e) {
        $hata_mesaji = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentiva | <?= htmlspecialchars($bolum_adi) ?> Randevu</title>
    <style>
        :root {
          --mavi: #3d5588;
          --gradyan: linear-gradient(135deg, #6a88c9, #7877ac);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body {
          background: #f1f5f9;
          display: flex; justify-content: center; align-items: center;
          min-height: 100vh; padding: 20px;
        }

        .konteynir {
          max-width: 750px; width: 100%;
          background: white; padding: 30px;
          border-radius: 20px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        h1 {
          background: var(--gradyan);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          text-align: center; margin-bottom: 25px; font-size: 28px;
        }

        .mesaj-kutusu {
          padding: 12px; margin-bottom: 20px; border-radius: 10px; font-size: 14px; font-weight: 500;
          line-height: 1.5;
        }
        .hata { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
        .basari { background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; }

        .form-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 15px;
        }

        .tam { grid-column: span 2; }

        label {
          display: block; margin-bottom: 5px;
          font-weight: 600; color: #475569; font-size: 13px;
        }

        input, select, textarea {
          width: 100%; padding: 12px;
          border: 1px solid #e2e8f0; border-radius: 10px;
          background: #f8fafc; font-size: 14px; outline: none;
        }

        input:focus, select:focus, textarea:focus {
          border-color: var(--mavi);
          box-shadow: 0 0 0 3px rgba(106, 136, 201, 0.1);
        }

        textarea { height: 80px; resize: none; }

        .alt-baslik {
          font-size: 15px; font-weight: 700; color: var(--mavi);
          margin-top: 10px; margin-bottom: 5px;
        }

        .ana-buton {
          margin-top: 20px; width: 100%; padding: 15px;
          border: none; border-radius: 12px;
          background: var(--gradyan); color: white;
          font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .ana-buton:hover { opacity: 0.9; transform: translateY(-2px); }
        
        .geri {
          display: block; text-align: center; margin-top: 15px;
          text-decoration: none; color: #94a3b8; font-size: 13px;
        }
        @media (max-width: 500px) { .form-grid { grid-template-columns: 1fr; } .tam { grid-column: span 1; } }
    </style>
</head>
<body>

<div class="konteynir">
    <h1><?= htmlspecialchars($bolum_adi) ?> Randevu Formu</h1>

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

    <form action="" method="POST" class="form-grid">
        
        <div class="tam alt-baslik"> Kişisel Bilgiler</div>
        <div>
            <label>T.C. Kimlik No</label>
            <input type="text" value="<?= htmlspecialchars($_SESSION['tc_id'] ?? '') ?>" readonly>
        </div>
        <div>
            <label>Telefon</label>
            <input type="tel" value="<?= htmlspecialchars($_SESSION['telefon'] ?? '') ?>" readonly>
        </div>

        <div class="tam alt-baslik">Randevu Detayları</div>
        <div class="tam">
            <label>Doktor Seçiniz</label>
            <select name="doktor_id" required>
                <option value="">Seçiniz...</option>
                <?php foreach($doktorlar as $doktor): ?>
                    <option value="<?= $doktor['doktor_id'] ?>">Dt. <?= htmlspecialchars($doktor['doktor_ad']." ".$doktor['doktor_soyad']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="tam">
            <label>Yapılacak İşlem / Şikayet</label>
            <select name="islem_turu" required>
                <option value="">İşlem Seçiniz...</option>
                <?php foreach($islem_listesi as $islem): ?>
                    <option value="<?= htmlspecialchars($islem) ?>"><?= htmlspecialchars($islem) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Tarih</label>
            <input type="date" name="randevu_tarihi" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div>
            <label>Saat</label>
            <select name="randevu_saati" required>
                <option value="">Saat Seçin...</option>
                <option value="09:00:00">09:00</option>
                <option value="10:00:00">10:00</option>
                <option value="10:30:00">10:30</option>
                <option value="11:00:00">11:00</option>
                <option value="11:30:00">11:30</option>
                <option value="12:00:00">12:00</option>
                <option value="12:30:00">12:30</option>
                <option value="13:00:00">13:00</option>
                <option value="13:30:00">13:30</option>
                <option value="14:00:00">14:00</option>
                <option value="15:00:00">15:00</option>
                <option value="15:30:00">15:30</option>
                <option value="16:00:00">16:00</option>
                <option value="16:30:00">16:30</option>
            </select>
        </div>

        <div class="tam alt-baslik">Sağlık Bilgileri</div>
        <div class="tam">
          <label>Şikayetiniz / Notunuz</label>
          <textarea name="hasta_notu" placeholder="Belirtmek istediğiniz detaylar..."></textarea>
        </div>
        <div>
          <label>İlaç Alerjisi</label>
          <select name="ilac_alerjisi">
            <option value="Yok">Yok</option>
            <option value="Var">Var (Lütfen not kısmına yazın)</option>
          </select>
        </div>
        <div>
          <label>Kronik Hastalık</label>
          <select name="kronik_hastalik">
            <option value="Yok">Yok</option>
            <option value="Var">Var (Lütfen not kısmına yazın)</option>
          </select>
        </div>

        <div class="tam">
          <button type="submit" name="randevu_kaydet" class="ana-buton">Randevu Oluştur</button>
          <a href="index.php" class="geri">← Ana Sayfaya Dön</a>
        </div>

    </form>
</div>

</body>
</html>