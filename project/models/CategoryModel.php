<?php

require_once __DIR__ . '/BaseModel.php';
class Category extends BaseModel {
    
    public static function find($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); 
    }
}