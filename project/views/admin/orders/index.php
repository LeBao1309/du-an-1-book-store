<?php
/** @var array        $filters */
/** @var array        $pagination */
/** @var string       $csrf */
/** @var array|null   $order        Đơn hàng đang xem chi tiết (có thể null)
/** @var array        $itemsDetail  Danh sách item của đơn đang xem (có thể rỗng) */

$items       = $pagination['items'] ?? [];
$page        = $pagination['page'] ?? 1;
$lastPage    = $pagination['last_page'] ?? 1;
$total       = $pagination['total'] ?? 0;
$itemsDetail = $itemsDetail ?? [];

// Helpers dùng chung cho list + detail
if (!function_exists('wd_status_badge')) {
    function wd_status_badge(string $status): string {
        switch ($status) {
            case 'pending':
                return '<span class="badge badge-muted">Chờ xử lý</span>';
            case 'processing':
                return '<span class="badge badge-warning">Đang xử lý</span>';
            case 'shipped':
                return '<span class="badge badge-info">Đang giao</span>';
            case 'delivered':
                return '<span class="badge badge-success">Đã giao</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Đã hủy</span>';
            default:
                return '<span class="badge badge-muted">'.htmlspecialchars($status).'</span>';
        }
    }
}

if (!function_exists('wd_payment_badge')) {
    function wd_payment_badge(string $status): string {
        switch ($status) {
            case 'paid':
                return '<span class="badge badge-success">Đã thanh toán</span>';
            case 'failed':
                return '<span class="badge badge-danger">Thanh toán lỗi</span>';
            case 'refunded':
                return '<span class="badge badge-warning">Đã hoàn tiền</span>';
            default:
                return '<span class="badge badge-muted">Chưa thanh toán</span>';
        }
    }
}

// Chuẩn bị biến cho chi tiết (nếu có)
$hasDetail    = !empty($order);
$shippingStat = $hasDetail ? ($order['shipping_status'] ?? 'pending') : null;
$payStatus    = $hasDetail ? ($order['payment_status'] ?? 'pending') : null;
$totalRaw     = $hasDetail ? (float)$order['total']           : 0;
$discountRaw  = $hasDetail ? (float)$order['discount_amount'] : 0;
$finalRaw     = $hasDetail ? (float)$order['final_total']     : 0;
$allowChange  = $hasDetail ? !in_array($shippingStat, ['shipped', 'cancelled'], true) : false;
$currentOrderId = $hasDetail ? (int)$order['id'] : 0;
?>

<style>
/* ====== ONLY FOR ORDER ADMIN PAGE ====== */
.order-admin-page {
  margin-top: 4px;
}

/* grid tổng: giống user-admin (1 cột / 2 cột) */
.order-admin-page .admin-grid-2 {
  display: grid;
  gap: 18px;
  align-items: flex-start;
}

.order-admin-page .admin-grid-2.only-list {
  grid-template-columns: minmax(0, 1fr);
}

.order-admin-page .admin-grid-2.has-edit {
  grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
}

/* card chi tiết bên phải sticky */
.order-admin-page .order-detail-card {
  position: sticky;
  top: 10px;
  max-height: calc(100vh - 120px);
  overflow: auto;
  display: none; /* Ẩn mặc định, khi có has-edit mới hiện */
}

/* Khi có chi tiết thì hiện */
.order-admin-page .admin-grid-2.has-edit .order-detail-card {
  display: block;
}

/* header card chi tiết */
.order-admin-page .order-detail-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.order-admin-page .order-detail-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.order-admin-page .order-detail-meta {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}

/* section bên trong card chi tiết */
.order-admin-page .order-section {
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid #e5e7eb;
}

.order-admin-page .order-section:first-of-type {
  border-top: none;
  padding-top: 0;
  margin-top: 0;
}

.order-admin-page .order-meta-list p {
  margin: 2px 0;
  font-size: 14px;
}

/* Tổng tiền nổi bật */
.order-admin-page .order-money-main {
  font-size: 1.2rem;
  font-weight: 600;
  color: #16a34a;
}

/* highlight dòng đơn đang xem chi tiết */
#tblOrders tr.is-active-row {
  background: #e5e7ff;
}

/* nút icon nhỏ trong bảng (dùng lại style từ user-admin) */
.wd-icon-btn {
  border: none;
  background: #eef2ff;
  color: #4f46e5;
  width: 30px;
  height: 30px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  margin-right: 4px;
  transition: background 0.15s ease, transform 0.1s ease;
  text-decoration: none;
}

.wd-icon-btn:hover {
  background: #e0e7ff;
  transform: translateY(-1px);
}

/* badge trạng thái (nếu chưa có global .badge) */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 12px;
}
.badge-success {
  background: #ecfdf3;
  color: #16a34a;
}
.badge-warning {
  background: #fef9c3;
  color: #92400e;
}
.badge-danger {
  background: #fee2e2;
  color: #b91c1c;
}
.badge-muted {
  background: #e5e7eb;
  color: #4b5563;
}
</style>

<div class="order-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Quản lý đơn hàng</h2>
      <p class="admin-section-subtitle">
        Xem và quản lý các đơn hàng trong WiseDecision Bookstore.
      </p>
    </div>
  </div>

  <!-- FILTER BAR -->
  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="orders">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input type="text" name="keyword"
             value="<?= htmlspecialchars($filters['keyword'] ?? ''); ?>"
             class="wd-input"
             placeholder="Mã đơn / Email người mua">
    </div>

    <div class="filter-group">
      <label>Trạng thái vận chuyển</label>
      <select name="shipping_status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="pending"    <?= ($filters['shipping_status'] ?? '')==='pending'   ?'selected':''; ?>>Chờ xử lý</option>
        <option value="processing" <?= ($filters['shipping_status'] ?? '')==='processing'?'selected':''; ?>>Đang xử lý</option>
        <option value="shipped"    <?= ($filters['shipping_status'] ?? '')==='shipped'   ?'selected':''; ?>>Đang giao</option>
        <option value="delivered"  <?= ($filters['shipping_status'] ?? '')==='delivered' ?'selected':''; ?>>Đã giao</option>
        <option value="cancelled"  <?= ($filters['shipping_status'] ?? '')==='cancelled' ?'selected':''; ?>>Đã hủy</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Trạng thái thanh toán</label>
      <select name="payment_status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="pending"  <?= ($filters['payment_status'] ?? '')==='pending' ?'selected':''; ?>>Chưa thanh toán</option>
        <option value="paid"     <?= ($filters['payment_status'] ?? '')==='paid'    ?'selected':''; ?>>Đã thanh toán</option>
        <option value="failed"   <?= ($filters['payment_status'] ?? '')==='failed'  ?'selected':''; ?>>Lỗi thanh toán</option>
        <option value="refunded" <?= ($filters['payment_status'] ?? '')==='refunded'?'selected':''; ?>>Đã hoàn tiền</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Phương thức thanh toán</label>
      <select name="payment_method" class="wd-input">
        <option value="">Tất cả</option>
        <option value="cod"    <?= ($filters['payment_method'] ?? '')==='cod'   ?'selected':''; ?>>COD</option>
        <option value="card"   <?= ($filters['payment_method'] ?? '')==='card'  ?'selected':''; ?>>Thẻ</option>
        <option value="wallet" <?= ($filters['payment_method'] ?? '')==='wallet'?'selected':''; ?>>Ví điện tử</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Kênh / Provider</label>
      <select name="channel" class="wd-input">
        <option value="">Tất cả</option>
        <option value="vnpay" <?= ($filters['channel'] ?? '')==='vnpay'?'selected':''; ?>>VNPAY</option>
        <option value="cod"   <?= ($filters['channel'] ?? '')==='cod'  ?'selected':''; ?>>COD</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Từ ngày</label>
      <input type="date" name="from_date"
             value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>"
             class="wd-input">
    </div>

    <div class="filter-group">
      <label>Đến ngày</label>
      <input type="date" name="to_date"
             value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>"
             class="wd-input">
    </div>

    <div class="filter-actions">
      <button type="submit" class="wd-btn-secondary">Lọc</button>
    </div>
  </form>

  <!-- FLASH -->
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="wd-alert wd-alert-error">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="wd-alert wd-alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>

  <!-- GRID: Bảng + panel chi tiết giống user-admin -->
  <div
    class="admin-grid-2 <?= $hasDetail ? 'has-edit' : 'only-list'; ?>"
    id="orderGrid"
  >

    <!-- BẢNG ĐƠN HÀNG -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách đơn hàng (<?= (int)$total; ?>)</span>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblOrders">
          <thead>
          <tr>
            <th>ID</th>
            <th>Người mua</th>
            <th>Email</th>
            <th>Tổng tiền</th>
            <th>Giảm</th>
            <th>Thanh toán</th>
            <th>Vận chuyển</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php if (!$items): ?>
            <tr>
              <td colspan="9" style="text-align:center;padding:20px;color:#6b7280;">
                Chưa có đơn hàng nào.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($items as $o): ?>
              <?php
              $oid      = (int)$o['id'];
              $isActive = $hasDetail && $oid === $currentOrderId;
              ?>
              <tr class="<?= $isActive ? 'is-active-row' : ''; ?>">
                <td>#<?= $oid; ?></td>
                <td><?= htmlspecialchars($o['user_name'] ?? '—'); ?></td>
                <td><?= htmlspecialchars($o['user_email'] ?? '—'); ?></td>
                <td><?= number_format((float)$o['total'], 0, ',', '.'); ?>đ</td>
                <td><?= number_format((float)$o['discount_amount'], 0, ',', '.'); ?>đ</td>
                <td><?= wd_payment_badge($o['payment_status'] ?? 'pending'); ?></td>
                <td><?= wd_status_badge($o['shipping_status'] ?? 'pending'); ?></td>
                <td><?= htmlspecialchars($o['created_at']); ?></td>
                <td>
                  <!-- Vẫn là reload để lấy đủ chi tiết, nhưng style như nút icon nhỏ -->
                  <a
                    href="index.php?c=orders&a=show&id=<?= $oid; ?>"
                    class="wd-icon-btn"
                    title="Xem chi tiết đơn hàng"
                  >
                    👁
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($lastPage > 1): ?>
        <div class="admin-pagination">
          <?php for ($p = 1; $p <= $lastPage; $p++): ?>
            <?php
              $qs = http_build_query([
                'c' => 'orders',
                'a' => 'index',
                'page' => $p,
                'keyword' => $filters['keyword'] ?? '',
                'shipping_status' => $filters['shipping_status'] ?? '',
                'payment_status' => $filters['payment_status'] ?? '',
                'payment_method' => $filters['payment_method'] ?? '',
                'channel' => $filters['channel'] ?? '',
                'from_date' => $filters['from_date'] ?? '',
                'to_date' => $filters['to_date'] ?? '',
              ]);
            ?>
            <a class="page-link <?= $p === (int)$page ? 'active' : ''; ?>"
               href="index.php?<?= $qs; ?>">
              <?= $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL CHI TIẾT BÊN PHẢI (giống style user-edit-card) -->
    <?php if ($hasDetail): ?>
      <div class="admin-card order-detail-card" id="orderDetailCard">
        <div class="order-detail-header">
          <div>
            <h3>Đơn hàng #<?= (int)$order['id']; ?></h3>
            <div class="order-detail-meta">
              Tạo lúc: <?= htmlspecialchars($order['created_at']); ?>
            </div>
          </div>
          <a href="index.php?c=orders&a=index" class="wd-btn-secondary" style="font-size:13px;">
            Đóng chi tiết
          </a>
        </div>

        <div class="admin-card-body">

          <!-- Thông tin khách + địa chỉ -->
          <div class="order-section order-meta-list">
            <p><strong>Người mua:</strong> <?= htmlspecialchars($order['user_name'] ?? '—'); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['user_email'] ?? '—'); ?></p>
            <p><strong>Điện thoại giao hàng:</strong> <?= htmlspecialchars($order['shipping_phone'] ?? '—'); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong><br>
              <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '—')); ?>
            </p>
            <?php if (!empty($order['note'])): ?>
              <p><strong>Ghi chú:</strong><br>
                <?= nl2br(htmlspecialchars($order['note'])); ?>
              </p>
            <?php endif; ?>
          </div>

          <!-- Trạng thái & tổng tiền -->
          <div class="order-section">
            <p><strong>Vận chuyển:</strong> <?= wd_status_badge($shippingStat); ?></p>
            <p><strong>Thanh toán:</strong> <?= wd_payment_badge($payStatus); ?></p>

            <hr>

            <p><strong>Tổng tiền (trước giảm):</strong>
              <?= number_format($totalRaw, 0, ',', '.'); ?>đ
            </p>
            <p><strong>Giảm giá:</strong>
              <?= number_format($discountRaw, 0, ',', '.'); ?>đ
              <?php if (!empty($order['coupon_id'])): ?>
                <span style="color:#6b7280;">(coupon ID: <?= (int)$order['coupon_id']; ?>)</span>
              <?php endif; ?>
            </p>
            <p><strong>Thành tiền:</strong>
              <span class="order-money-main">
                <?= number_format($finalRaw, 0, ',', '.'); ?>đ
              </span>
            </p>

            <?php if ($shippingStat === 'cancelled'): ?>
              <hr>
              <p><strong>Lý do hủy:</strong><br>
                <?= nl2br(htmlspecialchars($order['cancel_reason'] ?? '—')); ?>
              </p>
            <?php endif; ?>
          </div>

          <!-- Sản phẩm trong đơn -->
          <div class="order-section">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:6px;">Sản phẩm trong đơn</h4>
            <div class="admin-table-wrapper">
              <table class="admin-table">
                <thead>
                <tr>
                  <th>Sách</th>
                  <th>Phiên bản</th>
                  <th>Giá</th>
                  <th>SL</th>
                  <th>Tạm tính</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$itemsDetail): ?>
                  <tr>
                    <td colspan="5" style="text-align:center;padding:16px;color:#6b7280;">
                      Không có sản phẩm nào trong đơn.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($itemsDetail as $it): ?>
                    <tr>
                      <td><?= htmlspecialchars($it['book_title'] ?? '—'); ?></td>
                      <td><?= htmlspecialchars($it['variant_format'] ?? '—'); ?></td>
                      <td><?= number_format((float)$it['price'], 0, ',', '.'); ?>đ</td>
                      <td><?= (int)$it['quantity']; ?></td>
                      <td><?= number_format((float)$it['subtotal'], 0, ',', '.'); ?>đ</td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Form cập nhật trạng thái -->
          <div class="order-section">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:6px;">Cập nhật trạng thái đơn hàng</h4>

            <?php if (!$allowChange): ?>
              <p style="color:#6b7280;font-size:14px;">
                Đơn hàng đã ở trạng thái cuối (<?= htmlspecialchars($shippingStat); ?>),
                không thể cập nhật thêm.
              </p>
            <?php else: ?>
              <form method="post" action="index.php?c=orders&a=updateStatus" id="orderStatusForm">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf'] ?? ''); ?>">
                <input type="hidden" name="id" value="<?= (int)$order['id']; ?>">

                <label class="wd-label">Trạng thái vận chuyển</label>
                <select name="shipping_status" id="shippingStatusSelect" class="wd-input" required>
                  <option value="pending"    <?= $shippingStat==='pending'   ?'selected':''; ?>>Chờ xử lý</option>
                  <option value="processing" <?= $shippingStat==='processing'?'selected':''; ?>>Đang xử lý</option>
                  <option value="shipped"    <?= $shippingStat==='shipped'   ?'selected':''; ?>>Đang giao</option>
                  <option value="delivered"  <?= $shippingStat==='delivered' ?'selected':''; ?>>Đã giao</option>
                  <option value="cancelled"  <?= $shippingStat==='cancelled' ?'selected':''; ?>>Đã hủy</option>
                </select>

                <div id="cancelReasonWrap" style="margin-top:12px;display:none;">
                  <label class="wd-label">Lý do hủy</label>
                  <textarea name="reason" id="cancelReasonInput"
                            rows="3" class="wd-input"
                            placeholder="Nhập lý do hủy đơn…"></textarea>
                </div>

                <button type="submit" class="wd-btn-primary" style="margin-top:14px;">
                  Cập nhật
                </button>
              </form>
            <?php endif; ?>
          </div>

        </div>
      </div>
    <?php endif; ?>

  </div> <!-- /.admin-grid-2 -->
</div> <!-- /.order-admin-page -->

<script>
  // Hiện/ẩn lý do hủy theo trạng thái shipping
  (function () {
    const select = document.getElementById('shippingStatusSelect');
    const wrap   = document.getElementById('cancelReasonWrap');
    if (!select || !wrap) return;

    function toggleReason() {
      if (select.value === 'cancelled') {
        wrap.style.display = 'block';
      } else {
        wrap.style.display = 'none';
      }
    }

    select.addEventListener('change', toggleReason);
    toggleReason();
  })();
</script>
