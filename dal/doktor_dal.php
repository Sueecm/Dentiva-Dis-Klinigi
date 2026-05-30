<?php
require_once __DIR__ . '/db.php';

class doktor_dal {
    private $db;

    public function __construct() {
        $this->db = db::connect();
    }

    public function doktorEkle($ad, $soyad, $bolum_id) {
        $stmt = $this->db->prepare("CALL doktorEkle(?, ?, ?)");
        return $stmt->execute([$ad, $soyad, $bolum_id]);
    }

    public function doktorSil($id) {
        $stmt = $this->db->prepare("CALL doktorSil(?)");
        return $stmt->execute([$id]);
    }

    public function tumDoktorlariGetir() {
        $stmt = $this->db->prepare("CALL doktorHepsi()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function doktorDetayliGetir() {
        $stmt = $this->db->prepare("CALL doktorDetay()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bolumeGoreDoktorlariGetir($bolum_id) {
        $tumDoktorlar = $this->tumDoktorlariGetir();
        $filtreli = [];
        foreach ($tumDoktorlar as $d) {
            if ((int)$d['bolum_id'] === (int)$bolum_id) {
                $filtreli[] = $d;
            }
        }
        return $filtreli;
    }
}
?>