<?php
$newBooks = $newBooks ?? [];
$bestSellers = $bestSellers ?? [];
?>

<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <span class="chip" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3);">
        🚀 Khám phá tri thức mới
      </span>
      <h1>Đọc sách là cách<br/>để bạn nhìn ra thế giới</h1>
      <p>
        Hàng ngàn đầu sách chọn lọc đang chờ bạn.
        Giao hàng nhanh, đóng gói cẩn thận và quà tặng kèm hấp dẫn.
      </p>
      
      <div class="hero-actions">
        <a href="index.php?controller=category&action=index" class="btn btn-light">
          Mua ngay
        </a>
        <a href="#homeTrending" class="btn btn-ghost" style="border: 1px solid rgba(255,255,255,0.5);">
          Xem sách mới ↓
        </a>
      </div>
    </div>

    <div class="hero-art">
      <img src="https://cdn-icons-png.flaticon.com/512/3330/3330314.png" alt="Book Store Banner"/>
    </div>
  </div>
</section>

<section class="container section" id="category">
  <div class="section-head">
    <h2>Danh mục nổi bật</h2>
    <div class="dots">
      <button class="dot prev" data-target="#catTrack">‹</button>
      <button class="dot next" data-target="#catTrack">›</button>
    </div>
  </div>
  <div class="track" id="catTrack">
    <a class="pill" href="index.php?controller=category&action=index&id=1">Khoa học</a>
    <a class="pill" href="index.php?controller=category&action=index&id=2">Văn học</a>
    <a class="pill" href="index.php?controller=category&action=index&id=3">Kinh tế</a>
    <a class="pill" href="index.php?controller=category&action=index&id=4">Kỹ năng sống</a>
    <a class="pill" href="index.php?controller=category&action=index&id=5">Thiếu nhi</a>
  </div>
</section>

<section class="container section">
  <div class="section-head">
    <h2>Sản phẩm mới</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>

  <div class="grid cards-5" id="homeTrending">
    <?php if (!empty($newBooks)): ?>
      <?php foreach ($newBooks as $book): ?>
        <?php
          $rawImage = $book['image_url'] ?? '';
          $imageSrc = ($rawImage === '' || $rawImage === null) 
              ? 'https://dummyimage.com/300x400/eee/aaa&text=No+Image' 
              : $ASSET . '/' . ltrim($rawImage, '/');
        ?>

        <div class="card h-100 shadow-sm product-card">
          
          <div class="card-img-container position-relative">
              <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$book['id'] ?>" 
                 class="btn-wishlist-overlay" 
                 title="Thêm vào yêu thích">
                 ♥
              </a>
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block w-100 h-100">
                <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                     class="card-img-top"
                     alt="<?= htmlspecialchars($book['title']) ?>">
              </a>
          </div>
          
          <div class="card-body d-flex flex-column pb-5"> 
            <p class="card-author text-muted small mb-1">Tác giả</p> 
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

          <div class="card-footer">
             <div class="d-flex gap-1">
                
                <form method="get" action="index.php" class="m-0 flex-grow-1">
                  <input type="hidden" name="controller" value="cart">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary w-100" title="Thêm vào giỏ hàng">
                    + Giỏ hàng
                  </button>
                </form>

                <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" 
                   class="btn btn-sm btn-outline-dark flex-grow-1" title="Xem chi tiết">
                  Xem chi tiết
                </a>

             </div>
          </div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có sản phẩm nào.</p>
    <?php endif; ?>
  </div>
</section>

<section class="container section">
  <div class="section-head">
    <h2>Bán chạy nhất</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>
  <div class="grid cards-6" id="homeBestseller">
     <?php if(empty($bestSellers)): ?>
        <p class="text-muted">Đang cập nhật...</p>
     <?php endif; ?>
  </div>
</section>

<section class="container features" id="features">
  <div class="feature"><span>🚚</span> Miễn phí vận chuyển</div>
  <div class="feature"><span>🛡️</span> Hoàn tiền đảm bảo</div>
  <div class="feature"><span>💬</span> Hỗ trợ 24/7</div>
  <div class="feature"><span>💳</span> Thanh toán linh hoạt</div>
</section>