<?php

require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

final class HomeController extends BaseController
{
    public function index(): string
    {
      // Lấy 8 sản phẩm mới nhất
      $newBooks = BookModel::getNewestProducts(8);
      
      // Lấy 8 sản phẩm bán chạy
      try {
          $bestSellers = BookModel::getBestSellers(8);
      } catch (Exception $e) {
          $bestSellers = [];
      }
      
      // Lấy 8 sản phẩm giảm giá
      try {
          $discountedBooks = BookModel::getDiscountedBooks(8);
      } catch (Exception $e) {
          $discountedBooks = [];
      }
      
      // Lấy danh mục cha
      try {
          $categories = CategoryModel::getParentCategoriesWithCount();
      } catch (Exception $e) {
          $categories = [];
      }
      
      return $this->render('page/index', [
          'newBooks' => $newBooks,
          'bestSellers' => $bestSellers,
          'discountedBooks' => $discountedBooks,
          'categories' => $categories
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
        $results = BookModel::searchByName($q, 5);
        
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
