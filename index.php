<?php
ob_start();
session_start();
require_once __DIR__ . '/bl/admin_bl.php'; 
require_once __DIR__ . '/bl/randevu_bl.php'; 

$adminService = new admin_bl();
$randevuService = new randevu_bl();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['talep_gonder'])) {
    try {
        $adminService->talepOlustur(
            $_POST['ad_soyad'],
            $_POST['email'],
            $_POST['telefon'],
            $_POST['ilgili_bolum'],
            $_POST['sikayet_turu'],
            $_POST['aciklama']
        );
        echo "<script>alert('Talebiniz başarıyla alınmıştır.'); window.location.href='index.php';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Hata: " . $e->getMessage() . "');</script>";
    }
}

$randevular = [];
if (isset($_SESSION['hasta_id'])) {
    try {
        $randevular = $randevuService->hastaninDetayliRandevulari($_SESSION['hasta_id']);
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentiva | Diş Kliniği Randevu Sistemi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #f5f9ff;
            color: #546491;
            line-height: 1.6;
        }

    
        header {
            background: linear-gradient(135deg, #9eb5e7, #183a97);
            color: white;
            padding: 15px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .logo-konteyner {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-ikon {
            font-size: 30px;
            color: #ffffff;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .logo-metin {
            font-size: 24px;
            font-weight: 800;
            color: white;
            letter-spacing: 0.5px;
        }

        nav {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            font-size: 15px;
        }

        nav a:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .login-btn, .register-btn, .logout-btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
        }

        .login-btn {
            border: 2px solid white;
            color: white;
        }

        .login-btn:hover {
            background: white;
            color: #4d73c4;
        }

        .register-btn {
            background: white;
            color: #4362a5;
        }

        .logout-btn {
            background: #ff4d4d;
            color: white;
        }

        .user-welcome {
            font-size: 14px;
            font-weight: 600;
            color: #ffdada;
        }

        .hero-image {
            width: 100%;
            height: 550px;
            background: linear-gradient(rgba(37,99,235,0.5), rgba(29,78,216,0.5)),
                        url("https://png.pngtree.com/thumb_back/fh260/background/20241113/pngtree-closeup-portrait-of-a-smiling-woman-holding-transparent-dental-aligners-in-image_16584491.jpg");
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-text {
            max-width: 800px;
            color: white;
            padding: 20px;
        }

        .hero-text h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .hero-text p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .hero-btn {
            padding: 15px 40px;
            border: none;
            border-radius: 12px;
            background: white;
            color: #3b599b;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .hero-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .welcome {
            padding: 80px 8%;
            display: flex;
            justify-content: center;
        }

        .welcome-box {
            width: 100%;
            max-width: 1200px;
            background: white;
            padding: 60px 40px;
            border-radius: 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        }

        .welcome-box h2 {
            font-size: 36px;
            color: #4571cf;
            margin-bottom: 15px;
        }

        .clinic-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .clinic-card {
            background: #f8fbff;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .clinic-card:hover {
            transform: translateY(-10px);
            border-color: #2e519b;
            box-shadow: 0 10px 20px rgba(37,99,235,0.1);
        }

        .clinic-card h3 { color: #335fbd; margin-bottom: 10px; }
        .clinic-card p { color: #64748b; margin-bottom: 20px; font-size: 15px; }

        .clinic-card button {
            padding: 10px 25px;
            background: #4c75cc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .services {
            padding: 80px 8%;
            background: #eff6ff;
            text-align: center;
        }

        .section-title h2 { font-size: 36px; color: #4e679c; margin-bottom: 10px; }
        .section-title p { color: #64748b; margin-bottom: 50px; }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .card:hover { transform: translateY(-5px); }

        .appointment {
            padding: 80px 8%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            width: 100%;
            max-width: 700px;
            display: grid;
            gap: 15px;
        }

        input, select, textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            background: #fcfdfe;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #6583c4;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .submit-btn {
            background: #445f9b;
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover { background: #586faf; }

    
        footer {
            background: #0f172a;
            color: #94a3b8;
            text-align: center;
            padding: 30px;
        }
       
.appointment {
    padding: 80px 8%;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.section-title {
    text-align: center;
    margin-bottom: 40px;
}

.section-title h2 {
    font-size: 36px;
    color: #4e679c;
    margin-bottom: 10px;
}

.section-title p {
    color: #64748b;
    font-size: 16px;
}

form {
    width: 100%;
    max-width: 700px;
    display: grid;
    gap: 18px;
    background: #fcfdfe;
    padding: 40px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

input, select, textarea {
    width: 100%;
    padding: 15px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    font-size: 15px;
    transition: 0.3s;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #6583c4;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}

textarea {
    height: 120px;
    resize: vertical;
}

.submit-btn {
    background: #445f9b;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

.submit-btn:hover {
    background: #35508b;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(68, 95, 155, 0.3);
}

footer {
    background: #0f172a;
    color: #94a3b8;
    text-align: center;
    padding: 40px 20px;
    font-size: 14px;
    border-top: 5px solid #3b599b;
}

@media (max-width: 768px) {
    form {
        padding: 20px;
    }
}
        @media (max-width: 768px) {
            header { flex-direction: column; gap: 20px; text-align: center; }
            nav { gap: 15px; }
            .hero-text h1 { font-size: 32px; }
            .welcome-box { padding: 40px 20px; }
        }

    </style>
</head>
<body>

<header>
    <a href="index.php" class="logo-konteyner">
        <i class="fa-solid fa-tooth logo-ikon"></i>
        <span class="logo-metin">Dentiva</span>
    </a>

    <nav>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="admin/panel.php" style="background: #5957cc; color: white; padding: 8px 15px; border-radius: 10px; text-decoration: none; font-weight: bold;">
                Yönetim Paneli
            </a>
        <?php endif; ?>
        
        <a href="index.php">Ana Sayfa</a>
        <a href="#Hizmetlerimiz">Hizmetler</a>
        <a href="#randevu">Randevu</a>
        <a href="#">İletişim</a>
        <?php if (isset($_SESSION['hasta_id'])): ?>
            <a href="profil.php">Profilim</a>
        <?php endif; ?>
    </nav>

    <div class="auth-buttons">
        <?php if (isset($_SESSION['hasta_id'])): ?>
            <span class="user-welcome">Merhaba, <?= htmlspecialchars(explode(' ', $_SESSION['ad_soyad'])[0]) ?></span>
            <a href="cikis.php" class="logout-btn">Çıkış Yap</a>
        <?php else: ?>
            <a href="giris.php" class="login-btn">Giriş Yap</a>
            <a href="kayit.php" class="register-btn">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</header>

<section class="hero-image">
    <div class="hero-text">
        <h1>Sağlıklı Gülüşler İçin Modern Diş Kliniği</h1>
        <p>Uzman doktor kadromuz ve gelişmiş teknolojimiz ile sizlere güvenilir diş sağlığı hizmeti sunuyoruz.</p>
        <a href="#randevu" class="hero-btn">Şikayet ve Bilgi Talebi</a>
    </div>
</section>

<section class="welcome">
    <div class="welcome-box">
        <h2>Klinik Seçimi Yapınız</h2>
        <p>Hızlı randevu almak istediğiniz uzmanlık alanını seçin.</p>
        
        <div class="clinic-cards">
            <div class="clinic-card">
                <h3>Genel Diş Hekimliği</h3>
                <p>Dolgu, kontrol ve diş temizliği</p>
                <a href="randevu_form.php?klinik=Genel Diş Hekimliği"><button>Randevu Al</button></a>
            </div>
            <div class="clinic-card">
                <h3>Endodonti</h3>
                <p>Kanal tedavisi ve kök tedavileri</p>
                <a href="randevu_form.php?klinik=Endodonti"><button>Randevu Al</button></a>
            </div>
            <div class="clinic-card">
                <h3>Ortodonti</h3>
                <p>Diş teli ve çarpıklık tedavileri</p>
                <a href="randevu_form.php?klinik=Ortodonti"><button>Randevu Al</button></a>
            </div>
            <div class="clinic-card">
                <h3>Periodontoloji</h3>
                <p>Diş eti hastalıkları uzmanlığı</p>
                <a href="randevu_form.php?klinik=Periodontoloji"><button>Randevu Al</button></a>
            </div>
            <div class="clinic-card">
                <h3>Ağız ve Çene Cerrahisi</h3>
                <p>İmplant ve cerrahi operasyonlar</p>
                <a href="randevu_form.php?klinik=Ağız ve Çene Cerrahisi"><button>Randevu Al</button></a>
            </div>
            <div class="clinic-card">
                <h3>Estetik Diş Hekimliği</h3>
                <p>Gülüş tasarımı ve beyazlatma</p>
                <a href="randevu_form.php?klinik=Estetik Diş Hekimliği"><button>Randevu Al</button></a>
            </div>
        </div>
    </div>
</section>

<section class="services" id="Hizmetlerimiz">
    <div class="section-title">
        <h2>Hizmetlerimiz</h2>
        <p>Profesyonel ekibimizle her adımda yanınızdayız.</p>
    </div>
    <div class="cards">
        <div class="card">
            <h3>Modern Teknoloji</h3>
            <p>3D görüntüleme ve dijital diş hekimliği ile hatasız teşhis.</p>
        </div>
        <div class="card">
            <h3>Uzman Kadro</h3>
            <p>Alanında uzman, tecrübeli hekimlerimizle güvenli tedavi.</p>
        </div>
        <div class="card">
            <h3>Steril Ortam</h3>
            <p>En yüksek hijyen standartlarında cerrahi ve muayene imkanı.</p>
        </div>
    </div>
</section>

<section class="appointment" id="randevu">
    <div class="section-title">
        <h2>Şikayet ve Bilgi Talebi</h2>
        <p>Diş probleminiz hakkında uzmanlarımıza danışın.</p>
    </div>
    <form action="index.php" method="POST">
        <input type="text" name="ad_soyad" placeholder="Ad Soyad" required>
        <input type="email" name="email" placeholder="E-Posta Adresi" required>
        <input type="tel" name="telefon" placeholder="Telefon Numarası" required>
        <select name="ilgili_bolum" required>
            <option value="">İlgili Bölümü Seçiniz</option>
            <option>Genel Diş Hekimliği</option>
            <option>Endodonti</option>
            <option>Ortodonti</option>
            <option>Periodontoloji</option>
            <option>Ağız ve Çene Cerrahisi</option>
            <option>Estetik Diş Hekimliği</option>
        </select>
        <select name="sikayet_turu" required>
            <option value="">Şikayet Türü</option>
            <option>Diş Ağrısı</option>
            <option>Diş Eti Kanaması</option>
            <option>Diş Hassasiyeti</option>
            <option>Diğer</option>
        </select>
        <textarea name="aciklama" placeholder="Şikayetinizi detaylıca açıklayın..."></textarea>
        <button type="submit" name="talep_gonder" class="submit-btn">Soruyu Gönder</button>
    </form>
</section>

<footer>
    <p>© 2026 Dentiva | Tüm Hakları Saklıdır.</p>
</footer>

</body>
</html>