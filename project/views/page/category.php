<?php
$allCategories = $allCategories ?? [];
$currentCatId = $category['id'] ?? 0;
$searchQuery = $searchQuery ?? '';
$sort = $sort ?? 'newest';
?>

<div class="container mt-5">
  <div class="row">
    <aside class="col-lg-3">
      <div class="filter-block mb-4">
        <h3 class="filter-title">Danh mục</h3>
        <ul class="filter-list">
          <?php if (!empty($allCategories)): ?>
            <?php foreach ($allCategories as $cat): ?>
              <li>
                <a href="index.php?controller=category&action=index&id=<?= (int)$cat['id'] ?>"
                   class="<?= (int)$cat['id'] === $currentCatId && $searchQuery === '' ? 'active' : '' ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
          <li class="mt-2">
            <a href="index.php?controller=category&action=index&id=0"
               class="<?= $currentCatId === 0 && $searchQuery === '' ? 'active' : '' ?>">
              Xem tất cả
            </a>
          </li>
        </ul>
      </div>
    </aside>

    <main class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
        <h2 class="h4 mb-0"><?= htmlspecialchars($category['name'] ?? 'Tất cả sản phẩm') ?></h2>
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="category">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="id" value="<?= $currentCatId ?>">
            <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
            <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
              <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
              <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Giá: Thấp - Cao</option>
              <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Giá: Cao - Thấp</option>
            </select>
        </form>
      </div>

      <div class="row">
        <?php if (!empty($books)): ?>
          <?php foreach ($books as $book): ?>
            <?php
              $rawImage = $book['image_url'] ?? '';
              $imageSrc = ($rawImage === '' || $rawImage === null) 
                  ? 'https://dummyimage.com/300x400/eee/aaa&text=No+Image' 
                  : $ASSET . '/' . ltrim($rawImage, '/');
            ?>

            <div class="col-lg-4 col-md-6 mb-4">
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
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><div class="alert alert-warning">Không có sản phẩm nào.</div></div>
        <?php endif; ?>
      </div>

      <nav class="mt-4">
         <ul class="pagination justify-content-center">
            <?php
            // Logic phân trang giữ nguyên từ file cũ
            if ($searchQuery !== '') {
                $pageParams = "controller=category&action=index&q=" . urlencode($searchQuery);
            } else {
                $pageParams = "controller=category&action=index&id=$currentCatId";
            }
            $pageParams .= "&sort=" . urlencode($sort);
            ?>

            <?php if ($page > 1): ?>
              <li class="page-item"><a class="page-link" href="?<?= $pageParams ?>&page=<?= $page - 1 ?>">&laquo;</a></li>
            <?php else: ?>
              <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= $pageParams ?>&page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <li class="page-item"><a class="page-link" href="?<?= $pageParams ?>&page=<?= $page + 1 ?>">&raquo;</a></li>
            <?php else: ?>
              <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            <?php endif; ?>
         </ul>
      </nav>
    </main>
  </div>
</div>