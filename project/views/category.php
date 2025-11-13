<div class="container mt-5"> <h2 class="mb-4">Danh mục: <?= htmlspecialchars($category['name'] ?? 'Không xác định') ?></h2>

  <div class="row">
    <?php if (!empty($books)): ?>
      <?php foreach ($books as $book): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100">
            <img src="<?= $ASSET ?>/<?= htmlspecialchars($book['image_url'] ?: 'https://dummyimage.com/300x400/ccc/fff.png&text=No+Image') ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
              <p class="card-text text-danger fw-bold">
                <?= number_format($book['display_price'] ?? 0) ?>₫
              </p>
              <a href="?c=product&a=detail&id=<?= $book['id'] ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Không có sản phẩm trong danh mục này.</p>
    <?php endif; ?>
  </div>
</div>