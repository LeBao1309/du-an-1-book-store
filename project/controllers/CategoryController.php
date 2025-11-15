<?php

require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BookModel.php';

class CategoryController extends BaseController 
{
    public function index(int $categoryId = 0): string
    {
        if ($categoryId > 0) {
            $category = Category::find($categoryId);
            $books    = Book::getByCategory($categoryId);
        } else {
            $category = ['name' => 'Tất cả sản phẩm'];
            $books    = Book::getAll();
        }

        return $this->render('page/category', [
            'category' => $category,
            'books'    => $books,
        ]);
    }
}