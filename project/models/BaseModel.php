<?php 

class BaseModel {
   protected static ?\PDO $pdo = null;
   protected static function db(): \PDO
    {
        if (self::$pdo === null) {
            // Minimal in-file "config"
            $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
            $DB_PORT = getenv('DB_PORT') ?: '3306';
            $DB_NAME = getenv('DB_NAME') ?: 'du_an_1_book_store';
            $DB_USER = getenv('DB_USER') ?: 'root';
            $DB_PASS = getenv('DB_PASS') ?: 'root123';

            $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
            self::$pdo = new \PDO($dsn, $DB_USER, $DB_PASS, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }
}