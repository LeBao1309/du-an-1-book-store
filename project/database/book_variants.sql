-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 13, 2025 lúc 05:20 AM
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
-- Cấu trúc bảng cho bảng `book_variants`
--

CREATE TABLE `book_variants` (
  `id` int NOT NULL,
  `book_id` int NOT NULL,
  `format` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g., hardcover, paperback',
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `book_variants`
--

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

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `book_variants`
--
ALTER TABLE `book_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_variant_book` (`book_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `book_variants`
--
ALTER TABLE `book_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `book_variants`
--
ALTER TABLE `book_variants`
  ADD CONSTRAINT `fk_variant_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
