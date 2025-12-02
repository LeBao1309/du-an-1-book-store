<?php
require_once __DIR__ . '/BaseModel.php';

class Publisher extends BaseModel {
    public static function getAll() {
        $sql = "SELECT DISTINCT p.* FROM publisher p
                JOIN book_publisher bp ON p.id = bp.publisher_id
                ORDER BY p.name ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}