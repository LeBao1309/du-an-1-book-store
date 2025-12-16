<?php
// views/payment/vnpay_return.php
?>
<div class="container my-5">
  <h1 class="mb-4">Kết quả thanh toán VNPay</h1>

  <?php if (!empty($success) && $success): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($message ?? 'Thanh toán VNPay thành công.', ENT_QUOTES, 'UTF-8') ?>
    </div>

    <!-- Thông tin đơn hàng -->
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <?php if (!empty($orderId)): ?>
          <p class="mb-2">
            <strong>Mã đơn hàng:</strong>
            <?= htmlspecialchars((string)$orderId, ENT_QUOTES, 'UTF-8') ?>
          </p>
        <?php endif; ?>

        <?php if (!empty($amount)): ?>
          <?php $amountVnd = (int)$amount / 100; ?>
          <p class="mb-2">
            <strong>Số tiền thanh toán:</strong>
            <?= number_format($amountVnd) ?>₫
          </p>
        <?php endif; ?>

        <p class="text-muted mt-2 mb-0">
          Cảm ơn bạn đã thanh toán qua VNPay. Đơn hàng của bạn sẽ được xử lý trong thời gian sớm nhất.
        </p>
      </div>
    </div>

    <!-- Danh sách sản phẩm trong đơn -->
    <?php if (!empty($orderItems) && is_array($orderItems)): ?>
      <div class="card shadow-sm mt-4">
        <div class="card-header">
          Sản phẩm trong đơn hàng
        </div>

        <!-- ✅ TĂNG LỀ GIỐNG COD: bỏ p-0, thêm padding cho khu vực table -->
        <div class="card-body">
          <div class="table-responsive px-2 px-md-3 py-2">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Sách</th>
                  <th class="text-center">Số lượng</th>
                  <th class="text-end">Đơn giá</th>
                  <th class="text-end">Thành tiền</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($orderItems as $item): ?>
                  <?php
                    $qty      = (int)($item['quantity'] ?? 0);
                    $price    = (int)($item['price'] ?? 0);
                    $subtotal = (int)($item['subtotal'] ?? ($qty * $price));

                    $bookTitle = $item['title'] ?? 'Không tên';
                    $format    = $item['format'] ?? null;

                    $rawImage = $item['image_url'] ?? '';
                    if (!empty($rawImage)) {
                      $imageSrc = (isset($ASSET) ? $ASSET . '/' : '') . ltrim($rawImage, '/');
                    } else {
                      $imageSrc = isset($ASSET)
                        ? $ASSET . '/img/placeholder-book.png'
                        : '/assets/img/placeholder-book.png';
                    }
                  ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <img
                          src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                          alt="<?= htmlspecialchars($bookTitle, ENT_QUOTES, 'UTF-8') ?>"
                          style="width:60px; height:80px; object-fit:cover; margin-right:12px; border-radius:4px;"
                        >
                        <div>
                          <div style="font-weight: 600;">
                            <?= htmlspecialchars($bookTitle, ENT_QUOTES, 'UTF-8') ?>
                          </div>

                          <?php if (!empty($format)): ?>
                            <div class="text-muted" style="font-size: 13px;">
                              Loại bìa: <?= htmlspecialchars($format, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>

                    <td class="text-center"><?= $qty ?></td>
                    <td class="text-end"><?= number_format($price) ?>₫</td>
                    <td class="text-end"><?= number_format($subtotal) ?>₫</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-primary mt-4">
      Tiếp tục mua sách
    </a>

  <?php else: ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($message ?? 'Thanh toán VNPay thất bại hoặc dữ liệu không hợp lệ.', ENT_QUOTES, 'UTF-8') ?>
    </div>

    <a href="index.php?controller=cart&action=index" class="btn btn-secondary mt-3">
      Quay lại giỏ hàng
    </a>
  <?php endif; ?>
</div>
