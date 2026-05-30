<?php
require_once __DIR__ . '/db.php';

class hasta_dal {
    private $db;

    public function __construct() {
        $this->db = db::connect();
    }

    public function hastaEkle($tc, $ad, $soyad, $email, $telefon, $sifre) {
        $stmt = $this->db->prepare("CALL hastaEkle(?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$tc, $ad, $soyad, $email, $telefon, $sifre]);
    }

    public function hastaSil($id) {
        $stmt = $this->db->prepare("CALL hastaSil(?)");
        return $stmt->execute([$id]);
    }

    public function hastaBul($filtre) {
        $stmt = $this->db->prepare("CALL hastaBul(?)");
        $stmt->execute([$filtre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function epostaIleGetir($email) {
        $stmt = $this->db->prepare("CALL hastaBul(?)");
        $stmt->execute([$email]);
        $sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sonuclar as $satir) {
          
            if (isset($satir['email']) && $satir['email'] === $email) {
                return $satir;
            }
        }
        return null;
    }

    public function tumHastalariGetir() {
        $stmt = $this->db->prepare("CALL hastaHepsi()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toplamHastaSayisi() {
        $stmt = $this->db->prepare("CALL toplamHastaSayisi()");
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['Toplam Hasta Sayısı'] ?? 0;
    }
}
?>