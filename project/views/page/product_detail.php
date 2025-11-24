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
    
    // Cập nhật link nút
    const bookId = <?php echo (int)$book['id']; ?>;
    document.getElementById('addToCartBtn').href = 
        'index.php?controller=cart&action=add&id=' + bookId + '&variant_id=' + selectedVariantId;
    document.getElementById('buyNowBtn').href = 
        'index.php?controller=cart&action=add&id=' + bookId + '&variant_id=' + selectedVariantId + '&buynow=1';
}
</script>