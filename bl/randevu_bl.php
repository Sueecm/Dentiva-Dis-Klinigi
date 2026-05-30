<?php
require_once __DIR__ . '/../dal/randevu_dal.php';
require_once __DIR__ . '/../dal/bolum_dal.php';
require_once __DIR__ . '/../dal/doktor_dal.php';

class randevu_bl {
    private $randevuDAL;
    private $bolumDAL;
    private $doktorDAL;

    public function __construct() {
        $this->randevuDAL = new randevu_dal();
        $this->bolumDAL   = new bolum_dal();
        $this->doktorDAL  = new doktor_dal();
    }

    public function klinikVerileriniGetir($bolum_adi) {
        $bolum = $this->bolumDAL->bolumBulAdIle($bolum_adi);
        if (!$bolum) {
            throw new Exception("Hata: '$bolum_adi' bölümü sistemde bulunamadı!");
        }
        return [
            'bolum_id' => $bolum['bolum_id'],
            'doktorlar' => $this->doktorDAL->bolumeGoreDoktorlariGetir($bolum['bolum_id'])
        ];
    }

    public function randevuOlustur($hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, $not, $alerji, $kronik) {
        if (empty($doktor_id) || empty($tarih) || empty($saat)) {
            throw new Exception("Lütfen doktor, tarih ve saat alanlarını eksiksiz doldurunuz.");
        }
        

        $mevcutRandevular = $this->randevuDAL->tumRandevulariGetir();
        foreach ($mevcutRandevular as $r) {
            if ((int)$r['doktor_id'] === (int)$doktor_id && 
                $r['randevu_tarihi'] === $tarih && 
                $r['randevu_saati'] === $saat) {
                throw new Exception("Seçtiğiniz doktorun bu tarih ve saatte zaten bir randevusu bulunmaktadır.");
            }
        }

        return $this->randevuDAL->randevuEkle($hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, trim($not), $alerji, $kronik);
    }

    public function hastaninDetayliRandevulari($hasta_id) {
        $detayli = $this->randevuDAL->randevuDetayliGetir();
        $hamRandevular = $this->randevuDAL->hastaninRandevulariniGetir($hasta_id);
        
        $hastaninIdleri = array_column($hamRandevular, 'randevu_id');
        $sonuc = [];
        
        foreach ($detayli as $d) {
            if (in_array($d['randevu_id'], $hastaninIdleri)) {
                $sonuc[] = $d;
            }
        }
        return $sonuc;
    }
}
?>