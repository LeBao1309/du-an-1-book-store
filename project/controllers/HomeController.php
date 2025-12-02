<?php

require_once __DIR__ . '/../models/BookModel.php';

final class HomeController extends BaseController
{
    public function index(): string
    {
      // Lấy 8 sản phẩm mới nhất
      $newBooks = Book::getNewestProducts(8);
      
      return $this->render('page/index', [
          'newBooks' => $newBooks
      ]);
    }

    // API trả về JSON cho thanh tìm kiếm
    public function ajaxSearch(): void
    {
        $q = trim($_GET['q'] ?? '');
        
        // Nếu từ khóa rỗng, trả mảng rỗng
        if ($q === '') {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        // Tìm kiếm (limit 5 kết quả)
        $results = Book::searchByName($q, 5);
        
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    public function profile(): string
    {
        $this->requireAuth();
        $user = $_SESSION['user'];
        return $this->render('page/profile', compact('user'));
    }
}