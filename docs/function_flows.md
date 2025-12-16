# Chức năng & Logic Luồng Chính (tóm tắt)

## Xác thực & tài khoản
- **Đăng nhập**: kiểm `users.is_active=1`, khớp mật khẩu; lưu `$_SESSION['user']`.
- **Đăng ký**: kiểm email hợp lệ/duy nhất, mật khẩu ≥6, khớp confirm, tạo user role `user`.
- **Đăng xuất**: xóa session user/admin, regen session id.
- **Quên mật khẩu (user/admin)**: tạo token `password_resets`, gửi email reset (link có token, hiệu lực 60 phút). Form reset kiểm token còn hạn, đặt mật khẩu mới, `password_resets.used_at`, flash login.

## Q&A / Bình luận
- **Bình luận/đánh giá**: user đã mua (đơn `delivered`) mới được comment; lưu rating vào `comments`, gọi `CommentModel::recalculateBookRating` cập nhật `books.rating_avg/review_count`. Admin xóa comment cập nhật lại rating.
- **Hỏi đáp sản phẩm**: bảng `questions/answers/answer_votes`. Tạo câu hỏi, trả lời (shop đánh dấu `is_shop_answer`). Bỏ cột up/down local; đếm vote qua `answer_votes` (`vote_type` up/down). Admin xem/xóa/update `questions/answers`.

## Cart → Checkout → Payment
- Giỏ hàng session theo `variant_id => quantity`; hydrate bằng giá/ảnh từ DB.
- **Checkout**: cần địa chỉ mặc định và số điện thoại; áp mã coupon (`CouponModel::validateForOrder`), tính `final_total = total - discount`.
- **COD**: tạo `orders` với `shipping_status='pending'`, `payment_status='pending'`, thêm `order_items`, ghi `payment` (method `cod`, status `pending`). Sau đó xóa cart + coupon + shipping session.
- **VNPay**: build URL có `return` `?controller=payment&action=vnpayReturn`. Khi VNPay trả về `ResponseCode=00`, tạo `orders` với `payment_status='paid'`, payment method `card`, provider `VNPay`, lưu transaction_code; ghi `payment` (status `success`). Nếu fail, render thất bại, không tạo order.

## Đơn hàng (user)
- Lịch sử: join `orders` + `payment` theo user. User chỉ thấy đơn của mình.
- Xác nhận nhận hàng: `OrderModel::confirmReceived` chuyển `shipping_status` `shipped -> delivered`, nếu `payment_status=pending` thì set `paid`.
- Hủy đơn (user): chỉ khi `shipping_status='pending'`, set `cancelled`, lưu lý do.

## Đơn hàng (admin)
- **Lọc**: keyword (ID/email), `shipping_status`, `payment_status` (`pending/paid/failed/refunded`), `payment_method`, `channel` (provider), `from_date/to_date`. Phân trang 20 bản ghi, order `created_at DESC`.
- **Trạng thái vận chuyển**: cho phép chuyển `pending -> processing/cancelled`; `processing -> shipped/delivered/cancelled`; `shipped -> delivered/cancelled`; chặn nếu đã `delivered` hoặc `cancelled`. Hủy: nếu `payment_status='paid'` thì set `refunded`, ngược lại `pending`.
- **Payment badge** hiển thị theo `orders.payment_status` (không theo bảng `payment.status`).
- **Xem chi tiết**: panel phải hiển thị meta, items, tổng, trạng thái; nút cập nhật trạng thái (kèm lý do khi hủy).

## Danh mục/biến thể/sách (admin)
- CRUD danh mục dạng tree (parent_id), modal tạo, panel cập nhật sticky.
- Biến thể sách: list + panel xem nhanh; soft-delete/restore qua action/CSRF.

## Email
- Template reset mật khẩu HTML + CTA (brand màu `#0ea5a5`), fallback text. Gửi qua `EmailService::sendResetTemplate` (PHPMailer nếu có; nếu chưa cấu hình SMTP thì lưu log `storage/logs/dev_mail.log`).
