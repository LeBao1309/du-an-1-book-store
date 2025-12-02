<?php
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/AuthorModel.php';    
require_once __DIR__ . '/../models/PublisherModel.php'; 

class CategoryController extends BaseController 
{
    public function index(): string
    {
        // 1. Nhận dữ liệu từ URL
        $categoryId  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $searchQuery = trim($_GET['q'] ?? '');
        $sort        = $_GET['sort'] ?? 'newest';
        $page        = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Nhận lọc giá
        $minPrice    = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
        $maxPrice    = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

        if ($page < 1) $page = 1;

        $limit = 9; 
        $offset = ($page - 1) * $limit;

        // 2. Gom vào mảng params
        $filterParams = [
            'category_id'  => $categoryId,
            'keyword'      => $searchQuery,
            'sort'         => $sort,
            'min_price'    => $minPrice,
            'max_price'    => $maxPrice
        ];

        // 3. Gọi Model
        $books      = Book::filter($filterParams, $limit, $offset);
        $totalBooks = Book::countFilter($filterParams);
        $totalPages = ceil($totalBooks / $limit);
        if ($totalPages == 0) $totalPages = 1;

        // 4. Lấy dữ liệu Sidebar (Chỉ cần Category)
        // Lưu ý: Dùng getTree() để hiển thị sidebar cây
        $allCategories = Category::getAll(); // Vẫn lấy all để map tên nếu cần
        
        // Tên tiêu đề trang
        $currentCategory = $categoryId > 0 ? Category::find($categoryId) : ['name' => 'Tất cả sản phẩm', 'id'=>0];
        if($searchQuery) $currentCategory['name'] = "Tìm kiếm: " . htmlspecialchars($searchQuery);

        // 5. Truyền ra View
        return $this->render('page/category', [
            'category'      => $currentCategory,
            'books'         => $books,
            'allCategories' => $allCategories,
            'currentParams' => $filterParams, 
            'page'          => $page,
            'totalPages'    => $totalPages,
            'totalBooks'    => $totalBooks
        ]);
    }
}