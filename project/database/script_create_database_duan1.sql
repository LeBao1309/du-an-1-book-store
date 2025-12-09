-- ===============================================================
-- 0. THIẾT LẬP MÔI TRƯỜNG & DATABASE
-- ===============================================================
DROP DATABASE IF EXISTS du_an_1_book_store;
CREATE DATABASE du_an_1_book_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE du_an_1_book_store;

-- ===============================================================
-- 1. BẢNG AUTHORS (TÁC GIẢ)
-- ===============================================================
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,

    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ===============================================================
-- 2. BẢNG PUBLISHER (NHÀ XUẤT BẢN)
-- ===============================================================
CREATE TABLE publisher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,

    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug)
) ENGINE=InnoDB;


-- ===============================================================
-- 3. BẢNG CATEGORIES (DANH MỤC)
-- ===============================================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_id INT DEFAULT NULL,

    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),

    CONSTRAINT fk_category_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- ===============================================================
-- 4. BẢNG USERS (NGƯỜI DÙNG)
-- ===============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',

    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ===============================================================
-- 4b. BẢNG PASSWORD RESETS (KHÔI PHỤC MẬT KHẨU)
-- ===============================================================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    CONSTRAINT fk_pr_user
      FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;


-- ===============================================================
-- 5. BẢNG USER_ADDRESS (SỔ ĐỊA CHỈ)
-- ===============================================================
CREATE TABLE user_address (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    full_address TEXT NOT NULL,
    shipping_phone VARCHAR(20),
    is_default TINYINT(1) DEFAULT 0,

    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),

    CONSTRAINT fk_address_user
      FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;


-- ===============================================================
-- 6. BẢNG COUPONS (MÃ GIẢM GIÁ)
-- (Tạo trước bảng orders để orders có thể tham chiếu tới)
-- ===============================================================
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
    value DECIMAL(10,2) NOT NULL,

    max_discount DECIMAL(10,2) DEFAULT NULL,
    min_order_total DECIMAL(10,2) DEFAULT 0,

    usage_limit INT DEFAULT NULL,
    max_uses_per_user INT DEFAULT NULL,
    used_count INT DEFAULT 0,

    starts_at DATETIME NULL,
    ends_at DATETIME NULL,

    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_active (is_active),
    INDEX idx_time (starts_at, ends_at)
) ENGINE=InnoDB;


-- ===============================================================
-- 7. BẢNG BOOKS (SÁCH)
-- ===============================================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) NOT NULL UNIQUE,
    category_id INT NOT NULL,

    description TEXT,
    short_desc VARCHAR(500),

    rating_avg DECIMAL(3,2) DEFAULT 0.00,
    review_count INT DEFAULT 0,

    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_category (category_id),

    CONSTRAINT fk_book_category
      FOREIGN KEY (category_id) REFERENCES categories(id)
      ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ===============================================================
-- 8. BẢNG BOOK_IMAGES (ẢNH SÁCH)
-- ===============================================================
CREATE TABLE book_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,

    image_url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,

    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_book_images_book
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ===============================================================
-- 9. BẢNG BOOK_AUTHORS (SÁCH - TÁC GIẢ, N-N)
-- ===============================================================
CREATE TABLE book_authors (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              book_id INT NOT NULL,
                              author_id INT NOT NULL,
                              UNIQUE KEY uk_book_author (book_id, author_id),
                              CONSTRAINT fk_ba_book
                                  FOREIGN KEY (book_id) REFERENCES books(id)
                                      ON DELETE CASCADE,
                              CONSTRAINT fk_ba_author
                                  FOREIGN KEY (author_id) REFERENCES authors(id)
                                      ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================================================
-- 10. BẢNG BOOK_PUBLISHER (SÁCH - NXB, 1-N)
-- ===============================================================
CREATE TABLE book_publisher (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                book_id INT NOT NULL,
                                publisher_id INT NOT NULL,
                                UNIQUE KEY uk_book (book_id) COMMENT 'Mỗi sách chỉ có 1 NXB',
                                INDEX idx_publisher (publisher_id),
                                CONSTRAINT fk_bp_book
                                    FOREIGN KEY (book_id) REFERENCES books(id)
                                        ON DELETE CASCADE,
                                CONSTRAINT fk_bp_publisher
                                    FOREIGN KEY (publisher_id) REFERENCES publisher(id)
                                        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ===============================================================
-- 11. BẢNG BOOK_VARIANTS (PHIÊN BẢN SÁCH)
-- ===============================================================
CREATE TABLE book_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,

    format VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock INT DEFAULT 0,

    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    deleted_at DATETIME DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_variant_book
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ===============================================================
-- 12. BẢNG COMMENTS (BÌNH LUẬN)
-- ===============================================================
CREATE TABLE comments (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          book_id INT NOT NULL,
                          user_id INT NOT NULL,
                          content TEXT NOT NULL,
                          rating TINYINT CHECK (rating BETWEEN 1 AND 5),
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          INDEX idx_book (book_id),
                          INDEX idx_user (user_id),
                          CONSTRAINT fk_comment_book
                              FOREIGN KEY (book_id) REFERENCES books(id)
                                  ON DELETE CASCADE,
                          CONSTRAINT fk_comment_user
                              FOREIGN KEY (user_id) REFERENCES users(id)
                                  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================================================
-- 13. BẢNG WISHLIST (DANH SÁCH YÊU THÍCH)
-- ===============================================================
CREATE TABLE wishlist (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          book_id INT NOT NULL,
                          user_id INT NOT NULL,
                          UNIQUE KEY uk_book_user (book_id, user_id),
                          CONSTRAINT fk_wl_book
                              FOREIGN KEY (book_id) REFERENCES books(id)
                                  ON DELETE CASCADE,
                          CONSTRAINT fk_wl_user
                              FOREIGN KEY (user_id) REFERENCES users(id)
                                  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================================================
-- 14. BẢNG ORDERS (ĐƠN HÀNG)
-- ===============================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_address_id INT NULL COMMENT 'ID địa chỉ trong sổ địa chỉ',

    -- Các trường tiền tệ & Coupon
    total DECIMAL(10,2) NOT NULL,
    coupon_id INT NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    
    -- Cột tính toán tự động (Generated Column) cho MySQL 5.7+
    final_total DECIMAL(10,2) GENERATED ALWAYS AS (total - discount_amount) STORED,

    -- Trạng thái (ĐÃ SỬA: Thêm 'delivered')
    shipping_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',

    -- Thông tin ship
    shipping_address TEXT NOT NULL,
    shipping_phone VARCHAR(20),
    note TEXT,

    -- Hủy đơn
    cancel_reason TEXT NULL,
    cancelled_by_user_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_user (user_id),
    INDEX idx_status (shipping_status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_cancelled_by (cancelled_by_user_id),

    -- Foreign Keys
    CONSTRAINT fk_order_user
        FOREIGN KEY (user_id) REFERENCES users(id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_order_user_address
        FOREIGN KEY (user_address_id) REFERENCES user_address(id)
            ON DELETE SET NULL,
    CONSTRAINT fk_orders_coupon
        FOREIGN KEY (coupon_id) REFERENCES coupons(id)
            ON DELETE SET NULL,
    CONSTRAINT fk_orders_cancelled_by_user
        FOREIGN KEY (cancelled_by_user_id) REFERENCES users(id)
            ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===============================================================
-- 15. BẢNG ORDER_ITEMS (CHI TIẾT ĐƠN HÀNG)
-- ===============================================================
CREATE TABLE order_items (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             order_id INT NOT NULL,
                             variant_id INT NOT NULL,
                             quantity INT NOT NULL,
                             price DECIMAL(10,2) NOT NULL COMMENT 'Giá tại thời điểm đặt hàng',
                             subtotal DECIMAL(10,2) NOT NULL,
                             INDEX idx_order (order_id),
                             INDEX idx_variant (variant_id),
                             CONSTRAINT fk_oi_order
                                 FOREIGN KEY (order_id) REFERENCES orders(id)
                                     ON DELETE CASCADE,
                             CONSTRAINT fk_oi_variant
                                 FOREIGN KEY (variant_id) REFERENCES book_variants(id)
                                     ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ===============================================================
-- 16. BẢNG PAYMENT (THANH TOÁN)
-- ===============================================================
CREATE TABLE payment (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         order_id INT NOT NULL,
                         payment_method ENUM('cod', 'card', 'wallet') NOT NULL,
                         amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                         status ENUM('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
                         provider VARCHAR(50) DEFAULT NULL COMMENT 'Ví dụ: VNPay, Momo, Stripe',
                         transaction_code VARCHAR(100) DEFAULT NULL,
                         raw_response TEXT NULL,
                         paid_at DATETIME NULL,

                         UNIQUE KEY uk_order (order_id),
                         CONSTRAINT fk_payment_order
                             FOREIGN KEY (order_id) REFERENCES orders(id)
                                 ON DELETE CASCADE
) ENGINE=InnoDB;
