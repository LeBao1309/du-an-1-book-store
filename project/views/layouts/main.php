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

// Xác định danh mục đang được chọn (dùng cho select menu nếu cần)
$currentCatId = 'all';
if (isset($_GET['controller']) && $_GET['controller'] === 'category') {
    $currentCatId = (int)($_GET['id'] ?? 0);
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WiseDecision Bookstore — Thế giới sách</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="<?= $ASSET; ?>/css/styles.css" />

  <style>
    /* Toast thông báo */
    .wd-toast{position:fixed;right:16px;bottom:16px;background:#16a34a;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.12); z-index: 9999;}
    .wd-toast.error{background:#dc2626}
    
    /* Style bổ sung cho icon mạng xã hội ở footer */
    .social-icon {
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        color: white !important;
        text-decoration: none;
        font-weight: bold;
        transition: transform 0.2s;
    }
    .social-icon:hover { transform: translateY(-3px); }
    
    /* Style cho icon thanh toán */
    .pay-icon {
        background: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        color: #333;
        font-weight: bold;
    }
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
      }, 3000);
    </script>
  <?php endif; ?>

  <header class="header">
    <div class="container header-inner">
      <a class="brand" href="index.php?controller=home&action=index">
        <img src="<?= $ASSET; ?>/img/logo.png" 
             alt="WiseDecision Bookstore" 
             style="height: 50px; width: auto;"/> 
      </a>

      <form class="search" method="GET" action="index.php">
        <input type="hidden" name="controller" value="category">
        <input type="hidden" name="action" value="index">
        <input type="text" name="q" placeholder="Tìm kiếm sách, tác giả..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off" />
        <button class="btn" type="submit">Tìm</button>
      </form>

      <nav class="actions">
        <a href="index.php?controller=account&action=wishlist" class="nav-icon" title="Yêu thích">❤</a>

        <?php if (!empty($currentUser)): ?>
          <a href="index.php?controller=account&action=profile" class="nav-icon" title="Tài khoản: <?= htmlspecialchars($currentUser['name'] ?? ''); ?>">👤</a>
          <a href="index.php?controller=auth&action=logout" class="nav-icon" title="Đăng xuất">🚪</a>
        <?php else: ?>
          <a href="index.php?controller=auth&action=login" class="nav-icon" title="Đăng nhập">👤</a>
        <?php endif; ?>

        <a href="index.php?controller=cart&action=index" class="nav-icon cart" title="Giỏ hàng">
          <span>🛒</span>
          <?php if($cartCount > 0): ?>
            <i class="badge" id="cartBadge"><?= $cartCount ?></i>
          <?php endif; ?>
        </a>
      </nav>
    </div>

    <div class="container navline">
      <ul class="nav">
        <li><a href="index.php?controller=home&action=index">Trang chủ</a></li>
        <li><a href="index.php?controller=category&action=index">Danh mục</a></li>
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

  <main class="container section" style="min-height: 500px;">
    <?= $content ?>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        
        <div>
          <a class="brand foot" href="index.php?controller=home&action=index">
             <img src="<?= $ASSET; ?>/img/amban.png" 
                  alt="WiseDecision Bookstore Logo Âm Bản" 
                  style="height: 60px; width: auto; margin-bottom: 20px; display: block;"/> 
          </a>

          <p style="opacity: 0.8;">
            Nơi hội tụ những cuốn sách giá trị nhất. Chúng tôi tin rằng mỗi cuốn sách là một người bạn, một người thầy vĩ đại.
          </p>
          
          <div style="margin-top: 25px; display: flex; gap: 15px;">
             <a href="#" class="social-icon" style="background:#3b5998;" title="Facebook">f</a>
             <a href="#" class="social-icon" style="background:#E1306C;" title="Instagram">ig</a>
             <a href="#" class="social-icon" style="background:#1DA1F2;" title="Twitter">tw</a>
          </div>
        </div>

        <div>
          <h4>Khám phá</h4>
          <ul>
            <li><a href="index.php?controller=category&action=index&id=1">Sách Khoa học</a></li>
            <li><a href="index.php?controller=category&action=index&id=2">Sách Văn học</a></li>
            <li><a href="index.php?controller=category&action=index&id=3">Sách Kinh tế</a></li>
            <li><a href="index.php?controller=category&action=index&id=5">Sách Thiếu nhi</a></li>
          </ul>
        </div>

        <div>
          <h4>Hỗ trợ</h4>
          <ul>
            <li><a href="#">Tra cứu đơn hàng</a></li>
            <li><a href="#">Chính sách đổi trả</a></li>
            <li><a href="#">Phương thức thanh toán</a></li>
            <li><a href="#">Liên hệ hợp tác</a></li>
          </ul>
        </div>

        <div>
          <h4>Đăng ký nhận tin</h4>
          <p>Nhận mã giảm giá 10% cho đơn hàng đầu tiên.</p>
          <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Cảm ơn bạn!');">
            <input type="email" placeholder="Email..." required>
            <button type="submit">Gửi</button>
          </form>
          
          <h4 style="margin-top: 30px; font-size: 14px; margin-bottom: 15px;">Thanh toán an toàn</h4>
          <div style="display: flex; gap: 8px;">
             <span class="pay-icon">VISA</span>
             <span class="pay-icon">MOMO</span>
             <span class="pay-icon">COD</span>
          </div>
        </div>

      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="container">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
           <span>&copy; <?= date('Y') ?> BookStore. All rights reserved.</span>
           <span>
               <a href="#" style="color: inherit; margin-right: 15px;">Điều khoản</a>
               <a href="#" style="color: inherit;">Bảo mật</a>
           </span>
        </div>
      </div>
    </div>
  </footer>

  <script>
    const sidebar = document.getElementById('sidebar');
    const closeBtn = document.getElementById('closeSidebar');
    // Nút mở menu mobile (nếu bạn thêm vào header sau này)
    // const openBtn = document.querySelector('.btn-menu-mobile'); 

    if(closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
    }
  </script>
</body>
</html>