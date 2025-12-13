<?php
/** @var array $items */
/** @var array $filters */
/** @var array $books */
/** @var int $page */
/** @var int $last_page */
/** @var string $csrf */
?>
<style>
  .variant-admin-page .admin-card {
    border-radius: 20px;
    box-shadow: 0 15px 45px rgba(15,23,42,0.08);
  }
  .variant-admin-page .admin-card-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
  }
  .variant-admin-page .badge {
    padding:3px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
  }
  .badge-success { background:#d1fae5; color:#065f46; }
  .badge-muted { background:#e5e7eb; color:#4b5563; }
  .badge-danger { background:#fee2e2; color:#b91c1c; }
  .admin-table tbody tr:hover {
    background:#f9fafb;
  }
</style>

<div class="variant-admin-page">
  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Biến thể sách</h2>
      <p class="admin-section-subtitle">Đồng bộ style với danh mục: quản lý định dạng, giá và tồn kho.</p>
    </div>
    <a href="index.php?c=book_variants&a=create" class="wd-btn-primary">+ Thêm biến thể</a>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="book_variants">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Từ khóa</label>
      <input type="text" class="wd-input" name="keyword" placeholder="Tên định dạng..."
             value="<?= htmlspecialchars($filters['keyword']); ?>">
    </div>

    <div class="filter-group">
      <label>Sách</label>
      <select name="book_id" class="wd-input">
        <option value="">Tất cả</option>
        <?php foreach ($books as $book): ?>
          <option value="<?= $book['id']; ?>" <?= ($filters['book_id'] == $book['id']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($book['title']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="filter-group">
      <label>Trạng thái</label>
      <select name="status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="1" <?= $filters['status'] === '1' ? 'selected' : ''; ?>>Hoạt động</option>
        <option value="0" <?= $filters['status'] === '0' ? 'selected' : ''; ?>>Ẩn</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Loại bản ghi</label>
      <select name="deleted" class="wd-input">
        <option value="0" <?= (int)$filters['deleted'] === 0 ? 'selected' : ''; ?>>Đang hoạt động</option>
        <option value="1" <?= (int)$filters['deleted'] === 1 ? 'selected' : ''; ?>>Đã xóa</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="wd-btn-secondary">Lọc</button>
    </div>
  </form>

  <div class="admin-card">
    <div class="admin-card-header">
      <span>Danh sách biến thể</span>
      <small style="color:#6b7280">Hiển thị <?= count($items); ?> / <?= $last_page; ?> trang</small>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Sách</th>
        <th>Định dạng</th>
        <th>Giá</th>
        <th>Giá KM</th>
        <th>Tồn kho</th>
        <th>Trạng thái</th>
        <th style="width:140px;">Thao tác</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $variant): ?>
        <tr>
          <td><?= (int)$variant['id']; ?></td>
          <td><?= htmlspecialchars($variant['book_title']); ?></td>
          <td><?= htmlspecialchars($variant['format']); ?></td>
          <td><?= number_format($variant['price']); ?>₫</td>
          <td><?= $variant['sale_price'] !== null ? number_format($variant['sale_price']) . '₫' : '—'; ?></td>
          <td><?= (int)$variant['stock']; ?></td>
          <td>
            <?php if ((int)$filters['deleted'] === 1): ?>
              <span class="badge badge-danger">Đã xóa</span>
            <?php else: ?>
              <?php if ((int)$variant['is_active'] === 1): ?>
                <span class="badge badge-success">Đang bán</span>
              <?php else: ?>
                <span class="badge badge-muted">Ẩn</span>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if ((int)$filters['deleted'] === 1): ?>
              <form method="post" action="index.php?c=book_variants&a=restore" style="display:inline;">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                <input type="hidden" name="id" value="<?= (int)$variant['id']; ?>">
                <button type="submit" class="wd-icon-btn" title="Khôi phục">
                  ⟳
                </button>
              </form>
            <?php else: ?>
              <a href="index.php?c=book_variants&a=edit&id=<?= (int)$variant['id']; ?>"
                 class="wd-btn-secondary" style="padding:4px 10px;">Sửa</a>
              <form method="post" action="index.php?c=book_variants&a=delete" style="display:inline;"
                    onsubmit="return confirm('Xóa biến thể này?');">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                <input type="hidden" name="id" value="<?= (int)$variant['id']; ?>">
                <button type="submit" class="wd-icon-btn danger" title="Xóa">
                  🗑
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <tr><td colspan="8" style="text-align:center;">Chưa có biến thể nào.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

    <?php if ($last_page > 1): ?>
      <div class="admin-pagination">
        <?php for ($p = 1; $p <= $last_page; $p++): ?>
          <a class="page-link <?= $p === (int)$page ? 'active' : ''; ?>"
             href="index.php?c=book_variants&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&book_id=<?= urlencode($filters['book_id']); ?>&status=<?= urlencode($filters['status']); ?>&deleted=<?= (int)$filters['deleted']; ?>">
            <?= $p; ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
