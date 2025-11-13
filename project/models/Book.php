<?php

class Book {
    
    /**
     * Hàm này bạn đã có: Lấy sách theo 1 danh mục
     */
    public static function getByCategory($categoryId) {
        global $conn;
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.category_id = ?
                GROUP BY b.id, b.title";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * SỬA: Thêm hàm mới này
     * Lấy TẤT CẢ sách
     */
    public static function getAll() {
        global $conn;
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                /* (Không có dòng WHERE) */
                GROUP BY b.id, b.title";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // --- (Các hàm khác như findById, getVariants, getImages giữ nguyên) ---

    public static function findById($id) {
        global $conn;
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getVariants($bookId) {
        global $conn;
        $sql = "SELECT id, format, price, sale_price, stock
                FROM book_variants 
                WHERE book_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getImages($bookId) {
        global $conn;
        $sql = "SELECT image_url, sort_order 
                FROM book_images 
                WHERE book_id = ? 
                ORDER BY sort_order ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}