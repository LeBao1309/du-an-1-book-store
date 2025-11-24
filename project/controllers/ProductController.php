<?php

require_once __DIR__ . '/../models/BookModel.php';

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
        $book = Book::findById($id);
        if (!$book) {
            http_response_code(404);
            return $this->render('page/404');
        }

        // Lấy danh sách biến thể (format, giá, stock...)
        $variants = Book::getVariants($id);

        // Lấy danh sách ảnh
        $images = Book::getImages($id);

        // === MỚI: Lấy sản phẩm liên quan (cùng danh mục) ===
        $relatedProducts = Book::getRelatedProducts($id, $book['category_id'], 4);

        return $this->render('page/product_detail', [
            'book'            => $book,
            'variants'        => $variants,
            'images'          => $images,
            'relatedProducts' => $relatedProducts, // Thêm biến mới
        ]);
    }
}