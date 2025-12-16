<?php

/**
 * Base Model - Lớp cơ sở cho tất cả các Model
 * Quản lý kết nối database với PDO
 */
abstract class BaseModel
{
    /**
     * @var PDO|null Instance PDO singleton
     */
    protected static ?\PDO $pdo = null;

    /**
     * Lấy kết nối database PDO (Singleton Pattern)
     * @return PDO
     */
    protected static function db(): \PDO
    {
        if (self::$pdo === null) {
            // Cấu hình database từ environment variables
            $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
            $DB_PORT = getenv('DB_PORT') ?: '3306';
            $DB_NAME = getenv('DB_NAME') ?: 'du_an_1_book_store';
            $DB_USER = getenv('DB_USER') ?: 'root';
            $DB_PASS = getenv('DB_PASS') ?: '';

            $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
            
            self::$pdo = new \PDO($dsn, $DB_USER, $DB_PASS, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        
        return self::$pdo;
    }

    /**
     * Đóng kết nối database
     * @return void
     */
    public static function closeConnection(): void
    {
        self::$pdo = null;
    }
}