-- ========================================
-- Script tạo dữ liệu ảo cho "Sản phẩm bán chạy"
-- ========================================

-- Xóa dữ liệu demo cũ (nếu có)
DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = 999);
DELETE FROM orders WHERE user_id = 999;

-- 1. Tạo user mẫu (nếu chưa có)
INSERT IGNORE INTO users (id, name, email, password, role, is_active, created_at)
VALUES (999, 'Khách hàng demo', 'demo@bookstore.com', '$2y$10$abcdefghijklmnopqrstuv', 'user', 1, NOW());

-- 2. Tạo 10 đơn hàng đã giao (delivered) trong 2 tháng qua
INSERT INTO orders (user_id, total, shipping_status, shipping_address, shipping_phone, created_at)
VALUES 
(999, 450000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(999, 380000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(999, 520000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(999, 290000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(999, 610000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(999, 340000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(999, 470000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(999, 550000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 40 DAY)),
(999, 420000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 45 DAY)),
(999, 390000, 'delivered', '123 Đường ABC, Quận 1, TP.HCM', '0901111111', DATE_SUB(NOW(), INTERVAL 50 DAY));

-- Lấy ID đơn hàng vừa tạo
SET @order_id_start = LAST_INSERT_ID();

-- 3. Thêm order_items vào các đơn hàng
-- Sách ID 1: Bán 20 cuốn (TOP 1)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 0, v.id, 4, v.price, v.price * 4
FROM book_variants v WHERE v.book_id = 1 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 1, v.id, 5, v.price, v.price * 5
FROM book_variants v WHERE v.book_id = 1 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 2, v.id, 4, v.price, v.price * 4
FROM book_variants v WHERE v.book_id = 1 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 3, v.id, 7, v.price, v.price * 7
FROM book_variants v WHERE v.book_id = 1 LIMIT 1;

-- Sách ID 3: Bán 12 cuốn (TOP 2)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 1, v.id, 4, v.price, v.price * 4
FROM book_variants v WHERE v.book_id = 3 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 6, v.id, 8, v.price, v.price * 8
FROM book_variants v WHERE v.book_id = 3 LIMIT 1;

-- Sách ID 4: Bán 10 cuốn (TOP 4)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 2, v.id, 3, v.price, v.price * 3
FROM book_variants v WHERE v.book_id = 4 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 7, v.id, 7, v.price, v.price * 7
FROM book_variants v WHERE v.book_id = 4 LIMIT 1;

-- Sách ID 5: Bán 9 cuốn (TOP 5)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 3, v.id, 2, v.price, v.price * 2
FROM book_variants v WHERE v.book_id = 5 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 8, v.id, 7, v.price, v.price * 7
FROM book_variants v WHERE v.book_id = 5 LIMIT 1;

-- Sách ID 6: Bán 8 cuốn (TOP 6)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 4, v.id, 3, v.price, v.price * 3
FROM book_variants v WHERE v.book_id = 6 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 9, v.id, 5, v.price, v.price * 5
FROM book_variants v WHERE v.book_id = 6 LIMIT 1;

-- Sách ID 8: Bán 7 cuốn (TOP 7)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 5, v.id, 4, v.price, v.price * 4
FROM book_variants v WHERE v.book_id = 8 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 6, v.id, 3, v.price, v.price * 3
FROM book_variants v WHERE v.book_id = 8 LIMIT 1;

-- Sách ID 9: Bán 6 cuốn (TOP 8)
INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 7, v.id, 3, v.price, v.price * 3
FROM book_variants v WHERE v.book_id = 9 LIMIT 1;

INSERT INTO order_items (order_id, variant_id, quantity, price, subtotal)
SELECT @order_id_start + 8, v.id, 3, v.price, v.price * 3
FROM book_variants v WHERE v.book_id = 9 LIMIT 1;

-- Hoàn thành
SELECT '✅ Đã tạo dữ liệu ảo thành công!' as status;
SELECT 'Tạo 10 đơn hàng delivered với tổng 82 sản phẩm đã bán' as summary;
SELECT 'Top sách bán chạy nhất:' as info;
SELECT '1. Sách ID 1: 20 cuốn' as top1;
SELECT '2. Sách ID 3: 12 cuốn' as top2;
SELECT '3. Sách ID 4: 10 cuốn' as top3;
SELECT '4. Sách ID 5: 9 cuốn' as top4;
SELECT '5. Sách ID 6: 8 cuốn' as top5;
