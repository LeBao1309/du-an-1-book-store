<?php
$newBooks = $newBooks ?? [];
$bestSellers = $bestSellers ?? [];
$discountedBooks = $discountedBooks ?? [];
$categories = $categories ?? [];
?>

<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <span class="chip" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3);">
        🚀 Khám phá tri thức mới
      </span>
      <h1>Đọc sách là cách<br/>để bạn nhìn ra thế giới</h1>
      <p>Hàng ngàn đầu sách chọn lọc đang chờ bạn. Giao hàng nhanh, đóng gói cẩn thận.</p>
      <div class="hero-actions">
        <a href="index.php?controller=category&action=index" class="btn btn-light">Mua ngay</a>
        <a href="#homeTrending" class="btn btn-ghost" style="border: 1px solid rgba(255,255,255,0.5);">Xem sách mới ↓</a>
      </div>
    </div>
    <div class="hero-art">
      <img src="https://cdn-icons-png.flaticon.com/512/3330/3330314.png" alt="Book Store Banner"/>
    </div>
  </div>
</section>

<!-- Danh mục nổi bật -->
<section class="container section">
  <div class="section-head">
    <h2>📚 Danh mục sách</h2>
  </div>

  <div class="categories-wrapper">
    <button class="cat-nav-btn prev" aria-label="Previous categories">‹</button>
    
    <div class="categories-slider">
      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
          <a href="index.php?controller=category&action=index&id=<?= (int)$cat['id'] ?>" class="category-card">
            <div class="category-icon">📖</div>
            <h6 class="category-name"><?= htmlspecialchars($cat['name']) ?></h6>
            <small class="category-count"><?= (int)$cat['book_count'] ?> sách</small>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <button class="cat-nav-btn next" aria-label="Next categories">›</button>
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
              <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$book['id'] ?>" class="btn-wishlist-overlay" title="Thêm vào yêu thích">♥</a>
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block w-100 h-100">
                <img src="<?= htmlspecialchars($imageSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
              </a>
          </div>
          
          <div class="card-body d-flex flex-column pb-4"> 
            <p class="card-author text-muted small mb-1">
                 <?= htmlspecialchars($book['author_names'] ?? 'Đang cập nhật') ?>
            </p> 
            
            <h6 class="card-title product-title mb-2">
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>">
                <?= htmlspecialchars($book['title']) ?>
              </a>
            </h6>

            <?php if(!empty($book['publisher_name'])): ?>
               <div class="mb-2">
                   <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                       <?= htmlspecialchars($book['publisher_name']) ?>
                   </span>
               </div>
            <?php endif; ?>

            <div class="price-wrap mt-auto">
              <span class="product-price text-danger fw-bold fs-5">
                <?= number_format($book['display_price'] ?? 0) ?>₫
              </span>
            </div>
          </div>

          <div class="card-footer bg-white border-top-0">
             <div class="d-flex gap-2">
                <form method="get" action="index.php" class="m-0 flex-grow-1">
                  <input type="hidden" name="controller" value="cart">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary w-100" title="Thêm vào giỏ">
                    + Giỏ
                  </button>
                </form>
                <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-dark flex-grow-1" title="Xem chi tiết">
                  Xem
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

<!-- Sản phẩm bán chạy -->
<section class="container section">
  <div class="section-head">
    <h2>📈 Sản phẩm bán chạy</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>

  <div class="grid cards-5">
    <?php if (!empty($bestSellers)): ?>
      <?php foreach ($bestSellers as $book): ?>
        <?php
          $rawImage = $book['image_url'] ?? '';
          $imageSrc = ($rawImage === '' || $rawImage === null) 
              ? 'https://dummyimage.com/300x400/eee/aaa&text=No+Image' 
              : $ASSET . '/' . ltrim($rawImage, '/');
        ?>

        <div class="card h-100 shadow-sm product-card">
          <div class="card-img-container position-relative">
              <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$book['id'] ?>" class="btn-wishlist-overlay" title="Thêm vào yêu thích">♥</a>
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block w-100 h-100">
                <img src="<?= htmlspecialchars($imageSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
              </a>
          </div>
          
          <div class="card-body d-flex flex-column pb-4"> 
            <p class="card-author text-muted small mb-1">
                 <?= htmlspecialchars($book['author_names'] ?? 'Đang cập nhật') ?>
            </p> 
            
            <h6 class="card-title product-title mb-2">
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>">
                <?= htmlspecialchars($book['title']) ?>
              </a>
            </h6>

            <?php if(!empty($book['publisher_name'])): ?>
               <div class="mb-2">
                   <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                       <?= htmlspecialchars($book['publisher_name']) ?>
                   </span>
               </div>
            <?php endif; ?>

            <div class="price-wrap mt-auto">
              <span class="product-price text-danger fw-bold fs-5">
                <?= number_format($book['display_price'] ?? 0) ?>₫
              </span>
              <small class="text-muted ms-2">
                Đã bán: <?= (int)$book['total_sold'] ?>
              </small>
            </div>
          </div>

          <div class="card-footer bg-white border-top-0">
             <div class="d-flex gap-2">
                <form method="get" action="index.php" class="m-0 flex-grow-1">
                  <input type="hidden" name="controller" value="cart">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary w-100" title="Thêm vào giỏ">
                    + Giỏ
                  </button>
                </form>
                <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-dark flex-grow-1" title="Xem chi tiết">
                  Xem
                </a>
             </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có dữ liệu bán hàng.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Sách giảm giá -->
<section class="container section">
  <div class="section-head">
    <h2>🔥 Sách giảm giá sốc</h2>
    <a class="see-all" href="index.php?controller=category&action=index">Xem tất cả</a>
  </div>

  <div class="grid cards-5">
    <?php if (!empty($discountedBooks)): ?>
      <?php foreach ($discountedBooks as $book): ?>
        <?php
          $rawImage = $book['image_url'] ?? '';
          $imageSrc = ($rawImage === '' || $rawImage === null) 
              ? 'https://dummyimage.com/300x400/eee/aaa&text=No+Image' 
              : $ASSET . '/' . ltrim($rawImage, '/');
          
          $discountPercent = (int)($book['discount_percent'] ?? 0);
        ?>

        <div class="card h-100 shadow-sm product-card">
          <div class="card-img-container position-relative">
              <?php if ($discountPercent > 0): ?>
                <span class="badge-sale">-<?= $discountPercent ?>%</span>
              <?php endif; ?>
              <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$book['id'] ?>" class="btn-wishlist-overlay" title="Thêm vào yêu thích">♥</a>
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block w-100 h-100">
                <img src="<?= htmlspecialchars($imageSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
              </a>
          </div>
          
          <div class="card-body d-flex flex-column pb-4"> 
            <p class="card-author text-muted small mb-1">
                 <?= htmlspecialchars($book['author_names'] ?? 'Đang cập nhật') ?>
            </p> 
            
            <h6 class="card-title product-title mb-2">
              <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>">
                <?= htmlspecialchars($book['title']) ?>
              </a>
            </h6>

            <?php if(!empty($book['publisher_name'])): ?>
               <div class="mb-2">
                   <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                       <?= htmlspecialchars($book['publisher_name']) ?>
                   </span>
               </div>
            <?php endif; ?>

            <div class="price-wrap mt-auto">
              <span class="product-price text-danger fw-bold fs-5">
                <?= number_format($book['sale_price'] ?? 0) ?>₫
              </span>
              <small class="text-decoration-line-through text-muted ms-2">
                <?= number_format($book['original_price'] ?? 0) ?>₫
              </small>
            </div>
          </div>

          <div class="card-footer bg-white border-top-0">
             <div class="d-flex gap-2">
                <form method="get" action="index.php" class="m-0 flex-grow-1">
                  <input type="hidden" name="controller" value="cart">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-primary w-100" title="Thêm vào giỏ">
                    + Giỏ
                  </button>
                </form>
                <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-dark flex-grow-1" title="Xem chi tiết">
                  Xem
                </a>
             </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có sách giảm giá.</p>
    <?php endif; ?>
  </div>
</section>

<section class="container features" id="features">
  <div class="feature"><span>🚚</span> Miễn phí vận chuyển</div>
  <div class="feature"><span>🛡️</span> Hoàn tiền đảm bảo</div>
  <div class="feature"><span>💬</span> Hỗ trợ 24/7</div>
  <div class="feature"><span>💳</span> Thanh toán linh hoạt</div>
</section>
