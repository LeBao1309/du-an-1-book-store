<?php
// views/payment/vnpay_return.php
?>
<div class="container my-5">
  <h1 class="mb-4">Kết quả thanh toán VNPay</h1>

  <?php if (!empty($success) && $success): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($message ?? 'Thanh toán VNPay thành công.') ?>
    </div>

    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <?php if (!empty($orderId)): ?>
          <p class="mb-2">
            <strong>Mã giao dịch:</strong>
            <?= htmlspecialchars((string)$orderId) ?>
          </p>
        <?php endif; ?>

        <?php if (!empty($amount)): ?>
          <?php
            // VNPay trả số tiền đã nhân 100, nên chia lại cho dễ đọc
            $amountVnd = (int)$amount / 100;
          ?>
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

    <a href="index.php" class="btn btn-primary mt-3">
      Về trang chủ
    </a>

  <?php else: ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($message ?? 'Thanh toán VNPay thất bại hoặc dữ liệu không hợp lệ.') ?>
    </div>

    <a href="index.php?controller=cart&action=index" class="btn btn-secondary mt-3">
      Quay lại giỏ hàng
    </a>
  <?php endif; ?>
</div>
