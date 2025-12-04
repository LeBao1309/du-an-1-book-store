<?php
// 1. Lấy cây danh mục
require_once __DIR__ . '/../../models/CategoryModel.php';
$catTree = CategoryModel::getTree();

// 2. Tham số
$currentParams = $currentParams ?? [
    'category_id' => 0, 'keyword' => '', 'sort' => 'newest', 
    'min_price' => 0, 'max_price' => 0
];
$books = $books ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;

// 3. Cấu hình Slider
$MAX_RANGE = 2000000; 
$currMin = !empty($currentParams['min_price']) ? $currentParams['min_price'] : 0;
$currMax = !empty($currentParams['max_price']) ? $currentParams['max_price'] : $MAX_RANGE;
if($currMax > $MAX_RANGE) $currMax = $MAX_RANGE;
?>

<div class="container mt-4">
  <div class="row">
    
    <aside class="col-lg-3 mb-4">
      <div class="filter-block p-3 border rounded bg-white shadow-sm">
        <h5 class="mb-3 fw-bold text-primary">⚡ Bộ lọc tìm kiếm</h5>
        
        <form action="index.php" method="GET" id="filterForm">
            <input type="hidden" name="controller" value="category">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="q" value="<?= htmlspecialchars($currentParams['keyword'] ?? '') ?>">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($currentParams['sort'] ?? 'newest') ?>">

            <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2">📂 Danh mục</h6>
                <div class="category-tree mt-2">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="index.php?controller=category&action=index" 
                               class="<?= ($currentParams['category_id'] == 0) ? 'fw-bold text-primary' : 'text-dark' ?> text-decoration-none">
                               -- Tất cả --
                            </a>
                        </li>

                        <?php foreach ($catTree as $parent): ?>
                            <?php 
                                // Kiểm tra xem có cần mở danh mục này không (khi đang chọn nó hoặc con của nó)
                                $hasChild = !empty($parent['children']);
                                $isActiveParent = ($currentParams['category_id'] == $parent['id']);
                                $isActiveChild = false;
                                if ($hasChild) {
                                    foreach($parent['children'] as $c) {
                                        if($currentParams['category_id'] == $c['id']) {
                                            $isActiveChild = true; break;
                                        }
                                    }
                                }
                                $isOpen = $isActiveParent || $isActiveChild;
                            ?>
                            <li class="mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="index.php?controller=category&action=index&id=<?= $parent['id'] ?>" 
                                       class="<?= $isActiveParent ? 'fw-bold text-primary' : 'text-dark' ?> text-decoration-none flex-grow-1">
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </a>

                                    <?php if ($hasChild): ?>
                                        <button type="button" 
                                                class="btn-toggle-cat <?= $isOpen ? '' : 'collapsed' ?>" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#cat-<?= $parent['id'] ?>" 
                                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($hasChild): ?>
                                    <div class="collapse <?= $isOpen ? 'show' : '' ?>" id="cat-<?= $parent['id'] ?>">
                                        <ul class="list-unstyled ms-3 border-start ps-3 mt-1" style="border-color: #e5e7eb !important;">
                                            <?php foreach($parent['children'] as $child): ?>
                                                <li>
                                                    <a href="index.php?controller=category&action=index&id=<?= $child['id'] ?>" 
                                                       class="d-block py-1 <?= ($currentParams['category_id'] == $child['id']) ? 'fw-bold text-primary' : 'text-secondary' ?> text-decoration-none" style="font-size: 0.9rem;">
                                                        <?= htmlspecialchars($child['name']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="fw-bold border-bottom pb-2">💰 Khoảng giá</h6>
                <div class="price-input-container mt-3">
                    <div class="price-display-text mb-3 text-center">
                        <span id="displayMin">0</span> ₫ — <span id="displayMax">2.000.000</span> ₫
                    </div>
                    <div class="slider-container">
                        <div class="progress" id="priceProgress"></div>
                    </div>
                    <div class="range-input">
                        <input type="range" class="range-min" min="0" max="<?= $MAX_RANGE ?>" value="<?= $currMin ?>" step="10000">
                        <input type="range" class="range-max" min="0" max="<?= $MAX_RANGE ?>" value="<?= $currMax ?>" step="10000">
                    </div>
                    <input type="hidden" name="min_price" id="inputMin" value="<?= $currMin ?>">
                    <input type="hidden" name="max_price" id="inputMax" value="<?= $currMax ?>">
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Áp dụng giá</button>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <a href="index.php?controller=category&action=index" class="text-secondary small text-decoration-underline">Xóa bộ lọc</a>
            </div>
        </form>
      </div>
    </aside>

    <main class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
        <h2 class="h4 mb-0"><?= htmlspecialchars($category['name'] ?? 'Tất cả sản phẩm') ?></h2>
        <form method="GET" action="index.php" class="d-flex align-items-center">
            <input type="hidden" name="controller" value="category">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="id" value="<?= $currentParams['category_id'] ?>">
            <input type="hidden" name="min_price" value="<?= $currentParams['min_price'] ?>">
            <input type="hidden" name="max_price" value="<?= $currentParams['max_price'] ?>">
            <input type="hidden" name="q" value="<?= htmlspecialchars($currentParams['keyword']) ?>">
            
            <label class="me-2 small text-muted">Sắp xếp:</label>
            <select name="sort" class="form-select form-select-sm w-auto shadow-none" onchange="this.form.submit()">
              <option value="newest" <?= $currentParams['sort'] === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
              <option value="price-asc" <?= $currentParams['sort'] === 'price-asc' ? 'selected' : '' ?>>Giá thấp - cao</option>
              <option value="price-desc" <?= $currentParams['sort'] === 'price-desc' ? 'selected' : '' ?>>Giá cao - thấp</option>
            </select>
        </form>
      </div>

      <div class="row">
        <?php if (!empty($books)): ?>
          <?php foreach ($books as $book): ?>
            <?php
              $rawImage = $book['image_url'] ?? '';
              $imageSrc = ($rawImage === '' || $rawImage === null) ? 'https://dummyimage.com/300x400/eee/aaa&text=No+Image' : $ASSET . '/' . ltrim($rawImage, '/');
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100 shadow-sm product-card">
                <div class="card-img-container position-relative">
                    <a href="index.php?controller=account&action=addWishlist&id=<?= (int)$book['id'] ?>" class="btn-wishlist-overlay" title="Yêu thích">♥</a>
                    <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="d-block w-100 h-100">
                      <img src="<?= htmlspecialchars($imageSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                    </a>
                </div>
                <div class="card-body d-flex flex-column pb-4">
                  <p class="card-author text-muted small mb-1"><?= htmlspecialchars($book['author_names'] ?? 'Đang cập nhật') ?></p> 
                  <h6 class="card-title product-title mb-2">
                    <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                  </h6>
                  <?php if(!empty($book['publisher_name'])): ?>
                       <div class="mb-2"><span class="badge bg-light text-dark border fw-normal"><?= htmlspecialchars($book['publisher_name']) ?></span></div>
                  <?php endif; ?>
                  <div class="price-wrap mt-auto">
                    <span class="product-price text-danger fw-bold fs-5"><?= number_format($book['display_price'] ?? 0) ?>₫</span>
                  </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                   <div class="d-flex gap-2">
                      <form method="get" action="index.php" class="m-0 flex-grow-1">
                        <input type="hidden" name="controller" value="cart">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-primary w-100">+ Giỏ</button>
                      </form>
                      <a href="index.php?controller=product&action=detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm btn-outline-dark flex-grow-1">Xem</a>
                   </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><div class="alert alert-warning text-center">Không tìm thấy sản phẩm nào.</div></div>
        <?php endif; ?>
      </div>
      
       <nav class="mt-4">
         <ul class="pagination justify-content-center">
            <?php
            $queryParams = $_GET; unset($queryParams['page']);
            $baseUrl = 'index.php?' . http_build_query($queryParams);
            ?>
            <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="<?= $baseUrl ?>&page=<?= $page - 1 ?>">&laquo;</a></li><?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="<?= $baseUrl ?>&page=<?= $page + 1 ?>">&raquo;</a></li><?php endif; ?>
         </ul>
      </nav>
    </main>
  </div>
</div>

<script>
    // JS cho Slider Giá
    const rangeInput = document.querySelectorAll(".range-input input"),
          progress = document.getElementById("priceProgress"),
          displayMin = document.getElementById("displayMin"),
          displayMax = document.getElementById("displayMax"),
          inputMin = document.getElementById("inputMin"),
          inputMax = document.getElementById("inputMax");
    let priceGap = 50000, maxRange = <?= $MAX_RANGE ?>;
    function formatCurrency(n){return new Intl.NumberFormat('vi-VN').format(n);}
    function updateProgress(){
        let minVal = parseInt(rangeInput[0].value), maxVal = parseInt(rangeInput[1].value);
        displayMin.textContent = formatCurrency(minVal);
        displayMax.textContent = formatCurrency(maxVal);
        inputMin.value = minVal; inputMax.value = maxVal;
        progress.style.left = (minVal/maxRange)*100 + "%";
        progress.style.right = 100 - (maxVal/maxRange)*100 + "%";
    }
    updateProgress();
    rangeInput.forEach(input=>{
        input.addEventListener("input", e=>{
            let minVal = parseInt(rangeInput[0].value), maxVal = parseInt(rangeInput[1].value);
            if((maxVal-minVal)<priceGap){
                if(e.target.className==="range-min") rangeInput[0].value = maxVal-priceGap;
                else rangeInput[1].value = minVal+priceGap;
            }else{ updateProgress(); }
        });
    });
</script>
