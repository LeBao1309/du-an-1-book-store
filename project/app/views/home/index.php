<?php
// (File này được gọi bởi HomeController)
// (Nó nhận 2 biến: $currentUser và $successMsg)
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
  
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css" />
</head>
<body>
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
      <a class="brand" href="<?php echo BASE_URL; ?>/"><img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bo⊑kStore</a>
      <div class="search">
        <select class="cat-select" aria-label="Chọn danh mục">
          <option value="all">Tất cả danh mục</option>
          <option>Truyện</option>
          <option>Kinh tế</option>
          <option>Kỹ năng</option>
          <option>Thiếu nhi</option>
        </select>
        <input type="text" placeholder="Tìm kiếm sách, tác giả..." />
        <button class="btn">Tìm</button>
      </div>
      <nav class="actions">
        <a href="#" class="nav-icon" title="Yêu thích">❤</a>
        
        <?php // $currentUser được truyền từ HomeController ?>
        <?php if (isset($currentUser) && $currentUser): ?>
            <a href="#" class="nav-icon" title="Tài khoản: <?php echo htmlspecialchars($currentUser['name']); ?>">👤</a>
            <a href="<?php echo BASE_URL; ?>/logout" class="nav-icon" title="Đăng xuất">🚪</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/login" class="nav-icon" title="Tài khoản">👤</a>
        <?php endif; ?>
        
        <a href="<?php echo BASE_URL; ?>/cart" class="nav-icon cart" title="Giỏ hàng"><span>🛒</span><i class="badge" id="cartBadge">0</i></a>
      </nav>
    </div>
    <div class="container navline">
      <button class="btn btn-cat" id="btnCat">☰ Danh mục</button>
      <ul class="nav">
        <li><a class="active" href="<?php echo BASE_URL; ?>/">Trang chủ</a></li>
        <li><a href="<?php echo BASE_URL; ?>/category">Sản phẩm</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Giới thiệu</a></li>
        <li><a href="#">Liên hệ</a></li>
      </ul>
    </div>
  </header>

  <aside class="offcanvas" id="sidebar">
    <div class="offcanvas-header">
      <strong>Danh mục</strong>
      <button id="closeSidebar" aria-label="Đóng">✕</button>
    </div>
    <ul class="tree">
      <li><a href="#">Sách mới</a></li>
      <li>
        <span>Văn học</span>
        <ul>
          <li><a href="#">Tiểu thuyết</a></li>
          <li><a href="#">Truyện ngắn</a></li>
          <li><a href="#">Light novel</a></li>
        </ul>
      </li>
      <li>
        <span>Kinh tế</span>
        <ul>
          <li><a href="#">Marketing</a></li>
          <li><a href="#">Khởi nghiệp</a></li>
          <li><a href="#">Quản trị</a></li>
        </ul>
      </li>
      <li>
        <span>Thiếu nhi</span>
        <ul>
          <li><a href="#">Truyện tranh</a></li>
          <li><a href="#">Khoa học</a></li>
        </ul>
      </li>
    </ul>
  </aside>
  
  <?php // 4. TÍCH HỢP: Hiển thị thông báo (nếu có) ?>
  <?php if (isset($successMsg) && $successMsg): ?>
      <div class="container" style="padding-top: 10px;">
          <p style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 8px;">
              <?php echo htmlspecialchars($successMsg); ?>
          </p>
      </div>
  <?php endif; ?>

  <section class="hero">
    <div class="container grid hero-inner">
      <div class="hero-text">
        <span class="chip">Special Offer</span>
        <h1>There is nothing<br/>better than to read</h1>
        <p>Tìm món quà hoàn hảo cho mọi người trong danh sách của bạn.</p>
        <div class="hero-actions">
          <a href="<?php echo BASE_URL; ?>/category" class="btn btn-light">Mua ngay</a>
          <a href="#" class="btn btn-ghost">Khám phá</a>
        </div>
      </div>
      <div class="hero-art">
        <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200" alt="books"/>
      </div>
    </div>
  </section>

  <section class="container section">
    <div class="section-head">
      <h2>Danh mục nổi bật</h2>
      <div class="dots">
        <button class="dot prev" data-target="#catTrack">‹</button>
        <button class="dot next" data-target="#catTrack">›</button>
      </div>
    </div>
    <div class="track" id="catTrack">
      <a class="pill" href="#">Trinh thám</a>
      <a class="pill" href="#">Self-help</a>
      <a class="pill" href="#">Kinh doanh</a>
      <a class="pill" href="#">Thiếu nhi</a>
      <a class="pill" href="#">Tâm lý</a>
      <a class="pill" href="#">Lịch sử</a>
      <a class="pill" href="#">CNTT</a>
      <a class="pill" href="#">Khoa học</a>
      <a class="pill" href="#">Nấu ăn</a>
      <a class="pill" href="#">Sức khỏe</a>
    </div>
  </section>

  <section class="container grid promos">
    <a class="promo" href="#">
      <div>
        <span class="label">Summer sale</span>
        <h3>Sale 25% OFF</h3>
        <p>Cho hàng nghìn đầu sách</p>
        <span class="link">Shop now →</span>
      </div>
      <img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?q=80&w=900" alt="sale"/>
    </a>
    <a class="promo" href="#">
      <div>
        <span class="label">Novel every day</span>
        <h3>Sale 45% OFF</h3>
        <p>Đọc nhiều giá tốt</p>
        <span class="link">Shop now →</span>
      </div>
      <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=900" alt="sale2"/>
    </a>
  </section>

  <section class="container section">
    <div class="section-head"><h2>Đang thịnh hành</h2><a class="see-all" href="#">Xem tất cả</a></div>
    <div class="grid cards-5" id="homeTrending">
      <p>Đang tải sách...</p>
    </div>
  </section>

  <section class="container section">
    <div class="section-head"><h2>Bán chạy</h2><a class="see-all" href="#">Xem tất cả</a></div>
    <div class="grid cards-6" id="homeBestseller">
      </div>
  </section>

  <section class="container features">
    <div class="feature"><span>🚚</span> Free Shipping</div>
    <div class="feature"><span>🛡️</span> Money Guarantee</div>
    <div class="feature"><span>💬</span> Online Support</div>
    <div class="feature"><span>💳</span> Flexible Payment</div>
  </section>

  <footer class="footer">
    <div class="container grid footer-grid">
      <div>
        <a class="brand foot" href="<?php echo BASE_URL; ?>/"><img src="https://dummyimage.com/36x36/0eb/ffffff.png&text=B" alt="logo"/> Bo⊑kStore</a>
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
    <div class="copy">© 2025 WiseDecision Bookstore</div>
  </footer>

  </body>
</html>