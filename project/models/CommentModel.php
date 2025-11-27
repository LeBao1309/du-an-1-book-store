<?php

require_once __DIR__ . '/BaseModel.php';

class Comment extends BaseModel {
    
    /**
     * Lấy tất cả bình luận của 1 cuốn sách
     */
    public static function getByBookId($bookId) {
        $db = self::db();
        
        $sql = "SELECT c.*, u.name as user_name 
                FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.book_id = ?
                ORDER BY c.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookId]);
        
        return $stmt->fetchAll();
    }

    /**
     * Tính rating trung bình của 1 cuốn sách
     */
    public static function getAverageRating($bookId) {
        $db = self::db();
        
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total
                FROM comments 
                WHERE book_id = ? AND rating IS NOT NULL";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookId]);
        
        return $stmt->fetch();
    }

    /**
     * Thêm bình luận mới
     */
    public static function create($bookId, $userId, $content, $rating) {
        $db = self::db();
        
        $sql = "INSERT INTO comments (book_id, user_id, content, rating, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($sql);
        
        return $stmt->execute([$bookId, $userId, $content, $rating]);
    }
}
