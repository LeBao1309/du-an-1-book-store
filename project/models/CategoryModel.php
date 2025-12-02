<?php
require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    
    /**
     * Tìm một danh mục theo ID
     */
    public static function find($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); 
    }

    /**
     * Lấy tất cả danh mục (phẳng)
     */
    public static function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh mục dạng Cây (Cha -> Con) để hiển thị Menu
     */
    public static function getTree() {
        $sql = "SELECT * FROM categories ORDER BY parent_id ASC, name ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        $allCats = $stmt->fetchAll();

        $tree = [];
        // Lấy danh mục Cha (parent_id = NULL hoặc 0)
        foreach ($allCats as $key => $cat) {
            if (empty($cat['parent_id'])) {
                $cat['children'] = [];
                $tree[$cat['id']] = $cat;
                unset($allCats[$key]);
            }
        }

        // Gán các danh mục còn lại vào cha của nó
        foreach ($allCats as $cat) {
            if (!empty($cat['parent_id']) && isset($tree[$cat['parent_id']])) {
                $tree[$cat['parent_id']]['children'][] = $cat;
            }
        }

        return $tree;
    }
}