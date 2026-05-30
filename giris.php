<?php
ob_start();
session_start();
require_once __DIR__ . '/bl/hasta_bl.php';

$hata_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];

    $hastaService = new hasta_bl();
    try {
        $kullanici = $hastaService->girisYap($email, $sifre);
        
        $_SESSION['hasta_id'] = $kullanici['ID'] ?? $kullanici['hasta_id'];
        $_SESSION['ad_soyad'] = ($kullanici['Adı'] ?? $kullanici['hasta_ad']) . " " . ($kullanici['Soyadı'] ?? $kullanici['hasta_soyad']);
        $_SESSION['telefon']  = $kullanici['Telefon'] ?? $kullanici['telefon'];
        $_SESSION['tc_id']    = $kullanici['TC Kimlik No'] ?? $kullanici['tc_id'];

        if ($email === 'sueda.cam26@gmail.com') {
            $_SESSION['is_admin'] = true;
            header("Location: admin/panel.php");
        } else {
            $_SESSION['is_admin'] = false;
            header("Location: index.php");
        }
        ob_end_flush();
        exit;
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
    <title>Dentiva | Giriş Yap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #eef4ff; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .login-box { width: 100%; max-width: 1000px; background: white; border-radius: 25px; overflow: hidden; display: grid; grid-template-columns: 1.2fr 1fr; box-shadow: 0 15px 35px rgba(0,0,0,0.1); min-height: 600px; }
        .login-left { background: linear-gradient(rgba(53, 80, 139, 0.4), rgba(80, 107, 165, 0.7)), url('https://png.pngtree.com/thumb_back/fw800/background/20251225/pngtree-dentist-examining-patient-in-modern-dental-clinic-office-image_20922917.webp'); background-size: cover; background-position: center; color: white; padding: 60px; display: flex; flex-direction: column; justify-content: center; }
        .login-left h1 { font-size: 3rem; line-height: 1.2; margin-bottom: 20px; text-shadow: 2px 2px 10px rgba(0,0,0,0.3); }
        .login-left p { font-size: 1.2rem; opacity: 0.9; }
        .login-right { padding: 60px; display: flex; flex-direction: column; justify-content: center; }
        .login-right h2 { color: #264da0; font-size: 2rem; margin-bottom: 10px; }
        .hata-kutusu { background: #fee2e2; color: #60539b; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #3b477c; font-size: 14px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; color: #666; font-weight: 500; }
        input { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 10px; outline: none; font-size: 16px; transition: 0.3s; }
        input:focus { border-color: #35508b; box-shadow: 0 0 0 3px rgba(53, 80, 139, 0.1); }
        .btn-login { width: 100%; padding: 15px; background: #35508b; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 18px; transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background: #6075a1; transform: translateY(-2px); }
        .footer-links { margin-top: 30px; text-align: center; color: #777; }
        .footer-links a { color: #35508b; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-left">
        <h1>Tekrar Hoş Geldiniz!</h1>
        <p>Sağlıklı gülüşler için randevularınızı yönetmeye başlayın.</p>
    </div>
    <div class="login-right">
        <h2>Giriş Yap</h2>
        <p style="color: #888; margin-bottom: 30px;">Bilgilerinizi girerek hesabınıza erişin.</p>
        <?php if(!empty($hata_mesaji)): ?>
            <div class="hata-kutusu">
                <strong>Hata:</strong> <?php echo htmlspecialchars($hata_mesaji); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="giris.php">
            <div class="input-group">
                <label>E-posta Adresi</label>
                <input type="email" name="email" placeholder="ornek@mail.com" required>
            </div>
            <div class="input-group">
                <label>Şifre</label>
                <input type="password" name="sifre" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Giriş Yap</button>
        </form>
        <div class="footer-links">
            <p>Hesabınız yok mu? <a href="kayit.php">Hemen Kayıt Ol</a></p>
            <a href="index.php" style="display:inline-block; margin-top:15px; font-size: 14px;">← Ana Sayfaya Dön</a>
        </div>
    </div>
</div>
</body>
</html>