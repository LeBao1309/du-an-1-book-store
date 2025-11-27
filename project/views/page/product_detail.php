<?php
// Các biến: $book, $variants, $images, $relatedProducts
?>

<style>
/* Màu chính của web - GIỐNG BANNER */
:root {
    --primary-color: #0fbfbf;
    --primary-hover: #0aa5a5;
    --primary-light: #e0f9f9;
    --primary-dark: #088a8a;
}

/* Nút quay lại ở trên */
.back-button-top {
    margin-bottom: 20px;
}

.btn-back-top {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back-top:hover {
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
}

/* Alert thông báo */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 15px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.product-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(15, 191, 191, 0.15);
    margin-bottom: 30px;
    border: 1px solid rgba(15, 191, 191, 0.2);
}

.main-image {
    border: 2px solid var(--primary-light);
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
}

.main-image img {
    max-width: 100%;
    height: auto;
}

.thumb-images {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.thumb-item {
    width: 80px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
}

.thumb-item:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-title {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #1a1a1a;
}

.badge-category {
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
    box-shadow: 0 3px 10px rgba(15, 191, 191, 0.3);
}

.product-price {
    font-size: 36px;
    color: #dc3545;
    font-weight: bold;
    margin: 20px 0;
}

/* Nút chọn biến thể bìa cứng/mềm */
.variant-selector {
    margin: 25px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
    border-radius: 12px;
    border: 2px solid var(--primary-light);
}

.variant-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.variant-btn {
    flex: 1;
    min-width: 140px;
    padding: 18px 25px;
    border: 3px solid #ddd;
    background: white;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.variant-btn:hover {
    border-color: var(--primary-color);
    background: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 191, 191, 0.25);
}

.variant-btn.active {
    border-color: var(--primary-color);
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.4);
}

.variant-btn .format-name {
    font-weight: bold;
    font-size: 17px;
    display: block;
    margin-bottom: 8px;
}

.variant-btn .format-price {
    font-size: 20px;
    color: #dc3545;
    font-weight: bold;
}

.variant-btn.active .format-price {
    color: white;
}

.variant-btn .format-stock {
    font-size: 13px;
    color: #666;
    margin-top: 6px;
}

.variant-btn.active .format-stock {
    color: rgba(255, 255, 255, 0.95);
}

.description-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    margin-bottom: 25px;
}

/* Phần chọn số lượng */
.quantity-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    margin-bottom: 20px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn-quantity {
    width: 40px;
    height: 40px;
    border: 2px solid var(--primary-color);
    background: white;
    color: var(--primary-color);
    font-size: 20px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-quantity:hover {
    background: var(--primary-color) !important;
    color: white !important;
    transform: scale(1.1);
}

.btn-quantity:active {
    transform: scale(0.95);
}

#quantityInput {
    width: 80px;
    height: 40px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 0 10px;
}

#quantityInput:focus {
    outline: none;
    border-color: var(--primary-color);
}

/* Nút hành động */
.action-buttons {
    display: flex;
    gap: 15px;
}

.btn-add-cart {
    flex: 1;
    padding: 18px;
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.35);
}

.btn-add-cart:hover {
    background: linear-gradient(180deg, var(--primary-hover) 0%, var(--primary-dark) 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 191, 191, 0.45);
}

.btn-buy-now {
    flex: 1;
    padding: 18px;
    background: linear-gradient(180deg, #ffc107 0%, #ff9800 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.35);
}

.btn-buy-now:hover {
    background: linear-gradient(180deg, #ff9800 0%, #f57c00 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.45);
}

/* Sản phẩm liên quan */
.related-section {
    margin-top: 50px;
    padding-top: 40px;
    border-top: 3px solid var(--primary-color);
}

.related-title {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 30px;
    color: var(--primary-color);
    position: relative;
    padding-bottom: 12px;
}

.related-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
    border-radius: 2px;
}

.related-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    margin-bottom: 20px;
    background: white;
}

.related-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 8px 25px rgba(15, 191, 191, 0.25);
    transform: translateY(-8px);
}

.related-image {
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: var(--primary-light);
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.related-card:hover .related-image img {
    transform: scale(1.1);
}

.related-info {
    padding: 18px;
}

.related-info h5 {
    font-size: 16px;
    font-weight: 600;
    height: 44px;
    overflow: hidden;
    margin-bottom: 12px;
    color: #333;
}

.related-price {
    font-size: 20px;
    color: #dc3545;
    font-weight: bold;
    margin-bottom: 12px;
}

.btn-view {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-view:hover {
    background: linear-gradient(180deg, var(--primary-hover) 0%, var(--primary-dark) 100%);
    color: white;
    transform: scale(1.02);
}

/* Phần đánh giá và bình luận */
.review-section {
    margin-top: 40px;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(15, 191, 191, 0.15);
    border: 1px solid rgba(15, 191, 191, 0.2);
}

.review-title {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 30px;
    color: var(--primary-color);
    padding-bottom: 12px;
    border-bottom: 3px solid var(--primary-color);
}

.review-summary {
    text-align: center;
    background: linear-gradient(135deg, #f0fffe 0%, var(--primary-light) 100%);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.rating-big {
    font-size: 48px;
    font-weight: bold;
    color: var(--primary-color);
}

.rating-stars-big {
    font-size: 28px;
    color: #ffc107;
    margin: 10px 0;
}

.total-reviews {
    color: #666;
    font-size: 16px;
}

/* Form viết bình luận */
.write-review-box {
    background: #f0f9f9;
    padding: 25px;
    border-radius: 12px;
    margin: 25px 0;
    border: 2px solid var(--primary-light);
}

.write-review-box h4 {
    color: #333;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

/* Rating sao xịn xò */
.star-rating-input {
    display: flex;
    gap: 8px;
    font-size: 45px;
    margin: 15px 0;
    justify-content: center;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 3px dashed #e0e0e0;
}

.star {
    cursor: pointer;
    color: #ddd;
    transition: all 0.2s ease;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.star:hover {
    transform: scale(1.3) rotate(15deg);
    color: #ffc107;
}

.star.active {
    color: #ffc107;
    transform: scale(1.15);
    animation: starPulse 0.3s ease;
}

@keyframes starPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1.15); }
}

.rating-hint {
    text-align: center;
    color: #999;
    font-size: 14px;
    margin-top: 10px;
    font-style: italic;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

textarea.form-control {
    resize: vertical;
}

.btn-submit-review {
    background: var(--primary-color);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit-review:hover {
    background: var(--primary-hover);
}

.login-prompt {
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin: 25px 0;
}

.login-prompt a {
    color: var(--primary-color);
    font-weight: bold;
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

/* Danh sách bình luận */
.comments-list {
    margin-top: 30px;
}

.comments-list h4 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #333;
}

.comment-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
}

.comment-item:hover {
    border-color: var(--primary-color);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.comment-header strong {
    color: #333;
    font-size: 16px;
}

.comment-rating {
    font-size: 18px;
    color: #ffc107;
}

.comment-date {
    color: #999;
    font-size: 13px;
    margin-bottom: 10px;
}

.comment-content {
    color: #555;
    line-height: 1.6;
}

.no-comments {
    text-align: center;
    padding: 40px;
    color: #999;
    background: #f8f9fa;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .variant-buttons {
        flex-direction: column;
    }
    
    .variant-btn {
        min-width: 100%;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="container py-4">
  <!-- NÚT QUAY LẠI Ở TRÊN -->
  <div class="back-button-top">
    <a href="index.php?controller=category&action=index" class="btn-back-top">
      ← Quay lại danh sách
    </a>
  </div>

  <!-- THÔNG BÁO SUCCESS/ERROR -->
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
      ✅ <?php echo htmlspecialchars($_SESSION['success']); ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error">
      ❌ <?php echo htmlspecialchars($_SESSION['error']); ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- THÔNG TIN SẢN PHẨM -->
  <div class="product-container">
    <div class="row">
      <!-- Cột ảnh -->
      <div class="col-md-5">
        <?php
          $mainImage = !empty($images) ? $images[0]['image_url'] : '';
          if (empty($mainImage)) {
              $mainImageSrc = 'https://via.placeholder.com/400x500?text=No+Image';
          } else {
              $mainImageSrc = $ASSET . '/' . $mainImage;
          }
        ?>
        <div class="main-image" id="mainImage">
          <img src="<?php echo htmlspecialchars($mainImageSrc); ?>" 
               alt="<?php echo htmlspecialchars($book['title']); ?>">
        </div>

        <?php if (!empty($images) && count($images) > 1): ?>
          <div class="thumb-images">
            <?php foreach ($images as $img): 
                $thumbSrc = $ASSET . '/' . $img['image_url'];
            ?>
              <div class="thumb-item" onclick="changeImage('<?php echo htmlspecialchars($thumbSrc); ?>')">
                <img src="<?php echo htmlspecialchars($thumbSrc); ?>" alt="thumb">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Cột thông tin -->
      <div class="col-md-7">
        <h1 class="product-title">
          <?php echo htmlspecialchars($book['title']); ?>
        </h1>

        <div class="mb-3">
          <span class="badge-category">📚 Danh mục: <?php echo (int)$book['category_id']; ?></span>
        </div>

        <?php if (!empty($book['rating_avg'])): ?>
          <div class="mt-2 mb-3">
            <span style="color: #ffc107; font-size: 20px;">
              <?php 
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= round($book['rating_avg'])) {
                        echo '★';
                    } else {
                        echo '☆';
                    }
                }
              ?>
            </span>
            <span class="text-muted" style="font-size: 15px;">
              <?php echo number_format($book['rating_avg'], 1); ?> 
              (<?php echo (int)$book['review_count']; ?> đánh giá)
            </span>
          </div>
        <?php endif; ?>

        <?php
          // Tính giá hiển thị
          $displayPrice = 0;
          $selectedVariantId = null;
          if (!empty($variants)) {
              foreach ($variants as $v) {
                  $p = !empty($v['sale_price']) ? $v['sale_price'] : $v['price'];
                  if (empty($p)) {
                      $p = 0;
                  }
                  if ($p > 0 && ($displayPrice == 0 || $p < $displayPrice)) {
                      $displayPrice = $p;
                      $selectedVariantId = $v['id'];
                  }
              }
          }
        ?>

        <div class="product-price" id="productPrice">
          <?php echo number_format($displayPrice); ?>₫
        </div>

        <!-- CHỌN BIẾN THỂ BÌA CỨNG / BÌA MỀM -->
        <?php if (!empty($variants)): ?>
          <div class="variant-selector">
            <strong style="display: block; margin-bottom: 15px; font-size: 16px; color: #333;">
              ✨ Chọn phiên bản:
            </strong>
            <div class="variant-buttons">
              <?php 
              $variantIndex = 0;
              foreach ($variants as $v): 
                  $vPrice = !empty($v['sale_price']) ? $v['sale_price'] : $v['price'];
                  $activeClass = ($variantIndex === 0) ? 'active' : '';
                  $variantIndex++;
              ?>
                <div class="variant-btn <?php echo $activeClass; ?>" 
                     data-id="<?php echo $v['id']; ?>"
                     data-price="<?php echo $vPrice; ?>"
                     onclick="selectVariant(this)">
                  <span class="format-name">📖 <?php echo htmlspecialchars($v['format']); ?></span>
                  <span class="format-price"><?php echo number_format($vPrice); ?>₫</span>
                  <span class="format-stock">Còn: <?php echo (int)$v['stock']; ?> sản phẩm</span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="description-box">
          <strong style="display: block; margin-bottom: 10px; color: var(--primary-color); font-size: 16px;">
            📝 Mô tả sản phẩm:
          </strong>
          <?php 
            $desc = !empty($book['description']) ? $book['description'] : 'Chưa có mô tả';
          ?>
          <p style="line-height: 1.6; color: #555; margin: 0;">
            <?php echo nl2br(htmlspecialchars($desc)); ?>
          </p>
        </div>

        <!-- CHỌN SỐ LƯỢNG -->
        <div class="quantity-section">
          <strong style="font-size: 16px; color: #333; display: block; margin-bottom: 12px;">
            Số lượng:
          </strong>
          <div class="quantity-controls">
            <button type="button" class="btn-quantity" onclick="decreaseQuantity()">
              −
            </button>
            <input type="number" id="quantityInput" value="1" min="1" max="999" onchange="validateQuantity()">
            <button type="button" class="btn-quantity" onclick="increaseQuantity()">
              +
            </button>
          </div>
        </div>

        <!-- NÚT THÊM GIỎ VÀ MUA NGAY -->
        <div class="action-buttons">
          <a href="index.php?controller=cart&action=add&id=<?php echo (int)$book['id']; ?>" 
             class="btn-add-cart" id="addToCartBtn">
            🛒 Thêm vào giỏ hàng
          </a>
          <a href="index.php?controller=cart&action=add&id=<?php echo (int)$book['id']; ?>&buynow=1" 
             class="btn-buy-now" id="buyNowBtn">
            ⚡ Mua ngay
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- PHẦN ĐÁNH GIÁ VÀ BÌNH LUẬN -->
  <div class="review-section">
    <h2 class="review-title">⭐ Đánh giá & Bình luận</h2>
    
    <!-- Thống kê đánh giá -->
    <?php if (!empty($commentStats) && $commentStats['total'] > 0): ?>
      <div class="review-summary">
        <span class="rating-big"><?php echo number_format($commentStats['avg_rating'], 1); ?></span>
        <div class="rating-stars-big">
          <?php 
            $avgRating = round($commentStats['avg_rating']);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $avgRating) {
                    echo '★';
                } else {
                    echo '☆';
                }
            }
          ?>
        </div>
        <p class="total-reviews"><?php echo (int)$commentStats['total']; ?> đánh giá</p>
      </div>
    <?php endif; ?>

    <!-- Form viết bình luận (ĐƠN GIẢN) -->
    <?php if (!empty($_SESSION['user'])): ?>
    <div class="write-review-box">
      <h4>✍️ Viết đánh giá của bạn</h4>
      <form action="index.php?controller=product&action=addComment" method="POST" id="reviewForm">
        <input type="hidden" name="book_id" value="<?php echo (int)$book['id']; ?>">
        <input type="hidden" name="rating" id="ratingValue" value="" required>
        
        <div class="form-group">
          <label>Đánh giá của bạn: <span id="ratingText" style="color: var(--primary-color); font-weight: bold;"></span></label>
          <div class="star-rating-input">
            <span class="star" data-rating="1" onclick="setRating(1)">★</span>
            <span class="star" data-rating="2" onclick="setRating(2)">★</span>
            <span class="star" data-rating="3" onclick="setRating(3)">★</span>
            <span class="star" data-rating="4" onclick="setRating(4)">★</span>
            <span class="star" data-rating="5" onclick="setRating(5)">★</span>
          </div>
          <p class="rating-hint">👆 Click vào sao để chọn điểm</p>
        </div>
        
        <div class="form-group">
          <label>Nội dung bình luận:</label>
          <textarea name="content" class="form-control" rows="4" 
                    placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." 
                    required></textarea>
        </div>
        
        <button type="submit" class="btn-submit-review">
          📝 Gửi đánh giá
        </button>
      </form>
    </div>
    <?php else: ?>
    <div class="login-prompt">
      <p>Bạn cần <a href="index.php?controller=auth&action=login">đăng nhập</a> để viết đánh giá</p>
    </div>
    <?php endif; ?>

    <!-- Danh sách bình luận -->
    <div class="comments-list">
      <h4>💬 Bình luận từ khách hàng</h4>
      
      <?php if (!empty($comments) && count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-item">
          <div class="comment-header">
            <strong>👤 <?php echo htmlspecialchars($comment['user_name']); ?></strong>
            
            <?php if (!empty($comment['rating'])): ?>
            <span class="comment-rating">
              <?php 
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $comment['rating']) {
                        echo '★';
                    } else {
                        echo '☆';
                    }
                }
              ?>
            </span>
            <?php endif; ?>
          </div>
          
          <p class="comment-date">
            <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
          </p>
          
          <div class="comment-content">
            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-comments">
          <p>Chưa có bình luận nào cho sản phẩm này.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- SẢN PHẨM LIÊN QUAN -->
  <?php if (!empty($relatedProducts)): ?>
  <div class="related-section">
    <h2 class="related-title">📚 Sản phẩm liên quan</h2>
    <div class="row">
      <?php foreach ($relatedProducts as $product): ?>
        <div class="col-md-3 col-sm-6">
          <div class="related-card">
            <div class="related-image">
              <?php
                $imgSrc = !empty($product['image_url']) 
                    ? $ASSET . '/' . $product['image_url']
                    : 'https://via.placeholder.com/200x250?text=No+Image';
              ?>
              <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                   alt="<?php echo htmlspecialchars($product['title']); ?>">
            </div>
            <div class="related-info">
              <h5><?php echo htmlspecialchars($product['title']); ?></h5>
              <div class="related-price">
                <?php echo number_format($product['display_price']); ?>₫
              </div>
              <a href="index.php?controller=product&action=detail&id=<?php echo (int)$product['id']; ?>" 
                 class="btn-view">
                Xem chi tiết
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
// Hàm chọn rating sao
function setRating(rating) {
    // Lưu giá trị vào hidden input
    document.getElementById('ratingValue').value = rating;
    
    // Bỏ active của tất cả sao
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => star.classList.remove('active'));
    
    // Thêm active cho các sao được chọn
    for (let i = 0; i < rating; i++) {
        stars[i].classList.add('active');
    }
    
    // Hiển thị text mô tả
    const ratingTexts = {
        1: '(1 sao - Không hài lòng)',
        2: '(2 sao - Chưa tốt lắm)',
        3: '(3 sao - Bình thường)',
        4: '(4 sao - Tốt)',
        5: '(5 sao - Tuyệt vời!)'
    };
    document.getElementById('ratingText').textContent = ratingTexts[rating];
}

// Validate form trước khi submit
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    const rating = document.getElementById('ratingValue').value;
    if (!rating) {
        e.preventDefault();
        alert('⚠️ Vui lòng chọn số sao đánh giá!');
        return false;
    }
});

// Đổi ảnh khi click thumbnail
function changeImage(newSrc) {
    document.querySelector('#mainImage img').src = newSrc;
}

// Chọn biến thể
let selectedVariantId = <?php echo !empty($selectedVariantId) ? $selectedVariantId : 0; ?>;

function selectVariant(element) {
    // Bỏ active của tất cả
    document.querySelectorAll('.variant-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Active nút được chọn
    element.classList.add('active');
    
    // Lấy thông tin
    selectedVariantId = element.getAttribute('data-id');
    const price = element.getAttribute('data-price');
    
    // Cập nhật giá
    document.getElementById('productPrice').innerHTML = 
        new Intl.NumberFormat('vi-VN').format(price) + '₫';
    
    // Cập nhật link với số lượng
    updateCartLinks();
}

// Hàm tăng số lượng
function increaseQuantity() {
    const input = document.getElementById('quantityInput');
    const currentValue = parseInt(input.value) || 1;
    if (currentValue < 999) {
        input.value = currentValue + 1;
        updateCartLinks();
    }
}

// Hàm giảm số lượng
function decreaseQuantity() {
    const input = document.getElementById('quantityInput');
    const currentValue = parseInt(input.value) || 1;
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateCartLinks();
    }
}

// Hàm validate số lượng
function validateQuantity() {
    const input = document.getElementById('quantityInput');
    let value = parseInt(input.value) || 1;
    if (value < 1) value = 1;
    if (value > 99) value = 99;
    input.value = value;
    updateCartLinks();
}

// Cập nhật link với số lượng
function updateCartLinks() {
    const quantity = document.getElementById('quantityInput').value;
    const bookId = <?php echo (int)$book['id']; ?>;
    const variantParam = selectedVariantId ? '&variant_id=' + selectedVariantId : '';
    
    document.getElementById('addToCartBtn').href = 
        'index.php?controller=cart&action=add&id=' + bookId + variantParam + '&quantity=' + quantity;
    document.getElementById('buyNowBtn').href = 
        'index.php?controller=cart&action=add&id=' + bookId + variantParam + '&quantity=' + quantity + '&buynow=1';
}
</script>