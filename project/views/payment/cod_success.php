<?php
// views/payment/cod_success.php
?>
<div class="container my-5">
  <h1 class="mb-4">Đặt hàng thành công</h1>

  <div class="alert alert-success">
    Đơn hàng của bạn đã được tạo với hình thức
    <strong>thanh toán khi nhận hàng (COD)</strong>.
  </div>

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

  <a href="index.php" class="btn btn-primary mt-3">
    Tiếp tục mua sách
  </a>
</div>
