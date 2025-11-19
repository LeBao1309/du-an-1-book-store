<?php

require_once __DIR__ . '/../models/BookModel.php';

class BookController extends BaseController
{
    /**
     * Trang chi tiết sách
     * URL: index.php?controller=book&action=detail&id=1
     */
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

        // Lấy các biến thể (bìa mềm/bìa cứng/ebook...)
        $variants = Book::getVariants($id);

        // Lấy các ảnh của sách
        $images = Book::getImages($id);

        // Tính giá hiển thị (min(sale_price, price))
        $displayPrice = null;
        foreach ($variants as $v) {
            $p = $v['sale_price'] ?? $v['price'] ?? 0;
            $p = (float)$p;
            if ($p > 0 && ($displayPrice === null || $p < $displayPrice)) {
                $displayPrice = $p;
            }
        }

        return $this->render('page/book_detail', [
            'book'         => $book,
            'variants'     => $variants,
            'images'       => $images,
            'displayPrice' => $displayPrice,
        ]);
    }
}
