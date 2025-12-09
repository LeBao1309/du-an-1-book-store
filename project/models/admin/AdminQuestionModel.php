<?php

require_once __DIR__ . '/../BaseModel.php';

/**
 * AdminQuestionModel - quản lý Q&A sản phẩm trong admin.
 */
final class AdminQuestionModel extends BaseModel
{
    public static function paginate(array $filters, int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($filters['keyword'])) {
            $where[] = "(q.question LIKE :kw OR u.name LIKE :kw OR b.title LIKE :kw)";
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }
        if (isset($filters['answered']) && $filters['answered'] !== '') {
            $where[] = "q.is_answered = :ans";
            $params[':ans'] = (int)$filters['answered'];
        }
        if (!empty($filters['book_id'])) {
            $where[] = "q.book_id = :bid";
            $params[':bid'] = (int)$filters['book_id'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $base = "
            FROM product_questions q
            JOIN users u ON q.user_id = u.id
            JOIN books b ON q.book_id = b.id
            $whereSql
        ";

        $pdo = self::db();

        $countStmt = $pdo->prepare("SELECT COUNT(*) " . $base);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $lastPage = (int)ceil($total / $perPage);

        $sql = "
            SELECT q.*, u.name AS user_name, b.title AS book_title,
                   (SELECT COUNT(*) FROM question_answers qa WHERE qa.question_id = q.id) AS answer_count
            $base
            ORDER BY q.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [],
            'total' => $total,
            'page' => $page,
            'last_page' => max(1, $lastPage),
        ];
    }

    public static function getQuestion(int $id): ?array
    {
        $sql = "
            SELECT q.*, u.name AS user_name, b.title AS book_title
            FROM product_questions q
            JOIN users u ON q.user_id = u.id
            JOIN books b ON q.book_id = b.id
            WHERE q.id = :id
        ";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function getAnswers(int $questionId): array
    {
        $sql = "
            SELECT qa.*, u.name AS user_name, u.role
            FROM question_answers qa
            JOIN users u ON qa.user_id = u.id
            WHERE qa.question_id = :qid
            ORDER BY qa.created_at ASC
        ";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':qid' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function addAnswer(int $questionId, int $userId, string $content, bool $isShop = true): bool
    {
        $pdo = self::db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO question_answers (question_id, user_id, answer, is_shop_answer, created_at)
                VALUES (:qid, :uid, :ans, :shop, NOW())
            ");
            $stmt->execute([
                ':qid' => $questionId,
                ':uid' => $userId,
                ':ans' => $content,
                ':shop'=> $isShop ? 1 : 0,
            ]);

            $pdo->prepare("
                UPDATE product_questions
                SET is_answered = 1, updated_at = NOW()
                WHERE id = :qid
            ")->execute([':qid' => $questionId]);

            $pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $pdo->rollBack();
            error_log('addAnswer error: ' . $e->getMessage());
            return false;
        }
    }

    public static function deleteQuestion(int $id): bool
    {
        $stmt = self::db()->prepare("DELETE FROM product_questions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function deleteAnswer(int $id): bool
    {
        $pdo = self::db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("SELECT question_id FROM question_answers WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $qid = (int)$stmt->fetchColumn();
            if (!$qid) {
                $pdo->rollBack();
                return false;
            }

            $pdo->prepare("DELETE FROM question_answers WHERE id = :id")
                ->execute([':id' => $id]);

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM question_answers WHERE question_id = :qid");
            $stmt->execute([':qid' => $qid]);
            $remain = (int)$stmt->fetchColumn();

            $pdo->prepare("
                UPDATE product_questions
                SET is_answered = :flag, updated_at = NOW()
                WHERE id = :qid
            ")->execute([':flag' => $remain > 0 ? 1 : 0, ':qid' => $qid]);

            $pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $pdo->rollBack();
            error_log('deleteAnswer error: ' . $e->getMessage());
            return false;
        }
    }
}
