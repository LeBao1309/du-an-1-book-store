# 📚 WiseDecision Bookstore - Chi tiết Logic & Luồng Hoạt động

**Dự án:** Du án 1 - Hệ thống quản lý và bán sách trực tuyến  
**Công nghệ:** PHP, MySQL, PDO, Bootstrap 5  
**Kiến trúc:** MVC (Model-View-Controller)

---

## 📋 Mục lục
1. [Tổng quan kiến trúc](#tổng-quan-kiến-trúc)
2. [Hệ thống xác thực & tài khoản](#hệ-thống-xác-thực--tài-khoản)
3. [Quản lý danh mục & sản phẩm](#quản-lý-danh-mục--sản-phẩm)
4. [Giỏ hàng & thanh toán](#giỏ-hàng--thanh-toán)
5. [Quản trị viên](#quản-trị-viên)
6. [Tính năng bổ sung](#tính-năng-bổ-sung)

---

## 🏗️ Tổng quan kiến trúc

### Entry Points
```
Frontend: project/index.php
Admin:    project/wd-admin/index.php
```

### Routing Pattern
**Frontend (index.php):**
- URL: `?controller=HOME&action=ACTION&id=ID&slug=SLUG`
- Controllers: HomeController, CategoryController, AuthController, CartController, BookController, PaymentController, AccountController, etc.

**Admin (wd-admin/index.php):**
- URL: `?c=CONTROLLER&a=ACTION`
- Controllers: DashboardAdminController, BookAdminController, OrderAdminController, CouponAdminController, etc.

### Database Connection
- **Singleton Pattern**: BaseModel cung cấp kết nối PDO duy nhất
- **Config**: Đọc từ environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASS) hoặc mặc định
- **Charset**: UTF-8MB4 (hỗ trợ emoji, ký tự đặc biệt)

### Project Structure
```
project/
├── controllers/          # Xử lý logic HTTP
│   ├── BaseController.php
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── CartController.php
│   ├── PaymentController.php
│   ├── AccountController.php
│   └── admin/           # Admin controllers
├── models/             # Tương tác database
│   ├── BaseModel.php
│   ├── UserModel.php
│   ├── BookModel.php
│   ├── OrderModel.php
│   └── admin/          # Admin models
├── views/              # Giao diện HTML
│   ├── layouts/main.php
│   ├── admin/__layout_admin.php
│   └── ...
├── services/           # EmailService
├── config/             # email.php
├── database/           # Scripts SQL
├── public/             # CSS, JS, Images
└── wd-admin/index.php  # Admin entry point
```

---

## 🔐 Hệ thống xác thực & tài khoản

### 1. Đăng Nhập (User)

**URL:** `?controller=auth&action=login` (GET/POST)

**Luồng:**
```
1. GET: Hiển thị form đăng nhập
   - Hiển thị lỗi (nếu có từ session trước)
   - Tạo CSRF token và lưu vào $_SESSION['_csrf']

2. POST: Xử lý đăng nhập
   a) Validate CSRF token
      - Kiểm tra $_POST['_csrf'] === $_SESSION['_csrf']
      - Nếu lỗi → die("CSRF validation failed")
   
   b) Validate dữ liệu
      - Email: trim, không rỗng
      - Password: không rỗng
   
   c) Query DB
      - UserModel::findByEmail($email)
      - Kiểm tra: is_active = 1
      - password_verify($password, hashed_password)
   
   d) Kết quả
      - ✅ Thành công:
        * Lưu $_SESSION['user'] = ['id','email','name','role']
        * Flash success message
        * Redirect → trang chủ
      
      - ❌ Thất bại:
        * Error: "Email/Mật khẩu không đúng hoặc tài khoản bị khóa"
        * Hiển thị lại form
```

**Database Query:**
```sql
SELECT * FROM users 
WHERE email = :email 
  AND is_active = 1 
  AND is_deleted = 0
```

### 2. Đăng Ký (User)

**URL:** `?controller=auth&action=register` (GET/POST)

**Luồng:**
```
1. GET: Hiển thị form đăng ký

2. POST: Xử lý đăng ký
   a) Validate CSRF
   
   b) Validate input
      - name: không rỗng (string)
      - email: phải hợp lệ (filter_var(..., FILTER_VALIDATE_EMAIL))
      - password: tối thiểu 6 ký tự
      - password2 (confirm): phải khớp với password
   
   c) Check tồn tại
      - UserModel::findByEmail($email)
      - Nếu tồn tại → Error: "Email đã tồn tại"
   
   d) Tạo user mới
      - Hash password: password_hash($password, PASSWORD_BCRYPT)
      - Insert vào DB: 
        INSERT INTO users (name, email, password, role, is_active)
        VALUES (:name, :email, :hashed_password, 'user', 1)
   
   e) Tự động đăng nhập
      - $_SESSION['user'] = ['id','email','name','role'=>'user']
      - Redirect → trang chủ
```

### 3. Đăng Xuất

**URL:** `?controller=auth&action=logout`

**Luồng:**
```
1. Xóa session user
   unset($_SESSION['user'])
   unset($_SESSION['cart'])
   unset($_SESSION['checkout_*'])
   
2. Flash success message
3. Redirect → trang chủ
```

### 4. Quên Mật Khẩu (User)

**URL:** `?controller=auth&action=forgot` (GET/POST)

**Luồng:**
```
1. GET: Hiển thị form nhập email

2. POST: Xử lý yêu cầu reset
   a) Validate CSRF
   
   b) Validate email
      - filter_var(..., FILTER_VALIDATE_EMAIL)
   
   c) Query user
      - UserModel::findByEmail($email)
      - Kiểm tra: is_active = 1
   
   d) Tạo reset token
      - token = bin2hex(random_bytes(32)) → 64 ký tự hex
      - expires_at = NOW() + 60 phút
      - Insert vào password_resets table
      
      SQL:
      INSERT INTO password_resets (user_id, token, expires_at)
      VALUES (:user_id, :token, :expires_at)
   
   e) Gửi email
      - EmailService::sendResetTemplate()
      - Reset link: 
        ?controller=auth&action=reset&token=ABC123...
      - Thời hạn: 60 phút
   
   f) Response
      - Luôn hiển thị: "Nếu email tồn tại, chúng tôi đã gửi..."
      - (Ẩn thông tin tồn tại tài khoản để bảo mật)
```

### 5. Đặt Lại Mật Khẩu (User)

**URL:** `?controller=auth&action=reset?token=ABC...` (GET/POST)

**Luồng:**
```
1. GET: Hiển thị form đặt lại mật khẩu
   a) Lấy token từ URL
   b) Query: UserModel::findResetByToken($token)
      - Kiểm tra token tồn tại
      - Kiểm tra used_at IS NULL (chưa dùng)
      - Kiểm tra expires_at > NOW() (chưa hết hạn)
   c) Nếu lỗi → Error: "Liên kết không hợp lệ hoặc đã hết hạn"
   d) Nếu OK → Hiển thị form input mật khẩu mới

2. POST: Xử lý đặt lại
   a) Validate CSRF
   
   b) Validate password
      - Tối thiểu 6 ký tự
      - Nhập lại phải khớp
   
   c) Verify token
      - UserModel::findResetByToken($token)
      - Kiểm tra như trên
   
   d) Update mật khẩu
      - Hash password mới
      - UPDATE users SET password = :new_hash
        WHERE id = :user_id
      
      - Mark token đã dùng:
        UPDATE password_resets SET used_at = NOW()
        WHERE token = :token
   
   e) Result
      - ✅ Success: Flash "Đặt lại mật khẩu thành công"
      - Redirect → trang đăng nhập
```

### 6. Đăng Nhập Admin

**URL:** `wd-admin/index.php?c=auth&a=login` (GET/POST)

**Luồng:** Tương tự User nhưng:
- Kiểm tra `role = 'admin'` trong database
- Lưu `$_SESSION['admin']` thay vì `$_SESSION['user']`
- Chuyển hướng đến dashboard admin khi thành công
- Yêu cầu CSRF token cho tất cả POST requests

**Database Query:**
```sql
SELECT * FROM users 
WHERE email = :email 
  AND role = 'admin'
  AND is_active = 1 
  AND is_deleted = 0
```

---

## 📚 Quản lý danh mục & sản phẩm

### Cấu trúc Database Sách

**Bảng chính:**
```
books
├── id, title, slug, category_id
├── description, short_desc
├── rating_avg, review_count
├── is_active, is_deleted
└── created_at, updated_at

book_variants (1 sách = N biến thể)
├── book_id, format (Bìa mềm/cứng/Ebook)
├── price, sale_price
├── stock
└── created_at

book_authors (N-N relationship)
├── book_id, author_id
└── (một sách có nhiều tác giả, một tác giả viết nhiều sách)

book_publisher (1-N relationship)
├── book_id, publisher_id
└── (một sách có 1 nhà xuất bản)

book_images
├── book_id, image_url, sort_order
└── (ảnh chính: sort_order = 0)

categories (Danh mục cây)
├── id, name, slug, parent_id
├── is_active, is_deleted
└── (danh mục cha: parent_id = NULL)
```

### 1. Xem Danh Mục Sách (Trang Danh Sách)

**URL:** `?controller=category&action=index[&id=5&q=keyword&sort=newest&page=1&min_price=100&max_price=500]`

**Luồng:**
```
1. Parse URL parameters
   - category_id (int): Lọc theo danh mục
   - q (string): Từ khóa tìm kiếm
   - sort: newest | price-asc | price-desc
   - page (int): Trang hiện tại (mặc định 1)
   - min_price, max_price: Lọc theo giá

2. Query sách
   a) Chuẩn bị params filter
   
   b) BookModel::filter($params, limit=9, offset)
      SQL (simplified):
      SELECT b.id, b.title, b.slug,
             MIN(IFNULL(v.sale_price, v.price)) as display_price,
             MIN(i.image_url) as image_url,
             GROUP_CONCAT(a.name) as author_names
      FROM books b
      LEFT JOIN book_variants v ON v.book_id = b.id
      LEFT JOIN book_images i ON i.book_id = b.id
      LEFT JOIN book_authors ba ON b.id = ba.book_id
      LEFT JOIN authors a ON ba.author_id = a.id
      WHERE b.is_active = 1
        AND (category_id = ? OR parent_id = ?)  -- Lấy cả cha lẫn con
        AND b.title LIKE ?                      -- Tìm kiếm
      GROUP BY b.id
      HAVING display_price BETWEEN ? AND ?      -- Lọc giá
      ORDER BY display_price ASC|DESC
      LIMIT 9 OFFSET ?
   
   c) Count tổng: BookModel::countFilter($params)
      - Tính số trang: total_pages = ceil(total / 9)
   
   d) Lấy danh mục sidebar: CategoryModel::getAll()

3. Render view
   - Danh sách sách với ảnh, giá, tác giả
   - Sidebar filter (danh mục cây, khoảng giá)
   - Pagination
```

### 2. Xem Chi Tiết Sách

**URL:** `?controller=book&action=detail&id=123`

**Luồng:**
```
1. Validate ID
   - int $id > 0
   - Nếu lỗi → HTTP 404

2. Query sách
   a) BookModel::findById($id)
      - Lấy thông tin: title, description, rating_avg, review_count
      - Kiểm tra is_active = 1, is_deleted = 0
   
   b) Nếu không tìm thấy → HTTP 404

3. Lấy biến thể (variants)
   a) BookModel::getVariants($id)
      SQL:
      SELECT id, format, price, sale_price, stock
      FROM book_variants
      WHERE book_id = ? AND is_deleted = 0
      ORDER BY price ASC
   
   b) Tính giá hiển thị
      - display_price = MIN(sale_price hoặc price)
      - Dùng cho cả trang

4. Lấy ảnh
   a) BookModel::getImages($id)
      SQL:
      SELECT image_url, sort_order
      FROM book_images
      WHERE book_id = ? AND is_deleted = 0
      ORDER BY sort_order ASC
   
   b) Ảnh đầu tiên (sort_order=0) là ảnh chính

5. Lấy thêm info
   a) Tác giả: book_authors JOIN authors
   b) Nhà xuất bản: book_publisher JOIN publisher
   c) Bình luận: CommentModel::getByBookId($id)
   d) Rating trung bình: CommentModel::getAverageRating($id)
   e) Sách liên quan: BookModel::getRelatedProducts($id, $category_id, 4)

6. Check wishlist (nếu user đăng nhập)
   - WishlistModel::check($user_id, $book_id)

7. Render view: page/book_detail
```

### 3. Tìm Kiếm Sách (Live Search)

**URL AJAX:** `?controller=home&action=ajaxSearch&q=keyword`

**Luồng:**
```
1. Parse keyword
   - q = trim($_GET['q'])
   - Nếu trống → return []

2. Query tìm kiếm
   a) BookModel::searchByName($q, limit=5)
      SQL:
      SELECT b.id, b.title, b.slug,
             MIN(i.image_url) as image_url,
             MIN(IFNULL(v.sale_price, v.price)) as price,
             GROUP_CONCAT(a.name) as author_names
      FROM books b
      LEFT JOIN book_images i ON i.book_id = b.id
      LEFT JOIN book_variants v ON v.book_id = b.id
      LEFT JOIN book_authors ba ON b.id = ba.book_id
      LEFT JOIN authors a ON ba.author_id = a.id
      WHERE b.is_active = 1
        AND (
          b.title LIKE %keyword%
          OR a.name LIKE %keyword%
          OR p.name LIKE %keyword%
        )
      GROUP BY b.id
      LIMIT 5

3. Return JSON
   - [{ id, title, slug, image_url, price, author_names }, ...]
   - Header: Content-Type: application/json
```

### 4. Admin: Quản lý Sách

**URL:** `wd-admin/index.php?c=products&a=index`

**Luồng Liệt kê:**
```
1. Parse filters
   - keyword: tìm theo tên
   - category_id: lọc danh mục
   - status: active/inactive
   - deleted: 0 (còn) / 1 (đã xóa)
   - page: phân trang

2. Query
   a) AdminBookModel::paginate($filters, $page, 10)
      - Lấy 10 sách/trang
      - Support filter, search, soft delete

3. Render admin table
   - Danh sách sách, trạng thái, hành động (edit, delete, images)
```

**Luồng Tạo Sách:**
```
1. POST /wd-admin/index.php?c=products&a=store

2. Validate CSRF

3. Validate input
   - title: không rỗng, string
   - category_id: > 0, tồn tại
   - short_desc, description: optional
   - author_ids[], publisher_ids[]: optional arrays
   - is_active: checkbox → 0 hoặc 1

4. Process
   a) Generate slug
      - Nếu slug truyền vào → dùng
      - Nếu không → slugify(title)
      - Check unique, nếu trùng → slug-1, slug-2, etc.
   
   b) Insert book
      INSERT INTO books (title, slug, category_id, short_desc, 
                         description, is_active, created_at)
      VALUES (:title, :slug, :category_id, ...)
   
   c) Sync authors
      DELETE FROM book_authors WHERE book_id = ?
      INSERT INTO book_authors VALUES (book_id, author_id) × N
   
   d) Set publisher
      INSERT INTO book_publisher (book_id, publisher_id)
      VALUES (?, ?)

5. Flash success
6. Redirect → danh sách sách
```

**Luồng Upload Ảnh:**
```
1. POST /wd-admin/index.php?c=products&a=storeImages?id=123

2. Validate CSRF

3. Process file upload
   a) $_FILES['images'][] - multiple
   b) Validate:
      - Loại file: jpg, jpeg, png, gif
      - Size: < 5MB
   
   c) Save ảnh
      - Đường dẫn: /public/images/books/
      - Tên: {book_id}_{timestamp}_{random}.jpg
   
   d) Lưu DB
      INSERT INTO book_images (book_id, image_url, sort_order)
      VALUES (?, '/path/to/image.jpg', ?)
      
      - sort_order = 0 → ảnh chính

4. Return JSON { success, message, images: [...] }
```

---

## 🛒 Giỏ hàng & thanh toán

### Cấu trúc Giỏ Hàng (Session)

**Format mới:**
```php
$_SESSION['cart'] = [
    variant_id => ['variant_id' => int, 'quantity' => int],
    variant_id => ['variant_id' => int, 'quantity' => int],
    ...
]

Example:
$_SESSION['cart'] = [
    45 => ['variant_id' => 45, 'quantity' => 2],
    67 => ['variant_id' => 67, 'quantity' => 1],
]
```

**Migration từ format cũ:**
- Format cũ: `[book_id => ['id','title','price','quantity']]`
- CartController::normalizeCartSession() tự động migrate
- Tìm variant rẻ nhất của mỗi book: BookModel::getCheapestVariantId($bookId)

### 1. Thêm Sách vào Giỏ

**URL:** `?controller=cart&action=add&id=BOOK_ID[&variant_id=VARIANT_ID]`

**Luồng:**
```
1. Validate book ID
   - int $id > 0
   - BookModel::findById($id) tồn tại

2. Normalize giỏ hiện tại
   - CartController::normalizeCartSession()

3. Chọn variant
   a) Ưu tiên: variant_id từ URL
      - Validate: BookModel::isVariantOfBook($variant_id, $book_id)
   
   b) Fallback: variant rẻ nhất
      - $variant_id = BookModel::getCheapestVariantId($book_id)

4. Check variant hợp lệ
   - Phải tồn tại, stock > 0

5. Add vào giỏ
   a) Nếu variant đã có trong giỏ
      - $_SESSION['cart'][$variant_id]['quantity'] += qty
   
   b) Nếu new
      - $_SESSION['cart'][$variant_id] = [
          'variant_id' => $variant_id,
          'quantity' => qty
        ]

6. Flash success
7. Redirect hoặc stay (AJAX)
```

### 2. Xem Giỏ Hàng

**URL:** `?controller=cart&action=index`

**Luồng:**
```
1. Normalize session

2. Hydrate từ DB
   a) Lấy variant_ids từ session
   b) BookModel::getCartItemsByVariantIds($variant_ids)
      SQL (simplified):
      SELECT bv.id as variant_id, 
             b.id as book_id,
             b.title, bv.format,
             IFNULL(bv.sale_price, bv.price) as unit_price,
             i.image_url
      FROM book_variants bv
      JOIN books b ON bv.book_id = b.id
      LEFT JOIN book_images i ON b.id = i.book_id 
                             AND i.sort_order = 0
      WHERE bv.id IN (?, ?, ...)
   
   c) Index theo variant_id

3. Build cart items
   - Duyệt $_SESSION['cart']
   - Kết hợp DB data (price, image, title)
   - Tính subtotal = unit_price × quantity
   - Tính total = SUM(subtotal)

4. Render view
   - Danh sách items với xoá, thay đổi số lượng
   - Tổng tiền
   - Nút Checkout
```

### 3. Cập Nhật Số Lượng / Xoá khỏi Giỏ

**URL (AJAX):**
- Update: `?controller=cart&action=update` (POST)
- Delete: `?controller=cart&action=remove&variant_id=45` (POST)

**Luồng Update:**
```
1. Parse POST
   - variant_id: int
   - quantity: int (0 = remove)

2. Validate CSRF

3. Update session
   a) Nếu quantity <= 0
      - Xoá: unset($_SESSION['cart'][$variant_id])
   
   b) Nếu quantity > 0
      - $_SESSION['cart'][$variant_id]['quantity'] = $qty

4. Return JSON
   - { success, totalPrice, cartCount }
```

### 4. Checkout - Chọn Địa Chỉ

**URL:** `?controller=payment&action=checkout` (GET/POST)

**GET - Hiển thị trang checkout:**
```
1. Require login

2. Normalize giỏ, hydrate từ DB

3. Lấy địa chỉ user
   a) UserModel::getAddresses($user_id)
      SQL:
      SELECT * FROM user_address
      WHERE user_id = ? AND is_deleted = 0
      ORDER BY is_default DESC

4. Chọn địa chỉ mặc định
   - Ưu tiên: $_SESSION['checkout_shipping']
   - Fallback: addresses[0]
   - Validate: shipping_phone, full_address không rỗng

5. Check coupon (nếu áp dụng)
   a) $_SESSION['checkout_coupon']['code']
   b) CouponModel::validateForOrder($code, $user_id, $total)
   c) Tính discount, final_total

6. Render checkout page
   - Cart items
   - Địa chỉ giao hàng
   - Thông tin thanh toán
```

**POST - Chọn địa chỉ:**
```
1. URL: ?controller=payment&action=selectShipping

2. Validate CSRF

3. Parse
   - shipping_id: int

4. Query
   a) UserModel::getAddressByIdAndUser($shipping_id, $user_id)
      - Kiểm tra quyền sở hữu

5. Update session
   - $_SESSION['checkout_shipping'] = $shipping

6. Flash success
7. Redirect → checkout
```

### 5. Áp Dụng Mã Giảm Giá

**URL:** `?controller=payment&action=applyCoupon` (POST)

**Luồng:**
```
1. Validate CSRF

2. Parse code
   - coupon_code = trim($_POST['coupon_code'])

3. Hydrate giỏ từ DB
   - Lấy totalAmount

4. Validate coupon
   a) CouponModel::validateForOrder($code, $user_id, $total)
   
   b) Kiểm tra:
      - Code tồn tại, không bị xóa
      - is_active = 1
      - Trong thời gian sử dụng (starts_at <= NOW() <= ends_at)
      - Chưa hết lượt dùng (used_count < usage_limit)
      - User chưa dùng quá số lần cho phép
      - Giá trị đơn >= min_order_total
   
   c) Tính discount
      - Nếu type = 'percent'
        discount = total × value / 100
        discount = MIN(discount, max_discount)
      - Nếu type = 'fixed'
        discount = value
      - discount = MAX(0, MIN(discount, total))

5. Update session
   - $_SESSION['checkout_coupon'] = [
       'code' => $code,
       'coupon_id' => $coupon['id'],
       'discount' => $discount
     ]

6. Flash success/warning

7. Redirect → checkout
```

### 6. Xử lý Thanh Toán

**URL:** `?controller=payment&action=process` (POST)

**Luồng:**
```
1. Validate method POST

2. Validate CSRF

3. Hydrate giỏ
   - cartItems, totalAmount

4. Validate
   - Giỏ không trống
   - Có địa chỉ shipping hợp lệ

5. Lấy info
   a) User ID từ session
   b) Shipping info: $_SESSION['checkout_shipping']
   c) Coupon: $_SESSION['checkout_coupon'] (optional)

6. Parse payment method
   - payment_method = $_POST['payment_method']
   - Loại: 'COD' (Cash on Delivery) | 'VNPAY' (VNPay)

7. Nếu VNPAY
   a) Tạo order (status: pending)
   b) Redirect → VNPay gateway
   c) VNPay callback xử lý kết quả

8. Nếu COD
   a) Tạo order
      OrderModel::createFromCart(
        $user_id,
        $shipping,
        $cart,
        'pending',           # shipping_status
        $note,
        $coupon_id,
        $discount_amount,
        'pending',           # payment_status
        'COD',               # payment_method
        null,                # transactionCode
      )
   
   b) Order object
      {
        id: int,
        user_id: int,
        shipping_status: 'pending',
        payment_status: 'pending',
        total: float,
        coupon_id: null,
        discount_amount: 0,
        payment_method: 'COD',
        created_at: timestamp
      }
   
   c) Clear session
      - unset($_SESSION['cart'])
      - unset($_SESSION['checkout_*'])
   
   d) Flash success
   e) Redirect → thanh toán thành công (order confirmation)
```

### 7. Order Database Structure

**Bảng orders:**
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shipping_status ENUM('pending','processing','shipped','cancelled'),
    payment_status ENUM('pending','completed','failed'),
    total DECIMAL(10,2),
    coupon_id INT,
    discount_amount DECIMAL(10,2),
    payment_method VARCHAR(50),  -- COD, VNPAY, etc.
    transaction_code VARCHAR(100),
    note TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id)
)

Bảng order_items:
id INT, order_id INT, variant_id INT, 
quantity INT, unit_price DECIMAL(10,2),
subtotal DECIMAL(10,2)

Bảng payment:
id INT, order_id INT, payment_method VARCHAR(50),
status VARCHAR(50), transaction_code VARCHAR(100),
created_at DATETIME
```

---

## 👥 Quản Trị Viên

### 1. Dashboard Admin

**URL:** `wd-admin/index.php?c=dashboard&a=index`

**Luồng:**
```
1. Require login admin

2. Parse range
   - range = $_GET['range'] ?? 'last_12_months'
   - Loại: all | this_month | this_year | last_12_months

3. Query KPI
   a) AdminDashboardModel::kpiStats($range)
   
   b) Lấy:
      - Total books (không filter range)
      - Total categories (không filter range)
      - Total users (role = 'user')
      - Total admins (role = 'admin')
      
      - Total orders (trong range)
      - Total revenue (shipped orders trong range)
      - Orders this week vs last week (growth %)
      - Revenue this week vs last week

4. Query charts
   a) Revenue by month (last 12 months)
   b) Orders by payment method
   c) Orders by shipping status
   d) Top 10 best-selling books

5. Render dashboard
   - KPI cards (summary)
   - Charts (revenue, orders)
   - Recent orders table
```

### 2. Quản lý Đơn Hàng

**URL:** `wd-admin/index.php?c=orders&a=index`

**Luồng Liệt kê:**
```
1. Parse filters
   - keyword: tìm order_id, user name, email
   - shipping_status: pending|processing|shipped|cancelled
   - payment_status: pending|completed|failed
   - payment_method: COD|VNPAY
   - from_date, to_date: lọc thời gian
   - page: phân trang

2. Query
   a) AdminOrderModel::filter($filters, $page, 20)
      SQL:
      SELECT o.*, u.name, u.email, p.payment_method,
             (SELECT COUNT(*) FROM order_items 
              WHERE order_id = o.id) as item_count
      FROM orders o
      JOIN users u ON o.user_id = u.id
      LEFT JOIN payment p ON o.id = p.order_id
      WHERE ...filters...
      ORDER BY o.created_at DESC
      LIMIT 20 OFFSET ?

3. Render table
   - Order ID, customer, total, status, ngày tạo
   - Nút view detail, update status
```

**Luồng Xem Chi Tiết:**
```
1. URL: ?c=orders&a=show&id=123

2. Query order
   a) AdminOrderModel::find($id)
   
   b) Items: AdminOrderModel::items($id)
      SQL:
      SELECT oi.*, b.title, bv.format, i.image_url
      FROM order_items oi
      JOIN book_variants bv ON oi.variant_id = bv.id
      JOIN books b ON bv.book_id = b.id
      LEFT JOIN book_images i ON b.id = i.book_id

3. Render detail page
   - Order info (ID, customer, địa chỉ, tổng tiền)
   - Order items (bảng)
   - Status history
   - Form update status
```

**Luồng Cập Nhật Trạng Thái:**
```
1. URL: ?c=orders&a=updateStatus (POST)

2. Validate CSRF

3. Parse
   - id: int (order ID)
   - shipping_status: pending|processing|shipped|cancelled
   - reason: string (optional)

4. Validate
   - Order tồn tại
   - Status hợp lệ

5. Update DB
   a) AdminOrderModel::updateShippingStatus($id, $status, $admin_id, $reason)
      UPDATE orders 
      SET shipping_status = ?, updated_at = NOW()
      WHERE id = ?
   
   b) Insert log
      INSERT INTO order_status_logs 
      (order_id, admin_id, old_status, new_status, reason, created_at)

6. Flash success

7. Redirect → order detail
```

### 3. Quản lý Mã Giảm Giá

**URL:** `wd-admin/index.php?c=coupons&a=index`

**Luồng Liệt kê:**
```
1. Query tất cả coupon
   a) AdminCouponModel::all()
   b) Không filter deleted (có thể soft delete)

2. Report usage
   a) AdminCouponModel::usageReport()
      SQL:
      SELECT c.id, c.code, c.type, c.value,
             c.used_count, c.usage_limit,
             COUNT(o.id) as orders_used
      FROM coupons c
      LEFT JOIN orders o ON c.id = o.coupon_id
      GROUP BY c.id

3. Render table
   - Code, type, value, used/limit, hành động
```

**Luồng Tạo Coupon:**
```
1. URL: ?c=coupons&a=store (POST)

2. Validate CSRF

3. Parse input
   - code: string, unique, uppercase
   - type: percent|fixed
   - value: number
   - max_discount: number (for percent type)
   - min_order_total: number (default 0)
   - usage_limit: int (null = unlimited)
   - max_uses_per_user: int (null = unlimited)
   - starts_at, ends_at: datetime (optional)
   - is_active: checkbox

4. Validate
   - code không trống, unique
   - type & value hợp lệ
   - Nếu percent type: 0 < value <= 100
   - Nếu fixed: value > 0

5. Insert DB
   INSERT INTO coupons (code, type, value, ...)
   VALUES (?, ?, ?, ...)

6. Flash success
7. Redirect → danh sách
```

### 4. Quản lý Bình Luận

**URL:** `wd-admin/index.php?c=comments&a=index`

**Luồng:**
```
1. Parse filters
   - keyword: tìm tên sách, nội dung
   - rating: 1-5
   - page

2. Query
   a) AdminCommentModel::paginate($filters, $page, 20)
      SQL:
      SELECT c.*, b.title, u.name, u.email
      FROM comments c
      JOIN books b ON c.book_id = b.id
      JOIN users u ON c.user_id = u.id
      WHERE ...filters...
      ORDER BY c.created_at DESC

3. Render table
   - User, book, rating, content, ngày, hành động (delete)

4. Delete comment
   a) Soft delete: is_deleted = 1, deleted_at = NOW()
   b) Recalculate book rating:
      UPDATE books SET rating_avg = (
        SELECT AVG(rating) FROM comments 
        WHERE book_id = ? AND is_deleted = 0
      )
```

### 5. Quản lý Tác Giả

**URL:** `wd-admin/index.php?c=authors&a=index`

**Luồng Tương Tự:**
```
1. CRUD full (Create, Read, Update, Delete)
2. Filter: keyword, status, deleted
3. Phân trang
4. Slug generation (auto từ name)
5. Soft delete
```

### 6. Quản lý Nhà Xuất Bản

**URL:** `wd-admin/index.php?c=publishers&a=index`

**Luồng Tương Tự Authors**

### 7. Quản lý Người Dùng

**URL:** `wd-admin/index.php?c=users&a=index`

**Luồng:**
```
1. Liệt kê users
   - Filters: keyword, role, status
   
2. Hành động
   - View detail
   - Lock/unlock account (is_active)
   - Delete (soft delete)
```

---

## 💬 Tính năng bổ sung

### 1. Wishlist (Danh sách Yêu thích)

**URL:** `?controller=account&action=wishlist` (GET)

**Luồng GET:**
```
1. Require login

2. Query wishlist
   a) WishlistModel::getWishlist($user_id)
      SQL:
      SELECT b.id, b.title, b.slug,
             MIN(i.image_url) as image_url,
             MIN(IFNULL(v.sale_price, v.price)) as price,
             GROUP_CONCAT(a.name) as author_names
      FROM wishlist w
      JOIN books b ON w.book_id = b.id
      LEFT JOIN book_variants v ON v.book_id = b.id
      LEFT JOIN book_images i ON i.book_id = b.id AND i.sort_order = 0
      LEFT JOIN book_authors ba ON b.id = ba.book_id
      LEFT JOIN authors a ON ba.author_id = a.id
      WHERE w.user_id = ?
      GROUP BY b.id

3. Render wishlist page
   - Danh sách sách yêu thích
   - Nút "Thêm vào giỏ", "Xoá khỏi yêu thích"
```

**Thêm vào Wishlist (AJAX):**
```
1. URL: ?controller=account&action=toggleWishlist (POST)

2. Parse book_id

3. Check
   a) WishlistModel::check($user_id, $book_id)

4. Action
   a) Nếu có → WishlistModel::remove(...)
   b) Nếu không → WishlistModel::add(...)

5. Return JSON { success, inWishlist }
```

### 2. Bình Luận & Đánh Giá

**URL:** `?controller=product&action=addComment` (POST)

**Luồng:**
```
1. Require login

2. Parse
   - book_id: int
   - content: string
   - rating: int (1-5)

3. Validate
   - book_id tồn tại
   - content không rỗng
   - rating 1-5
   - User đã mua và nhận hàng (orders.shipping_status = 'delivered')

4. Insert comment
   a) Lấy order_id hợp lệ để gắn "đã mua"
      - OrderModel::getLatestDeliveredOrderIdForBook($user_id, $book_id)

   b) CommentModel::create($book_id, $user_id, $order_id, $content, $rating)
      INSERT INTO comments (book_id, user_id, order_id, content, rating, created_at)
      VALUES (?, ?, ?, ?, ?, NOW())
   
   c) Recalculate book rating
      SELECT AVG(rating) as avg_rating, COUNT(*) as total
      FROM comments
      WHERE book_id = ? AND rating IS NOT NULL
      
      UPDATE books 
      SET rating_avg = ?, review_count = ?
      WHERE id = ?

5. Render UI
   - Danh sách bình luận hiển thị badge: `✅ Đã mua • Đơn #<order_id>`
```

### 3. Liên Hệ (Contact Form)

**URL:** `?controller=contact&action=send` (POST AJAX)

**Luồng:**
```
1. Parse form
   - name: string
   - email: string
   - phone: string (optional)
   - subject: string
   - message: string

2. Validate
   - Tất cả fields không rỗng
   - Email hợp lệ
   - Message tối thiểu 10 ký tự
   - CSRF token

3. Gửi email
   a) EmailService::sendContactEmail(
      $toEmail = 'admin@bookstore.com',
      $fromName,
      $fromEmail,
      $subject,
      $message,
      $phone
    )
   
   b) Template HTML gửi đi

4. Save local
   - Fallback nếu SMTP fail: lưu vào file

5. Return JSON
   - { success, message: "Cảm ơn bạn, chúng tôi sẽ liên hệ..." }
```

---

## 📧 EmailService

### Cấu hình

**File:** `config/email.php`
```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password',  // Google App Password, not Gmail password
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'WiseDecision Bookstore',
];
```

### Phương thức

**1. sendPlain() - Email text đơn giản**
```php
$emailService->sendPlain(
    $toEmail, $toName, $subject, $body
);
```

**2. sendContactEmail() - Email liên hệ HTML**
```php
$emailService->sendContactEmail(
    $toEmail,
    $fromName,
    $fromEmail,
    $subject,
    $messageBody,
    $phone
);
```

**3. sendResetTemplate() - Email reset password**
```php
$emailService->sendResetTemplate(
    $toEmail,
    $toName,
    $subject,
    $resetLink  // Full URL với token
);
```

### Fallback Mechanisms
- Nếu SMTP không cấu hình → log và lưu local (`/storage/logs/`)
- Nếu gửi fail → lưu vào file `.eml`
- Hỗ trợ PHPMailer (tự động nếu installed) hoặc socket SMTP

---

## 🔒 Security Features

### 1. CSRF Protection
- Tất cả form POST sử dụng token
- Token lưu trong `$_SESSION['_csrf']`
- Generate: `bin2hex(random_bytes(16))`
- BaseController::csrfToken() - GET
- BaseController::checkCsrf() - VALIDATE

### 2. Password Security
- Hash: `password_hash(..., PASSWORD_BCRYPT)`
- Verify: `password_verify($input, $hash)`
- Min 6 ký tự
- Token reset: 60 phút expiry

### 3. Input Validation
- Email: `filter_var(..., FILTER_VALIDATE_EMAIL)`
- Trim, sanitize string inputs
- Type casting (int, float)
- Prepared statements (PDO)

### 4. Authorization
- Check session: `$_SESSION['user']` hoặc `$_SESSION['admin']`
- Require login: `$this->requireAuth()`
- Admin-only: Check `$_SESSION['admin']`
- Ownership check: Verify user ID match

### 5. SQL Injection Prevention
- PDO prepared statements
- `:param` style binding
- No string concatenation in queries

---

## 📊 Database Relationships

```
users (1) ─── (N) orders
         └─── (N) user_address
         └─── (N) comments
         └─── (N) wishlist

books (1) ─── (N) book_variants
      ├─── (N) book_images
      ├─── (N) book_authors (N-N via book_authors)
      ├─── (1) book_publisher (1-N via book_publisher)
      ├─── (N) comments
      └─── (N) wishlist

authors (1) ─── (N) book_authors (N-N with books)
publisher (1) ─── (N) book_publisher (1-N with books)

categories (1) ─── (N) categories (self-referencing: parent_id)
           └─── (N) books

orders (1) ─── (N) order_items
       └─── (N) payment
       └─── (1) coupons (optional)

coupons (1) ─── (N) orders
```

---

## 🚀 Luồng Chính

### User Journey - Mua Sách

```
1. Trang Chủ → Home Controller
   - Lấy 8 sách mới
   - Lấy 8 sách bán chạy
   - Lấy 8 sách giảm giá

2. Xem Danh Mục → Category Controller
   - Filter, search, sort
   - Phân trang

3. Xem Chi Tiết → Book Controller
   - Hiển thị biến thể, ảnh, bình luận
   - Show wishlist button

4. Thêm vào Giỏ → Cart Controller
   - Chọn variant
   - Update session

5. Xem Giỏ → Cart Controller
   - Hydrate từ DB
   - Update, remove items

6. Checkout → Payment Controller
   - Chọn địa chỉ
   - Áp dụng coupon
   - Chọn phương thức thanh toán

7. Thanh Toán
   - COD: Order tạo ngay, flash confirm
   - VNPay: Redirect tới gateway

8. Order Confirmation
   - Hiển thị order detail
   - Email confirmation

9. Account → Account Controller
   - Xem order history
   - Xem wishlist
   - Update profile, password
```

### Admin Journey - Quản Lý

```
1. Login → Dashboard
   - KPI summary
   - Charts

2. Quản Lý Sách
   - CRUD books
   - Upload gallery ảnh
   - Manage variants

3. Quản Lý Đơn
   - Liệt kê với filter
   - Update shipping status
   - Xem chi tiết

4. Quản Lý Mã Giảm Giá
   - CRUD coupons
   - View usage report

5. Quản Lý Nội Dung
   - Xem/Delete comments
   - Manage Q&A

6. Quản Lý Catalog
   - Categories, Authors, Publishers
   - CRUD operations
```

---

## 📝 Kết Luận

Hệ thống WiseDecision Bookstore được thiết kế theo mô hình MVC chuẩn với:

✅ **Xác thực an toàn**: CSRF, password hashing, session management  
✅ **Database tiền tiến**: Normalized schema, relationships, soft delete  
✅ **Business logic rõ ràng**: Controllers xử lý logic, Models query DB  
✅ **User experience**: Responsive design, live search, wishlist  
✅ **Admin powerful**: Dashboard, advanced filtering, status management  
✅ **Flexible payment**: COD, VNPay support  
✅ **Promotional tools**: Coupon system with complex validation  
✅ **Communication**: Email system, contact form, order notifications  

Tất cả luồng được thiết kế để đảm bảo tính toàn vẹn dữ liệu, bảo mật, và trải nghiệm người dùng tối ưu.
