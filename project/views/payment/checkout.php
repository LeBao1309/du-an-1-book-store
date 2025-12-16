<?php
// views/payment/checkout.php

// Kỳ vọng controller truyền:
// $cartItems, $user, $shipping, $addresses,
// $totalQuantity, $totalAmount, $discountAmount, $finalTotal,
// $appliedCoupon, $canCheckout, $csrf, $ASSET
?>
<style>
.checkout-page .card { border-radius: 14px; }
.checkout-actions { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.address-modal .css-modal-body { max-height: 60vh; overflow: auto; }
@media (max-width: 768px) {
  .checkout-page h1 { font-size: 22px; }
  .checkout-page .card-body { padding: 14px; }
  .checkout-page .table-responsive { border: 1px solid #e5e7eb; border-radius: 10px; }
  .checkout-page th, .checkout-page td { white-space: nowrap; }
  .checkout-actions { flex-direction: column; align-items: stretch; }
  .checkout-actions .btn { width: 100%; }
}
</style>

<div class="container my-5">
  <div class="checkout-page">
  <h1 class="mb-4">Thanh toán đơn hàng</h1>

  <!-- Tóm tắt giỏ hàng -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      Tóm tắt đơn hàng
    </div>

    <div class="card-body">
      <?php if (!empty($cartItems)): ?>

        <?php
          // ✅ User lấy từ controller (không đọc session ở view)
          $fullName = 'Chưa cập nhật';
          if (!empty($user['name'])) {
            $fullName = $user['name'];
          } elseif (!empty($user['full_name'])) {
            $fullName = $user['full_name'];
          }

          $email = !empty($user['email']) ? $user['email'] : 'Chưa cập nhật';

          // ✅ Shipping lấy từ controller
          $shippingPhone   = !empty($shipping['shipping_phone']) ? $shipping['shipping_phone'] : 'Chưa cập nhật';
          $shippingAddress = !empty($shipping['full_address'])   ? $shipping['full_address']   : 'Chưa cập nhật';
        ?>

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
              <?php foreach ($cartItems as $item): ?>
                <?php
                  // ✅ ảnh từ cartItems
                  $rawImage = $item['image_url'] ?? '';
                  $imageSrc = ($rawImage === '' || $rawImage === null)
                    ? $ASSET . '/img/placeholder-book.png'
                    : $ASSET . '/' . ltrim($rawImage, '/');

                  // ✅ số lượng/giá/subtotal từ hydrate (DB)
                  $qty      = (int)($item['quantity'] ?? 0);
                  $price    = (int)($item['price'] ?? 0);
                  $subtotal = (int)($item['subtotal'] ?? 0);
                ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img
                        src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($item['title'] ?? 'Sách', ENT_QUOTES, 'UTF-8') ?>"
                        style="width:55px; height:75px; object-fit:cover; margin-right:12px; border-radius:4px;"
                      >
                      <div>
                        <div class="fw-semibold">
                          <?= htmlspecialchars($item['title'] ?? 'Không tên', ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <?php if (!empty($item['format'])): ?>
                          <div class="text-muted small">
                            <?= htmlspecialchars($item['format'], ENT_QUOTES, 'UTF-8') ?>
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

            <!-- 👇 THÔNG TIN NGƯỜI NHẬN & ĐỊA CHỈ GIAO HÀNG -->
            <tfoot>
              <tr>
                <td colspan="4">
                  <div class="mt-3 p-3 border rounded bg-light">
                    <h5 class="mb-3">Thông tin người nhận & địa chỉ giao hàng</h5>

                    <p class="mb-1">
                      <strong>Họ tên:</strong>
                      <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="mb-1">
                      <strong>Email:</strong>
                      <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="mb-1">
                      <strong>Số điện thoại nhận hàng:</strong>
                      <?= htmlspecialchars($shippingPhone, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="mb-0">
                      <strong>Địa chỉ giao hàng:</strong>
                      <?= htmlspecialchars($shippingAddress, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>

          <!-- MODAL CHỌN / THÊM ĐỊA CHỈ GIAO HÀNG (THUẦN CSS) -->
          <div id="addressModal" class="css-modal-overlay">
            <div class="css-modal-box">

              <?php if (!empty($addresses)): ?>
                <!-- ✅ TRƯỜNG HỢP CÓ ĐỊA CHỈ -->
                <form action="index.php?controller=payment&action=selectShipping" method="POST">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

                  <div class="css-modal-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chọn địa chỉ giao hàng</h5>
                    <a href="#" class="css-modal-close" aria-label="Đóng">&times;</a>
                  </div>

                  <div class="css-modal-body">
                    <?php foreach ($addresses as $addr): ?>
                      <div class="form-check mb-2 p-2 border rounded">
                        <input
                          class="form-check-input"
                          type="radio"
                          name="shipping_id"
                          id="addr_<?= (int)$addr['id'] ?>"
                          value="<?= (int)$addr['id'] ?>"
                          <?= isset($shipping['id']) && (int)$shipping['id'] === (int)$addr['id'] ? 'checked' : '' ?>
                        >
                        <label class="form-check-label" for="addr_<?= (int)$addr['id'] ?>">
                          <div><strong><?= htmlspecialchars($addr['receiver_name'] ?? $fullName, ENT_QUOTES, 'UTF-8') ?></strong></div>
                          <div>Điện thoại: <?= htmlspecialchars($addr['shipping_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                          <div>Địa chỉ: <?= htmlspecialchars($addr['full_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>

                          <?php if (!empty($addr['is_default'])): ?>
                            <span class="badge bg-success mt-1">Mặc định</span>
                          <?php endif; ?>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="css-modal-footer d-flex justify-content-between">
                    <a href="index.php?controller=account&action=address" class="btn btn-sm btn-address-trigger">
                      + Thêm địa chỉ
                    </a>

                    <div>
                      <a href="#" class="btn btn-secondary btn-sm">Đóng</a>
                      <button type="submit" class="btn btn-success btn-sm">
                        Chọn địa chỉ này
                      </button>
                    </div>
                  </div>
                </form>

              <?php else: ?>
                <!-- ❌ TRƯỜNG HỢP KHÔNG CÓ ĐỊA CHỈ -->
                <div class="css-modal-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Địa chỉ giao hàng</h5>
                  <a href="#" class="css-modal-close" aria-label="Đóng">&times;</a>
                </div>

                <div class="css-modal-body">
                  <div class="alert alert-info mb-0">
                    Bạn chưa có địa chỉ giao hàng nào.
                  </div>
                </div>

                <div class="css-modal-footer d-flex justify-content-between">
                  <a href="index.php?controller=account&action=address" class="btn btn-sm btn-address-trigger">
                    + Thêm địa chỉ
                  </a>
                  <a href="#" class="btn btn-secondary btn-sm">Đóng</a>
                </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- ⭐ Nút mở MODAL -->
          <div class="text-start mt-3">
            <a href="#addressModal" class="btn btn-sm btn-address-trigger">
              Chọn / thêm địa chỉ giao hàng
            </a>
          </div>
        </div>

        <!-- ================= TỔNG TIỀN + COUPON ================= -->
        <div class="mt-3">
          <p class="mb-1"><strong>Tổng số lượng:</strong> <?= (int)($totalQuantity ?? 0) ?> cuốn</p>
          <p class="mb-1"><strong>Tạm tính:</strong> <?= number_format((int)($totalAmount ?? 0)) ?>₫</p>

          <div class="d-flex align-items-center gap-2 mb-2">
            <?php if (!empty($appliedCoupon)): ?>
              <span class="badge bg-success">
                Đã áp dụng mã: <?= htmlspecialchars($appliedCoupon['code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
              </span>

              <form action="index.php?controller=payment&action=removeCoupon" method="post" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-link btn-sm text-danger p-0">Bỏ mã</button>
              </form>
            <?php else: ?>
              <form action="index.php?controller=payment&action=applyCoupon" method="post" class="d-flex gap-2 flex-wrap">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="coupon_code" class="form-control form-control-sm"
                       placeholder="Nhập mã giảm giá" style="max-width:200px;">
                <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
              </form>
            <?php endif; ?>
          </div>

          <?php if (!empty($discountAmount) && (float)$discountAmount > 0): ?>
            <p class="mb-1 text-success">
              <strong>Giảm giá:</strong> -<?= number_format((int)$discountAmount) ?>₫
            </p>
          <?php endif; ?>

          <p class="fs-5 mb-0">
            <strong>Tổng thanh toán:</strong>
            <span class="text-danger fw-bold">
              <?= number_format((int)($finalTotal ?? 0)) ?>₫
            </span>
          </p>
        </div>

      <?php else: ?>
        <p>Giỏ hàng đang trống.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Form chọn phương thức thanh toán -->
  <div class="card shadow-sm">
    <div class="card-header">
      Chọn phương thức thanh toán
    </div>

    <div class="card-body">
      <?php $canCheckout = $canCheckout ?? false; ?>

      <?php if (!$canCheckout): ?>
        <div class="alert alert-warning">
          Bạn chưa có <strong>số điện thoại nhận hàng</strong> và <strong>địa chỉ giao hàng mặc định</strong>.
          Vui lòng cập nhật trước khi đặt hàng.
        </div>

        <a href="index.php?controller=account&action=address" class="btn btn-outline-primary">
          Cập nhật địa chỉ giao hàng
        </a>
      <?php else: ?>
        <form action="index.php?controller=payment&action=process" method="POST">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="cod" checked>
              <label class="form-check-label" for="pm_cod">
                Thanh toán khi nhận hàng (COD)
              </label>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_method" id="pm_vnpay" value="vnpay">
              <label class="form-check-label" for="pm_vnpay">
                Thanh toán qua VNPay
              </label>
            </div>
          </div>

          <div class="checkout-actions">
            <a href="index.php?controller=cart&action=index" class="btn btn-outline-secondary">
              ⬅ Quay lại giỏ hàng
            </a>

            <button type="submit" class="btn btn-success">
              Đặt hàng & Thanh toán
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
  </div>
</div>
