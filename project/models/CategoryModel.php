<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Category Model - Qu?n l� danh m?c s�ch
 */
final class CategoryModel extends BaseModel
{
    /**
     * T�m m?t danh m?c theo ID
     * @param int $id ID c?a danh m?c
     * @return array|null Th�ng tin danh m?c
     */
    public static function find(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding category: " . $e->getMessage());
            return null;
        }
    }

    /**
     * L?y t?t c? danh m?c (ph?ng)
     * @return array Danh s�ch danh m?c
     */
    public static function getAll(): array
    {
        try {
            $sql = "SELECT * FROM categories ORDER BY name ASC";
            $stmt = self::db()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * L?y danh m?c d?ng C�y (Cha -> Con) d? hi?n th? Menu
     * @return array Danh m?c d?ng c�y
     */
    public static function getTree(): array
    {
        try {
            $sql = "SELECT * FROM categories ORDER BY parent_id ASC, name ASC";
            $stmt = self::db()->prepare($sql);
            $stmt->execute();
            $allCats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $tree = [];
            
            // L?y danh m?c Cha (parent_id = NULL ho?c 0)
            foreach ($allCats as $key => $cat) {
                if (empty($cat['parent_id'])) {
                    $cat['children'] = [];
                    $tree[$cat['id']] = $cat;
                    unset($allCats[$key]);
                }
            }
            
            // G�n c�c danh m?c c�n l?i v�o cha c?a n�
            foreach ($allCats as $cat) {
                if (!empty($cat['parent_id']) && isset($tree[$cat['parent_id']])) {
                    $tree[$cat['parent_id']]['children'][] = $cat;
                }
            }

            return $tree;
        } catch (PDOException $e) {
            error_log("Error getting category tree: " . $e->getMessage());
            return [];
        }
    }

    /**
     * L?y danh m?c cha k�m s? lu?ng s�ch
     * @return array Danh s�ch danh m?c cha v?i s? lu?ng s�ch
     */
    public static function getParentCategoriesWithCount(): array
    {
        try {
            $sql = "SELECT 
                        c.id, 
                        c.name, 
                        c.slug, 
                        COUNT(DISTINCT b.id) as book_count
                    FROM categories c
                    LEFT JOIN books b ON (
                        (b.category_id = c.id AND b.is_active = 1)
                        OR (b.category_id IN (SELECT id FROM categories WHERE parent_id = c.id) AND b.is_active = 1)
                    )
                    WHERE c.parent_id IS NULL OR c.parent_id = 0
                    GROUP BY c.id, c.name, c.slug
                    ORDER BY c.name ASC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting parent categories with count: " . $e->getMessage());
            return [];
        }
    }
}
