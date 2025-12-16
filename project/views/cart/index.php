<?php
// views/cart/index.php
// Controller truyền: 
// $cartItems, $totalQuantity, $totalPrice, $csrf, $ASSET
?>
<style>
.cart-table-wrapper .table { min-width: 100%; }
.cart-actions { display: flex; gap: 10px; justify-content: space-between; align-items: center; flex-wrap: wrap; }
@media (max-width: 768px) {
  .cart-table-wrapper { overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
  .cart-actions { flex-direction: column; align-items: stretch; }
  .cart-actions .btn { width: 100%; }
  .table thead { display: none; }
  .table tbody tr { display: block; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .table tbody td { display: flex; justify-content: space-between; gap: 8px; padding: 6px 12px; }
  .table tbody td:first-child { flex-direction: column; align-items: flex-start; }
  .table tbody td:last-child { justify-content: flex-end; }
  .table tbody td:before { content: attr(data-label); font-weight: 600; color: #475569; }
}
</style>

<div class="container my-5">
  <h1 class="mb-4">Giỏ hàng của bạn</h1>

  <?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
      Giỏ hàng đang trống. Hãy chọn vài cuốn sách yêu thích nhé 📚
    </div>

    <a href="index.php?controller=home&action=index" class="btn btn-primary">
      ⬅ Tiếp tục mua sách
    </a>

  <?php else: ?>
  <div class="card p-3 shadow-sm cart-table-wrapper">

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
        <?php foreach ($cartItems as $item): ?>
          <?php
            // Ảnh
            $rawImage = $item['image_url'] ?? '';
            $imageSrc = ($rawImage === '' || $rawImage === null)
              ? $ASSET . '/img/placeholder-book.png'
              : $ASSET . '/' . ltrim($rawImage, '/');

            // Dữ liệu hydrate từ DB
            $qty      = (int)$item['quantity'];
            $price    = (int)$item['price'];
            $subtotal = (int)$item['subtotal'];
            $variantId = (int)$item['variant_id'];
          ?>
          <tr>
            <td data-label="Sách">
              <div class="d-flex align-items-center">
                <img
                  src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($item['title'] ?? 'Sách', ENT_QUOTES, 'UTF-8') ?>"
                  style="width:60px; height:80px; object-fit:cover; margin-right:12px; border-radius:4px;"
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

            <td class="text-end" data-label="Đơn giá">
              <?= number_format($price) ?>₫
            </td>

            <td class="text-center" style="width:160px;" data-label="Số lượng">
              <div class="d-flex justify-content-center align-items-center gap-1">

                <!-- GIẢM -->
                <form
                  action="index.php?controller=cart&action=updateSingle"
                  method="POST"
                  class="d-inline"
                >
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="id" value="<?= $variantId ?>">
                  <input type="hidden" name="direction" value="dec">
                  <button type="submit" class="btn btn-sm btn-outline-secondary">-</button>
                </form>

                <!-- SỐ LƯỢNG -->
                <input
                  type="number"
                  value="<?= $qty ?>"
                  class="form-control form-control-sm text-center"
                  style="max-width:60px;"
                  readonly
                >

                <!-- TĂNG -->
                <form
                  action="index.php?controller=cart&action=updateSingle"
                  method="POST"
                  class="d-inline"
                >
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="id" value="<?= $variantId ?>">
                  <input type="hidden" name="direction" value="inc">
                  <button type="submit" class="btn btn-sm btn-outline-secondary">+</button>
                </form>
              </div>
            </td>

            <td class="text-end" data-label="Thành tiền">
              <?= number_format($subtotal) ?>₫
            </td>

            <td class="text-end" data-label="Xóa">
              <a
                href="index.php?controller=cart&action=remove&id=<?= $variantId ?>"
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

    <div class="d-flex justify-content-between align-items-center mt-3 cart-actions">
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

        <?php $loggedIn = !empty($_SESSION['user']); ?>
        <?php if ($loggedIn): ?>
          <a
            href="index.php?controller=payment&action=checkout"
            class="btn btn-success"
          >
            Thanh toán
          </a>
        <?php else: ?>
          <a
            href="index.php?controller=auth&action=login"
            class="btn btn-success"
          >
            Đăng nhập để thanh toán
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
<?php endif; ?>
</div>
