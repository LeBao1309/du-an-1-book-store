
-- bảng categories
INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`) VALUES
(1, 'Sách Khoa học', 'sach-khoa-hoc', NULL),
(2, 'Sách Văn học', 'sach-van-hoc', NULL),
(3, 'Sách Kinh tế', 'sach-kinh-te', NULL),
(4, 'Sách Kỹ năng sống', 'sach-ky-nang-song', NULL),
(5, 'Sách Thiếu nhi', 'sach-thieu-nhi', NULL);

-- bảng books
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

-- bảng book_variants
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

-- bảng book_images
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

-- ===============================================================
-- DATA MẪU BỔ SUNG (authors, publisher, users, address, orders, ...)
-- Chạy SAU script_insert_data_duan1.sql hiện tại
-- Password mẫu cho user: 123456
-- ===============================================================

    use du_an_1_book_store;
-- 1) AUTHORS
INSERT INTO authors (id, name, slug) VALUES
(1, 'Stephen Hawking', 'stephen-hawking'),
(2, 'Paulo Coelho', 'paulo-coelho'),
(3, 'Haruki Murakami', 'haruki-murakami'),
(4, 'Nguyễn Nhật Ánh', 'nguyen-nhat-anh'),
(5, 'Dale Carnegie', 'dale-carnegie'),
(6, 'Napoleon Hill', 'napoleon-hill'),
(7, 'Adam Smith', 'adam-smith'),
(8, 'Karl Marx', 'karl-marx'),
(9, 'J.K. Rowling', 'jk-rowling'),
(10, 'Fyodor Dostoevsky', 'fyodor-dostoevsky');

-- 2) PUBLISHER
INSERT INTO publisher (id, name, slug) VALUES
(1, 'NXB Trẻ', 'nxb-tre'),
(2, 'NXB Kim Đồng', 'nxb-kim-dong'),
(3, 'NXB Lao Động', 'nxb-lao-dong'),
(4, 'NXB Nhã Nam', 'nxb-nha-nam'),
(5, 'NXB Tổng Hợp', 'nxb-tong-hop');

-- 3) BOOK_AUTHORS (N-N)
INSERT INTO book_authors (id, book_id, author_id) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 7),
(4, 4, 7),
(5, 5, 1),
(6, 6, 1),
(7, 7, 2),
(8, 8, 5),
(9, 9, 3),
(10, 10, 6),
(11, 11, 2),
(12, 12, 10),
(13, 13, 7),
(14, 14, 8),
(15, 15, 6),
(16, 16, 6),
(17, 17, 3),
(18, 18, 7),
(19, 19, 5),
(20, 20, 4),
(21, 21, 5),
(22, 22, 5),
(23, 23, 5),
(24, 24, 5),
(25, 25, 4),
(26, 26, 4),
(27, 27, 4),
(28, 28, 9),
(29, 29, 4),
(30, 30, 4);

-- 4) BOOK_PUBLISHER (mỗi sách 1 NXB)
INSERT INTO book_publisher (id, book_id, publisher_id) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 4),
(8, 8, 4),
(9, 9, 4),
(10, 10, 4),
(11, 11, 4),
(12, 12, 4),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1),
(20, 20, 1),
(21, 21, 1),
(22, 22, 1),
(23, 23, 1),
(24, 24, 1),
(25, 25, 2),
(26, 26, 2),
(27, 27, 2),
(28, 28, 2),
(29, 29, 2),
(30, 30, 2);


-- 5) USERS
-- bcrypt cho password 123456 (PHP password_verify dùng được)
INSERT INTO users (id, name, email, password, role, is_active) VALUES
(1, 'Lê Quang Gia Bảo', 'lbaol1309@gmail.com', '$2y$10$H9EJpWM18r4.KpV7H5ZoeONIAVhYoifdaP.4KvaRxXuJJm9t46xLe', 'user', 1),
(2, 'Lê Quang Gia Bảo', 'lbaol13091@gmail.com', '$2y$10$0IOmHi0MAwZmAg66jkAHEe8Ful.um7I1AF3SDU2AfhOO5ewC8RZzq', 'user', 1),
(3, 'Trần Thị B', 'b@wise.local', '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1),
(4, 'Lê Văn C', 'c@wise.local', '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1),
(5, 'Phạm Thị D', 'd@wise.local', '$2y$10$pjC161ShZr1Fkj711onlB.zP45o22OFL2/EpgUdnYyqV6zFHPcAHW', 'user', 1);

-- 6) USER_ADDRESS (>=2 địa chỉ / user)
INSERT INTO user_address (id, user_id, full_address, shipping_phone, is_default) VALUES
(1, 2, '12 Nguyễn Trãi, Q1, TP.HCM', '0909000001', 1),
(2, 2, '45 Lê Lợi, Q1, TP.HCM', '0909000002', 0),
(3, 3, '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '0909000003', 1),
(4, 3, '102 Phan Xích Long, Phú Nhuận, TP.HCM', '0909000004', 0),
(5, 4, '15 Trần Hưng Đạo, Đà Nẵng', '0909000005', 1),
(6, 4, '200 Nguyễn Văn Linh, Đà Nẵng', '0909000006', 0),
(7, 5, '33 Võ Thị Sáu, Hà Nội', '0909000007', 1),
(8, 5, '77 Xuân Thủy, Cầu Giấy, Hà Nội', '0909000008', 0);

-- 7) COMMENTS (review mẫu)
INSERT INTO comments (id, book_id, user_id, content, rating) VALUES
(1, 1, 2, 'Nhận xét mẫu cho sách 1 bởi user 2', 4),
(2, 1, 3, 'Nhận xét mẫu cho sách 1 bởi user 3', 4),
(3, 1, 4, 'Nhận xét mẫu cho sách 1 bởi user 4', 3),
(4, 2, 2, 'Nhận xét mẫu cho sách 2 bởi user 2', 4),
(5, 2, 3, 'Nhận xét mẫu cho sách 2 bởi user 3', 5),
(6, 2, 4, 'Nhận xét mẫu cho sách 2 bởi user 4', 4),
(7, 7, 2, 'Nhận xét mẫu cho sách 7 bởi user 2', 4),
(8, 7, 3, 'Nhận xét mẫu cho sách 7 bởi user 3', 4),
(9, 7, 4, 'Nhận xét mẫu cho sách 7 bởi user 4', 4),
(10, 9, 2, 'Nhận xét mẫu cho sách 9 bởi user 2', 5),
(11, 9, 3, 'Nhận xét mẫu cho sách 9 bởi user 3', 3),
(12, 9, 4, 'Nhận xét mẫu cho sách 9 bởi user 4', 5),
(13, 15, 2, 'Nhận xét mẫu cho sách 15 bởi user 2', 3),
(14, 15, 3, 'Nhận xét mẫu cho sách 15 bởi user 3', 4),
(15, 15, 4, 'Nhận xét mẫu cho sách 15 bởi user 4', 3),
(16, 19, 2, 'Nhận xét mẫu cho sách 19 bởi user 2', 3),
(17, 19, 3, 'Nhận xét mẫu cho sách 19 bởi user 3', 5),
(18, 19, 4, 'Nhận xét mẫu cho sách 19 bởi user 4', 4),
(19, 25, 2, 'Nhận xét mẫu cho sách 25 bởi user 2', 4),
(20, 25, 3, 'Nhận xét mẫu cho sách 25 bởi user 3', 5),
(21, 25, 4, 'Nhận xét mẫu cho sách 25 bởi user 4', 3),
(22, 28, 2, 'Nhận xét mẫu cho sách 28 bởi user 2', 5),
(23, 28, 3, 'Nhận xét mẫu cho sách 28 bởi user 3', 4),
(24, 28, 4, 'Nhận xét mẫu cho sách 28 bởi user 4', 4);

-- 8) WISHLIST
INSERT INTO wishlist (id, book_id, user_id) VALUES
(1, 7, 2),
(2, 15, 2),
(3, 19, 2),
(4, 1, 3),
(5, 2, 3),
(6, 25, 3),
(7, 9, 4),
(8, 28, 4);

-- 9) ORDERS (snapshot địa chỉ)
INSERT INTO orders (id, user_id, user_address_id, total, shipping_status, shipping_address, shipping_phone, note) VALUES
(1, 2, 1, 360000.00, 'delivered', '12 Nguyễn Trãi, Q1, TP.HCM', '0909000001', 'Giao giờ hành chính'),
(2, 3, 3, 412000.00, 'shipped', '88 Điện Biên Phủ, Bình Thạnh, TP.HCM', '0909000003', NULL),
(3, 2, 2, 298000.00, 'processing', '45 Lê Lợi, Q1, TP.HCM', '0909000002', NULL),
(4, 4, 5, 118000.00, 'delivered', '15 Trần Hưng Đạo, Đà Nẵng', '0909000005', NULL),
(5, 5, 7, 283000.00, 'shipped', '33 Võ Thị Sáu, Hà Nội', '0909000007', 'Giao giờ hành chính'),
(6, 3, 4, 416000.00, 'processing', '102 Phan Xích Long, Phú Nhuận, TP.HCM', '0909000004', NULL);

-- 10) ORDER_ITEMS
INSERT INTO order_items (id, order_id, variant_id, quantity, price, subtotal) VALUES
(1, 1, 1, 2, 62000.00, 124000.00),
(2, 1, 7, 1, 99000.00, 99000.00),
(3, 1, 19, 1, 137000.00, 137000.00),

(4, 2, 15, 1, 124000.00, 124000.00),
(5, 2, 16, 2, 144000.00, 288000.00),

(6, 3, 28, 1, 102000.00, 102000.00),
(7, 3, 29, 1, 122000.00, 122000.00),
(8, 3, 30, 1, 74000.00, 74000.00),

(9, 4, 34, 1, 64000.00, 64000.00),
(10, 4, 35, 1, 54000.00, 54000.00),

(11, 5, 47, 1, 113000.00, 113000.00),
(12, 5, 59, 2, 59000.00, 118000.00),
(13, 5, 60, 1, 52000.00, 52000.00),

(14, 6, 21, 1, 106000.00, 106000.00),
(15, 6, 22, 1, 90000.00, 90000.00),
(16, 6, 23, 2, 110000.00, 220000.00);

INSERT INTO payment (order_id, payment_method) VALUES
                                                   (1, 'cod'),
                                                   (2, 'card'),
                                                   (3, 'cod'),
                                                   (4, 'card'),
                                                   (5, 'cod'),
                                                   (6, 'card');

