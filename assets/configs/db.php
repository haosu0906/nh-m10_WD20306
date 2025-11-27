<?php
require_once __DIR__ . '/../configs/env.php';

class DB {
    private static $pdo = null;

    public static function get() {
        if (self::$pdo) return self::$pdo;
        
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8";
            self::$pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
            return self::$pdo;
        } catch (PDOException $e) {
            die("Lỗi kết nối DB: " . $e->getMessage());
        }
    }
}