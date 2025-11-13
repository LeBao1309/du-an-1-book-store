<?php
$BASE = defined('BASE_URL') ? constant('BASE_URL') : '';
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);

// $ASSET = rtrim($BASE, '/');
// if (!preg_match('~/public$~', $ASSET)) {
//     $ASSET .= '/public';
// }
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
    <script>setTimeout(function(){var t=document.querySelector('.wd-toast'); if(t) t.remove();},3000);</script>
  <?php endif; ?>
  ...

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
      <a class="brand" href="<?= $BASE; ?>/">
        <img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bo⊑kStore
      </a>

      <div class="search">
        <?php
          // Thêm logic này để biết ID nào đang được chọn
          $currentCatId = 'all'; // Mặc định
          if (isset($_GET['c']) && $_GET['c'] === 'category') {
            $currentCatId = (int)($_GET['id'] ?? 0);
          }
          // Nếu id=0 (trang "Tất cả sản phẩm") thì vẫn là 'all'
          if ($currentCatId === 0) $currentCatId = 'all';
        ?>

        <select class="cat-select" aria-label="Chọn danh mục" id="headerCatSelect">
          <option value="all" <?= $currentCatId === 'all' ? 'selected' : '' ?>>Tất cả danh mục</option>
          <option value="1" <?= $currentCatId === 1 ? 'selected' : '' ?>>Sách Khoa học</option>
          <option value="2" <?= $currentCatId === 2 ? 'selected' : '' ?>>Sách Văn học</option>
          <option value="3" <?= $currentCatId === 3 ? 'selected' : '' ?>>Sách Kinh tế</option>
          <option value="4" <?= $currentCatId === 4 ? 'selected' : '' ?>>Sách Kỹ năng sống</option>
          <option value="5" <?= $currentCatId === 5 ? 'selected' : '' ?>>Sách Thiếu nhi</option>
        </select>
        
        <input type="text" placeholder="Tìm kiếm sách, tác giả..." />
        <button class="btn" id="btnSearch">Tìm</button>
      </div>

      <nav class="actions">
        <a href="#" class="nav-icon" title="Yêu thích">❤</a>

        <?php if (!empty($currentUser)): ?>
          <a href="?c=home&a=profile" class="nav-icon" title="Tài khoản: <?= htmlspecialchars($currentUser['name'] ?? ''); ?>">👤</a>
          <a href="?c=auth&a=logout" class="nav-icon" title="Đăng xuất">🚪</a>
        <?php else: ?>
          <a href="?c=auth&a=login" class="nav-icon" title="Tài khoản">👤</a>
        <?php endif; ?>

        <a href="<?= $BASE; ?>/cart" class="nav-icon cart" title="Giỏ hàng">
          <span>🛒</span><i class="badge" id="cartBadge">0</i>
        </a>
      </nav>
    </div>

    <div class="container navline">
      <button class="btn btn-cat" id="btnCat">☰ Danh mục</button>
      <ul class="nav">
        <li><a class="active" href="<?= $BASE; ?>/">Trang chủ</a></li>
        <li><a href="?c=category&a=index&id=1">Sản phẩm</a></li> 
        <li><a href="<?= $BASE; ?>/blog">Blog</a></li>
        <li><a href="<?= $BASE; ?>/about">Giới thiệu</a></li>
        <li><a href="<?= $BASE; ?>/contact">Liên hệ</a></li>
      </ul>
    </div>
  </header>

  <aside class="offcanvas" id="sidebar" aria-hidden="true">
    <div class="offcanvas-header">
      <strong>Danh mục</strong>
      <button id="closeSidebar" aria-label="Đóng">✕</button>
    </div>
    
    <ul class="tree">
      <li>
        <a href="?c=category&a=index&id=1">Sách Khoa học</a>
      </li>
      <li>
        <a href="?c=category&a=index&id=2">Sách Văn học</a>
      </li>
      <li>
        <a href="?c=category&a=index&id=3">Sách Kinh tế</a>
      </li>
      <li>
        <a href="?c=category&a=index&id=4">Sách Kỹ năng sống</a>
      </li>
      <li>
        <a href="?c=category&a=index&id=5">Sách Thiếu nhi</a>
      </li>
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
        <a class="brand foot" href="<?= $BASE; ?>/">
          <img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bo⊑kStore
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
          <li><a href="<?= $BASE; ?>/about">Về chúng tôi</a></li>
          <li><a href="<?= $BASE; ?>/privacy">Chính sách bảo mật</a></li>
          <li><a href="<?= $BASE; ?>/terms">Điều khoản sử dụng</a></li>
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

  <script>
  // toast từ serverSuccess
 (function() {
    var sel = document.getElementById('headerCatSelect');
    if (sel) {
      sel.addEventListener('change', function() {
        var catId = this.value;
        if (catId && catId !== 'all') {
          window.location.href = '?c=category&a=index&id=' + catId;
        } else if (catId === 'all') {
          // SỬA Ở ĐÂY:
          window.location.href = '?c=category&a=index'; // <-- Bỏ 'id' đi
        }
      });
    }
  })();
  </script>
</body>
</html>