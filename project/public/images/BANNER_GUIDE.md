# Hướng dẫn thêm Banner Popup

## Bước 1: Chuẩn bị ảnh banner
1. Tạo hoặc tải ảnh banner với kích thước khuyến nghị: **700x350px**
2. Tên file: `banner-popup.jpg` (hoặc .png)
3. Copy vào thư mục: `project/public/images/`

## Bước 2: Nội dung gợi ý cho banner
- Sản phẩm nổi bật (sách bán chạy)
- Khuyến mãi giảm giá
- Chương trình ưu đãi đặc biệt
- Sự kiện ra mắt sách mới

## Bước 3: Tùy chỉnh nội dung popup
Chỉnh sửa trong file: `project/views/layouts/main.php`

Tìm đoạn:
```html
<h2>🎉 SALE 12.12</h2>
<h3>Mua sách giảm đến <span class="highlight">50%</span></h3>
```

Thay đổi theo nội dung quảng cáo của bạn!

## Lưu ý:
- Banner chỉ hiện 1 lần mỗi session (đóng thì không hiện lại cho đến khi tắt trình duyệt)
- Tự động hiện sau 1 giây khi vào trang
- Click overlay (vùng tối) hoặc nút X để đóng

## Test thử:
1. Mở http://localhost/du-an-1-book-store/project/
2. Banner sẽ tự động hiện sau 1 giây
3. Đóng và reload trang để kiểm tra không hiện lại
