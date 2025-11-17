<?php

require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BookModel.php';
// TODO: Nạp thêm các Model cho Filter (NXB, Loại sách)
// require_once __DIR__ . '/../models/PublisherModel.php';
// require_once __DIR__ . '/../models/GenreModel.php';

class CategoryController extends BaseController 
{
    public function index(int $categoryId = 0): string
    {
        // ... (Code logic phân trang của bạn) ...
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 9; 
        $offset = ($page - 1) * $limit;

        // === SỬA Ở ĐÂY ===
        // Khai báo $totalBooks ở ngoài để có thể dùng chung
        $totalBooks = 0; 
        
        if ($categoryId > 0) {
            $category = Category::find($categoryId);
            if (!$category) { 
                http_response_code(404);
                return $this->render('page/404'); 
            }
            $totalBooks = Book::countByCategory($categoryId); // Gán giá trị
            $books = Book::getByCategory($categoryId, $limit, $offset);
        } else {
            $category = ['name' => 'Tất cả sản phẩm', 'id' => 0]; 
            $totalBooks = Book::countAll(); // Gán giá trị
            $books = Book::getAll($limit, $offset); 
        }

        $totalPages = ceil($totalBooks / $limit);
        if ($totalPages == 0) $totalPages = 1;
        // ... (code kiểm tra $page > $totalPages) ...

        $allCategories = Category::getAll(); 
        $publishers = []; 

        return $this->render('page/category', [
            'category' => $category,
            'books' => $books,
            'allCategories' => $allCategories,
            'publishers' => $publishers,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalBooks' => $totalBooks // <-- TRUYỀN BIẾN NÀY RA VIEW
        ]);
    }
}