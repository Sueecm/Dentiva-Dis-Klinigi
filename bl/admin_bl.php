<?php
require_once __DIR__ . '/../dal/randevu_dal.php';
require_once __DIR__ . '/../dal/hasta_dal.php';
require_once __DIR__ . '/../dal/doktor_dal.php';
require_once __DIR__ . '/../dal/bolum_dal.php';
require_once __DIR__ . '/../dal/sikayet_dal.php';

class admin_bl {
    private $randevuDAL; 
    private $hastaDAL; 
    private $doktorDAL; 
    private $bolumDAL; 
    private $sikayetDAL;

    public function __construct() {
        $this->randevuDAL = new randevu_dal(); 
        $this->hastaDAL = new hasta_dal();
        $this->doktorDAL  = new doktor_dal();  
        $this->bolumDAL = new bolum_dal();
        $this->sikayetDAL = new sikayet_dal();
    }

    public function randevuOnayla($randevu_id) {
        $randevular = $this->randevuDAL->tumRandevulariGetir();
        foreach ($randevular as $r) {
            if ((int)$r['randevu_id'] === (int)$randevu_id) {
                return $this->randevuDAL->randevuGuncelle(
                    $r['randevu_id'], $r['hasta_id'], $r['doktor_id'], $r['bolum_id'],
                    $r['islem_turu'], $r['randevu_tarihi'], $r['randevu_saati'],
                    $r['hasta_notu'], $r['ilac_alerjisi'], $r['kronik_hastalik'], 'Onaylandı'
                );
            }
        }
        return false;
    }
    
    public function randevuSil($id) { return $this->randevuDAL->randevuSil($id); }
    public function tumRandevulariDetayli() { return $this->randevuDAL->randevuDetayliGetir(); }
    public function aktifRandevuSayisi() { return $this->randevuDAL->aktifRandevuSayisi(); }

    public function hastaEkle($tc, $ad, $soyad, $email, $tel, $sifre) {
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        return $this->hastaDAL->hastaEkle($tc, $ad, $soyad, $email, $tel, $sifre_hash);
    }
    
    public function hastaSil($id) { return $this->hastaDAL->hastaSil($id); }
    public function tumHastalar() { return $this->hastaDAL->tumHastalariGetir(); }
    public function toplamHastaSayisi() { return $this->hastaDAL->toplamHastaSayisi(); }

    public function doktorEkle($ad, $soyad, $bolum_id) { return $this->doktorDAL->doktorEkle($ad, $soyad, $bolum_id); }
    public function doktorSil($id) { return $this->doktorDAL->doktorSil($id); }
    public function tumDoktorlarDetayli() { return $this->doktorDAL->doktorDetayliGetir(); }

    public function bolumEkle($ad) { return $this->bolumDAL->bolumEkle($ad); }
    public function bolumSil($id) { return $this->bolumDAL->bolumSil($id); }
    public function tumBolumler() { return $this->bolumDAL->tumBolumleriGetir(); }

    public function talepOlustur($ad_soyad, $email, $telefon, $ilgili_bolum, $sikayet_turu, $aciklama) {
        $parcalar = explode(" ", trim($ad_soyad));
        $soyad = (count($parcalar) > 1) ? array_pop($parcalar) : ""; 
        $ad = implode(" ", $parcalar);
        return $this->sikayetDAL->talepEkle($ad, $soyad, $email, $telefon, $ilgili_bolum, $sikayet_turu, $aciklama);
    }
    
    public function talepSil($id) { return $this->sikayetDAL->talepSil($id); }
    public function tumTalepler() { return $this->sikayetDAL->tumTalepleriGetir(); }
} 
?>