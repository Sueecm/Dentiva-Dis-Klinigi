<?php
require_once __DIR__ . '/db.php';

class bolum_dal {
    private $db;

    public function __construct() {
        $this->db = db::connect();
    }

    public function bolumEkle($bolum_adi) {
        $stmt = $this->db->prepare("CALL bolumEkle(?)");
        return $stmt->execute([$bolum_adi]);
    }

    public function bolumSil($id) {
        $stmt = $this->db->prepare("CALL bolumSil(?)");
        return $stmt->execute([$id]);
    }

    public function tumBolumleriGetir() {
        $stmt = $this->db->prepare("CALL bolumHepsi()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bolumBulAdIle($bolum_adi) {
        $bolumler = $this->tumBolumleriGetir();
        foreach ($bolumler as $b) {
            if (mb_strtolower($b['bolum_adi'], 'UTF-8') === mb_strtolower($bolum_adi, 'UTF-8')) {
                return $b;
            }
        }
        return null;
    }
}
?>