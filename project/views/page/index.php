<?php
// Các biến này được truyền từ HomeController
$newBooks = $newBooks ?? [];

// Biến $BASE này dùng cho link danh mục, 
// nhưng trong cấu trúc của bạn, nó chưa được định nghĩa.
// Tạm thời tôi sẽ sửa link danh mục để nó hoạt động
// $BASE = ''; // (Sẽ bỏ qua, dùng link trực tiếp)
?>

<section class="hero">
  <div class="container grid hero-inner">
    <div class="hero-text">
      <span class="chip">Special Offer</span>
      <h1>There is nothing<br/>better than to read</h1>
      <p>Tìm món quà hoàn hảo cho mọi người trong danh sách của bạn.</p>
      <div class="hero-actions">
        <a href="index.php?controller=category&action=index" class="btn btn-light">Mua ngay</a>
        <a href="#features" class="btn btn-ghost">Khám phá</a>
      </div>
    </div>
    <div class="hero-art">
      <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200" alt="books"/>
    </div>
  </div>
</section>

<section class="container section" id="category">
  <div class="section-head">
    <h2>Danh mục nổi bật</h2>
    <div class="dots">
      <button class="dot prev" data-target="#catTrack" aria-label="Prev">‹</button>
      <button class="dot next" data-target="#catTrack" aria-label="Next">›</button>
    </div>
  </div>
  <div class="track" id="catTrack">
    <a class="pill" href="index.php?controller=category&action=index&id=1">Trinh thám</a>
    <a class="pill" href="index.php?controller=category&action=index&id=4">Self-help</a>
    <a class="pill" href="index.php?controller=category&action=index&id=3">Kinh doanh</a>
    <a class="pill" href="index.php?controller=category&action=index&id=5">Thiếu nhi</a>
  </div>
</section>

<section class="container section">
  <div class="section-head">
    <h2>Đang thịnh hành (Sản phẩm mới)</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>

  <div class="grid cards-5" id="homeTrending">
    
    <?php if (!empty($newBooks)): ?>
      <?php foreach ($newBooks as $book): ?>
        <?php
          // Xử lý ảnh (giống hệt category.php)
          $rawImage = $book['image_url'] ?? '';
          if ($rawImage === '' || $rawImage === null) {
              $imageSrc = 'https://dummyimage.com/300x400/eee/aaa&text=No+Image';
          } else {
              $imageSrc = $ASSET . '/' . ltrim($rawImage, '/');
          }
        ?>

        <div class="card h-100 shadow-sm product-card">
          
          <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block">
            <div class="card-img-container">
              <img
                src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                class="card-img-top"
                alt="<?= htmlspecialchars($book['title']) ?>"
              >
            </div>
          </a>
          
          <div class="card-body d-flex flex-column">
            <p class="card-author text-muted small mb-1">Tác giả (demo)</p> 
            <h6 class="card-title product-title mb-2">
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>">
                <?= htmlspecialchars($book['title']) ?>
              </a>
            </h6>
            <div class="price-wrap mt-auto">
              <span class="product-price">
                <?= number_format($book['display_price'] ?? 0) ?>₫
              </span>
            </div>
          </div>

          <div class="card-footer p-3 pt-2 border-top-0">
            <div class="d-flex gap-2">
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-dark w-100">
                Xem chi tiết
              </a>
              <a href="#" 
                 class="btn btn-sm btn-primary w-100" 
                 onclick="alert('Chức năng Thêm vào giỏ hàng sẽ được xử lý sau!'); return false;">
                Thêm vào giỏ
              </a>
            </div>
          </div>

        </div>
        <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có sản phẩm nào để hiển thị.</p>
    <?php endif; ?>

  </div>
</section>
<section class="container section">
  <div class="section-head">
    <h2>Bán chạy</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>
  <div class="grid cards-6" id="homeBestseller">
     </div>
</section>

<section class="container features" id="features">
  <div class="feature"><span>🚚</span> Free Shipping</div>
  <div class="feature"><span>🛡️</span> Money Guarantee</div>
  <div class="feature"><span>💬</span> Online Support</div>
  <div class="feature"><span>💳</span> Flexible Payment</div>
</section>