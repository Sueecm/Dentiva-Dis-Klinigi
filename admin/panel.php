<?php
session_start();
require_once __DIR__ . '/../bl/admin_bl.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../giris.php"); 
    exit;
}

$adminService = new admin_bl();

$mesaj = "";
$mesaj_turu = "";

if (isset($_GET['randevu_onayla'])) {
    $adminService->randevuOnayla($_GET['randevu_onayla']);
    header("Location: panel.php#randevular"); exit;
}
if (isset($_GET['randevu_sil'])) {
    $adminService->randevuSil($_GET['randevu_sil']);
    header("Location: panel.php#randevular"); exit;
}

if (isset($_POST['admin_randevu_ekle'])) {
    try {
        require_once __DIR__ . '/../bl/randevu_bl.php';
        $randevuService = new randevu_bl();
        
        $randevuService->randevuOlustur(
            $_POST['hasta_id'],
            $_POST['doktor_id'],
            $_POST['bolum_id'],
            $_POST['islem_turu'],
            $_POST['randevu_tarihi'],
            $_POST['randevu_saati'],
            $_POST['hasta_notu'] ?? '',
            $_POST['ilac_alerjisi'] ?? 'Yok',
            $_POST['kronik_hastalik'] ?? 'Yok'
        );
        
        $mesaj = "Randevu başarıyla sisteme eklendi.";
        $mesaj_turu = "basari";
    } catch (Exception $e) {
        $mesaj = "Randevu Eklenemedi: " . $e->getMessage();
        $mesaj_turu = "hata";
    }
}

if (isset($_POST['hasta_ekle'])) {
    try {
        $tc_id = trim($_POST['tc_id']);
        $email = trim($_POST['email']);
        
        $adminService->hastaEkle($tc_id, $_POST['ad'], $_POST['soyad'], $email, $_POST['telefon'], $_POST['sifre']);
        
        $mesaj = "Hasta başarıyla sisteme kaydedildi.";
        $mesaj_turu = "basari";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mesaj = "Hata: Bu T.C. Kimlik Numarası veya E-posta adresi ile zaten bir kayıt mevcut!";
            $mesaj_turu = "hata";
        } else {
            $mesaj = "Sistemsel bir hata oluştu: " . $e->getMessage();
            $mesaj_turu = "hata";
        }
    }
}

if (isset($_GET['hasta_sil'])) {
    try {
        $adminService->hastaSil($_GET['hasta_sil']);
        header("Location: panel.php#hastalar"); exit;
    } catch (PDOException $e) {
        $mesaj = "Silme işlemi başarısız: " . $e->getMessage();
        $mesaj_turu = "hata";
    }
}

if (isset($_POST['bolum_ekle'])) {
    try {
        $bolum_adi = trim($_POST['bolum_adi']);
        $adminService->bolumEkle($bolum_adi);
        $mesaj = "Bölüm başarıyla eklendi.";
        $mesaj_turu = "basari";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mesaj = "Hata: '" . htmlspecialchars($bolum_adi) . "' adında bir bölüm zaten kayıtlı!";
            $mesaj_turu = "hata";
        } else {
            $mesaj = "Sistemsel bir hata oluştu: " . $e->getMessage();
            $mesaj_turu = "hata";
        }
    }
}

if (isset($_GET['bolum_sil'])) {
    try {
        $adminService->bolumSil($_GET['bolum_sil']);
        header("Location: panel.php#bolumler"); exit;
    } catch (PDOException $e) {
        $mesaj = "İşlem Reddedildi: " . $e->getMessage();
        $mesaj_turu = "hata";
    }
}

if (isset($_POST['doktor_ekle'])) {
    $adminService->doktorEkle($_POST['ad'], $_POST['soyad'], $_POST['bolum_id']);
    header("Location: panel.php#doktorlar"); exit;
}

if (isset($_GET['doktor_sil'])) {
    try {
        $adminService->doktorSil($_GET['doktor_sil']);
        header("Location: panel.php#doktorlar"); exit;
    } catch (PDOException $e) {
        $mesaj = "İşlem Reddedildi: " . $e->getMessage();
        $mesaj_turu = "hata";
    }
}

if (isset($_GET['talep_sil'])) {
    $adminService->talepSil($_GET['talep_sil']);
    header("Location: panel.php#mesajlar"); exit;
}

$randevular = $adminService->tumRandevulariDetayli();
$hastalar   = $adminService->tumHastalar();
$bolumler   = $adminService->tumBolumler();
$doktorlar  = $adminService->tumDoktorlarDetayli();
$talepler   = $adminService->tumTalepler();

$toplamHasta = $adminService->toplamHastaSayisi();
$aktifRandevu = $adminService->aktifRandevuSayisi();

$hastaSayisi = is_array($toplamHasta) ? $toplamHasta['Toplam Hasta Sayısı'] : $toplamHasta;
$randevuSayisi = is_array($aktifRandevu) ? $aktifRandevu['Aktif Randevu Sayısı'] : $aktifRandevu;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dentiva | Admin Paneli</title>
    <style>
        :root { --mavi: #5576bd; --mor: #5957cc; --gradyan: linear-gradient(135deg, #6a88c9, #5957cc); --danger: #ef4444; --success: #22c55e; }
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; display: flex; }
        .sidebar { width: 260px; background: var(--gradyan); height: 100vh; color: white; padding: 25px; position: fixed; box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
        .sidebar h2 { font-size: 22px; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin-bottom: 8px; border-radius: 10px; font-weight: 600; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-bottom: 30px; }
        h3 { color: var(--mavi); border-left: 5px solid var(--mor); padding-left: 10px; margin-bottom: 20px; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { text-align: left; padding: 12px; background: #f8fafc; color: var(--mor); border-bottom: 2px solid #edf2f7; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .btn { padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-add { background: var(--gradyan); color: white; }
        .btn-delete { background: var(--danger); color: white; }
        .btn-success { background: var(--success); color: white; }
        .form-row { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        input, select, textarea { padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; flex: 1; min-width: 150px; font-family: inherit; }
        textarea { resize: vertical; min-height: 40px; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; }
        .badge-waiting { background: #fef3c7; color: #92400e; }
        .badge-done { background: #dcfce7; color: #166534; }
        .card-footer { margin-top: 20px; padding-top: 15px; border-top: 2px dashed #edf2f7; font-size: 12.5px; }
        .footer-stat { display: inline-block; font-weight: bold; color: #4338ca; background: #e0e7ff; padding: 5px 12px; border-radius: 6px; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Dentiva Admin</h2>
    <a href="#randevu-ekle">Randevu Ekle</a>
    <a href="#randevular">Randevu Kontrol</a>
    <a href="#hastalar">Hasta Yönetimi</a>
    <a href="#doktorlar">Doktorlar</a>
    <a href="#bolumler">Bölümler</a>
    <a href="#mesajlar">Mesajlar</a>
    <a href="../index.php" style="margin-top:50px; color:#ffdada;">← Siteye Dön</a>
</div>

<div class="main-content">

    <?php if (!empty($mesaj) && !isset($_POST['hasta_ekle']) && !isset($_POST['bolum_ekle']) && !isset($_POST['admin_randevu_ekle'])): ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: bold; 
                    color: <?= $mesaj_turu == 'basari' ? '#03543f' : '#9b1c1c' ?>; 
                    background-color: <?= $mesaj_turu == 'basari' ? '#def7ec' : '#fde8e8' ?>; 
                    border: 1px solid <?= $mesaj_turu == 'basari' ? '#bcf0da' : '#fbd5d5' ?>;">
            <?= htmlspecialchars($mesaj) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="randevu-ekle">
        <h3>Sisteme Yeni Randevu Ekle</h3>
        
        <?php if (!empty($mesaj) && isset($_POST['admin_randevu_ekle'])): ?>
            <div style="padding: 12px; margin-bottom: 15px; border-radius: 8px; font-weight: bold; 
                        color: <?= $mesaj_turu == 'basari' ? '#03543f' : '#9b1c1c' ?>; 
                        background-color: <?= $mesaj_turu == 'basari' ? '#def7ec' : '#fde8e8' ?>; 
                        border: 1px solid <?= $mesaj_turu == 'basari' ? '#bcf0da' : '#fbd5d5' ?>;">
                <?= htmlspecialchars($mesaj) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-row">
            <select name="hasta_id" required>
                <option value="">Hasta Seçiniz...</option>
                <?php foreach($hastalar as $h): ?>
                    <option value="<?= $h['hasta_id'] ?? $h['ID'] ?? '' ?>"><?= htmlspecialchars(($h['hasta_ad'] ?? $h['Adı'] ?? '') . " " . ($h['hasta_soyad'] ?? $h['Soyadı'] ?? '')) ?> (TC: <?= htmlspecialchars($h['tc_id'] ?? $h['TC Kimlik No'] ?? '') ?>)</option>
                <?php endforeach; ?>
            </select>

            <select name="bolum_id" id="bolum_id" onchange="formGuncelle()" required>
                <option value="">Bölüm Seçiniz...</option>
                <?php foreach($bolumler as $b): ?>
                    <option value="<?= $b['bolum_id'] ?>" data-ad="<?= htmlspecialchars($b['bolum_adi']) ?>"><?= htmlspecialchars($b['bolum_adi']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="doktor_id" id="doktor_id" required>
                <option value="">Önce Bölüm Seçiniz...</option>
                <?php foreach($doktorlar as $d): ?>
                    <option value="<?= $d['doktor_id'] ?>">Dt. <?= htmlspecialchars($d['Doktor Ad Soyad'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>

            <select name="islem_turu" id="islem_turu" required>
                <option value="">Önce Bölüm Seçiniz...</option>
            </select>

            <input type="date" name="randevu_tarihi" min="<?= date('Y-m-d') ?>" required>
            
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

            <select name="ilac_alerjisi">
                <option value="Yok">İlaç Alerjisi: Yok</option>
                <option value="Var">İlaç Alerjisi: Var</option>
            </select>

            <select name="kronik_hastalik">
                <option value="Yok">Kronik Hastalık: Yok</option>
                <option value="Var">Kronik Hastalık: Var</option>
            </select>

            <textarea name="hasta_notu" placeholder="Hasta Notu veya Açıklama (İsteğe Bağlı)" style="flex-basis: 100%;"></textarea>

            <button type="submit" name="admin_randevu_ekle" class="btn btn-add" style="flex-basis: 100%; padding: 12px; font-size: 14px;">Randevuyu Kaydet</button>
        </form>
    </div>

    <div class="card" id="randevular">
        <h3>Randevu Onay ve Kontrol</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Hasta</th>
                    <th>Doktor / Bölüm</th>
                    <th>Yapılacak İşlem</th>
                    <th>Tarih / Saat</th>
                    <th>Notlar, Alerji & Kronik</th>
                    <th>Durum</th>
                    <th>İşlem (Aksiyon)</th>
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
                        <p><strong>Not:</strong> <?= htmlspecialchars($r['hasta_notu'] ?? 'Yok') ?></p>
                        <p><strong>Alerji:</strong> <?= htmlspecialchars($r['ilac_alerjisi'] ?? 'Yok') ?></p>
                        <p><strong>Kronik:</strong> <?= htmlspecialchars($r['kronik_hastalik'] ?? 'Yok') ?></p>
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
        <div class="card-footer">
            <span class="footer-stat"> Aktif Bekleyen: <?= $randevuSayisi ?></span>
        </div>
    </div>

    <div class="card" id="hastalar">
        <h3>Hasta Kayıt / Silme</h3>
        
        <?php if (!empty($mesaj) && isset($_POST['hasta_ekle'])): ?>
            <div style="padding: 12px; margin-bottom: 15px; border-radius: 8px; font-weight: bold; 
                        color: <?= $mesaj_turu == 'basari' ? '#03543f' : '#9b1c1c' ?>; 
                        background-color: <?= $mesaj_turu == 'basari' ? '#def7ec' : '#fde8e8' ?>; 
                        border: 1px solid <?= $mesaj_turu == 'basari' ? '#bcf0da' : '#fbd5d5' ?>;">
                <?= $mesaj ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-row">
            <input type="text" name="tc_id" placeholder="T.C. No" maxlength="11" required>
            <input type="text" name="ad" placeholder="Ad" required>
            <input type="text" name="soyad" placeholder="Soyad" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="tel" name="telefon" placeholder="Telefon" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit" name="hasta_ekle" class="btn btn-add">Hasta Ekle</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>TC No</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hastalar as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['tc_id'] ?? $h['TC Kimlik No'] ?? '') ?></td>
                    <td><?= htmlspecialchars(($h['hasta_ad'] ?? $h['Adı'] ?? '') . " " . ($h['hasta_soyad'] ?? $h['Soyadı'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($h['email'] ?? $h['Mail'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['telefon'] ?? $h['Telefon'] ?? 'Belirtilmemiş') ?></td>
                    <td><a href="?hasta_sil=<?= $h['hasta_id'] ?? $h['ID'] ?? '' ?>" class="btn btn-delete" onclick="return confirm('Hastayı silmek randevularını da siler. Emin misiniz?')">Sil</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="card-footer">
            <span class="footer-stat"> Kayıtlı Toplam Hasta: <?= $hastaSayisi ?></span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="card" id="doktorlar">
            <h3>Doktor Ekle / Yönet</h3>
            <form method="POST" class="form-row">
                <input type="text" name="ad" placeholder="Ad" required>
                <input type="text" name="soyad" placeholder="Soyad" required>
                <select name="bolum_id" required>
                    <?php foreach($bolumler as $b): ?>
                        <option value="<?= $b['bolum_id'] ?>"><?= htmlspecialchars($b['bolum_adi']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="doktor_ekle" class="btn btn-add">Ekle</button>
            </form>
            <table>
                <tbody>
                    <?php foreach($doktorlar as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['Doktor Ad Soyad'] ?? '') ?> <br><small style="color: #64748b;"><?= htmlspecialchars($d['Uzmanlık Alanı'] ?? '') ?></small></td>
                        <td><a href="?doktor_sil=<?= $d['doktor_id'] ?>" class="btn btn-delete">Sil</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="card-footer">
                <span class="footer-stat">Kayıtlı Doktor: <?= count($doktorlar) ?></span>
            </div>
        </div>

        <div class="card" id="bolumler">
            <h3>Bölüm Ekle / Yönet</h3>
            
            <?php if (!empty($mesaj) && isset($_POST['bolum_ekle'])): ?>
                <div style="padding: 12px; margin-bottom: 15px; border-radius: 8px; font-weight: bold; 
                            color: <?= $mesaj_turu == 'basari' ? '#03543f' : '#9b1c1c' ?>; 
                            background-color: <?= $mesaj_turu == 'basari' ? '#def7ec' : '#fde8e8' ?>; 
                            border: 1px solid <?= $mesaj_turu == 'basari' ? '#bcf0da' : '#fbd5d5' ?>;">
                    <?= $mesaj ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-row">
                <input type="text" name="bolum_adi" placeholder="Bölüm Adı" required>
                <button type="submit" name="bolum_ekle" class="btn btn-add">Ekle</button>
            </form>
            <table>
                <tbody>
                    <?php foreach($bolumler as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['bolum_adi']) ?></td>
                        <td><a href="?bolum_sil=<?= $b['bolum_id'] ?>" class="btn btn-delete">Sil</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="card-footer">
                <span class="footer-stat"> Kayıtlı Bölüm: <?= count($bolumler) ?></span>
            </div>
        </div>
    </div>

    <div class="card" id="mesajlar">
        <h3>Şikayet ve Bilgi Talepleri</h3>
        <table>
            <thead><tr><th>Gönderen</th><th>Bölüm / Şikayet</th><th>Mesaj</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach($talepler as $t): ?>
                <tr>
                    <td><strong><?= htmlspecialchars(($t['ad'] ?? '') . " " . ($t['soyad'] ?? '')) ?></strong><br><small><?= htmlspecialchars($t['email'] ?? '') ?></small></td>
                    <td><?= htmlspecialchars($t['ilgili_bolum'] ?? '') ?> / <?= htmlspecialchars($t['sikayet_turu'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['aciklama'] ?? '') ?></td>
                    <td><a href="?talep_sil=<?= $t['id'] ?>" class="btn btn-delete">Sil</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="card-footer">
            <span class="footer-stat">Gelen Talep Sayısı: <?= count($talepler) ?></span>
        </div>
    </div>

</div>

<script>
    const islemListesi = {
        'Ağız ve Çene Cerrahisi': ["Gömülü Diş Çekimi (20’lik Diş)", "Cerrahi Diş Çekimi", "İmplant Muayenesi", "İmplant Operasyonu", "Kist Operasyonu", "Çene Ağrısı / Travma Muayenesi"],
        'Endodonti': ["Kanal Tedavisi Muayenesi", "Kanal Tedavisi (Başlangıç)", "Kanal Tedavisi Yenileme (Retreatment)", "Diş İçi Enfeksiyon / Apse Tedavisi", "Acil Diş Ağrısı (Sinir İltihabı)"],
        'Ortodonti': ["Ortodontik Muayene", "Braket (Tel) Tedavisi Başlangıç", "Tel Ayarlama / Kontrol", "Şeffaf Plak (Invisalign) Muayenesi", "Retainer (Pekiştirme Plağı) Kontrolü"],
        'Periodontoloji': ["Diş Eti Muayenesi", "Diş Eti İltihabı (Gingivitis) Tedavisi", "Periodontitis Tedavisi", "Küretaj (Derin Temizlik)", "Diş Eti Çekilmesi Tedavisi", "Diş Eti Estetik İşlemleri"],
        'Estetik Diş Hekimliği': ["Diş Beyazlatma (Bleaching)", "Laminate Veneer (Yaprak Porselen)", "Porselen Kaplama", "Bonding (Kompozit Estetik Dolgu)", "Gülüş Tasarımı (Smile Design)"],
        'Genel Diş Hekimliği': ["Genel Muayene & Kontrol", "Diş Dolgusu (Kompozit / Amalgam)", "Diş Taşı Temizliği (Detertraj)", "Diş Çekimi", "Röntgen Çekimi", "Acil Diş Ağrısı Tedavisi"]
    };

    const doktorListesi = [
        <?php foreach($doktorlar as $d): ?>
        { 
            id: "<?= $d['doktor_id'] ?>", 
            ad: "Dt. <?= htmlspecialchars($d['Doktor Ad Soyad'] ?? '') ?>", 
            bolum: "<?= htmlspecialchars($d['Uzmanlık Alanı'] ?? '') ?>" 
        },
        <?php endforeach; ?>
    ];

    function formGuncelle() {
        const bolumSelect = document.getElementById('bolum_id');
        const islemSelect = document.getElementById('islem_turu');
        const doktorSelect = document.getElementById('doktor_id');
        
        const secilenOption = bolumSelect.options[bolumSelect.selectedIndex];
        const bolumAdi = secilenOption ? secilenOption.getAttribute('data-ad') : '';
        
        const islemler = islemListesi[bolumAdi] || ["Genel Muayene & Kontrol", "Diş Dolgusu (Kompozit / Amalgam)", "Diş Taşı Temizliği (Detertraj)", "Diş Çekimi", "Röntgen Çekimi", "Acil Diş Ağrısı Tedavisi"];
        islemSelect.innerHTML = '<option value="">İşlem Seçiniz...</option>';
        
        if(bolumSelect.value !== "") {
            islemler.forEach(islem => {
                let option = document.createElement('option');
                option.value = islem;
                option.textContent = islem;
                islemSelect.appendChild(option);
            });
        } else {
            islemSelect.innerHTML = '<option value="">Önce Bölüm Seçiniz...</option>';
        }

        doktorSelect.innerHTML = '<option value="">Doktor Seçiniz...</option>';
        
        if(bolumSelect.value !== "") {
            const filtrelenmisDoktorlar = doktorListesi.filter(d => d.bolum === bolumAdi);
            
            if(filtrelenmisDoktorlar.length > 0) {
                filtrelenmisDoktorlar.forEach(d => {
                    let option = document.createElement('option');
                    option.value = d.id;
                    option.textContent = d.ad;
                    doktorSelect.appendChild(option);
                });
            } else {
                doktorSelect.innerHTML = '<option value="">Bu bölüme ait doktor bulunamadı!</option>';
            }
        } else {
            doktorListesi.forEach(d => {
                let option = document.createElement('option');
                option.value = d.id;
                option.textContent = d.ad;
                doktorSelect.appendChild(option);
            });
        }
    }
    
    window.onload = function() {
        document.getElementById('bolum_id').value = "";
        formGuncelle();
    };
</script>

</body>
</html>