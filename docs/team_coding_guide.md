# Hướng dẫn Coding Book Store

Tài liệu này giúp mọi thành viên bám sát kiến trúc, quy ước và quy trình đang triển khai trong dự án `du-an-1-book-store`.

## 1. Overview nhanh
- Tech stack: PHP 7.4+ thuần OOP, MVC đơn giản, MySQL, front-end HTML/CSS/JS.
- Entry point: `project/public/index.php` khởi động session, load config, autoloader rồi chạy Router.
- Mọi module backend gồm 3 mảnh: Controller (logic), Model (DB), View (UI) theo cấu trúc `app/`.

## 2. Cấu trúc thư mục quan trọng
```
project/
├── config/        # Hằng số (DB, BASE_URL, APP_NAME)
├── public/        # index.php, assets public
├── app/
│   ├── core/      # Router, Autoloader, Database, BaseModel, BaseController
│   ├── controllers
│   ├── models
│   └── views
└── docs/          # Tài liệu nội bộ
```
Nguyên tắc: mỗi module mới thêm file ở controller/model/view tương ứng; core chỉ chỉnh khi thật cần.

## 3. Quy trình làm việc (Git Flow)
1. `git pull origin develop` trước khi bắt đầu.
2. Tạo nhánh `feature/<ten-module>` và code trên nhánh đó. ( git checkout - b feature/<ten-module> )
3. Giữ commit message chuẩn: `feat: ...`, `fix: ...`, `docs: ...`.
4. Tự test (chạy trên XAMPP, kiểm tra form/route) → push → tạo PR để leader review.
5. Không chỉnh file người khác đang phụ trách trừ khi đã trao đổi.

## 4. Config 
- Thay đổi cấu hình tại `config/database.php` và `config/app.php`. Không hard-code URL/DSN trong code.
- Mọi file PHP đều dựa vào Autoloader 👉 chỉ cần khai báo class đúng tên file PascalCase.
- Route được khai báo ở `public/index.php` bằng `$router->get()` hoặc `$router->post()`.

## 5. Controller guideline
- Luôn `extends BaseController` để có helper: `view()`, `redirect()`, `setFlash()`, `currentUser()`, `csrfToken()`, ...
- Với trang private: gọi `$this->requireLogin()` hoặc `$this->requireRole([...])` ngay đầu action.
- Khi nhận form POST: gọi `$this->verifyCsrf()` trước khi đọc dữ liệu.
- Lấy input bằng `$this->param('field')` hoặc `$this->only(['field1','field2'])` để được sanitize tự động.
- Trả dữ liệu cho view dùng `$this->view('folder/file', ['key' => $value])`.
- Ví dụ skeleton:
```php
class BookController extends BaseController {
    private Book $bookModel;

    public function __construct() {
        $this->bookModel = new Book();
    }

    public function store() {
        $this->verifyCsrf();
        $this->requireRole('admin');
        $data = $this->only(['title','category_id','price']);
        $this->bookModel->insert($data);
        $this->setFlash('success', 'Thêm sách thành công');
        $this->redirect('/admin/books');
    }
}
```

## 6. Model guideline
- `extends BaseModel`, bắt buộc khai báo `$table` và `$primaryKey` khớp tên bảng.
- Tận dụng sẵn `find`, `findOne`, `insert`, `update`, `delete`, `count`, `paginate`.
- Thêm phương thức nghiệp vụ nếu cần (ví dụ `findByEmail`, `featured()`...).
- Không thực thi truy vấn thô ở Controller; Controller chỉ gọi hàm của Model.

## 7. View guideline
- View ở `app/views/<module>/<file>.php`, file viết chữ thường hoặc snake_case.
- Nhận biến từ Controller qua `extract`. Kiểm tra `isset()` trước khi dùng và escape bằng `htmlspecialchars()` khi in dữ liệu động.
- Tất cả link tài nguyên dùng `BASE_URL` (`<?php echo BASE_URL; ?>/css/styles.css`).
- UI nên tái sử dụng layout, class CSS từ `public/css/styles.css`. Tránh inline style dài trừ khi tạm thời.

## 8. Bảo mật & Session
- Session đã `session_start()` tại index.php → chỉ đọc/ghi trong Controller.
- Đăng nhập: lưu `user_id`, `name`, `role` vào `$_SESSION`. Đăng xuất dùng `session_destroy()`.
- CSRF: mọi form POST chèn `<input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrfToken); ?>">`.
- Password: luôn băm với `password_hash` và kiểm với `password_verify` (xem `User` model làm chuẩn).

## 9. Checklist trước khi push
- [ ] Đã tạo route và test thủ công (GET/POST hoạt động, không 404).
- [ ] Controller dùng helper thích hợp (login guard, flash message, redirect).
- [ ] Model không còn query trùng lặp hoặc SQL lộ ra controller.
- [ ] View render đúng dữ liệu, dùng `BASE_URL`, có xử lý trạng thái đăng nhập/flash nếu cần.
- [ ] Đã cập nhật docs nếu thêm module hoặc API mới.
- [ ] `composer`/dependency (nếu dùng) không thay đổi ngoài ý muốn.

Giữ nguyên các convention này giúp codebase thống nhất, dễ review và mở rộng khi thêm module mới.
