<div class="container mt-5">
  <h2 class="mb-4">
    Danh mục: <?= htmlspecialchars($category['name'] ?? 'Tất cả sản phẩm') ?>
  </h2>

  <div class="row">
    <?php if (!empty($books)): ?>
      <?php foreach ($books as $book): ?>

        <?php

          $rawImage = $book['image_url'] ?? '';

          if ($rawImage === '' || $rawImage === null) {
              $imageSrc = $ASSET . '/img/placeholder-book.png';
          } else {

              $imageSrc = $ASSET . '/' . ltrim($rawImage, '/');
          }
        ?>

        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm">
            <img
              src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
              class="card-img-top"
              alt="<?= htmlspecialchars($book['title']) ?>"
            >

            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-2">
                <?= htmlspecialchars($book['title']) ?>
              </h5>

              <p class="card-text text-danger fw-bold mb-3">
                <?= number_format($book['display_price'] ?? 0) ?>₫
              </p>

              <a
                href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>"
                class="btn btn-primary btn-sm mt-auto"
              >
                Xem chi tiết
              </a>
              <a href="index.php?controller=cart&action=add&id=<?= (int)$book['id'] ?>"
                class="btn btn-outline-primary btn-sm mt-2"
              >
                Thêm vào giỏ
              </a>

            </div>
          </div>
        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <p>Không có sản phẩm trong danh mục này.</p>
    <?php endif; ?>
  </div>
</div>
