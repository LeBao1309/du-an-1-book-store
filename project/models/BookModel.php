<?php

require_once __DIR__ . '/BaseModel.php';
class Book extends BaseModel {
    
    // === HÀM MỚI 3: LẤY SẢN PHẨM MỚI CHO TRANG CHỦ ===
    public static function getNewestProducts($limit = 8) {
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                GROUP BY b.id, b.title
                ORDER BY b.id DESC
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // === HẾT HÀM MỚI ===

    // === HÀM MỚI 4: LẤY SẢN PHẨM LIÊN QUAN (CÙNG DANH MỤC) ===
    public static function getRelatedProducts($bookId, $categoryId, $limit = 4) {
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.category_id = ? AND b.id != ?
                GROUP BY b.id, b.title
                ORDER BY RAND()
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $bookId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // === HẾT HÀM MỚI 4 ===

    // === HÀM ĐÃ SỬA (THÊM $sort) ===
    public static function searchByTitle($query, $limit, $offset, $sort = 'newest') {
        $searchTerm = '%' . $query . '%'; 

        // Logic Sắp xếp
        $orderBy = "ORDER BY b.id DESC"; // Mới nhất (mặc định)
        if ($sort === 'price-asc') {
            $orderBy = "ORDER BY display_price ASC"; // Giá tăng dần
        } elseif ($sort === 'price-desc') {
            $orderBy = "ORDER BY display_price DESC"; // Giá giảm dần
        }

        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.title LIKE ?
                GROUP BY b.id, b.title
                $orderBy
                LIMIT ? OFFSET ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $searchTerm); // Dấu ? thứ 1
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);  // Dấu ? thứ 2
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT); // Dấu ? thứ 3
        $stmt->execute(); 
        return $stmt->fetchAll(); 
    }

    // === HÀM MỚI (CHO TÌM KIẾM): ĐẾM SỐ SÁCH THEO TÊN (cho phân trang) ===
    public static function countByTitle($query) {
        $searchTerm = '%' . $query . '%'; // Thêm dấu % cho SQL LIKE

        $sql = "SELECT COUNT(id) FROM books WHERE title LIKE ?";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $searchTerm);
        $stmt->execute();
        return $stmt->fetchColumn(); 
    }


    // === HÀM MỚI 1: Đếm sách theo danh mục ===
    public static function countByCategory($categoryId) {
        $sql = "SELECT COUNT(id) FROM books WHERE category_id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn(); 
    }

    // === HÀM CŨ (ĐÃ SỬA - THÊM $sort) ===
    public static function getByCategory($categoryId, $limit, $offset, $sort = 'newest') {
        
        // Logic Sắp xếp
        $orderBy = "ORDER BY b.id DESC"; // Mới nhất (mặc định)
        if ($sort === 'price-asc') {
            $orderBy = "ORDER BY display_price ASC"; // Giá tăng dần
        } elseif ($sort === 'price-desc') {
            $orderBy = "ORDER BY display_price DESC"; // Giá giảm dần
        }

        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.category_id = ?
                GROUP BY b.id, b.title
                $orderBy
                LIMIT ? OFFSET ?";
        
        $stmt = self::db()->prepare($sql);

        $stmt->bindValue(1, $categoryId); // Dấu ? thứ 1
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);  // Dấu ? thứ 2
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT); // Dấu ? thứ 3
        $stmt->execute(); 

        return $stmt->fetchAll(); 
    }

    // === HÀM MỚI 2: Đếm tất cả sách ===
    public static function countAll() {
        $sql = "SELECT COUNT(id) FROM books";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // === HÀM CŨ (ĐÃ SỬA - THÊM $sort) ===
    public static function getAll($limit, $offset, $sort = 'newest') {

        // Logic Sắp xếp
        $orderBy = "ORDER BY b.id DESC"; // Mới nhất (mặc định)
        if ($sort === 'price-asc') {
            $orderBy = "ORDER BY display_price ASC"; // Giá tăng dần
        } elseif ($sort === 'price-desc') {
            $orderBy = "ORDER BY display_price DESC"; // Giá giảm dần
        }

        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                GROUP BY b.id, b.title
                $orderBy
                LIMIT ? OFFSET ?";
        
        $stmt = self::db()->prepare($sql);

        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);  // Dấu ? thứ 1
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT); // Dấu ? thứ 2
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // (Các hàm còn lại giữ nguyên)
    public static function findById($id) {
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
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

    // === HÀM MỚI: LẤY SẢN PHẨM LIÊN QUAN (CÙNG DANH MỤC) ===
    public static function getRelatedProducts($bookId, $categoryId, $limit = 4) {
        $sql = "SELECT b.id, b.title, 
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id
                WHERE b.category_id = ? AND b.id != ?
                GROUP BY b.id, b.title
                ORDER BY RAND()
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $bookId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // === HẾT HÀM MỚI ===
}