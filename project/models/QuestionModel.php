<?php
require_once 'BaseModel.php';

class QuestionModel extends BaseModel
{
    /**
     * Lấy danh sách câu hỏi của sản phẩm
     */
    public static function getQuestionsByBookId($bookId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                    q.*,
                    u.name as user_name,
                    u.email as user_email,
                    (SELECT COUNT(*) FROM answers WHERE question_id = q.id) as answer_count
                FROM questions q
                LEFT JOIN users u ON q.user_id = u.id
                WHERE q.book_id = ?
                ORDER BY q.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số câu hỏi của sản phẩm
     */
    public static function countQuestionsByBookId($bookId)
    {
        $sql = "SELECT COUNT(*) as total FROM questions WHERE book_id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$bookId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Lấy chi tiết câu hỏi
     */
    public static function getQuestionById($id)
    {
        $sql = "SELECT 
                    q.*,
                    u.name as user_name,
                    u.email as user_email
                FROM questions q
                LEFT JOIN users u ON q.user_id = u.id
                WHERE q.id = ?";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Tạo câu hỏi mới
     */
    public static function createQuestion($bookId, $userId, $question)
    {
        $sql = "INSERT INTO questions (book_id, user_id, question) VALUES (?, ?, ?)";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$bookId, $userId, $question]);
    }

    /**
     * Lấy câu trả lời của một câu hỏi
     */
    public static function getAnswersByQuestionId($questionId)
    {
        $sql = "SELECT 
                    a.*,
                    u.name as user_name,
                    u.email as user_email,
                    u.role as user_role
                FROM answers a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.question_id = ?
                ORDER BY a.is_shop_answer DESC, a.created_at ASC";
        
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$questionId]);
        return $stmt->fetchAll();
    }

    /**
     * Tạo câu trả lời
     */
    public static function createAnswer($questionId, $userId, $answer, $isShopAnswer = false)
    {
        $sql = "INSERT INTO answers (question_id, user_id, answer, is_shop_answer) 
                VALUES (?, ?, ?, ?)";
        $stmt = self::db()->prepare($sql);
        $result = $stmt->execute([$questionId, $userId, $answer, $isShopAnswer ? 1 : 0]);
        
        // Cập nhật trạng thái đã trả lời
        if ($result) {
            $updateSql = "UPDATE questions SET is_answered = 1 WHERE id = ?";
            $updateStmt = self::db()->prepare($updateSql);
            $updateStmt->execute([$questionId]);
        }
        
        return $result;
    }

    /**
     * Kiểm tra user đã vote cho câu trả lời chưa
     */
    public static function getUserVote($answerId, $userId)
    {
        $sql = "SELECT vote_type FROM answer_votes WHERE answer_id = ? AND user_id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$answerId, $userId]);
        return $stmt->fetch();
    }

    /**
     * Vote cho câu trả lời
     */
    public static function voteAnswer($answerId, $userId, $voteType)
    {
        // Chuẩn hóa giá trị vote theo enum 'up' | 'down'
        if ($voteType === 'upvote') $voteType = 'up';
        if ($voteType === 'downvote') $voteType = 'down';

        // Kiểm tra đã vote chưa
        $existingVote = self::getUserVote($answerId, $userId);
        
        if ($existingVote) {
            // Nếu vote giống nhau thì xóa vote (unlike)
            if ($existingVote['vote_type'] === $voteType) {
                $sql = "DELETE FROM answer_votes WHERE answer_id = ? AND user_id = ?";
                $stmt = self::db()->prepare($sql);
                $result = $stmt->execute([$answerId, $userId]);
                
                return ['action' => 'removed', 'vote_type' => $voteType];
            } else {
                // Nếu vote khác thì đổi vote
                $sql = "UPDATE answer_votes SET vote_type = ? WHERE answer_id = ? AND user_id = ?";
                $stmt = self::db()->prepare($sql);
                $result = $stmt->execute([$voteType, $answerId, $userId]);
                
                return ['action' => 'changed', 'vote_type' => $voteType];
            }
        } else {
            // Tạo vote mới
            $sql = "INSERT INTO answer_votes (answer_id, user_id, vote_type) VALUES (?, ?, ?)";
            $stmt = self::db()->prepare($sql);
            $result = $stmt->execute([$answerId, $userId, $voteType]);
            
            return ['action' => 'added', 'vote_type' => $voteType];
        }
    }

    /**
     * Lấy vote counts của câu trả lời
     */
    public static function getAnswerVoteCounts($answerId)
    {
        $sqlUp   = "SELECT COUNT(*) FROM answer_votes WHERE answer_id = ? AND vote_type = 'up'";
        $sqlDown = "SELECT COUNT(*) FROM answer_votes WHERE answer_id = ? AND vote_type = 'down'";
        $stmtUp = self::db()->prepare($sqlUp);
        $stmtDown = self::db()->prepare($sqlDown);
        $stmtUp->execute([$answerId]);
        $stmtDown->execute([$answerId]);
        return [
            'upvotes' => (int)$stmtUp->fetchColumn(),
            'downvotes' => (int)$stmtDown->fetchColumn(),
        ];
    }
}
