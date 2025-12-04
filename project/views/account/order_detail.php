<?php $active = 'orders'; ?>
<section class="container my-4">
    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
            <div class="card shadow-sm border-0 acc-content">
                <div class="acc-content__head d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-white fw-bold">Đơn hàng #<?= $order['id'] ?></h4>
                    <a href="index.php?controller=account&action=orders" class="btn btn-sm btn-light text-dark fw-bold">
                        ⬅ Quay lại
                    </a>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Địa chỉ nhận hàng</h6>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                                <p class="mb-1">SĐT: <?= htmlspecialchars($order['shipping_phone']) ?></p>
                                <p class="mb-0"><?= htmlspecialchars($order['shipping_address']) ?></p>
                                <?php if (!empty($order['note'])): ?>
                                    <hr class="my-2">
                                    <small class="text-muted">Ghi chú: <?= htmlspecialchars($order['note']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Thông tin thanh toán</h6>
                            <div class="border rounded p-3">
                                <p class="mb-2">
                                    Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong>
                                </p>
                                <p class="mb-2">
                                    Thanh toán: <span class="badge bg-secondary"><?= strtoupper($order['payment_method'] ?? 'COD') ?></span>
                                </p>
                                <p class="mb-0">
                                    Trạng thái: 
                                    <span class="fw-bold text-primary">
                                        <?= ucfirst($order['shipping_status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Sản phẩm đã mua</h6>
                    <div class="table-responsive border rounded">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 200px;">Sản phẩm</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <?php 
                                        // 1. Lấy đường dẫn ảnh từ Database
                                        $dbPath = $item['image_url'] ?? '';

                                        // 2. CẤU HÌNH ĐƯỜNG DẪN GỐC (Đã thêm public/)
                                        $baseUrl = '/duan1/du-an-1-book-store/project/public/';

                                        // 3. Nối đường dẫn
                                        if (!empty($dbPath)) {
                                            $imgSrc = $baseUrl . $dbPath;
                                        } else {
                                            $imgSrc = 'https://dummyimage.com/80x120/eee/999?text=No+Img';
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                                     alt="Book Img"
                                                     onerror="this.src='https://dummyimage.com/80x120/eee/999?text=Error';"
                                                     style="width: 80px; height: 120px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; margin-right: 15px;">
                                                
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($item['title']) ?></div>
                                                    <small class="text-muted">Phân loại: <?= htmlspecialchars($item['format'] ?? 'Tiêu chuẩn') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end text-muted"><?= number_format($item['price']) ?>₫</td>
                                        <td class="text-end fw-bold"><?= number_format($item['subtotal']) ?>₫</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng tiền thanh toán:</td>
                                    <td class="text-end text-danger fs-5 fw-bold">
                                        <?= number_format($order['total']) ?>₫
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>