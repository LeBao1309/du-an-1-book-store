<?php
// views/payment/checkout.php
?>
<div class="container my-5">
  <h1 class="mb-4">Thanh toán đơn hàng</h1>

  <!-- Tóm tắt giỏ hàng -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      Tóm tắt đơn hàng
    </div>
    <div class="card-body">
      <?php if (!empty($cart)): ?>
        <?php
          // 🔹 LẤY THÔNG TIN USER (TỪ SESSION HOẶC BIẾN CONTROLLER TRUYỀN XUỐNG)
          $currentUser = $currentUser ?? ($_SESSION['user'] ?? null);

          // Tuỳ cấu trúc session: name hoặc full_name
          $fullName = 'Chưa cập nhật';
          if (is_array($currentUser)) {
              if (!empty($currentUser['name'])) {
                  $fullName = $currentUser['name'];
              } elseif (!empty($currentUser['full_name'])) {
                  $fullName = $currentUser['full_name'];
              }
          }

          $email = is_array($currentUser) && !empty($currentUser['email'])
              ? $currentUser['email']
              : 'Chưa cập nhật';

          // 🔹 LẤY ĐỊA CHỈ GIAO HÀNG MẶC ĐỊNH TỪ $shipping
          // $shipping được PaymentController::checkout() truyền xuống
          $shipping = $shipping ?? null;

          $shippingPhone = 'Chưa có địa chỉ mặc định';
          $shippingAddress = 'Chưa có địa chỉ mặc định';

          if (is_array($shipping)) {
              if (!empty($shipping['shipping_phone'])) {
                  $shippingPhone = $shipping['shipping_phone'];
              }
              if (!empty($shipping['full_address'])) {
                  $shippingAddress = $shipping['full_address'];
              }
          }
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
            <?php foreach ($cart as $item): ?>
            <?php
                // Lấy ảnh, fallback nếu không có
                $rawImage = $item['image_url'] ?? '';
                if ($rawImage === '' || $rawImage === null) {
                    $imageSrc = $ASSET . '/img/placeholder-book.png';
                } else {
                    $imageSrc = $ASSET . '/' . ltrim($rawImage, '/');
                }

                $qty      = (int)($item['quantity'] ?? 0);
                $price    = (int)($item['price'] ?? 0);
                $subtotal = $qty * $price;
            ?>
            <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img
                      src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                      alt="<?= htmlspecialchars($item['title'] ?? 'Sách') ?>"
                      style="width:55px; height:75px; object-fit:cover; margin-right:12px; border-radius:4px;"
                    >
                    <div>
                      <?= htmlspecialchars($item['title'] ?? 'Không tên') ?>
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
                      <?= htmlspecialchars($fullName) ?>
                    </p>
                    <p class="mb-1">
                      <strong>Email:</strong>
                      <?= htmlspecialchars($email) ?>
                    </p>
                    <p class="mb-1">
                      <strong>Số điện thoại nhận hàng:</strong>
                      <?= htmlspecialchars($shippingPhone) ?>
                    </p>
                    <p class="mb-0">
                      <strong>Địa chỉ giao hàng:</strong>
                      <?= htmlspecialchars($shippingAddress) ?>
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
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

                  <div class="css-modal-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chọn địa chỉ giao hàng</h5>
                    <!-- Nút đóng modal: quay về "#" -->
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
                          <?= isset($shipping['id']) && $shipping['id'] == $addr['id'] ? 'checked' : '' ?>
                        >
                        <label class="form-check-label" for="addr_<?= (int)$addr['id'] ?>">
                          <div><strong><?= htmlspecialchars($addr['receiver_name'] ?? $fullName) ?></strong></div>
                          <div>Điện thoại: <?= htmlspecialchars($addr['shipping_phone'] ?? '') ?></div>
                          <div>Địa chỉ: <?= htmlspecialchars($addr['full_address'] ?? '') ?></div>
                          <?php if (!empty($addr['is_default'])): ?>
                            <span class="badge bg-success mt-1">Mặc định</span>
                          <?php endif; ?>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="css-modal-footer d-flex justify-content-between">
                    <!-- NÚT THÊM ĐỊA CHỈ: LUÔN HIỆN -->
                    <a
                      href="index.php?controller=account&action=address"
                      class="btn btn-sm btn-address-trigger"
                    >
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
                  <a
                    href="index.php?controller=account&action=address"
                    class="btn btn-sm btn-address-trigger"
                  >
                    + Thêm địa chỉ
                  </a>
                  <a href="#" class="btn btn-secondary btn-sm">Đóng</a>
                </div>


              <?php endif; ?>

            </div>
          </div>
        <!-- ⭐ Nút mở MODAL chọn / thêm địa chỉ (căn trái) -->
          <div class="text-start mt-3">
            <a
              href="#addressModal"
              class="btn btn-sm btn-address-trigger"
            >
              Chọn / thêm địa chỉ giao hàng
            </a>
          </div>


        </div>

        <div class="mt-3 text-end">
          <p class="mb-1">
            <strong>Tổng số lượng:</strong> <?= (int)($totalQuantity ?? 0) ?> cuốn
          </p>
          <p class="fs-5 mb-0">
            <strong>Tổng tiền:</strong>
            <span class="text-danger fw-bold">
              <?= number_format((int)($totalAmount ?? 0)) ?>₫
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

      <?php
        // Biến này được truyền từ PaymentController::checkout()
        $canCheckout = $canCheckout ?? false;
      ?>

      <?php if (!$canCheckout): ?>
        <!-- ❌ CHƯA CÓ ĐỊA CHỈ + SĐT HỢP LỆ -->
        <div class="alert alert-warning">
          Bạn chưa có <strong>số điện thoại nhận hàng</strong> và <strong>địa chỉ giao hàng mặc định</strong>.
          Vui lòng cập nhật trước khi đặt hàng.
        </div>

        <a
          href="index.php?controller=account&action=address"
          class="btn btn-outline-primary"
        >
          Cập nhật địa chỉ giao hàng
        </a>
      <?php else: ?>
        <!-- ✅ ĐỦ ĐIỀU KIỆN: HIỆN FORM THANH TOÁN -->
        <form action="index.php?controller=payment&action=process" method="POST">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

          <div class="mb-3">
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                name="payment_method"
                id="pm_cod"
                value="cod"
                checked
              >
              <label class="form-check-label" for="pm_cod">
                Thanh toán khi nhận hàng (COD)
              </label>
            </div>

            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                name="payment_method"
                id="pm_vnpay"
                value="vnpay"
              >
              <label class="form-check-label" for="pm_vnpay">
                Thanh toán qua VNPay
              </label>
            </div>

            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                name="payment_method"
                id="pm_momo"
                value="momo"
              >
              <label class="form-check-label" for="pm_momo">
                Thanh toán qua MoMo
              </label>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a
              href="index.php?controller=cart&action=index"
              class="btn btn-outline-secondary"
            >
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
