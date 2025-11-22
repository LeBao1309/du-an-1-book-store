<?php
// User hiện tại (nếu có)
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);

// Đường dẫn asset tương đối (từ index.php)
$ASSET = 'public';

// Đếm số lượng sản phẩm trong giỏ
$cart      = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $item) {
    $cartCount += (int)($item['quantity'] ?? 0);
}


// Xác định danh mục đang được chọn (dùng cho select)
$currentCatId = 'all';
if (isset($_GET['controller']) && $_GET['controller'] === 'category') {
    $currentCatId = (int)($_GET['id'] ?? 0);
}
if ($currentCatId === 0) {
    $currentCatId = 'all';
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WiseDecision Bookstore — Trang chủ</title>
  ...
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="<?= $ASSET; ?>/css/styles.css" />

  <style>
    .wd-toast{position:fixed;right:16px;bottom:16px;background:#16a34a;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.12)}
    .wd-toast.error{background:#dc2626}
  </style>
</head>

<body>
  <?php if (!empty($flash) && !empty($flash['message'])): ?>
    <div class="wd-toast <?= $flash['type']==='error' ? 'error' : '' ?>">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <script>
      setTimeout(function(){
        var t=document.querySelector('.wd-toast');
        if(t) t.remove();
      },3000);
    </script>
  <?php endif; ?>

  <div class="topbar">
    <div class="container">
      <div class="topbar-left">Hotline: 1900 0123 · support@wisedecision.io.vn</div>
      <div class="topbar-right">
        <a href="#">Theo dõi đơn</a>
        <span class="dot"></span>
        <a href="#">Hỗ trợ</a>
      </div>
    </div>
  </div>

  <header class="header">
    <div class="container header-inner">
      <a class="brand" href="index.php?controller=home&action=index">
        <img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> BookStore
      </a>

      <form class="search" method="GET" action="index.php">
        
        <input type="hidden" name="controller" value="category">
        <input type="hidden" name="action" value="index">

        <input type="text" 
               name="q" 
               placeholder="Tìm kiếm sách, tác giả..." 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
               autocomplete="off" />
        
        <button class="btn" type="submit">Tìm</button>
      
      </form>
      <nav class="actions">
        <a href="#" class="nav-icon" title="Yêu thích">❤</a>

        <?php if (!empty($currentUser)): ?>
          <a href="index.php?controller=account&action=profile"
             class="nav-icon"
             title="Tài khoản: <?= htmlspecialchars($currentUser['name'] ?? ''); ?>">👤</a>
          <a href="index.php?controller=auth&action=logout"
             class="nav-icon"
             title="Đăng xuất">🚪</a>
        <?php else: ?>
          <a href="index.php?controller=auth&action=login"
             class="nav-icon"
             title="Tài khoản">👤</a>
        <?php endif; ?>

        <a href="index.php?controller=cart&action=index" class="nav-icon cart" title="Giỏ hàng">
          <span>🛒</span>
          <i class="badge" id="cartBadge">
            <?= (int)$cartCount ?>
          </i>
        </a>
      </nav>
    </div>

    <div class="container navline">
      <ul class="nav">
        <a href="index.php?controller=home&action=index">Trang chủ</a>
        <a href="index.php?controller=category&action=index">Xem danh mục</a>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Giới thiệu</a></li>
        <li><a href="#">Liên hệ</a></li>
      </ul>
    </div>
  </header>

  <aside class="offcanvas" id="sidebar" aria-hidden="true">
    <div class="offcanvas-header">
      <strong>Danh mục</strong>
      <button id="closeSidebar" aria-label="Đóng">✕</button>
    </div>
    
    <ul class="tree">
      <li><a href="index.php?controller=category&action=index&id=1">Sách Khoa học</a></li>
      <li><a href="index.php?controller=category&action=index&id=2">Sách Văn học</a></li>
      <li><a href="index.php?controller=category&action=index&id=3">Sách Kinh tế</a></li>
      <li><a href="index.php?controller=category&action=index&id=4">Sách Kỹ năng sống</a></li>
      <li><a href="index.php?controller=category&action=index&id=5">Sách Thiếu nhi</a></li>
    </ul>
  </aside>

  <?php if (!empty($successMsg)): ?>
    <div id="serverSuccess" data-message="<?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'); ?>"></div>
  <?php endif; ?>

  <main class="container section">
    <?= $content ?>
  </main>

  <footer class="footer">
    <div class="container grid footer-grid">
      <div>
        <a class="brand foot" href="index.php?controller=home&action=index">
          <img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bookstore
        </a>
        <p class="muted">Hiệu sách online của bạn. Sách thật. Giá tốt. Giao nhanh.</p>
      </div>
      <div>
        <h4>Liên hệ</h4>
        <ul>
          <li>1900 0123</li>
          <li>support@wisedecision.io.vn</li>
          <li>08:00–21:00, hằng ngày</li>
        </ul>
      </div>
      <div>
        <h4>Thông tin</h4>
        <ul>
          <li><a href="#">Về chúng tôi</a></li>
          <li><a href="#">Chính sách bảo mật</a></li>
          <li><a href="#">Điều khoản sử dụng</a></li>
        </ul>
      </div>
      <div>
        <h4>Bản tin</h4>
        <form class="newsletter" onsubmit="return false;">
          <input type="email" placeholder="Nhập email của bạn"/>
          <button class="btn solid">Đăng ký</button>
        </form>
      </div>
    </div>
    <div class="copy">© <?= date('Y'); ?> WiseDecision Bookstore</div>
  </footer>

</body>
</html>