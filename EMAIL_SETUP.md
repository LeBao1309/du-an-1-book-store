# ✅ HƯỚNG DẪN CẤU HÌNH EMAIL - NHANH GỌN

## Chỉ cần 3 bước:

### 1️⃣ Lấy App Password từ Gmail
- Vào: https://myaccount.google.com/apppasswords
- Bật 2-Step Verification (nếu chưa có)
- Tạo App Password cho "Mail" → Copy 16 ký tự

### 2️⃣ Điền vào ContactController.php
Mở file `project/controllers/ContactController.php`, tìm dòng 15:
```php
$this->emailService->setAppPassword('YOUR_APP_PASSWORD');
```
Thay `YOUR_APP_PASSWORD` bằng mật khẩu vừa copy (không có khoảng trắng).

### 3️⃣ Test thử
- Vào: http://localhost/du-an-1-book-store/project/?controller=contact
- Điền form và gửi
- Check email **toanvotruong276@gmail.com** (có thể ở Spam)

---

## ⚠️ QUAN TRỌNG: Bảo mật

**KHÔNG commit App Password lên GitHub!**

Tạo file `.gitignore` ở thư mục gốc:
```
.env
/project/config/email.php
*.log
```

---

## 🎯 Kết quả

Khi có người gửi form liên hệ, bạn sẽ nhận email đẹp với:
- ✅ Thông tin người gửi (tên, email, SĐT)
- ✅ Nội dung tin nhắn
- ✅ Thời gian gửi
- ✅ Format HTML chuyên nghiệp

---

**Hướng dẫn chi tiết:** Xem file `docs/GMAIL_APP_PASSWORD_GUIDE.md`
