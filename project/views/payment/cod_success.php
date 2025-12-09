<?php
// views/payment/cod_success.php
?>
<div class="container my-5">
  <h1 class="mb-4">Đặt hàng thành công</h1>

  <div class="alert alert-success">
    Đơn hàng của bạn đã được tạo với hình thức
    <strong>thanh toán khi nhận hàng (COD)</strong>.
  </div>

  <!-- Thông tin đơn hàng -->
  <div class="card shadow-sm mt-3">
    <div class="card-body">
      <p class="mb-2">
        <strong>Mã đơn hàng:</strong>
        <?= htmlspecialchars((string)$orderId) ?>
      </p>

      <p class="mb-2">
        <strong>Số tiền cần thanh toán khi nhận hàng:</strong>
        <?= number_format((int)$amount) ?>₫
      </p>

      <p class="text-muted mt-2 mb-0">
        Nhân viên cửa hàng sẽ liên hệ với bạn để xác nhận thông tin
        và giao hàng trong thời gian sớm nhất.
      </p>
    </div>
  </div>

  <!-- Danh sách sản phẩm trong đơn -->
  <?php if (!empty($orderItems) && is_array($orderItems)): ?>
    <div class="card shadow-sm mt-4">
      <div class="card-header">
        Sản phẩm trong đơn hàng
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
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
                  $price    = (float)($item['price'] ?? 0);
                  $subtotal = (float)($item['subtotal'] ?? ($qty * $price));

                  // Tên sách
                  $bookTitle = $item['title']  ?? 'Không tên';
                  $format    = $item['format'] ?? null;

                  // Ảnh sách
                  $rawImage = $item['image_url'] ?? '';
                  if (!empty($rawImage)) {
                      $imageSrc = (isset($ASSET) ? $ASSET . '/' : '') . ltrim($rawImage, '/');
                  } else {
                      // fallback ảnh mặc định
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
                        alt="<?= htmlspecialchars($bookTitle) ?>"
                        style="width:60px; height:80px; object-fit:cover; margin-right:12px; border-radius:4px;"
                      >
                      <div>
                        <!-- TÊN SẢN PHẨM -->
                        <div style="font-weight: 600;">
                          <?= htmlspecialchars($bookTitle) ?>
                        </div>

                        <!-- THÔNG TIN PHỤ (ví dụ loại bìa) -->
                        <?php if ($format): ?>
                          <div class="text-muted" style="font-size: 13px;">
                            Loại bìa: <?= htmlspecialchars($format) ?>
                          </div>
                        <?php endif; ?>

                        <!-- Nếu sau này anh cần thêm tác giả, mã sách... có thể thêm ở đây -->
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
</div>
