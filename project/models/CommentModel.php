<?php

require_once __DIR__ . '/BaseModel.php';

class Comment extends BaseModel {
    
    /**
     * Lấy tất cả bình luận của 1 cuốn sách (đơn giản)
     */
    public static function getByBookId($bookId) {
        // Lấy kết nối database
        $db = self::db();
        
        // Viết câu SQL - JOIN với bảng users để lấy tên người dùng
        $sql = "SELECT c.*, u.name as user_name 
                FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.book_id = ?
                ORDER BY c.created_at DESC";
        
        // Chuẩn bị và thực thi
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookId]);
        
        // Trả về tất cả bình luận dạng array
        return $stmt->fetchAll();
    }

    /**
     * Tính rating trung bình của 1 cuốn sách (đơn giản)
     */
    public static function getAverageRating($bookId) {
        $db = self::db();
        
        // Dùng AVG để tính trung bình, COUNT để đếm
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total
                FROM comments 
                WHERE book_id = ? AND rating IS NOT NULL";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookId]);
        
        return $stmt->fetch();
    }

    /**
     * Thêm bình luận mới (đơn giản)
     */
    public static function create($bookId, $userId, $content, $rating) {
        $db = self::db();
        
        // INSERT bình luận mới
        $sql = "INSERT INTO comments (book_id, user_id, content, rating, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($sql);
        
        // Thực thi và trả về kết quả true/false
        return $stmt->execute([$bookId, $userId, $content, $rating]);
    }
}
