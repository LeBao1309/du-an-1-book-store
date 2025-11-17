<?php

require_once __DIR__ . '/BaseModel.php';
class Category extends BaseModel {
    
    /**
     * Tìm một danh mục theo ID
     * (Dùng để lấy tên danh mục ở trang category)
     */
    public static function find($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); 
    }

    /**
     * Lấy tất cả danh mục
     * (Dùng để hiển thị ở sidebar filter)
     */
    public static function getAll() {
        // Sắp xếp theo tên cho dễ nhìn
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}