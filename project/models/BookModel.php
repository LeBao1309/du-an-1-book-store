<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Book Model - Quản lý thông tin sách
 */
final class BookModel extends BaseModel
{
    // 1. Lấy sách mới cho trang chủ
    public static function getNewestProducts($limit = 8) {
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url,
                       GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names,
                       p.name as publisher_name
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                LEFT JOIN book_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN book_publisher bp ON b.id = bp.book_id
                LEFT JOIN publisher p ON bp.publisher_id = p.id
                
                WHERE b.is_active = 1
                GROUP BY b.id, b.title, b.slug, p.name
                ORDER BY b.id DESC
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 2. Hàm Lọc Đa Năng (Danh mục + Từ khóa + Lọc Giá)
    public static function filter($params = [], $limit = 9, $offset = 0) {
        $sql = "SELECT b.id, b.title, b.slug, b.category_id,
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url,
                       GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names,
                       p.name as publisher_name
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                LEFT JOIN book_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN book_publisher bp ON b.id = bp.book_id
                LEFT JOIN publisher p ON bp.publisher_id = p.id
                WHERE b.is_active = 1 ";

        $bindings = [];

        // Lọc Danh mục (Lấy cả cha lẫn con)
        if (!empty($params['category_id'])) {
            $sql .= " AND (b.category_id = ? OR b.category_id IN (SELECT id FROM categories WHERE parent_id = ?)) ";
            $bindings[] = $params['category_id'];
            $bindings[] = $params['category_id'];
        }

        // Lọc từ khóa (cho trang danh sách thông thường)
        if (!empty($params['keyword'])) {
            $sql .= " AND b.title LIKE ? ";
            $bindings[] = '%' . $params['keyword'] . '%';
        }

        $sql .= " GROUP BY b.id, b.title, b.slug, b.category_id, p.name ";

        // Lọc Giá (Dùng HAVING vì display_price là alias được tính toán)
        $havingClause = [];
        if (!empty($params['min_price'])) {
            $havingClause[] = "display_price >= ?";
            $bindings[] = $params['min_price'];
        }
        if (!empty($params['max_price'])) {
            $havingClause[] = "display_price <= ?";
            $bindings[] = $params['max_price'];
        }

        if (!empty($havingClause)) {
            $sql .= " HAVING " . implode(' AND ', $havingClause);
        }

        // Sắp xếp
        $sort = $params['sort'] ?? 'newest';
        if ($sort === 'price-asc') $sql .= " ORDER BY display_price ASC ";
        elseif ($sort === 'price-desc') $sql .= " ORDER BY display_price DESC ";
        else $sql .= " ORDER BY b.id DESC ";

        $sql .= " LIMIT ? OFFSET ?";
        $bindings[] = $limit;
        $bindings[] = $offset;

        $stmt = self::db()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    // 3. Đếm tổng (để phân trang)
    public static function countFilter($params = []) {
        // Logic đếm đơn giản hóa để tránh lỗi query phức tạp
        $sql = "SELECT COUNT(DISTINCT b.id) 
                FROM books b
                WHERE b.is_active = 1 ";
        
        $bindings = [];
        if (!empty($params['category_id'])) {
            $sql .= " AND (b.category_id = ? OR b.category_id IN (SELECT id FROM categories WHERE parent_id = ?)) ";
            $bindings[] = $params['category_id'];
            $bindings[] = $params['category_id'];
        }
        if (!empty($params['keyword'])) {
            $sql .= " AND b.title LIKE ? ";
            $bindings[] = '%' . $params['keyword'] . '%';
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchColumn();
    }

    // 4. TÌM KIẾM THÔNG MINH (Ajax Search)
    // Tìm cả Tên sách, Tác giả, NXB
    public static function searchByName($keyword, $limit = 5) {
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(i.image_url) as image_url,
                       MIN(IFNULL(v.sale_price, v.price)) as price,
                       GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names
                FROM books b
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                LEFT JOIN book_variants v ON v.book_id = b.id
                -- JOIN các bảng liên quan để tìm kiếm
                LEFT JOIN book_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN book_publisher bp ON b.id = bp.book_id
                LEFT JOIN publisher p ON bp.publisher_id = p.id
                
                WHERE b.is_active = 1 
                  AND (
                      b.title LIKE ? 
                      OR a.name LIKE ? 
                      OR p.name LIKE ?
                  )
                GROUP BY b.id, b.title, b.slug
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $term = "%$keyword%"; // Từ khóa tìm kiếm dạng %abc%
        
        $stmt->bindValue(1, $term);
        $stmt->bindValue(2, $term);
        $stmt->bindValue(3, $term);
        $stmt->bindValue(4, $limit, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // --- Các hàm cơ bản khác (Giữ nguyên) ---
    public static function findById($id) {
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function getVariants($bookId) {
        $sql = "SELECT id, format, price, sale_price, stock FROM book_variants WHERE book_id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }
    public static function getImages($bookId) {
        $sql = "SELECT image_url, sort_order FROM book_images WHERE book_id = ? ORDER BY sort_order ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }
    
    public static function getRelatedProducts($bookId, $categoryId, $limit = 4) {
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                WHERE b.category_id = ? AND b.id != ? AND b.is_active = 1
                GROUP BY b.id, b.title, b.slug
                ORDER BY RAND()
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $bookId, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 5. Lấy sách bán chạy (dựa trên số lượng đã bán)
    public static function getBestSellers($limit = 8) {
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(IFNULL(v.sale_price, v.price)) as display_price, 
                       MIN(i.image_url) as image_url,
                       GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names,
                       p.name as publisher_name,
                       COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                LEFT JOIN book_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN book_publisher bp ON b.id = bp.book_id
                LEFT JOIN publisher p ON bp.publisher_id = p.id
                LEFT JOIN order_items oi ON v.id = oi.variant_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.shipping_status = 'delivered'
                
                WHERE b.is_active = 1
                  AND i.image_url IS NOT NULL
                GROUP BY b.id, b.title, b.slug, p.name
                HAVING total_sold > 0
                ORDER BY total_sold DESC
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 6. Lấy sách giảm giá nhiều nhất
    public static function getDiscountedBooks($limit = 8) {
        $sql = "SELECT b.id, b.title, b.slug,
                       MIN(v.price) as original_price,
                       MIN(v.sale_price) as sale_price,
                       MIN(i.image_url) as image_url,
                       GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') as author_names,
                       p.name as publisher_name,
                       ROUND((1 - MIN(v.sale_price) / MIN(v.price)) * 100) as discount_percent
                FROM books b
                LEFT JOIN book_variants v ON v.book_id = b.id
                LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
                LEFT JOIN book_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN book_publisher bp ON b.id = bp.book_id
                LEFT JOIN publisher p ON bp.publisher_id = p.id
                
                WHERE b.is_active = 1
                  AND v.sale_price IS NOT NULL
                  AND v.sale_price < v.price
                  AND i.image_url IS NOT NULL
                GROUP BY b.id, b.title, b.slug, p.name
                HAVING discount_percent >= 10
                ORDER BY discount_percent DESC
                LIMIT ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

        /**
     * Lấy variant rẻ nhất của 1 book (dùng để add-to-cart khi không chọn variant)
     */
    public static function getCheapestVariantId(int $bookId): int
    {
        $sql = "
            SELECT id
            FROM book_variants
            WHERE book_id = ?
            ORDER BY COALESCE(NULLIF(sale_price, 0), price) ASC, id ASC
            LIMIT 1
        ";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        $id = (int)$stmt->fetchColumn();
        return $id > 0 ? $id : 0;
    }

    /**
     * Hydrate giỏ hàng theo variant_ids (lấy title/format/ảnh/giá THỰC từ DB)
     */
    public static function getCartItemsByVariantIds(array $variantIds): array
    {
        if (empty($variantIds)) return [];

        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

        $sql = "
            SELECT
                bv.id AS variant_id,
                bv.book_id,
                bv.format,
                CAST(COALESCE(NULLIF(bv.sale_price, 0), bv.price) AS UNSIGNED) AS unit_price,
                b.title,
                img.image_url
            FROM book_variants bv
            JOIN books b ON b.id = bv.book_id
            LEFT JOIN book_images img
                ON img.book_id = b.id AND img.sort_order = 0
            WHERE bv.id IN ($placeholders)
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_values($variantIds));
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra variant có thuộc book không
     */
    public static function isVariantOfBook(int $variantId, int $bookId): bool
    {
        $sql = "SELECT COUNT(*) FROM book_variants WHERE id = ? AND book_id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$variantId, $bookId]);
        return (int)$stmt->fetchColumn() > 0;
    }

}
