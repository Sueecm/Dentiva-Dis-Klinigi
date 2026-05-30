<?php
class db {
    private static $host = 'localhost';
    private static $dbname = 'disklinik_sistemi';
    private static $username = 'root';
    private static $password = 'sueda2005';
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4", 
                    self::$username, 
                    self::$password
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Veritabanı bağlantı hatası: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>