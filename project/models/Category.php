<?php

// Sửa 1: Kế thừa BaseModel
class Category extends BaseModel {
    
    public static function find($id) {
        // Sửa 2: Bỏ 'global $conn', dùng 'self::db()' (PDO)
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // Sửa 3: fetch() cho 1 dòng
    }
}