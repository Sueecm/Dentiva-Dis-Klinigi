<?php
require_once __DIR__ . '/bl/hasta_bl.php';
$hata_mesaji = "";
$basari_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hastaService = new hasta_bl();
    try {
        $hastaService->kayitOl(
            $_POST['tc_id'], 
            $_POST['hasta_ad'], 
            $_POST['hasta_soyad'], 
            $_POST['email'], 
            $_POST['telefon'], 
            $_POST['sifre'], 
            $_POST['sifre_tekrar']
        );
        $basari_mesaji = "Kayıt başarıyla oluşturuldu! Giriş sayfasına yönlendiriliyorsunuz...";
        header("Refresh: 2; url=giris.php");
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
<title>Dentiva | Kayıt Ol</title>
<style>

* {
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family: 'Segoe UI', Arial, sans-serif;
}

body {
  background:#eef4ff;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:40px;
}


.register-box {
  width:100%;
  max-width:1100px;
  background:white;
  border-radius:25px;
  overflow:hidden;
  display:grid;
  grid-template-columns:1fr 1fr;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.register-left {
  background: linear-gradient(rgba(64, 82, 121, 0.75), rgba(50, 68, 109, 0.75)),
              url("https://www.turkeydentalclinic.com/wp-content/uploads/2024/11/dentist-turkey-antalya.jpg");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  color: white;
  padding: 60px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
}

.register-left h1 {
  font-size:42px;
  margin-bottom:20px;
}


.register-right {
  padding:60px;
}

.register-right h2 {
  color:hsl(221, 39%, 58%);
  font-size:34px;
  margin-bottom:20px;
}


.mesaj-kutusu {
  padding: 12px;
  margin-bottom: 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
}
.hata { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
.basari { background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; }


.register-form {
  display:grid;
  gap:18px;
}

.register-form input {
  padding:15px;
  border:1px solid #cbd5e1;
  border-radius:12px;
  font-size:16px;
  outline:none;
  transition:0.3s;
}

.register-form input:focus {
  border-color:#556fa7;
}

.register-form button {
  padding:15px;
  border:none;
  border-radius:12px;
  background:#5774b3;
  color:white;
  font-size:17px;
  font-weight:bold;
  cursor:pointer;
  transition:0.3s;
}

.register-form button:hover {
  background:#54679b;
  transform:translateY(-3px);
}

.login-text {
  margin-top:20px;
  text-align:center;
  color:#64748b;
}

.login-text a {
  color:#3d5ea7;
  text-decoration:none;
  font-weight:bold;
}

.geri {
  display: block;
  text-align: center;
  margin-top: 15px;
  text-decoration: none;
  color: #94a3b8;
  font-size: 14px;
}

@media(max-width:900px){
  .register-box { grid-template-columns:1fr; }
  .register-left, .register-right { padding:40px; }
}

@media(max-width:600px){
  .register-left h1 { font-size:32px; }
  .register-right h2 { font-size:28px; }
}
</style>
</head>
<body>
<div class="register-box">
  <div class="register-left">
    <h1>Dentiva'ya Hoş Geldiniz</h1>
  </div>
  <div class="register-right">
    <h2>Hasta Kayıt Formu</h2>
    <?php if(!empty($hata_mesaji)): ?>
        <div class="mesaj-kutusu hata"><?= htmlspecialchars($hata_mesaji) ?></div>
    <?php endif; ?>
    <?php if(!empty($basari_mesaji)): ?>
        <div class="mesaj-kutusu basari"><?= htmlspecialchars($basari_mesaji) ?></div>
    <?php endif; ?>
    <form class="register-form" action="kayit.php" method="POST">
        <input type="text" name="tc_id" placeholder="T.C. Kimlik No" maxlength="11" required>
        <input type="text" name="hasta_ad" placeholder="Ad" required>
        <input type="text" name="hasta_soyad" placeholder="Soyad" required>
        <input type="email" name="email" placeholder="E-Posta Adresi" required>
        <input type="tel" name="telefon" placeholder="Telefon Numarası" required>
        <input type="password" name="sifre" placeholder="Şifre Oluştur" required>
        <input type="password" name="sifre_tekrar" placeholder="Şifre Tekrar" required>
        <button type="submit" name="kayit_ol">Kayıt Ol</button>
    </form>
    <div class="login-text">Zaten hesabınız var mı? <a href="giris.php">Giriş Yap</a></div>
    <a href="index.php" class="geri">← Ana Sayfaya Dön</a>
  </div>
</div>
</body>
</html>