<?php
// Các biến này sẽ được truyền từ Controller
$allCategories = $allCategories ?? [];
$publishers = $publishers ?? []; 
// $genres đã bị xóa
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$currentCatId = $category['id'] ?? 0; // ID của danh mục đang xem
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
                   class="<?= (int)$cat['id'] === $currentCatId ? 'active' : '' ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
          
          <li class="mt-2">
            <a href="index.php?controller=category&action=index&id=0"
               class="<?= $currentCatId === 0 ? 'active' : '' ?>">
              Xem tất cả sản phẩm
            </a>
          </li>
        </ul>
      </div>
      <div class="filter-block">
        <h3 class="filter-title">Nhà xuất bản</h3>
        <ul class="filter-list">
          <li><a href="#">NXB Văn Học</a></li>
          <li><a href="#">NXB Tri Thức</a></li>
          <li><a href="#">NXB Thế Giới</a></li>
        </ul>
      </div>

      </aside>

    <main class="col-lg-9">
      
      <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
        <h2 class="h4 mb-0">
          <?= htmlspecialchars($category['name'] ?? 'Tất cả sản phẩm') ?>
        </h2>
        <select class="form-select w-auto" aria-label="Sắp xếp">
          <option value="newest" selected>Sắp xếp: Mới nhất</option>
          <option value="price-asc">Giá: Thấp đến Cao</option>
          <option value="price-desc">Giá: Cao đến Thấp</option>
        </select>
      </div>

      <div class="row">
        <?php if (!empty($books)): ?>
          <?php foreach ($books as $book): ?>
            <?php
              $rawImage = $book['image_url'] ?? '';
              if ($rawImage === '' || $rawImage === null) {
                  $imageSrc = 'https://dummyimage.com/300x400/eee/aaa&text=No+Image';
              } else {
                  $imageSrc = $ASSET . '/' . ltrim($rawImage, '/');
              }
            ?>

            <div class="col-lg-4 col-md-6 mb-4">
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
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Không có sản phẩm trong danh mục này.</p>
        <?php endif; ?>
      </div>

      <nav class="mt-4" aria-label="Phân trang">
        <ul class="pagination justify-content-center">
          
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?controller=category&action=index&id=<?= $currentCatId ?>&page=<?= $page - 1 ?>">&laquo;</a>
            </li>
          <?php else: ?>
            <li class="page-item disabled">
              <span class="page-link">&laquo;</span>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="?controller=category&action=index&id=<?= $currentCatId ?>&page=<?= $i ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?controller=category&action=index&id=<?= $currentCatId ?>&page=<?= $page + 1 ?>">&raquo;</a>
            </li>
          <?php else: ?>
            <li class="page-item disabled">
              <span class="page-link">&raquo;</span>
            </li>
          <?php endif; ?>

        </ul>
      </nav>

    </main>
  </div>
</div>