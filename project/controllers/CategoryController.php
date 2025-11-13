<?php
declare(strict_types=1);

// Sửa 1: Đảm bảo nó kế thừa BaseController
class CategoryController extends BaseController 
{
    public function index()
    {
        // 1. Lấy ID, nếu không có thì là 0
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $category = null;
        $books = [];

        // Sửa 2: Thêm IF/ELSE
        if ($categoryId > 0) {
            // Logic cũ: Lấy 1 danh mục
            $category = Category::find($categoryId);
            $books = Book::getByCategory($categoryId);
        } else {
            // Logic MỚI: Khi không có ID (Tất cả sản phẩm)
            // Tạo một mảng "giả" cho tên danh mục
            $category = ['name' => 'Tất cả sản phẩm']; 
            // Gọi hàm mới để lấy tất cả sách
            $books = Book::getAll(); 
        }

        // 3. Render
        return $this->render('category', [
            'category' => $category,
            'books' => $books
        ]);
    }
}