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
        $searchQuery = trim($_GET['q'] ?? '');

        // === THÊM MỚI (1): ĐỌC VÀ LỌC GIÁ TRỊ SẮP XẾP ===
        $sort = $_GET['sort'] ?? 'newest';
        $validSorts = ['newest', 'price-asc', 'price-desc'];
        if (!in_array($sort, $validSorts)) {
            $sort = 'newest';
        }
        // === HẾT THÊM MỚI (1) ===

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 9; 
        $offset = ($page - 1) * $limit;

        $totalBooks = 0; 
        
        if ($searchQuery !== '') {
            $category = [
                'name' => 'Kết quả tìm kiếm cho "' . htmlspecialchars($searchQuery) . '"', 
                'id' => 0 
            ];
            $totalBooks = Book::countByTitle($searchQuery);
            // === SỬA (2): TRUYỀN $sort VÀO HÀM ===
            $books = Book::searchByTitle($searchQuery, $limit, $offset, $sort); 

        } elseif ($categoryId > 0) {
            $category = Category::find($categoryId);
            if (!$category) { 
                http_response_code(404);
                return $this->render('page/404'); 
            }
            $totalBooks = Book::countByCategory($categoryId); 
            // === SỬA (3): TRUYỀN $sort VÀO HÀM ===
            $books = Book::getByCategory($categoryId, $limit, $offset, $sort);

        } else {
            $category = ['name' => 'Tất cả sản phẩm', 'id' => 0]; 
            $totalBooks = Book::countAll(); 
            // === SỬA (4): TRUYỀN $sort VÀO HÀM ===
            $books = Book::getAll($limit, $offset, $sort); 
        }

        $totalPages = ceil($totalBooks / $limit);
        if ($totalPages == 0) $totalPages = 1;

        $allCategories = Category::getAll(); 
        $publishers = []; 

        return $this->render('page/category', [
            'category' => $category,
            'books' => $books,
            'allCategories' => $allCategories,
            'publishers' => $publishers,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalBooks' => $totalBooks,
            'searchQuery' => $searchQuery,
            'sort' => $sort // <-- THÊM MỚI (5): TRUYỀN $sort RA VIEW
        ]);
    }
}