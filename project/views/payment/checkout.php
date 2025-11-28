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

           <!-- ⭐ Nút cập nhật địa chỉ căn TRÁI -->
          <div class="text-start mt-3">
            <a href="index.php?controller=account&action=address" 
              class="btn btn-outline-primary btn-sm">
              Cập nhật địa chỉ
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
