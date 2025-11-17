<div class="container my-5">
  <h1 class="mb-4">Giỏ hàng của bạn</h1>

  <?php if (empty($cart)): ?>
    <div class="alert alert-info">
      Giỏ hàng đang trống. Hãy chọn vài cuốn sách yêu thích nhé 📚
    </div>

    <a href="index.php?controller=home&action=index" class="btn btn-primary">
      ⬅ Tiếp tục mua sách
    </a>
  <?php else: ?>
    <form
      action="index.php?controller=cart&action=update"
      method="POST"
      class="card p-3 shadow-sm"
    >
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Sách</th>
              <th class="text-end">Đơn giá</th>
              <th class="text-center">Số lượng</th>
              <th class="text-end">Thành tiền</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($cart as $item): ?>
            <?php
              $rawImage = $item['image_url'] ?? '';
              if ($rawImage === '' || $rawImage === null) {
                  $imageSrc = $ASSET . '/img/placeholder-book.png';
              } else {
                  $imageSrc = $ASSET . '/' . ltrim($rawImage, '/');
              }
            ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img
                    src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($item['title'] ?? 'Sách') ?>"
                    style="width:60px; height:80px; object-fit:cover; margin-right:12px; border-radius:4px;"
                  >
                  <div>
                    <div class="fw-semibold">
                      <?= htmlspecialchars($item['title'] ?? 'Không tên') ?>
                    </div>
                  </div>
                </div>
              </td>
              <td class="text-end">
                <?= number_format((int)($item['price'] ?? 0)) ?>₫
              </td>
              <td class="text-center" style="width:120px;">
                <input
                  type="number"
                  name="quantities[<?= (int)$item['id'] ?>]"
                  value="<?= (int)($item['quantity'] ?? 1) ?>"
                  min="0"
                  class="form-control form-control-sm text-center"
                >
              </td>
              <td class="text-end">
                <?php
                  $qty   = (int)($item['quantity'] ?? 0);
                  $price = (int)($item['price'] ?? 0);
                  $subtotal = $qty * $price;
                ?>
                <?= number_format($subtotal) ?>₫
              </td>
              <td class="text-end">
                <a
                  href="index.php?controller=cart&action=remove&id=<?= (int)$item['id'] ?>"
                  class="btn btn-sm btn-outline-danger"
                >
                  Xóa
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="d-flex gap-2">
          <a
            href="index.php?controller=cart&action=clear"
            class="btn btn-outline-danger"
          >
            Xóa toàn bộ giỏ
          </a>
          <a
            href="index.php?controller=home&action=index"
            class="btn btn-outline-secondary"
          >
            ⬅ Tiếp tục mua sách
          </a>
        </div>

        <div class="text-end">
          <p class="mb-1">
            <strong>Tổng số lượng:</strong>
            <?= (int)$totalQuantity ?> cuốn
          </p>
          <p class="fs-5 mb-2">
            <strong>Tổng tiền:</strong>
            <span class="text-danger fw-bold">
              <?= number_format((int)$totalPrice) ?>₫
            </span>
          </p>
          <button type="submit" class="btn btn-primary">
            Cập nhật giỏ hàng
          </button>
          <button type="button" class="btn btn-success" disabled>
            Thanh toán (sẽ làm ở phase sau)
          </button>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
