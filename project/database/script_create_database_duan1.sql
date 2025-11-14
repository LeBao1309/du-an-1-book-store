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
CREATE TABLE payment (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         order_id INT NOT NULL,
                         amount DECIMAL(10,2) NOT NULL,
                         payment_method ENUM('cod', 'card', 'wallet') NOT NULL,
                         payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
                         transaction_code VARCHAR(255) NULL,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         UNIQUE KEY uk_order_id (order_id) COMMENT 'Mỗi đơn hàng chỉ có 1 thanh toán',
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



INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`) VALUES
                                                                 (1, 'Sách Khoa học', 'sach-khoa-hoc', NULL),
                                                                 (2, 'Sách Văn học', 'sach-van-hoc', NULL),
                                                                 (3, 'Sách Kinh tế', 'sach-kinh-te', NULL),
                                                                 (4, 'Sách Kỹ năng sống', 'sach-ky-nang-song', NULL),
                                                                 (5, 'Sách Thiếu nhi', 'sach-thieu-nhi', NULL);


-- Đổ dữ liệu cho bảng `books`
INSERT INTO `books` (`id`, `title`, `slug`, `category_id`, `description`, `short_desc`, `rating_avg`, `review_count`, `is_active`, `created_at`, `updated_at`) VALUES
                                                                                                                                                                   (1, 'Vũ trụ trong vỏ hạt dẻ', 'vu-tru-trong-vo-hat-de', 1, 'Cuốn sách kinh điển của Stephen Hawking', 'Khám phá bí ẩn vũ trụ', 4.80, 1200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (2, 'Lược sử thời gian', 'luoc-su-thoi-gian', 1, 'Giải thích khoa học vũ trụ bằng ngôn ngữ dễ hiểu', 'Công trình về không gian và thời gian', 4.90, 2500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (3, 'Nguồn gốc các loài', 'nguon-goc-cac-loai', 1, 'Charles Darwin và hành trình khám phá nguồn gốc sinh học', 'Thuyết tiến hóa', 4.70, 900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (4, 'Hành tinh xanh', 'hanh-tinh-xanh', 1, 'Những điều thú vị về hành tinh của chúng ta', 'Khám phá Trái Đất', 4.60, 400, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (5, 'Từ Big Bang đến Trí tuệ nhân tạo', 'tu-big-bang-den-ai', 1, 'Khoa học hiện đại và công nghệ tương lai', 'Tổng hợp kiến thức hiện đại', 4.50, 200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (6, 'Vũ trụ song song', 'vu-tru-song-song', 1, 'Khám phá các giả thuyết về vũ trụ song song', 'Lý thuyết đa vũ trụ', 4.40, 150, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (7, 'Nhà giả kim', 'nha-gia-kim', 2, 'Hành trình theo đuổi ước mơ của cậu bé chăn cừu', 'Tiểu thuyết truyền cảm hứng', 4.80, 5000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (8, 'Chuông nguyện hồn ai', 'chuong-nguyen-hon-ai', 2, 'Tác phẩm nổi tiếng của Ernest Hemingway', 'Tiểu thuyết chiến tranh', 4.70, 1000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (9, 'Rừng Na Uy', 'rung-na-uy', 2, 'Câu chuyện tuổi trẻ và nỗi cô đơn', 'Tác phẩm của Haruki Murakami', 4.60, 1500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (10, 'Tuổi trẻ đáng giá bao nhiêu', 'tuoi-tre-dang-gia-bao-nhieu', 2, 'Những suy ngẫm về tuổi trẻ và lựa chọn', 'Truyền cảm hứng sống', 4.50, 2300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (11, 'Người đua diều', 'nguoi-dua-dieu', 2, 'Câu chuyện cảm động tại Afghanistan', 'Tình bạn và chuộc lỗi', 4.90, 2100, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (12, 'Tội ác và hình phạt', 'toi-ac-va-hinh-phat', 2, 'Tác phẩm triết lý sâu sắc của Dostoevsky', 'Kinh điển văn học Nga', 4.80, 1800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (13, 'Sự giàu có của các quốc gia', 'su-giau-co-cua-cac-quoc-gia', 3, 'Tác phẩm của Adam Smith', 'Kinh điển kinh tế học', 4.90, 1200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (14, 'Tư bản', 'tu-ban', 3, 'Karl Marx viết về cơ chế kinh tế và xã hội', 'Phân tích tư bản', 4.70, 800, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (15, 'Cha giàu cha nghèo', 'cha-giau-cha-ngheo', 3, 'Cách tư duy khác biệt về tiền bạc', 'Bí quyết tài chính cá nhân', 4.80, 5000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (16, 'Nghĩ giàu làm giàu', 'nghi-giau-lam-giau', 3, 'Tác phẩm nổi tiếng của Napoleon Hill', 'Tư duy thành công', 4.70, 4500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (17, 'Tư duy nhanh và chậm', 'tu-duy-nhanh-va-cham', 3, 'Giải thích cách con người ra quyết định', 'Tâm lý học hành vi', 4.80, 2700, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (18, 'Quốc gia khởi nghiệp', 'quoc-gia-khoi-nghiep', 3, 'Tinh thần sáng tạo của một quốc gia nhỏ bé', 'Câu chuyện về Israel', 4.60, 1300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (19, 'Đắc nhân tâm', 'dac-nhan-tam', 4, 'Tác phẩm của Dale Carnegie', 'Kỹ năng giao tiếp và ứng xử', 4.90, 8000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (20, 'Tôi tài giỏi bạn cũng thế', 'toi-tai-gioi-ban-cung-the', 4, 'Khơi dậy tiềm năng học tập và thành công', 'Phát triển bản thân', 4.70, 7000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (21, 'Thói quen của người thành đạt', 'thoi-quen-cua-nguoi-thanh-dat', 4, 'Stephen Covey chia sẻ 7 thói quen thành công', 'Xây dựng thói quen tốt', 4.80, 4200, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (22, 'Sức mạnh của hiện tại', 'suc-manh-cua-hien-tai', 4, 'Học cách sống trọn vẹn trong hiện tại', 'Tâm lý tích cực', 4.70, 2500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (23, 'Nghệ thuật nói chuyện', 'nghe-thuat-noi-chuyen', 4, 'Hướng dẫn nghệ thuật thuyết trình và đối thoại', 'Kỹ năng giao tiếp', 4.50, 1900, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (24, 'Tư duy tích cực', 'tu-duy-tich-cuc', 4, 'Thay đổi cuộc sống nhờ năng lượng tích cực', 'Phát triển tinh thần', 4.60, 1600, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (25, 'Cho tôi xin một vé đi tuổi thơ', 'cho-toi-xin-mot-ve-di-tuoi-tho', 5, 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh', 'Tuổi thơ hồn nhiên', 4.90, 10000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (26, 'Mắt biếc', 'mat-biec', 5, 'Một trong những tiểu thuyết cảm động nhất của Nguyễn Nhật Ánh', 'Câu chuyện tình học trò', 4.80, 9500, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (27, 'Dế Mèn phiêu lưu ký', 'de-men-phieu-luu-ky', 5, 'Tác phẩm thiếu nhi kinh điển Việt Nam', 'Hành trình phiêu lưu', 4.70, 7000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (28, 'Harry Potter và Hòn đá phù thủy', 'harry-potter-va-hon-da-phu-thuy', 5, 'Tác phẩm của J.K. Rowling', 'Phép thuật và tình bạn', 4.90, 15000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (29, 'Totto-chan bên cửa sổ', 'totto-chan-ben-cua-so', 5, 'Tình bạn, giáo dục và lòng nhân ái', 'Câu chuyện cảm động', 4.80, 4000, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56'),
                                                                                                                                                                   (30, 'Những người bạn rừng xanh', 'nhung-nguoi-ban-rung-xanh', 5, 'Câu chuyện về động vật và tình bạn', 'Truyện thiếu nhi hiện đại', 4.60, 1300, 1, '2025-11-11 09:21:56', '2025-11-11 09:21:56');

INSERT INTO `book_variants` (`id`, `book_id`, `format`, `price`, `sale_price`, `stock`) VALUES
                                                                                            (1, 1, 'bìa mềm', 189000.00, 62000.00, 17),
                                                                                            (2, 2, 'bìa mềm', 176000.00, 144000.00, 51),
                                                                                            (3, 3, 'bìa mềm', 70000.00, 86000.00, 73),
                                                                                            (4, 4, 'bìa mềm', 196000.00, 139000.00, 89),
                                                                                            (5, 5, 'bìa mềm', 75000.00, 88000.00, 76),
                                                                                            (6, 6, 'bìa mềm', 53000.00, 57000.00, 61),
                                                                                            (7, 7, 'bìa mềm', 82000.00, 99000.00, 15),
                                                                                            (8, 8, 'bìa mềm', 149000.00, 59000.00, 83),
                                                                                            (9, 9, 'bìa mềm', 86000.00, 144000.00, 33),
                                                                                            (10, 10, 'bìa mềm', 79000.00, 72000.00, 86),
                                                                                            (11, 11, 'bìa mềm', 55000.00, 131000.00, 32),
                                                                                            (12, 12, 'bìa mềm', 126000.00, 133000.00, 96),
                                                                                            (13, 13, 'bìa mềm', 155000.00, 134000.00, 43),
                                                                                            (14, 14, 'bìa mềm', 55000.00, 61000.00, 78),
                                                                                            (15, 15, 'bìa mềm', 196000.00, 124000.00, 13),
                                                                                            (16, 16, 'bìa mềm', 173000.00, 144000.00, 60),
                                                                                            (17, 17, 'bìa mềm', 130000.00, 60000.00, 26),
                                                                                            (18, 18, 'bìa mềm', 105000.00, 78000.00, 63),
                                                                                            (19, 19, 'bìa mềm', 156000.00, 137000.00, 56),
                                                                                            (20, 20, 'bìa mềm', 134000.00, 88000.00, 55),
                                                                                            (21, 21, 'bìa mềm', 199000.00, 106000.00, 17),
                                                                                            (22, 22, 'bìa mềm', 130000.00, 90000.00, 75),
                                                                                            (23, 23, 'bìa mềm', 188000.00, 110000.00, 56),
                                                                                            (24, 24, 'bìa mềm', 105000.00, 90000.00, 23),
                                                                                            (25, 25, 'bìa mềm', 100000.00, 72000.00, 43),
                                                                                            (26, 26, 'bìa mềm', 178000.00, 73000.00, 94),
                                                                                            (27, 27, 'bìa mềm', 107000.00, 80000.00, 72),
                                                                                            (28, 28, 'bìa mềm', 64000.00, 102000.00, 65),
                                                                                            (29, 29, 'bìa mềm', 63000.00, 122000.00, 68),
                                                                                            (30, 30, 'bìa mềm', 141000.00, 74000.00, 71),
                                                                                            (32, 1, 'bìa mềm', 75000.00, 144000.00, 55),
                                                                                            (33, 2, 'bìa mềm', 84000.00, 63000.00, 32),
                                                                                            (34, 3, 'bìa cứng', 77000.00, 64000.00, 53),
                                                                                            (35, 4, 'bìa cứng', 134000.00, 54000.00, 85),
                                                                                            (36, 5, 'bìa cứng', 101000.00, 59000.00, 78),
                                                                                            (37, 6, 'bìa mềm', 54000.00, 53000.00, 40),
                                                                                            (38, 7, 'bìa mềm', 130000.00, 126000.00, 54),
                                                                                            (39, 8, 'ebook', 145000.00, 124000.00, 13),
                                                                                            (40, 9, 'ebook', 196000.00, 88000.00, 32),
                                                                                            (41, 10, 'ebook', 136000.00, 77000.00, 96),
                                                                                            (42, 11, 'bìa cứng', 90000.00, 68000.00, 43),
                                                                                            (43, 12, 'ebook', 195000.00, 130000.00, 43),
                                                                                            (44, 13, 'bìa mềm', 141000.00, 113000.00, 67),
                                                                                            (45, 14, 'ebook', 124000.00, 138000.00, 27),
                                                                                            (46, 15, 'bìa mềm', 194000.00, 95000.00, 70),
                                                                                            (47, 16, 'bìa cứng', 178000.00, 113000.00, 93),
                                                                                            (48, 17, 'bìa mềm', 75000.00, 88000.00, 73),
                                                                                            (49, 18, 'ebook', 128000.00, 140000.00, 27),
                                                                                            (50, 19, 'bìa mềm', 151000.00, 68000.00, 24),
                                                                                            (51, 20, 'bìa mềm', 169000.00, 127000.00, 80),
                                                                                            (52, 21, 'bìa mềm', 83000.00, 134000.00, 91),
                                                                                            (53, 22, 'bìa cứng', 128000.00, 88000.00, 66),
                                                                                            (54, 23, 'bìa cứng', 62000.00, 119000.00, 53),
                                                                                            (55, 24, 'ebook', 143000.00, 59000.00, 94),
                                                                                            (56, 25, 'ebook', 115000.00, 123000.00, 72),
                                                                                            (57, 26, 'ebook', 52000.00, 118000.00, 72),
                                                                                            (58, 27, 'ebook', 159000.00, 132000.00, 27),
                                                                                            (59, 28, 'bìa mềm', 168000.00, 59000.00, 42),
                                                                                            (60, 29, 'bìa mềm', 72000.00, 52000.00, 98),
                                                                                            (61, 30, 'bìa mềm', 120000.00, 110000.00, 96);


INSERT INTO `book_images` (`id`, `book_id`, `image_url`, `sort_order`) VALUES
                                                                           (1, 1, 'img/1.png', 1),
                                                                           (2, 2, 'img/2.png', 1),
                                                                           (3, 3, 'img/3.png', 1),
                                                                           (4, 4, 'img/4.png', 1),
                                                                           (5, 5, 'img/5.png', 1),
                                                                           (6, 6, 'img/6.png', 1),
                                                                           (7, 7, 'img/7.png', 1),
                                                                           (8, 8, 'img/8.png', 1),
                                                                           (9, 9, 'img/9.png', 1),
                                                                           (10, 10, 'img/10.png', 1),
                                                                           (11, 11, 'img/11.png', 1),
                                                                           (12, 12, 'img/12.png', 1),
                                                                           (13, 13, 'img/13.png', 1),
                                                                           (14, 14, 'img/14.png', 1),
                                                                           (15, 15, 'img/15.png', 1),
                                                                           (16, 16, 'img/16.png', 1),
                                                                           (17, 17, 'img/17.png', 1),
                                                                           (18, 18, 'img/18.png', 1),
                                                                           (19, 19, 'img/19.png', 1),
                                                                           (20, 20, 'img/20.png', 1),
                                                                           (21, 21, 'img/21.png', 1),
                                                                           (22, 22, 'img/22.png', 1),
                                                                           (23, 23, 'img/23.png', 1),
                                                                           (24, 24, 'img/24.png', 1),
                                                                           (25, 25, 'img/25.png', 1),
                                                                           (26, 26, 'img/26.png', 1),
                                                                           (27, 27, 'img/27.png', 1),
                                                                           (28, 28, 'img/28.png', 1),
                                                                           (29, 29, 'img/29.png', 1),
                                                                           (30, 30, 'img/30.png', 1);
