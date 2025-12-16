<?php
/** @var array $items */
/** @var array $filters */
/** @var array $books */
/** @var int $page */
/** @var int $last_page */
/** @var string $csrf */

$isTrash = (int)($filters['deleted'] ?? 0) === 1;
?>
<style>
  /* ===== UI đồng bộ với trang User / Danh mục ===== */
  .variant-admin-page {
    margin-top: 4px;
  }
  .variant-admin-page .admin-grid-2 {
    display: grid;
    gap: 18px;
    align-items: flex-start;
    transition: grid-template-columns 0.25s ease;
  }
  .variant-admin-page .admin-grid-2.only-list {
    grid-template-columns: minmax(0, 1fr);
  }
  .variant-admin-page .admin-grid-2.has-detail {
    grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
  }
  .variant-admin-page .variant-detail-card {
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 120px);
    overflow: auto;
    display: none;
    opacity: 0;
    transform: translateX(10px);
    transition: opacity 0.22s ease, transform 0.22s ease;
  }
  .variant-admin-page .admin-grid-2.has-detail .variant-detail-card {
    display: block;
  }
  .variant-admin-page .variant-detail-card.is-visible {
    opacity: 1;
    transform: translateX(0);
  }
  .variant-admin-page .variant-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }
  .variant-admin-page .variant-detail-meta {
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
  }
  .variant-admin-page .variant-meta-grid {
    display: grid;
    gap: 10px;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  }
  #tblVariants tr.is-active-row {
    background: #e5e7ff;
  }
  .wd-icon-btn {
    border: none;
    background: #eef2ff;
    color: #4f46e5;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    margin-right: 4px;
    transition: background 0.15s ease, transform 0.1s ease;
  }
  .wd-icon-btn:hover {
    background: #e0e7ff;
    transform: translateY(-1px);
  }
  .wd-icon-btn.danger {
    background: #fee2e2;
    color: #b91c1c;
  }
  .wd-icon-btn.danger:hover {
    background: #fecaca;
  }
  .wd-btn-danger {
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
  }
  .wd-btn-danger:hover { background:#b91c1c; }
  .badge {
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }
  .badge-success { background:#d1fae5; color:#065f46; }
  .badge-muted { background:#e5e7eb; color:#4b5563; }
  .badge-danger { background:#fee2e2; color:#b91c1c; }
  .price-chip {
    display: inline-block;
    padding: 3px 8px;
    background: #f3f4f6;
    border-radius: 10px;
    font-weight: 600;
  }
</style>

<div class="variant-admin-page">
  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Biến thể sách</h2>
      <p class="admin-section-subtitle">
        Đồng bộ giao diện với danh mục & người dùng: quản lý định dạng, giá và tồn kho.
      </p>
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

  <div class="admin-grid-2 only-list" id="variantGrid">
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách biến thể (<?= count($items); ?>)</span>
        <small style="color:#6b7280">Trang <?= (int)$page; ?>/<?= (int)$last_page; ?></small>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblVariants">
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
          <tr
            data-id="<?= (int)$variant['id']; ?>"
            data-book="<?= htmlspecialchars($variant['book_title']); ?>"
            data-format="<?= htmlspecialchars($variant['format']); ?>"
            data-price="<?= (int)$variant['price']; ?>"
            data-sale="<?= $variant['sale_price'] !== null ? (int)$variant['sale_price'] : ''; ?>"
            data-stock="<?= (int)$variant['stock']; ?>"
            data-active="<?= (int)$variant['is_active']; ?>"
            data-created="<?= htmlspecialchars($variant['created_at'] ?? ''); ?>"
          >
            <td><?= (int)$variant['id']; ?></td>
            <td><?= htmlspecialchars($variant['book_title']); ?></td>
            <td><?= htmlspecialchars($variant['format']); ?></td>
            <td><span class="price-chip"><?= number_format($variant['price']); ?>₫</span></td>
            <td><?= $variant['sale_price'] !== null ? '<span class="price-chip" style="background:#e0f2fe;color:#075985;">' . number_format($variant['sale_price']) . '₫</span>' : '—'; ?></td>
            <td><?= (int)$variant['stock']; ?></td>
            <td>
              <?php if ($isTrash): ?>
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
              <?php if ($isTrash): ?>
                <form method="post" action="index.php?c=book_variants&a=restore" style="display:inline;">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                  <input type="hidden" name="id" value="<?= (int)$variant['id']; ?>">
                  <button type="submit" class="wd-icon-btn js-variant-action" title="Khôi phục">
                    ⟳
                  </button>
                </form>
              <?php else: ?>
                <button type="button" class="wd-icon-btn js-view-variant" title="Xem nhanh">👁</button>
                <a href="index.php?c=book_variants&a=edit&id=<?= (int)$variant['id']; ?>"
                   class="wd-icon-btn js-variant-action" style="text-decoration:none;" title="Chỉnh sửa">✏️</a>
                <form method="post" action="index.php?c=book_variants&a=delete" style="display:inline;"
                      onsubmit="return confirm('Xóa biến thể này?');">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                  <input type="hidden" name="id" value="<?= (int)$variant['id']; ?>">
                  <button type="submit" class="wd-icon-btn danger js-variant-action" title="Xóa">
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

    <!-- PANEL xem nhanh biến thể (style giống danh mục) -->
    <div class="admin-card variant-detail-card" id="variantDetailCard">
      <div class="variant-detail-header">
        <div>
          <h3 id="variantDetailTitle" style="margin:0;font-size:16px;font-weight:600;">Xem nhanh biến thể</h3>
          <div class="variant-detail-meta" id="variantDetailMeta">
            Chọn một biến thể ở bảng bên trái để xem chi tiết.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseVariantDetail">✕</button>
      </div>

      <div class="variant-meta-grid" id="variantMetaGrid" aria-live="polite">
        <div>
          <div class="wd-label" style="margin-top:0;">Sách</div>
          <div id="variantBook">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Định dạng</div>
          <div id="variantFormat">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Giá</div>
          <div id="variantPrice">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Giá khuyến mãi</div>
          <div id="variantSale">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Tồn kho</div>
          <div id="variantStock">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Trạng thái</div>
          <div id="variantStatus">—</div>
        </div>
        <div>
          <div class="wd-label" style="margin-top:0;">Tạo lúc</div>
          <div id="variantCreated">—</div>
        </div>
      </div>

      <div style="margin-top:16px; display:flex; gap:8px;">
        <a href="#" id="variantEditLink" class="wd-btn-primary" style="flex:1; text-align:center;">Chỉnh sửa</a>
        <?php if ($isTrash): ?>
          <form method="post" action="index.php?c=book_variants&a=restore" style="flex:1;">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
            <input type="hidden" name="id" id="variantRestoreId">
            <button type="submit" class="wd-btn-secondary" style="width:100%;">Khôi phục</button>
          </form>
        <?php else: ?>
          <form method="post" action="index.php?c=book_variants&a=delete" style="flex:1;"
                onsubmit="return confirm('Xóa biến thể này?');">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
            <input type="hidden" name="id" id="variantDeleteId">
            <button type="submit" class="wd-btn-danger" style="width:100%;">Xóa</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  const gridVariant    = document.getElementById('variantGrid');
  const detailCard     = document.getElementById('variantDetailCard');
  const detailTitle    = document.getElementById('variantDetailTitle');
  const detailMeta     = document.getElementById('variantDetailMeta');
  const bookEl         = document.getElementById('variantBook');
  const formatEl       = document.getElementById('variantFormat');
  const priceEl        = document.getElementById('variantPrice');
  const saleEl         = document.getElementById('variantSale');
  const stockEl        = document.getElementById('variantStock');
  const statusEl       = document.getElementById('variantStatus');
  const createdEl      = document.getElementById('variantCreated');
  const editLink       = document.getElementById('variantEditLink');
  const closeDetailBtn = document.getElementById('btnCloseVariantDetail');
  const deleteIdInput  = document.getElementById('variantDeleteId');
  const restoreIdInput = document.getElementById('variantRestoreId');

  function formatCurrency(val) {
    if (val === '' || val === null || typeof val === 'undefined') return '—';
    return Number(val).toLocaleString('vi-VN') + '₫';
  }

  function updateDetail(tr) {
    document.querySelectorAll('#tblVariants tr.is-active-row')
      .forEach(row => row.classList.remove('is-active-row'));
    tr.classList.add('is-active-row');

    const id      = tr.dataset.id;
    const book    = tr.dataset.book || '—';
    const format  = tr.dataset.format || '—';
    const price   = tr.dataset.price || '';
    const sale    = tr.dataset.sale || '';
    const stock   = tr.dataset.stock || '0';
    const active  = tr.dataset.active === '1';
    const created = tr.dataset.created || '—';

    detailTitle.textContent = 'Biến thể #' + id;
    detailMeta.textContent  = 'Sách: ' + book + ' • Định dạng: ' + format;

    bookEl.textContent    = book;
    formatEl.textContent  = format;
    priceEl.textContent   = formatCurrency(price);
    saleEl.textContent    = sale !== '' ? formatCurrency(sale) : '—';
    stockEl.textContent   = stock;
    statusEl.innerHTML    = active
      ? '<span class="badge badge-success">Đang bán</span>'
      : '<span class="badge badge-muted">Ẩn</span>';
    createdEl.textContent = created;

    if (editLink) {
      editLink.href = 'index.php?c=book_variants&a=edit&id=' + id;
    }
    if (deleteIdInput) {
      deleteIdInput.value = id;
    }
    if (restoreIdInput) {
      restoreIdInput.value = id;
    }

    gridVariant.classList.remove('only-list');
    gridVariant.classList.add('has-detail');
    detailCard.classList.remove('is-visible');
    requestAnimationFrame(() => detailCard.classList.add('is-visible'));
  }

  function closeDetail() {
    detailCard.classList.remove('is-visible');
    setTimeout(() => {
      gridVariant.classList.remove('has-detail');
      gridVariant.classList.add('only-list');
      document.querySelectorAll('#tblVariants tr.is-active-row')
        .forEach(row => row.classList.remove('is-active-row'));
    }, 200);
  }

  document.querySelectorAll('#tblVariants tbody tr').forEach(tr => {
    tr.addEventListener('click', () => updateDetail(tr));
  });

  document.querySelectorAll('.js-view-variant').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const tr = btn.closest('tr');
      if (tr) updateDetail(tr);
    });
  });

  document.querySelectorAll('.js-variant-action').forEach(btn => {
    btn.addEventListener('click', (e) => e.stopPropagation());
  });

  closeDetailBtn?.addEventListener('click', closeDetail);
</script>
