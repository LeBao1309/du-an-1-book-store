-- ===============================================================
-- TẠO DATABASE
-- ===============================================================
CREATE DATABASE du_an_1_book_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE du_an_1_book_store;

-- ===============================================================
-- 1. BẢNG AUTHORS (TÁC GIẢ)
-- ===============================================================
CREATE TABLE authors (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         name VARCHAR(255) NOT NULL,
                         slug VARCHAR(255) NOT NULL UNIQUE,
                         INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ===============================================================
-- 2. BẢNG CATEGORIES (DANH MỤC)
-- ===============================================================
CREATE TABLE categories (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(255) NOT NULL,
                            slug VARCHAR(255) NOT NULL UNIQUE,
                            parent_id INT DEFAULT NULL,
                            INDEX idx_slug (slug),
                            INDEX idx_parent (parent_id),
                            CONSTRAINT fk_category_parent
                                FOREIGN KEY (parent_id) REFERENCES categories(id)
                                    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===============================================================
-- 3. BẢNG PUBLISHER (NHÀ XUẤT BẢN)
-- ===============================================================
CREATE TABLE publisher (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           name VARCHAR(255) NOT NULL,
                           slug VARCHAR(255) NOT NULL UNIQUE,
                           INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ===============================================================
-- 4. BẢNG USERS (NGƯỜI DÙNG)
-- ===============================================================
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(255) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL COMMENT 'BCrypt hashed',
                       role ENUM('admin', 'user') DEFAULT 'user',
                       is_active TINYINT(1) DEFAULT 1,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       INDEX idx_email (email)
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
                              INDEX idx_user (user_id),
                              CONSTRAINT fk_address_user
                                  FOREIGN KEY (user_id) REFERENCES users(id)
                                      ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================================================
-- 6. BẢNG BOOKS (SÁCH)
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
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                       INDEX idx_slug (slug),
                       INDEX idx_category (category_id),
                       CONSTRAINT fk_book_category
                           FOREIGN KEY (category_id) REFERENCES categories(id)
                               ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ===============================================================
-- 7. BẢNG BOOK_AUTHORS (SÁCH - TÁC GIẢ, N-N)
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
-- 8. BẢNG BOOK_PUBLISHER (SÁCH - NXB, 1-N)
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
-- 9. BẢNG BOOK_VARIANTS (PHIÊN BẢN SÁCH)
-- ===============================================================
CREATE TABLE book_variants (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               book_id INT NOT NULL,
                               format VARCHAR(50) COMMENT 'e.g., hardcover, paperback',
                               price DECIMAL(10,2) NOT NULL,
                               sale_price DECIMAL(10,2),
                               stock INT DEFAULT 0,
                               CONSTRAINT fk_variant_book
                                   FOREIGN KEY (book_id) REFERENCES books(id)
                                       ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================================================
-- 10. BẢNG COMMENTS (BÌNH LUẬN)
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
-- 11. BẢNG WISHLIST (DANH SÁCH YÊU THÍCH)
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
-- 12. BẢNG ORDERS (ĐƠN HÀNG)
-- ===============================================================
CREATE TABLE orders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        user_address_id INT NULL COMMENT 'ID địa chỉ trong sổ địa chỉ',
                        total DECIMAL(10,2) NOT NULL,

    -- Dữ liệu snapshot (ảnh chụp nhanh)
                        shipping_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
                        shipping_address TEXT NOT NULL,
                        shipping_phone VARCHAR(20),

                        note TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                        INDEX idx_user (user_id),
                        INDEX idx_status (shipping_status),

                        CONSTRAINT fk_order_user
                            FOREIGN KEY (user_id) REFERENCES users(id)
                                ON DELETE RESTRICT,
                        CONSTRAINT fk_order_user_address
                            FOREIGN KEY (user_address_id) REFERENCES user_address(id)
                                ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===============================================================
-- 13. BẢNG ORDER_ITEMS (CHI TIẾT ĐƠN HÀNG)
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
-- 14. BẢNG PAYMENT (THANH TOÁN)
-- ===============================================================
DROP TABLE IF EXISTS payment;

CREATE TABLE payment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  payment_method ENUM('cod', 'card', 'wallet') NOT NULL,
  UNIQUE KEY uk_order (order_id),
  CONSTRAINT fk_payment_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE book_images (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             book_id INT NOT NULL,
                             image_url VARCHAR(255) NOT NULL,
                             sort_order INT DEFAULT 0 comment '0 là thumbnail, 1 2 3,...là ảnh trong sản phẩm chi tiết',
                             CONSTRAINT fk_book_images_book
                                 FOREIGN KEY (book_id) REFERENCES books(id)
                                     ON DELETE CASCADE
) ENGINE=InnoDB;

