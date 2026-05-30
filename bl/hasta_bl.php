<?php
require_once __DIR__ . '/../dal/hasta_dal.php';

class hasta_bl {
    private $hastaDAL;

    public function __construct() {
        $this->hastaDAL = new hasta_dal();
    }

    public function kayitOl($tc_id, $ad, $soyad, $email, $telefon, $sifre, $sifre_tekrar) {
        if ($sifre !== $sifre_tekrar) {
            throw new Exception("Şifreler birbiriyle uyuşmuyor!");
        }
        if (strlen($tc_id) !== 11 || !is_numeric($tc_id)) {
            throw new Exception("T.C. Kimlik No 11 haneli ve sayısal olmalıdır.");
        }

        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        return $this->hastaDAL->hastaEkle($tc_id, trim($ad), trim($soyad), trim($email), trim($telefon), $sifre_hash);
    }

    public function girisYap($email, $sifre) {
        $kullanici = $this->hastaDAL->epostaIleGetir(trim($email));
        if (!$kullanici) {
            throw new Exception("Böyle bir hesap bulunamadı. Lütfen önce kayıt olun.");
        }
        if (!password_verify($sifre, $kullanici['sifre'])) {
            throw new Exception("Girdiğiniz şifre hatalı!");
        }
        return $kullanici;
    }
}
?>