# 📖 Book Management System - Tổng quan dự án

## 🎯 MỤC TIÊU DỰ ÁN

**WHAT**: Xây dựng hệ thống quản lý sách trực tuyến với đầy đủ chức năng từ hiển thị, tìm kiếm đến đặt hàng.

**WHY**: 
- Học cách làm việc nhóm với Git
- Thực hành OOP & MVC pattern
- Trải nghiệm quy trình phát triển web thực tế
- Phân chia công việc theo module rõ ràng

**WHO**: Nhóm 4 người (1 lead + 3 members mới)

---

## 🏗️ KIẾN TRÚC DỰ ÁN

### Tech Stack
```
Backend:  PHP 7.4+ (OOP thuần)
Frontend: HTML5, CSS3, JavaScript (Vanilla)
Database: MySQL 5.7+ / MariaDB
Pattern:  MVC đơn giản
```

### Cấu trúc thư mục
```
book_management/
├── app/
│   ├── controllers/     # Xử lý logic nghiệp vụ
│   ├── models/          # Tương tác database
│   ├── views/           # Giao diện người dùng
│   └── core/            # Core framework (Router, Database, Controller base)
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── index.php        # Entry point
├── config/
│   └── database.php     # Cấu hình DB
└── docs/                # Tài liệu này
```

---

## 📋 PHÂN CHIA MODULE (6 TUẦN)

### ✅ Tuần 1: Usecase Figma HTML-CSS (Hoàn thành)
- Thiết kế giao diện Figma
- Chuyển sang HTML/CSS tĩnh

### 🔄 Tuần 2: Database + Trang chủ + Trang chi tiết + Trang sản phẩm (Đang làm)
**Module 1 - Database Design**
- Schema design (✅ Đã có)
- Tạo database & tables
- Insert dữ liệu mẫu

**Module 2 - Trang chủ (Homepage)**
- Model: BookModel, CategoryModel
- Controller: HomeController
- View: Hiển thị sách nổi bật, danh mục
- API: GET /api/books/featured

**Module 3 - Trang chi tiết sách (Book Detail)**
- Model: BookModel, CommentModel
- Controller: BookController
- View: Thông tin sách, bình luận, đánh giá
- API: GET /api/books/:slug

**Module 4 - Trang danh sách sản phẩm (Book Listing)**
- Model: BookModel
- Controller: BookListController
- View: Lọc theo category, tìm kiếm, phân trang
- API: GET /api/books?category=&search=&page=

### Tuần 3: Giỏ hàng + Đăng nhập + Đăng ký (Sắp tới)

### Tuần 4: Admin CRUD sản phẩm + CRUD danh mục + Đơn hàng

### Tuần 5: Quản lý trang thái + CRUD mã giảm giá + Bình luận + Đánh giá

### Tuần 6: Tổng kết, Kiểm lỗi, Hosting, Domain

---

## 🔑 QUY TẮC LÀM VIỆC

### 1. Nguyên tắc SOLO Module
- **1 người = 1 module hoàn chỉnh** (Backend → Frontend)
- Không được làm chung 1 file cùng lúc
- Mỗi module có folder riêng trong `app/controllers`, `app/models`, `app/views`

### 2. Git Workflow
```bash
# Bước 1: Lấy code mới nhất
git pull origin main

# Bước 2: Tạo nhánh cho module của mình
git checkout -b feature/homepage

# Bước 3: Code xong, commit
git add .
git commit -m "feat: implement homepage module"

# Bước 4: Push lên Git
git push origin feature/homepage

# Bước 5: Tạo Pull Request để leader review
```

### 3. Coding Standards
- **Class names**: PascalCase (`BookController`, `BookModel`)
- **Method names**: camelCase (`getBookById()`, `createOrder()`)
- **Database columns**: snake_case (`created_at`, `user_id`)
- **File names**: PascalCase cho class, lowercase cho view (`BookController.php`, `detail.php`)

### 4. Commit Message Format
```
feat: thêm tính năng mới
fix: sửa lỗi
docs: cập nhật tài liệu
style: format code
refactor: tái cấu trúc code
```

---

## 📊 TIẾN ĐỘ HIỆN TẠI

| Tuần | Nội dung | Trạng thái | Người phụ trách |
|------|----------|-----------|----------------|
| 1 | Figma + HTML/CSS | ✅ Hoàn thành | Team |
| 2 | Database + 3 trang | 🔄 Đang làm | Phân công sau |
| 3-6 | ... | ⏳ Chưa bắt đầu | TBD |

---

## 🆘 LIÊN HỆ HỖ TRỢ

**Nếu gặp vấn đề:**
1. Đọc tài liệu trong folder `docs/`
2. Hỏi trong group chat
3. Tạo issue trên Git
4. Liên hệ leader

**Tài liệu tham khảo:**
- [01_DATABASE_SCHEMA.md](./01_DATABASE_SCHEMA.md) - Hiểu database
- [02_MVC_GUIDE.md](./02_MVC_GUIDE.md) - Cách làm việc với MVC
- [03_API_CONVENTION.md](./03_API_CONVENTION.md) - Quy ước API
- [04_GIT_WORKFLOW.md](./04_GIT_WORKFLOW.md) - Hướng dẫn Git chi tiết
- [05_CODING_EXAMPLES.md](./05_CODING_EXAMPLES.md) - Ví dụ code mẫu

---

## ✅ CHECKLIST TRƯỚC KHI BẮT ĐẦU

- [ ] Đã đọc hết tài liệu trong `docs/`
- [ ] Đã cài XAMPP/WAMP/MAMP
- [ ] Đã import database từ `duan1.sql`
- [ ] Đã clone Git repository
- [ ] Đã tạo branch cho module của mình
- [ ] Đã hiểu rõ module mình phụ trách

---

**Cập nhật lần cuối**: Tuần 2  
**Version**: 1.0  
**Người tạo**: Team Lead