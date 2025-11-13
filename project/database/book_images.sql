-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 13, 2025 lúc 04:16 AM
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
-- Cấu trúc bảng cho bảng `book_images`
--

CREATE TABLE `book_images` (
  `id` int NOT NULL,
  `book_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0' COMMENT '0 là thumbnail, 1 2 3,...là ảnh trong sản phẩm chi tiết'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `book_images`
--

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

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `book_images`
--
ALTER TABLE `book_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_book_images_book` (`book_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `book_images`
--
ALTER TABLE `book_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `book_images`
--
ALTER TABLE `book_images`
  ADD CONSTRAINT `fk_book_images_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
