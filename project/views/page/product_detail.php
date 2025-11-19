<?php
// Các biến được truyền từ ProductController:
// $book, $variants, $images
// Biến $ASSET đã được BaseController set = 'public'
?>
<div class="container py-4">
  <div class="row">
    <!-- Cột ảnh -->
    <div class="col-md-5">
      <?php
        // Ảnh chính
        $mainImage = null;
        if (!empty($images)) {
            $mainImage = $images[0]['image_url'] ?? null;
        }
        if ($mainImage === null || $mainImage === '') {
            $mainImageSrc = 'https://dummyimage.com/400x500/eee/aaa&text=No+Image';
        } else {
            $mainImageSrc = $ASSET . '/' . ltrim($mainImage, '/');
        }
      ?>
      <div class="border mb-3">
        <img src="<?= htmlspecialchars($mainImageSrc) ?>" 
             alt="<?= htmlspecialchars($book['title']) ?>" 
             class="img-fluid">
      </div>

      <?php if (!empty($images) && count($images) > 1): ?>
        <div class="d-flex gap-2 flex-wrap">
          <?php foreach ($images as $img): 
              $thumbSrc = $ASSET . '/' . ltrim($img['image_url'], '/');
          ?>
            <div style="width:70px;height:90px" class="border overflow-hidden">
              <img src="<?= htmlspecialchars($thumbSrc) ?>" 
                   class="img-fluid" 
                   alt="thumb">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Cột thông tin -->
    <div class="col-md-7">
      <h1 class="h3 mb-2">
        <?= htmlspecialchars($book['title']) ?>
      </h1>

      <p class="text-muted mb-1">
        Danh mục ID: <?= (int)$book['category_id'] ?>
      </p>

      <?php
        // Tính giá hiển thị: min(sale_price, price) trong các variant
        $displayPrice = 0;
        if (!empty($variants)) {
            $prices = [];
            foreach ($variants as $v) {
                $p = $v['sale_price'] ?? $v['price'] ?? 0;
                $p = (int)$p;
                if ($p > 0) $prices[] = $p;
            }
            if (!empty($prices)) {
                $displayPrice = min($prices);
            }
        }
      ?>

      <div class="mb-3">
        <span class="h4 text-danger">
          <?= number_format($displayPrice) ?>₫
        </span>
        <?php if (!empty($book['rating_avg'])): ?>
          <span class="ms-3 text-warning">
            ★ <?= number_format($book['rating_avg'], 1) ?> 
            (<?= (int)$book['review_count'] ?> đánh giá)
          </span>
        <?php endif; ?>
      </div>

      <?php if (!empty($variants)): ?>
        <div class="mb-3">
          <strong>Phiên bản:</strong>
          <div class="d-flex flex-wrap gap-2 mt-1">
            <?php foreach ($variants as $v): ?>
              <span class="badge bg-light text-dark border">
                <?= htmlspecialchars($v['format'] ?: 'Mặc định') ?> -
                <?= number_format($v['sale_price'] ?? $v['price']) ?>₫
                (Kho: <?= (int)$v['stock'] ?>)
              </span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <p><?= nl2br(htmlspecialchars($book['description'] ?? 'Chưa có mô tả')) ?></p>
      </div>

      <div class="d-flex gap-2">
        <a href="index.php?controller=cart&action=add&id=<?= (int)$book['id'] ?>" 
           class="btn btn-primary">
          Thêm vào giỏ
        </a>
        <a href="index.php?controller=category&action=index&"
           class="btn btn-outline-secondary">
          ← Quay lại danh sách
        </a>
      </div>
    </div>
  </div>
</div>
