<?php

require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/CommentModel.php';

class ProductController extends BaseController
{
    // URL: index.php?controller=product&action=detail&id=1
    public function detail(int $id): string
    {
        // Kiểm tra ID có hợp lệ không
        if ($id <= 0) {
            http_response_code(404);
            return $this->render('page/404');
        }

        // Lấy thông tin sách từ database
        $book = Book::findById($id);
        if (!$book) {
            http_response_code(404);
            return $this->render('page/404');
        }

        // Lấy các biến thể (bìa cứng, bìa mềm, ebook...)
        $variants = Book::getVariants($id);

        // Lấy danh sách ảnh
        $images = Book::getImages($id);

        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Book::getRelatedProducts($id, $book['category_id'], 4);

        // Lấy bình luận và thống kê rating
        $comments = Comment::getByBookId($id);
        $commentStats = Comment::getAverageRating($id);

        // Trả về view với dữ liệu
        return $this->render('page/product_detail', [
            'book'            => $book,
            'variants'        => $variants,
            'images'          => $images,
            'relatedProducts' => $relatedProducts,
            'comments'        => $comments,
            'commentStats'    => $commentStats,
        ]);
    }

    /**
     * Xử lý thêm bình luận (ĐƠN GIẢN)
     * URL: index.php?controller=product&action=addComment
     */
    public function addComment()
    {
        // 1. Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // 2. Lấy dữ liệu từ form
        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $userId = $_SESSION['user']['id'];

        // 3. Kiểm tra dữ liệu
        if ($bookId <= 0 || empty($content) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header("Location: index.php?controller=product&action=detail&id={$bookId}");
            exit;
        }

        // 4. Thêm bình luận vào database
        $result = Comment::create($bookId, $userId, $content, $rating);

        // 5. Thông báo và quay lại trang chi tiết
        if ($result) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header("Location: index.php?controller=product&action=detail&id={$bookId}");
        exit;
    }
}
