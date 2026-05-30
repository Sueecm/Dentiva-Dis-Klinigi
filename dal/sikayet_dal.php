<?php
require_once __DIR__ . '/db.php';

class sikayet_dal {
    private $db;

    public function __construct() {
        $this->db = db::connect();
    }

    public function talepEkle($ad, $soyad, $email, $telefon, $ilgili_bolum, $sikayet_turu, $aciklama) {
        $stmt = $this->db->prepare("CALL talepEkle(?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$ad, $soyad, $email, $telefon, $ilgili_bolum, $sikayet_turu, $aciklama]);
    }

    public function talepSil($id) {
        $stmt = $this->db->prepare("CALL talepSil(?)");
        return $stmt->execute([$id]);
    }

    public function tumTalepleriGetir() {
        $stmt = $this->db->prepare("CALL talepHepsi()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>