<?php
// Các biến nhận từ controller:
// $book, $variants, $images, $displayPrice
?>

<div class="container my-4">
    <div class="row">
        <!-- ẢNH SÁCH -->
        <div class="col-md-4">
            <?php 
                // Ảnh chính: lấy ảnh đầu tiên
                $mainImage = $images[0]['image_url'] ?? 'img/no-image.png';
            ?>
            <div class="border p-3 mb-3 text-center">
                <img src="<?= $ASSET . '/' . htmlspecialchars($mainImage) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>" 
                     class="img-fluid">
            </div>

            <?php if (count($images) > 1): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach ($images as $img): ?>
                        <div class="border p-1" style="width:70px; height:90px; overflow:hidden;">
                            <img src="<?= $ASSET . '/' . htmlspecialchars($img['image_url']) ?>" 
                                 alt="thumb" class="img-fluid">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- THÔNG TIN SÁCH -->
        <div class="col-md-8">
            <h1 class="h3 mb-3"><?= htmlspecialchars($book['title']) ?></h1>

            <p class="text-muted mb-1">
                Danh mục ID: <?= (int)$book['category_id'] ?> 
                <!-- (nếu muốn có tên danh mục thì join thêm sau) -->
            </p>

            <p class="mb-2">
                Đánh giá: 
                <strong><?= number_format($book['rating_avg'], 1) ?></strong>/5
                (<?= (int)$book['review_count'] ?> lượt)
            </p>

            <?php if ($displayPrice !== null): ?>
                <p class="h4 text-danger mb-3">
                    <?= number_format($displayPrice, 0, ',', '.') ?> đ
                </p>
            <?php else: ?>
                <p class="h5 text-muted mb-3">Liên hệ</p>
            <?php endif; ?>

            <!-- BIẾN THỂ SÁCH -->
            <?php if (!empty($variants)): ?>
                <div class="mb-3">
                    <h5 class="mb-2">Các phiên bản:</h5>
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>Loại bìa / Định dạng</th>
                            <th>Giá</th>
                            <th>Giá KM</th>
                            <th>Tồn kho</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($variants as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['format']) ?></td>
                                <td><?= number_format($v['price'], 0, ',', '.') ?> đ</td>
                                <td>
                                    <?php if (!empty($v['sale_price'])): ?>
                                        <span class="text-danger fw-bold">
                                            <?= number_format($v['sale_price'], 0, ',', '.') ?> đ
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= (int)$v['stock'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- NÚT THÊM GIỎ HÀNG -->
            <div class="mb-4">
                <a href="index.php?controller=cart&action=add&id=<?= (int)$book['id'] ?>" 
                   class="btn btn-primary">
                    Thêm vào giỏ hàng
                </a>
            </div>

            <!-- MÔ TẢ -->
            <div class="mt-4">
                <h5>Mô tả sách</h5>
                <p><?= nl2br(htmlspecialchars($book['description'] ?? 'Chưa có mô tả')) ?></p>
            </div>
        </div>
    </div>
</div>
