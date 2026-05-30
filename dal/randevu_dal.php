<?php
require_once __DIR__ . '/db.php';

class randevu_dal {
    private $db;

    public function __construct() {
        $this->db = db::connect();
    }

    public function randevuEkle($hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, $not, $alerji, $kronik) {
        $stmt = $this->db->prepare("CALL randevuEkle(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, $not, $alerji, $kronik]);
    }

    public function randevuSil($id) {
        $stmt = $this->db->prepare("CALL randevuSil(?)");
        return $stmt->execute([$id]);
    }

    public function randevuGuncelle($id, $hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, $not, $alerji, $kronik, $durum) {
        $stmt = $this->db->prepare("CALL randevuGuncelle(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $hasta_id, $doktor_id, $bolum_id, $islem, $tarih, $saat, $not, $alerji, $kronik, $durum]);
    }

    public function tumRandevulariGetir() {
        $stmt = $this->db->prepare("CALL randevularHepsi()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function randevuDetayliGetir() {
        $stmt = $this->db->prepare("CALL randevuDetay()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hastaninRandevulariniGetir($hasta_id) {
        $stmt = $this->db->prepare("CALL hastaRandevular(?)");
        $stmt->execute([$hasta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aktifRandevuSayisi() {
        $stmt = $this->db->prepare("CALL aktifRandevuSayisi()");
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['Aktif Randevu Sayısı'] ?? 0;
    }
}
?>