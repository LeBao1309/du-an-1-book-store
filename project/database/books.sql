-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 13, 2025 lúc 03:17 AM
-- Phiên bản máy phục vụ: 8.4.3
-- Phiên bản PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `du_an_1_book_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_desc` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_avg` decimal(3,2) DEFAULT '0.00',
  `review_count` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `books`
--

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

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_book_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
