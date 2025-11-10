<?php
// (File này được gọi bởi AuthController [cite: "project/app/controllers/AuthController.php"])
// (Nó nhận các biến: $error, $success, $csrfToken)
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Đăng nhập — WiseDecision Bookstore</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- 1. TÍCH HỢP: Dùng BASE_URL [cite: "project/config/database.php"] trỏ đến file CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css" />
  <style>
    /* Thêm style cho thông báo lỗi/thành công */
    .form-message {
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .form-message.error {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
    .form-message.success {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body>
  <!-- (Header từ template) -->
  <div class="topbar"><div class="container"><div>Hotline: 1900 0123 · support@wisedecision.io.vn</div><div><a href="#">Theo dõi đơn</a><span class="dot"></span><a href="#">Hỗ trợ</a></div></div></div>
  <header class="header">
    <div class="container header-inner">
      <a class="brand" href="<?php echo BASE_URL; ?>/"><img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bo⊑kStore</a>
      <div class="search"><select class="cat-select"><option>Tất cả danh mục</option></select><input type="text" placeholder="Tìm kiếm sách, tác giả..." /><button class="btn">Tìm</button></div>
      <nav class="actions"><a href="#" class="nav-icon">❤</a><a href="<?php echo BASE_URL; ?>/login" class="nav-icon">👤</a><a href="<?php echo BASE_URL; ?>/cart" class="nav-icon cart"><span>🛒</span><i id="cartBadge" class="badge">0</i></a></nav>
    </div>
    <div class="container navline">
      <button class="btn btn-cat" id="btnCat">☰ Danh mục</button>
      <ul class="nav"><li><a href="<?php echo BASE_URL; ?>/">Trang chủ</a></li><li><a class="active" href="#">Sản phẩm</a></li><li><a href="#">Blog</a></li><li><a href="#">Giới thiệu</a></li><li><a href="#">Liên hệ</a></li></ul>
    </div>
  </header>
  
  <main class="container section" style="max-width:520px">
    <div class="card" style="padding:16px">
      <h1>Đăng nhập</h1>
      
      <!-- 2. TÍCH HỢP: Hiển thị thông báo (Lỗi/Thành công) -->
      <?php // Các biến này được truyền từ AuthController::showLogin() [cite: "project/app/controllers/AuthController.php"] ?>
      <?php if (isset($error) && $error): ?>
          <p class="form-message error"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <?php if (isset($success) && $success): ?>
          <p class="form-message success"><?php echo htmlspecialchars($success); ?></p>
      <?php endif; ?>

      <!-- 
        3. TÍCH HỢP: Sửa lại Form
         - Xóa 'onsubmit'
         - Thêm 'action' và 'method'
         - Thêm 'name' cho các input
         - Thêm CSRF token
      -->
      <form action="<?php echo BASE_URL; ?>/login" method="POST">
        
        <!-- BẢO MẬT: Thêm CSRF token (từ AuthController [cite: "project/app/controllers/AuthController.php"]) -->
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required class="text" style="width:100%; margin-top: 5px;">
        
        <label for="password" style="display:block; margin-top:10px;">Mật khẩu</label>
        <input type="password" id="password" name="password" required class="text" style="width:100%; margin-top: 5px;">
        
        <button class="btn solid" style="margin-top:10px;width:100%">Đăng nhập</button>
      </form>
      <hr/>
      <p>Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>/register" style="color:var(--brand);">Đăng ký ngay</a></p>
    </div>
  </main>

  <footer class="footer">
    <!-- (Nội dung footer từ template) -->
  </footer>
  
  <!-- (Tạm thời vô hiệu hóa JS của template) -->
  <!-- <script src="./assets/js/app.js"></script> -->
  <!-- <script src="./assets/js/shop.js"></script> -->
</body>
</html>