<?php

// NẠP BOOKMODEL
require_once __DIR__ . '/../models/BookModel.php';

final class HomeController extends BaseController
{
    public function index(): string
    {
      // Lấy 8 sản phẩm mới nhất
      $newBooks = Book::getNewestProducts(8);
      
      // Chỉ truyền $newBooks ra view
      return $this->render('page/index', [
          'newBooks' => $newBooks
      ]);
    }

    public function profile(): string
    {
        $this->requireAuth();
        $user = $_SESSION['user'];
        return $this->render('page/profile', compact('user'));
    }
}