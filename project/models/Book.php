<?php

// Sửa 1: Kế thừa BaseModel
class Book extends BaseModel {
    
    public static function getByCategory($categoryId) {
        // Sửa 2: Bỏ 'global $conn', dùng 'self::db()' (PDO)
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.category_id = ?
                GROUP BY b.id, b.title";
        
        $stmt = self::db()->prepare($sql);
        // Sửa 3: execute kiểu PDO
        $stmt->execute([$categoryId]); 
        // Sửa 4: fetchAll kiểu PDO (BaseModel đã set FETCH_ASSOC)
        return $stmt->fetchAll(); 
    }

    public static function getAll() {
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                GROUP BY b.id, b.title";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findById($id) {
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(); // Sửa: fetch() cho 1 dòng
    }

    public static function getVariants($bookId) {
        $sql = "SELECT id, format, price, sale_price, stock
                FROM book_variants 
                WHERE book_id = ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public static function getImages($bookId) {
        $sql = "SELECT image_url, sort_order 
                FROM book_images 
                WHERE book_id = ? 
                ORDER BY sort_order ASC";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }
}