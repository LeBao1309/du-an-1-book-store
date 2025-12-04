<?php
// User hiện tại
$currentUser = $currentUser ?? ($_SESSION['user'] ?? null);
$ASSET = 'public';

// Đếm giỏ hàng
$cart      = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $item) {
    $cartCount += (int)($item['quantity'] ?? 0);
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
    /* 1. TOAST MESSAGE */
    .wd-toast{position:fixed;right:16px;bottom:16px;background:#16a34a;color:#fff;padding:12px 14px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.12); z-index: 9999;}
    .wd-toast.error{background:#dc2626}
    
    /* 2. CSS SEARCH AJAX (KHÔNG LÀM VỠ LAYOUT CŨ) */
    .search-wrapper {
        flex: 1;
        position: relative; 
        margin: 0 16px; /* Khoảng cách với logo và icon */
    }
    
    #liveSearchResults {
        position: absolute;
        top: 100%; left: 0; right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 16px 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
        overflow: hidden;
        margin-top: 4px;
    }
    
    .search-item {
        display: flex; align-items: center; padding: 12px;
        border-bottom: 1px solid #f1f5f9; text-decoration: none; color: var(--text);
        transition: background 0.2s;
    }
    .search-item:hover { background: #f0fdfa; }
    .search-item img { 
        width: 40px; height: 56px; object-fit: cover; border-radius: 4px; 
        margin-right: 12px; border: 1px solid #f1f5f9;
    }
    .search-info h6 { 
        margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #334155;
        line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .search-info span { font-size: 13px; color: #dc2626; font-weight: 700; }
    .search-info .author-name { font-size: 12px; color: #64748b; font-weight: 400; margin-left: 6px; }

    /* 3. FOOTER ICONS */
    .social-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white !important; text-decoration: none; font-weight: bold; transition: transform 0.2s; }
    .social-icon:hover { transform: translateY(-3px); }
    .pay-icon { background: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; color: #333; font-weight: bold; }
  </style>
</head>

<body>
  <?php if (!empty($flash) && !empty($flash['message'])): ?>
    <div class="wd-toast <?= $flash['type']==='error' ? 'error' : '' ?>">
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <script>setTimeout(function(){var t=document.querySelector('.wd-toast');if(t)t.remove();}, 3000);</script>
  <?php endif; ?>

  <header class="header">
    <div class="container header-inner">
      <a class="brand" href="index.php?controller=home&action=index">
        <img src="<?= $ASSET; ?>/images/logo.png" alt="WiseDecision Bookstore" style="height: 70px; width: auto;" /> 
      </a>

      <div class="search-wrapper">
          <form class="search" method="GET" action="index.php" autocomplete="off">
            <input type="hidden" name="controller" value="category">
            <input type="hidden" name="action" value="index">
            
            <input type="text" id="searchInput" name="q" 
                   placeholder="Tìm sách, tác giả, nhà xuất bản..." 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" />
            
            <button class="btn" type="submit">🔍</button>
          </form>
          
          <div id="liveSearchResults"></div>
      </div>

      <nav class="actions">
        <a href="index.php?controller=account&action=wishlist" class="nav-icon" title="Yêu thích">❤</a>
        
        <?php if (!empty($currentUser)): ?>
          <a href="index.php?controller=account&action=profile" class="nav-icon" title="Tài khoản">👤</a>
          <a href="index.php?controller=auth&action=logout" class="nav-icon" title="Đăng xuất">🚪</a>
        <?php else: ?>
          <a href="index.php?controller=auth&action=login" class="nav-icon" title="Đăng nhập">👤</a>
        <?php endif; ?>
        
        <a href="index.php?controller=cart&action=index" class="nav-icon cart" title="Giỏ hàng">
          <span>🛒</span>
          <?php if($cartCount > 0): ?> <i class="badge" id="cartBadge"><?= $cartCount ?></i> <?php endif; ?>
        </a>
      </nav>
    </div>

    <div class="container navline">
      <ul class="nav">
        <li><a href="index.php?controller=home&action=index">Trang chủ</a></li>
        <li><a href="index.php?controller=category&action=index">Danh mục</a></li>
        <li><a href="index.php?controller=about&action=index">Giới thiệu</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="index.php?controller=contact&action=index">Liên hệ</a></li>
      </ul>
    </div>
  </header>

  <aside class="offcanvas" id="sidebar" aria-hidden="true">
    <div class="offcanvas-header">
      <strong>Danh mục sản phẩm</strong>
      <button id="closeSidebar" aria-label="Đóng">✕</button>
    </div>
    <ul class="tree">
      <?php 
        // Lấy cây danh mục cho sidebar
        require_once __DIR__ . '/../../models/CategoryModel.php';
        $categoryTree = CategoryModel::getTree();
      ?>
      <?php foreach ($categoryTree as $parent): ?>
        <li>
          <a href="index.php?controller=category&action=index&id=<?= $parent['id'] ?>" class="fw-bold">
            <?= htmlspecialchars($parent['name']) ?>
          </a>
          <?php if (!empty($parent['children'])): ?>
            <ul style="padding-left: 20px; list-style: none; margin-bottom: 10px;">
              <?php foreach ($parent['children'] as $child): ?>
                <li>
                  <a href="index.php?controller=category&action=index&id=<?= $child['id'] ?>" style="font-size: 0.9em; color: #555;">
                    - <?= htmlspecialchars($child['name']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <main class="container section" style="min-height: 500px;">
    <?= $content ?>
  </main>

  <footer class="footer-section">
    <div class="footer-top-line"></div>

    <div class="container">
      <div class="row pt-5 pb-5">
        
        <div class="col-lg-4 col-md-6 mb-4">
          <a href="index.php" class="footer-brand">
             <img src="<?= $ASSET; ?>/images/amban.png" alt="WiseBook" />
             <span>WiseBook Store</span>
          </a>
          <p class="mt-3" style="line-height: 1.6; color: #ffffff !important;">
   WiseBook là nơi hội tụ những cuốn sách giá trị nhất, giúp bạn thay đổi tư duy và thay đổi cuộc đời.
</p>
          <div class="contact-info mt-4">
            <div class="item">
                <span>📍</span> 123 Đường Sách, Q.1, TP.HCM
            </div>
            <div class="item">
                <span>📞</span> 0909.123.456
            </div>
            <div class="item">
                <span>📧</span> hotro@wisebook.vn
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="footer-heading">Khám phá</h5>
          <ul class="footer-links">
            <li><a href="index.php?controller=home&action=index">Trang chủ</a></li>
            <li><a href="index.php?controller=category&action=index">Tất cả sách</a></li>
            <li><a href="#">Sách bán chạy</a></li>
            <li><a href="#">Sách mới về</a></li>
            <li><a href="#">Blog chia sẻ</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="footer-heading">Hỗ trợ</h5>
          <ul class="footer-links">
            <li><a href="#">Chính sách đổi trả</a></li>
            <li><a href="#">Chính sách bảo mật</a></li>
            <li><a href="#">Điều khoản dịch vụ</a></li>
            <li><a href="#">Hướng dẫn mua hàng</a></li>
            <li><a href="#">Liên hệ hợp tác</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
          <h5 class="footer-heading">Đăng ký nhận tin</h5>
          <p class="text-muted small">Nhận thông báo về sách mới và ưu đãi đặc biệt.</p>
          
          <form class="subscribe-form mb-4">
            <input type="email" placeholder="Email của bạn..." required>
            <button type="button">Gửi</button>
          </form>

          <h5 class="footer-heading" style="font-size: 14px;">Kết nối với chúng tôi</h5>
          <div class="social-links">
            <a href="#" class="sc-icon fb">
                <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.15-2.905-.15-2.888 0-4.964 1.35-4.964 4.092v2.418H7v4h2.631v11h4.369v-11z"/></svg>
            </a>
            <a href="#" class="sc-icon insta">
                <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            <a href="#" class="sc-icon tiktok">
                <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.35-1.17.82-1.5 1.53-.4 1.17-.18 2.55.8 3.39.89.75 2.05.97 3.16.82 1.25-.17 2.39-.99 2.95-2.13.38-.81.46-1.73.43-2.61-.03-4.32-.01-8.64-.01-12.96z"/></svg>
            </a>
          </div>

          <h5 class="footer-heading mt-4" style="font-size: 14px;">Thanh toán an toàn</h5>
          <div class="d-flex gap-2">
             <span class="pay-badge">VISA</span>
             <span class="pay-badge">MOMO</span>
             <span class="pay-badge">ZALO</span>
             <span class="pay-badge">COD</span>
          </div>
        </div>

      </div>
    </div>
<div class="footer-bottom">
  <div class="container d-flex flex-wrap justify-content-center align-items-center">
     <span>&copy; 2025 WiseDecision Bookstore. All rights reserved.</span>
  </div>
</div>
    </div>
  </footer>

  <script>
    // Xử lý đóng/mở sidebar mobile
    const sidebar = document.getElementById('sidebar');
    const closeBtn = document.getElementById('closeSidebar');
    if(closeBtn && sidebar) { closeBtn.addEventListener('click', () => sidebar.classList.remove('open')); }

    // --- SCRIPT LIVE AJAX SEARCH ---
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const resultsBox = document.getElementById('liveSearchResults');
        let timeout = null;

        if(searchInput && resultsBox) {
            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                
                // Clear timeout cũ (debounce: chống spam request)
                clearTimeout(timeout);

                if (keyword.length < 2) {
                    resultsBox.style.display = 'none';
                    resultsBox.innerHTML = '';
                    return;
                }

                // Chờ 300ms sau khi ngừng gõ mới gửi request
                timeout = setTimeout(() => {
                    fetch(`index.php?controller=home&action=ajaxSearch&q=${encodeURIComponent(keyword)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                let html = '';
                                data.forEach(book => {
                                    // Format tiền
                                    const price = new Intl.NumberFormat('vi-VN').format(book.price);
                                    // Xử lý ảnh
                                    const img = book.image_url ? 'public/' + book.image_url : 'https://dummyimage.com/40x56/eee/aaa';
                                    // Xử lý tác giả (nếu có)
                                    const author = book.author_names ? `<span class="author-name">(${book.author_names})</span>` : '';

                                    html += `
                                        <a href="index.php?controller=product&action=detail&id=${book.id}" class="search-item">
                                            <img src="${img}" alt="${book.title}">
                                            <div class="search-info">
                                                <h6>${book.title} ${author}</h6>
                                                <span>${price}₫</span>
                                            </div>
                                        </a>
                                    `;
                                });
                                // Nút xem tất cả
                                html += `
                                    <a href="index.php?controller=category&action=index&q=${encodeURIComponent(keyword)}" 
                                       class="search-item justify-content-center" style="background:#f8fafc; color:#0f766e; font-weight:600;">
                                        Xem tất cả kết quả cho "${keyword}"
                                    </a>
                                `;
                                resultsBox.innerHTML = html;
                                resultsBox.style.display = 'block';
                            } else {
                                resultsBox.innerHTML = '<div style="padding:15px;text-align:center;color:#64748b;">Không tìm thấy kết quả nào</div>';
                                resultsBox.style.display = 'block';
                            }
                        })
                        .catch(err => console.error(err));
                }, 300);
            });

            // Ẩn kết quả khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }
    });
  </script>
  
  <!-- Category Slider Navigation -->
  <script src="<?= $ASSET ?>/js/category-slider.js"></script>
</body>
</html>
