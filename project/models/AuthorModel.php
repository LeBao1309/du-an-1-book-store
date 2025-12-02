<?php
require_once __DIR__ . '/BaseModel.php';

class Author extends BaseModel {
    public static function getAll() {
        // Lấy danh sách tác giả có sách (để tránh lọc ra tác giả rỗng)
        $sql = "SELECT DISTINCT a.* FROM authors a 
                JOIN book_authors ba ON a.id = ba.author_id
                ORDER BY a.name ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}