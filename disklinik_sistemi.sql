-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 31 May 2026, 14:51:39
-- Sunucu sürümü: 8.0.45
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `disklinik_sistemi`
--

DELIMITER $$
--
-- Yordamlar
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `aktifRandevuSayisi` ()   BEGIN
    SELECT COUNT(*) as `Aktif Randevu Sayısı` FROM randevular WHERE durum = 'Beklemede';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bolumEkle` (`b_adi` VARCHAR(100))   BEGIN
    INSERT INTO bolumler (bolum_adi) 
    VALUES (b_adi);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bolumGuncelle` (`b_id` INT, `b_adi` VARCHAR(100))   BEGIN
    UPDATE bolumler 
    SET bolum_adi = b_adi 
    WHERE bolum_id = b_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bolumHepsi` ()   BEGIN
    SELECT * FROM bolumler;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bolumSil` (`b_id` INT)   BEGIN
    DELETE FROM bolumler WHERE bolum_id = b_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `doktorDetay` ()   BEGIN
    SELECT 
        d.doktor_id,
        CONCAT(d.doktor_ad, ' ', d.doktor_soyad) as `Doktor Ad Soyad`,
        b.bolum_adi as `Uzmanlık Alanı`
    FROM doktorlar d 
    INNER JOIN bolumler b ON d.bolum_id = b.bolum_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `doktorEkle` (`d_ad` VARCHAR(50), `d_soyad` VARCHAR(50), `d_bolum_id` INT)   BEGIN
    INSERT INTO doktorlar (doktor_ad, doktor_soyad, bolum_id)
    VALUES (d_ad, d_soyad, d_bolum_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `doktorGuncelle` (`d_id` INT, `d_ad` VARCHAR(50), `d_soyad` VARCHAR(50), `d_bolum_id` INT)   BEGIN
    UPDATE doktorlar 
    SET 
        doktor_ad = d_ad,
        doktor_soyad = d_soyad,
        bolum_id = d_bolum_id
    WHERE doktor_id = d_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `doktorHepsi` ()   BEGIN
    SELECT * FROM doktorlar;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `doktorSil` (`d_id` INT)   BEGIN
    DELETE FROM doktorlar 
    WHERE doktor_id = d_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaBul` (`filtre` VARCHAR(50))   BEGIN
    SELECT * FROM hastalar
    WHERE 
        hasta_id LIKE CONCAT('%', filtre, '%') OR
        tc_id LIKE CONCAT('%', filtre, '%') OR
        hasta_ad LIKE CONCAT('%', filtre, '%') OR
        hasta_soyad LIKE CONCAT('%', filtre, '%') OR
        telefon LIKE CONCAT('%', filtre, '%') OR
        email LIKE CONCAT('%', filtre, '%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaEkle` (`h_tc` CHAR(11), `h_ad` VARCHAR(50), `h_soyad` VARCHAR(50), `h_email` VARCHAR(255), `h_telefon` VARCHAR(11), `h_sifre` VARCHAR(255))   BEGIN
    INSERT INTO hastalar (tc_id, hasta_ad, hasta_soyad, email, telefon, sifre)
    VALUES (h_tc, h_ad, h_soyad, h_email, h_telefon, h_sifre);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaGuncelle` (`h_id` INT, `h_tc` CHAR(11), `h_ad` VARCHAR(50), `h_soyad` VARCHAR(50), `h_email` VARCHAR(255), `h_telefon` VARCHAR(11), `h_sifre` VARCHAR(255), `h_kayit_tarihi` TIMESTAMP)   BEGIN
    UPDATE hastalar 
    SET 
        tc_id = h_tc,
        hasta_ad = h_ad,
        hasta_soyad = h_soyad,
        email = h_email,
        telefon = h_telefon,
        sifre = h_sifre,
        kayit_tarihi = h_kayit_tarihi
    WHERE hasta_id = h_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaHepsi` ()   BEGIN
    SELECT 
        hasta_id as ID,
        tc_id as `TC Kimlik No`,
        hasta_ad as Adı,
        hasta_soyad as Soyadı,
        email as Mail,
        telefon as Telefon,
        kayit_tarihi as `Kayıt Tarihi`
    FROM hastalar;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaRandevular` (`h_id` INT)   BEGIN
    SELECT * FROM randevular WHERE hasta_id = h_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hastaSil` (`h_id` INT)   BEGIN
    DELETE FROM hastalar WHERE hasta_id = h_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `randevuDetay` ()   BEGIN
    SELECT 
        r.randevu_id,
        CONCAT(h.hasta_ad, ' ', h.hasta_soyad) as `Hasta Ad Soyad`,
        b.bolum_adi as `Bölüm`,
        CONCAT(d.doktor_ad, ' ', d.doktor_soyad) as `Doktor Ad Soyad`,
        r.islem_turu as `İşlem Türü`,
        r.randevu_tarihi as `Randevu Tarihi`,
        r.randevu_saati as `Randevu Saati`,
        r.hasta_notu,
        r.ilac_alerjisi,
        r.kronik_hastalik,
        r.durum as `Durum`
    FROM randevular r
    INNER JOIN hastalar h ON r.hasta_id = h.hasta_id
    INNER JOIN bolumler b ON r.bolum_id = b.bolum_id
    INNER JOIN doktorlar d ON r.doktor_id = d.doktor_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `randevuEkle` (`r_hasta_id` INT, `r_doktor_id` INT, `r_bolum_id` INT, `r_islem` VARCHAR(150), `r_tarih` DATE, `r_saat` TIME, `r_not` TEXT, `r_alerji` VARCHAR(255), `r_kronik` VARCHAR(255))   BEGIN
    INSERT INTO randevular(hasta_id, doktor_id, bolum_id, islem_turu, randevu_tarihi, randevu_saati, hasta_notu, ilac_alerjisi, kronik_hastalik)
    VALUES (r_hasta_id, r_doktor_id, r_bolum_id, r_islem, r_tarih, r_saat, r_not, r_alerji, r_kronik);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `randevuGuncelle` (`r_id` INT, `r_hasta_id` INT, `r_doktor_id` INT, `r_bolum_id` INT, `r_islem` VARCHAR(150), `r_tarih` DATE, `r_saat` TIME, `r_not` TEXT, `r_alerji` VARCHAR(255), `r_kronik` VARCHAR(255), `r_durum` VARCHAR(20))   BEGIN
    UPDATE randevular 
    SET 
        hasta_id = r_hasta_id,
        doktor_id = r_doktor_id,
        bolum_id = r_bolum_id,
        islem_turu = r_islem,
        randevu_tarihi = r_tarih,
        randevu_saati = r_saat,
        hasta_notu = r_not,
        ilac_alerjisi = r_alerji,
        kronik_hastalik = r_kronik,
        durum = r_durum
    WHERE randevu_id = r_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `randevularHepsi` ()   BEGIN
    SELECT * FROM randevular;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `randevuSil` (`r_id` INT)   BEGIN
    DELETE FROM randevular
    WHERE randevu_id = r_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `talepEkle` (`t_ad` VARCHAR(100), `t_soyad` VARCHAR(100), `t_email` VARCHAR(255), `t_telefon` VARCHAR(11), `t_ilgili_bolum` VARCHAR(100), `t_sikayet_turu` VARCHAR(100), `t_aciklama` TEXT)   BEGIN
    INSERT INTO sikayetvebilgi_talepleri (ad, soyad, email, telefon, ilgili_bolum, sikayet_turu, aciklama)
    VALUES (t_ad, t_soyad, t_email, t_telefon, t_ilgili_bolum, t_sikayet_turu, t_aciklama);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `talepHepsi` ()   BEGIN
    SELECT * FROM sikayetvebilgi_talepleri;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `talepSil` (`t_id` INT)   BEGIN
    DELETE FROM sikayetvebilgi_talepleri 
    WHERE id = t_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `toplamHastaSayisi` ()   BEGIN
    SELECT COUNT(*) as `Toplam Hasta Sayısı` FROM hastalar;
END$$

--
-- İşlevler
--
CREATE DEFINER=`root`@`localhost` FUNCTION `bolumDoktorSayisi` (`b_id` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE d_sayisi INT;
    SELECT COUNT(*) INTO d_sayisi
    FROM doktorlar
    WHERE bolum_id = b_id;
    RETURN d_sayisi;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `doktorRandevuSayisi` (`d_id` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE toplam INT;
    SELECT COUNT(*) INTO toplam
    FROM randevular
    WHERE doktor_id = d_id;
    RETURN toplam;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hastaRandevuSayisi` (`h_id` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE toplam INT;
    SELECT COUNT(*) INTO toplam
    FROM randevular
    WHERE hasta_id = h_id;
    RETURN toplam;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `randevuSaatiKontrol` (`r_tarih` DATE, `r_saat` TIME, `r_doktor_id` INT) RETURNS INT DETERMINISTIC BEGIN
    DECLARE toplam INT;
    SELECT COUNT(*) INTO toplam
    FROM randevular
    WHERE
    randevu_tarihi = r_tarih
    AND randevu_saati = r_saat
    AND doktor_id = r_doktor_id;

    RETURN toplam;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bolumler`
--

CREATE TABLE `bolumler` (
  `bolum_id` int NOT NULL,
  `bolum_adi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `bolumler`
--

INSERT INTO `bolumler` (`bolum_id`, `bolum_adi`) VALUES
(6, 'Ağız ve Çene Cerrahisi'),
(1, 'Endodonti'),
(5, 'Estetik Diş Hekimliği'),
(3, 'Genel Diş Hekimliği'),
(4, 'Ortodonti'),
(2, 'Periodontoloji');

--
-- Tetikleyiciler `bolumler`
--
DELIMITER $$
CREATE TRIGGER `tg_bolum_silme_kontrol` BEFORE DELETE ON `bolumler` FOR EACH ROW BEGIN
    declare dsayi int;
    declare mesaj varchar(250);

    select COUNT(*) into dsayi
    from doktorlar
    where bolum_id = OLD.bolum_id;

    IF (dsayi > 0) THEN

        set mesaj = CONCAT(
        'Bu bölümde kayıtlı ',
        dsayi,
        ' doktor olduğu için bölüm silinemez!'
        );

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = mesaj;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `doktorlar`
--

CREATE TABLE `doktorlar` (
  `doktor_id` int NOT NULL,
  `doktor_ad` varchar(50) NOT NULL,
  `doktor_soyad` varchar(50) NOT NULL,
  `bolum_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `doktorlar`
--

INSERT INTO `doktorlar` (`doktor_id`, `doktor_ad`, `doktor_soyad`, `bolum_id`) VALUES
(1, 'Suzan', 'Yeşil', 6),
(2, 'Elif', 'Durmuş', 1),
(4, 'Suzan', 'Durmuş', 4),
(5, 'Kaya', 'Can', 5),
(6, 'Selen', 'Nur', 3),
(7, 'Ahmet', 'Yılmaz', 2);

--
-- Tetikleyiciler `doktorlar`
--
DELIMITER $$
CREATE TRIGGER `tg_doktor_silme_kontrol` BEFORE DELETE ON `doktorlar` FOR EACH ROW BEGIN
    declare rsayi int;
    declare mesaj varchar(250);
    select COUNT(*) into rsayi
    from randevular
    where doktor_id = OLD.doktor_id;

    IF (rsayi > 0) THEN

        set mesaj = CONCAT(
        'Bu doktora ait ',
        rsayi,
        ' adet randevu bulunduğu için silinemez!'
        );

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = mesaj;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `hastalar`
--

CREATE TABLE `hastalar` (
  `hasta_id` int NOT NULL,
  `tc_id` char(11) NOT NULL,
  `hasta_ad` varchar(50) NOT NULL,
  `hasta_soyad` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(11) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `kayit_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Tablo döküm verisi `hastalar`
--

INSERT INTO `hastalar` (`hasta_id`, `tc_id`, `hasta_ad`, `hasta_soyad`, `email`, `telefon`, `sifre`, `kayit_tarihi`) VALUES
(1, '17827397232', 'Sueda', 'Çam', 'sueda.cam26@gmail.com', '05071013626', '$2y$10$ZaXufzU6lWDwyg4dErQ...ohz32C75g9SiO095zo8.qVtFT1STe9.', '2026-05-28 12:44:09'),
(2, '28791773127', 'Ayça', 'Durmuş', 'ayca@gmail.com', '05021713726', '$2y$10$TLOAidBOA.KBbloC0/QBBO1FvgfD/XctQzA81B3dunIwV7Vo5AfRe', '2026-05-28 12:48:18'),
(18, '19727387287', 'Şeyma', 'Nur', 'seyma@gmail.com', '05071013626', '$2y$10$1iqD0Nztd2ic8zm9RclZjeCA1kPbkgrtkdUINF9aANSF8mtq6P83u', '2026-05-30 12:34:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevular`
--

CREATE TABLE `randevular` (
  `randevu_id` int NOT NULL,
  `hasta_id` int NOT NULL,
  `doktor_id` int NOT NULL,
  `bolum_id` int NOT NULL,
  `islem_turu` varchar(150) NOT NULL,
  `randevu_tarihi` date NOT NULL,
  `randevu_saati` time NOT NULL,
  `hasta_notu` text,
  `ilac_alerjisi` varchar(255) DEFAULT NULL,
  `kronik_hastalik` varchar(255) DEFAULT NULL,
  `durum` varchar(20) DEFAULT 'Beklemede',
  `olusturma_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `randevular`
--

INSERT INTO `randevular` (`randevu_id`, `hasta_id`, `doktor_id`, `bolum_id`, `islem_turu`, `randevu_tarihi`, `randevu_saati`, `hasta_notu`, `ilac_alerjisi`, `kronik_hastalik`, `durum`, `olusturma_tarihi`) VALUES
(1, 1, 2, 1, 'Kanal Tedavisi Muayenesi', '2026-06-05', '11:00:00', 'Penisilin içerikli ilaçlara karşı alerjim bulunmaktadır.\r\nTedavi sürecinde bunun dikkate alınmasını rica ederim.', 'Var', 'Yok', 'Onaylandı', '2026-05-28 12:47:41'),
(2, 2, 6, 3, 'Diş Dolgusu (Kompozit / Amalgam)', '2026-05-29', '11:00:00', '', 'Yok', 'Yok', 'Onaylandı', '2026-05-28 12:48:35'),
(15, 18, 6, 3, 'Diş Taşı Temizliği (Detertraj)', '2026-06-03', '12:00:00', '', 'Yok', 'Yok', 'Onaylandı', '2026-05-30 12:35:01'),
(16, 18, 2, 1, 'Kanal Tedavisi (Başlangıç)', '2026-06-06', '11:30:00', '', 'Yok', 'Yok', 'Beklemede', '2026-05-30 12:35:22');

--
-- Tetikleyiciler `randevular`
--
DELIMITER $$
CREATE TRIGGER `tg_gecmis_tarih_kontrol` BEFORE INSERT ON `randevular` FOR EACH ROW BEGIN

    declare mesaj varchar(250);
    IF (NEW.randevu_tarihi < CURDATE()) THEN
        set mesaj = 'Geçmiş tarihe randevu oluşturulamaz!';
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = mesaj;

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sikayetvebilgi_talepleri`
--

CREATE TABLE `sikayetvebilgi_talepleri` (
  `id` int NOT NULL,
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(11) NOT NULL,
  `ilgili_bolum` varchar(100) DEFAULT NULL,
  `sikayet_turu` varchar(100) DEFAULT NULL,
  `aciklama` text NOT NULL
) ;

--
-- Tablo döküm verisi `sikayetvebilgi_talepleri`
--

INSERT INTO `sikayetvebilgi_talepleri` (`id`, `ad`, `soyad`, `email`, `telefon`, `ilgili_bolum`, `sikayet_turu`, `aciklama`) VALUES
(1, 'Sueda', 'Çam', 'suedacam26@hotmail.com', '05071013626', 'Endodonti', 'Diş Ağrısı', 'Kanal tedavisi sonrası ağrı normal midir?\r\nYaklaşık 2 gündür hafif sızlama devam ediyor.\r\nBilgi verebilir misiniz?'),
(2, 'Ayça', 'Durmuş', 'ayca@gmail.com', '05071013626', 'Genel Diş Hekimliği', 'Diğer', 'Geçtiğimiz hafta yapılan dolgu işleminden sonra dişimde hassasiyet oluştu.\r\nÖzellikle yemek yerken ağrı hissediyorum ve dolgunun yüksek kaldığını düşünüyorum.\r\nKontrol için dönüş yapılmasını rica ederim.'),
(9, 'Şeyma', 'Nur', 'seyma@gmail.com', '05071013626', 'Periodontoloji', 'Diş Hassasiyeti', '');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bolumler`
--
ALTER TABLE `bolumler`
  ADD PRIMARY KEY (`bolum_id`),
  ADD UNIQUE KEY `bolum_adi` (`bolum_adi`);

--
-- Tablo için indeksler `doktorlar`
--
ALTER TABLE `doktorlar`
  ADD PRIMARY KEY (`doktor_id`),
  ADD KEY `bolum_id` (`bolum_id`);

--
-- Tablo için indeksler `hastalar`
--
ALTER TABLE `hastalar`
  ADD PRIMARY KEY (`hasta_id`),
  ADD UNIQUE KEY `tc_id` (`tc_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `randevular`
--
ALTER TABLE `randevular`
  ADD PRIMARY KEY (`randevu_id`),
  ADD KEY `bolum_id` (`bolum_id`),
  ADD KEY `hasta_id` (`hasta_id`),
  ADD KEY `doktor_id` (`doktor_id`);

--
-- Tablo için indeksler `sikayetvebilgi_talepleri`
--
ALTER TABLE `sikayetvebilgi_talepleri`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bolumler`
--
ALTER TABLE `bolumler`
  MODIFY `bolum_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `doktorlar`
--
ALTER TABLE `doktorlar`
  MODIFY `doktor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `hastalar`
--
ALTER TABLE `hastalar`
  MODIFY `hasta_id` int NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `randevular`
--
ALTER TABLE `randevular`
  MODIFY `randevu_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `sikayetvebilgi_talepleri`
--
ALTER TABLE `sikayetvebilgi_talepleri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `doktorlar`
--
ALTER TABLE `doktorlar`
  ADD CONSTRAINT `doktorlar_ibfk_1` FOREIGN KEY (`bolum_id`) REFERENCES `bolumler` (`bolum_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `randevular`
--
ALTER TABLE `randevular`
  ADD CONSTRAINT `randevular_ibfk_1` FOREIGN KEY (`bolum_id`) REFERENCES `bolumler` (`bolum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `randevular_ibfk_2` FOREIGN KEY (`hasta_id`) REFERENCES `hastalar` (`hasta_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `randevular_ibfk_3` FOREIGN KEY (`doktor_id`) REFERENCES `doktorlar` (`doktor_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
