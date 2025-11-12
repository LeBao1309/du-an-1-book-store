<?php
$BASE = defined('BASE_URL') ? constant('BASE_URL') : '';
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);

$ASSET = rtrim($BASE, '/');
if (!preg_match('~/public$~', $ASSET)) {
    $ASSET .= '/public';
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WiseDecision Bookstore — Trang chủ</title>
  <meta name="description" content="Mua sách hay, giao nhanh, giá tốt tại WiseDecision Bookstore" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- dùng $ASSET cho static -->
  <link rel="stylesheet" href="<?= $ASSET; ?>/css/styles.css" />
  <style>
    .wd-toast{position:fixed;right:16px;bottom:16px;background:#16a34a;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.12)}
    .wd-toast.error{background:#dc2626}
  </style>
</head>

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
        <select class="cat-select" aria-label="Chọn danh mục">
          <option value="all">Tất cả danh mục</option>
          <option>Truyện</option>
          <option>Kinh tế</option>
          <option>Kỹ năng</option>
          <option>Thiếu nhi</option>
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
        <li><a href="<?= $BASE; ?>/category">Sản phẩm</a></li>
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
      <li><a href="<?= $BASE; ?>/category/new">Sách mới</a></li>
      <li>
        <span>Văn học</span>
        <ul>
          <li><a href="<?= $BASE; ?>/category/tieu-thuyet">Tiểu thuyết</a></li>
          <li><a href="<?= $BASE; ?>/category/truyen-ngan">Truyện ngắn</a></li>
          <li><a href="<?= $BASE; ?>/category/light-novel">Light novel</a></li>
        </ul>
      </li>
      <li>
        <span>Kinh tế</span>
        <ul>
          <li><a href="<?= $BASE; ?>/category/marketing">Marketing</a></li>
          <li><a href="<?= $BASE; ?>/category/khoi-nghiep">Khởi nghiệp</a></li>
          <li><a href="<?= $BASE; ?>/category/quan-tri">Quản trị</a></li>
        </ul>
      </li>
      <li>
        <span>Thiếu nhi</span>
        <ul>
          <li><a href="<?= $BASE; ?>/category/truyen-tranh">Truyện tranh</a></li>
          <li><a href="<?= $BASE; ?>/category/khoa-hoc">Khoa học</a></li>
        </ul>
      </li>
    </ul>
  </aside>

  <?php if (!empty($successMsg)): ?>
    <div id="serverSuccess" data-message="<?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'); ?>"></div>
  <?php endif; ?>

  <!-- vùng render view con -->
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
  (function(){
    var el = document.getElementById('serverSuccess');
    if(!el) return;
    var msg = el.getAttribute('data-message');
    if(!msg) return;
    var t = document.createElement('div');
    t.className = 'wd-toast';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 3000);
  })();
  </script>
</body>
</html>
