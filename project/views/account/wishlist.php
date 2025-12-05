<?php 
// Đảm bảo biến ASSET tồn tại
$ASSET = $ASSET ?? 'public';
$active = 'wishlist'; 
?>

<section class="container my-4">
    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
            <div class="card shadow-sm border-0 acc-content">
                <div class="acc-content__head">
                    <h4 class="mb-0 text-white fw-bold">Sách Yêu Thích</h4>
                    <div class="text-white-50 small">Những cuốn sách bạn đã lưu lại</div>
                </div>

                <div class="card-body p-4">
                    <?php if (empty($books)): ?>
                        <div class="text-center py-5">
                            <div style="font-size: 50px; margin-bottom: 20px;">💔</div>
                            <h5>Danh sách yêu thích đang trống</h5>
                            <p class="text-muted">Hãy dạo một vòng và thả tim cho cuốn sách bạn thích nhé!</p>
                            <a href="index.php?controller=category&action=index" class="btn btn-outline-success mt-2">
                                Khám phá ngay
                            </a>
                        </div>
                    <?php else: ?>
                        
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($books as $book): ?>
                                <?php
                                    // Xử lý đường dẫn ảnh
                                    $rawImage = $book['image_url'] ?? '';
                                    if (empty($rawImage)) {
                                        $imgSrc = 'https://dummyimage.com/150x200/eee/aaa&text=No+Image';
                                    } else {
                                        $imgSrc = $ASSET . '/' . ltrim($rawImage, '/');
                                    }
                                    
                                    // Xử lý giá
                                    $price = isset($book['display_price']) ? (float)$book['display_price'] : 0;
                                ?>
                                
                                <div class="card shadow-sm border-0 overflow-hidden position-relative wishlist-item">
                                    
                                    <a href="index.php?controller=account&action=removeWishlist&id=<?= $book['id'] ?>" 
                                       class="btn-remove"
                                       onclick="return confirm('Xóa cuốn này khỏi yêu thích?')"
                                       title="Xóa bỏ">
                                        ✕
                                    </a>

                                    <div class="d-flex">
                                        <div class="wishlist-img">
                                            <a href="index.php?controller=product&action=detail&id=<?= $book['id'] ?>">
                                                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                                            </a>
                                        </div>

                                        <div class="wishlist-info flex-grow-1 p-3 d-flex flex-column justify-content-center">
                                            
                                            <h5 class="mb-1 product-title-link">
                                                <a href="index.php?controller=product&action=detail&id=<?= $book['id'] ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($book['title']) ?>
                                                </a>
                                            </h5>
                                            
                                            <p class="text-muted small mb-2">
                                                Tác giả: <?= htmlspecialchars($book['author_names'] ?? 'Đang cập nhật') ?>
                                            </p>

                                            <div class="mb-3">
                                                <span class="text-danger fw-bold fs-5">
                                                    <?= number_format($price, 0, ',', '.') ?>₫
                                                </span>
                                            </div>

                                            <div>
                                                <form action="index.php" method="GET" class="m-0">
                                                    <input type="hidden" name="controller" value="cart">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">
                                                        🛒 Thêm vào giỏ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .acc-content__head {
        background: linear-gradient(180deg, var(--brand, #0ea5a5), var(--brand-2, #0fbf9b));
        padding: 20px 22px;
        color: #fff;
    }
    .wishlist-item {
        transition: transform 0.2s;
        border: 1px solid #eee !important;
    }
    .wishlist-item:hover {
        border-color: var(--brand, #0ea5a5) !important;
        transform: translateY(-2px);
    }
    .wishlist-img {
        width: 120px;
        height: 160px;
        flex-shrink: 0;
        background: #f8f9fa;
    }
    .wishlist-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-remove {
        position: absolute; top: 0; right: 0;
        background: #fee2e2; color: #ef4444;
        width: 30px; height: 30px;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none; border-bottom-left-radius: 10px;
        font-weight: bold; z-index: 10; transition: 0.2s;
    }
    .btn-remove:hover { background: #ef4444; color: white; }
    @media (max-width: 576px) {
        .wishlist-img { width: 90px; height: 120px; }
        .product-title-link { font-size: 16px; }
    }
</style>