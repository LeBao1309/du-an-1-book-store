<?php

require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/QuestionModel.php';

class ProductController extends BaseController
{
    // URL: index.php?controller=product&action=detail&id=1
    public function detail(int $id): string
    {
        if ($id <= 0) {
            http_response_code(404);
            return $this->render('page/404');
        }

        // Lấy thông tin sách
        $book = BookModel::findById($id);
        if (!$book) {
            http_response_code(404);
            return $this->render('page/404');
        }

        // Lấy danh sách biến thể (format, giá, stock...)
        $variants = BookModel::getVariants($id);

        // Lấy danh sách ảnh
        $images = BookModel::getImages($id);

        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = BookModel::getRelatedProducts($id, $book['category_id'], 4);

        // Lấy bình luận và thống kê rating
        $comments = CommentModel::getByBookId($id);
        $commentStats = CommentModel::getAverageRating($id);
        
        // Kiểm tra user đã mua sản phẩm chưa
        $hasPurchased = false;
        if (!empty($_SESSION['user'])) {
            $hasPurchased = OrderModel::hasUserPurchasedBook($_SESSION['user']['id'], $id);
        }

        // Lấy câu hỏi và câu trả lời
        $questions = QuestionModel::getQuestionsByBookId($id, 10, 0);
        
        // Lấy câu trả lời cho mỗi câu hỏi
        foreach ($questions as &$question) {
            $question['answers'] = QuestionModel::getAnswersByQuestionId($question['id']);
            
            // Lấy vote status của user hiện tại
            if (!empty($_SESSION['user'])) {
                foreach ($question['answers'] as &$answer) {
                    $userVote = QuestionModel::getUserVote($answer['id'], $_SESSION['user']['id']);
                    $answer['user_vote'] = $userVote ? $userVote['vote_type'] : null;
                }
            }
        }
        
        $totalQuestions = QuestionModel::countQuestionsByBookId($id);

        return $this->render('page/product_detail', [
            'book'            => $book,
            'variants'        => $variants,
            'images'          => $images,
            'relatedProducts' => $relatedProducts,
            'comments'        => $comments,
            'commentStats'    => $commentStats,
            'hasPurchased'    => $hasPurchased,
            'questions'       => $questions,
            'totalQuestions'  => $totalQuestions,
        ]);
    }

    /**
     * Xử lý thêm bình luận
     * URL: index.php?controller=product&action=addComment
     */
    public function addComment()
    {
        // Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để bình luận!';
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Lấy dữ liệu từ form
        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $userId = $_SESSION['user']['id'];

        // Kiểm tra dữ liệu
        if ($bookId <= 0 || empty($content) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header("Location: index.php?controller=product&action=detail&id={$bookId}");
            exit;
        }
        
        // Kiểm tra user đã mua sản phẩm này chưa
        if (!OrderModel::hasUserPurchasedBook($userId, $bookId)) {
            $_SESSION['error'] = 'Bạn cần mua sản phẩm này trước khi đánh giá!';
            header("Location: index.php?controller=product&action=detail&id={$bookId}");
            exit;
        }

        // Thêm bình luận vào database
        $result = CommentModel::create($bookId, $userId, $content, $rating);

        // Thông báo và quay lại trang chi tiết
        if ($result) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header("Location: index.php?controller=product&action=detail&id={$bookId}");
        exit;
    }

    /**
     * Xử lý đặt câu hỏi
     * URL: index.php?controller=product&action=askQuestion
     */
    public function askQuestion()
    {
        // Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt câu hỏi!']);
            exit;
        }

        // Lấy dữ liệu từ POST
        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
        $question = isset($_POST['question']) ? trim($_POST['question']) : '';
        $userId = $_SESSION['user']['id'];

        // Kiểm tra dữ liệu
        if ($bookId <= 0 || empty($question)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập câu hỏi!']);
            exit;
        }

        // Tạo câu hỏi
        $result = QuestionModel::createQuestion($bookId, $userId, $question);

        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Câu hỏi của bạn đã được gửi!',
                'user_name' => $_SESSION['user']['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!']);
        }
        exit;
    }

    /**
     * Xử lý trả lời câu hỏi
     * URL: index.php?controller=product&action=answerQuestion
     */
    public function answerQuestion()
    {
        // Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để trả lời!']);
            exit;
        }

        // Lấy dữ liệu từ POST
        $questionId = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
        $answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
        $userId = $_SESSION['user']['id'];
        $isShopAnswer = ($_SESSION['user']['role'] === 'admin') ? 1 : 0;

        // Kiểm tra dữ liệu
        if ($questionId <= 0 || empty($answer)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập câu trả lời!']);
            exit;
        }

        // Tạo câu trả lời
        $result = QuestionModel::createAnswer($questionId, $userId, $answer, $isShopAnswer);

        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Câu trả lời của bạn đã được gửi!',
                'user_name' => $_SESSION['user']['name'],
                'user_role' => $_SESSION['user']['role'],
                'is_shop_answer' => $isShopAnswer
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!']);
        }
        exit;
    }

    /**
     * Xử lý vote câu trả lời
     * URL: index.php?controller=product&action=voteAnswer
     */
    public function voteAnswer()
    {
        // Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để vote!']);
            exit;
        }

        // Lấy dữ liệu từ POST
        $answerId = isset($_POST['answer_id']) ? (int)$_POST['answer_id'] : 0;
        $voteType = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';
        $userId = $_SESSION['user']['id'];

        // Kiểm tra dữ liệu
        if ($answerId <= 0 || !in_array($voteType, ['upvote', 'downvote'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
            exit;
        }

        // Vote
        $result = QuestionModel::voteAnswer($answerId, $userId, $voteType);

        if ($result) {
            // Lấy số vote mới
            $voteCounts = QuestionModel::getAnswerVoteCounts($answerId);
            echo json_encode([
                'success' => true,
                'action' => $result['action'],
                'vote_type' => $result['vote_type'],
                'upvotes' => $voteCounts['upvotes'],
                'downvotes' => $voteCounts['downvotes']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
        }
        exit;
    }
}
