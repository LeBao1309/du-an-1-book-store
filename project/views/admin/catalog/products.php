<?php
/** @var array $pagination */
/** @var array $filters */
/** @var string $csrf */
/** @var array $categories */
/** @var array $authors */
/** @var array $publishers */

$items    = $pagination['items'] ?? [];
$page     = $pagination['page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$total    = $pagination['total'] ?? 0;

$authorMapJs = [];
foreach ($authors as $a) {
    $authorMapJs[$a['id']] = $a['name'];
}

$publisherMapJs = [];
foreach ($publishers as $p) {
    $publisherMapJs[$p['id']] = $p['name'];
}
?>

<style>
/* ====== ONLY FOR PRODUCT ADMIN PAGE ====== */
.product-admin-page {
  margin-top: 4px;
}

/* grid tổng: bảng + panel edit bên phải */
.product-admin-page .admin-grid-2 {
  display: grid;
  gap: 18px;
  align-items: flex-start;
}
.product-admin-page .admin-grid-2.only-list {
  grid-template-columns: minmax(0, 1fr);
}
.product-admin-page .admin-grid-2.has-edit {
  grid-template-columns: minmax(0, 2.2fr) minmax(380px, 1.4fr);
}

/* panel edit bên phải sticky */
.product-admin-page .product-edit-card {
  position: sticky;
  top: 10px;
  max-height: calc(100vh - 120px);
  overflow: auto;
  display: none;
}
.product-admin-page .admin-grid-2.has-edit .product-edit-card {
  display: block;
}

/* header panel edit */
.product-admin-page .product-edit-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.product-admin-page .product-edit-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}
.product-admin-page .product-edit-meta {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}

/* highlight row đang chỉnh sửa */
#tblProducts tr.is-active-row {
  background: #e5e7ff;
}

/* icon button giống user/coupon */
.product-admin-page .wd-icon-btn {
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
.product-admin-page .wd-icon-btn:hover {
  background: #e0e7ff;
  transform: translateY(-1px);
}
.product-admin-page .wd-icon-btn.danger {
  background: #fee2e2;
  color: #b91c1c;
}
.product-admin-page .wd-icon-btn.danger:hover {
  background: #fecaca;
}

/* badge đơn giản (nếu chưa có global) */
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
.badge-muted {
  background: #e5e7eb;
  color: #4b5563;
}

/* token field cho tác giả / NXB */
.wd-token-field {
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  padding: 6px 8px;
  background: #f9fafb;
}
.wd-chip-list {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 4px;
}
.wd-chip-selected {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 999px;
  background: #eef2ff;
  font-size: 12px;
}
.wd-chip-selected button {
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 13px;
  line-height: 1;
}

/* modal popup (tạo mới + xóa) dùng chung với coupon/user */
.wd-modal {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 60;
}
.wd-modal.is-open {
  display: flex;
}
.wd-modal__backdrop {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.35);
}
.wd-modal__dialog {
  position: relative;
  background: #ffffff;
  border-radius: 20px;
  padding: 18px 20px 20px;
  width: 560px;
  max-width: calc(100% - 32px);
  box-shadow: 0 24px 80px rgba(15, 23, 42, 0.3);
  z-index: 1;
}
.wd-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}
.wd-modal__header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}
.wd-modal__body {
  font-size: 14px;
}

/* layout form trong modal create product */
.product-modal-form {
  margin-top: 4px;
}
.product-modal-row-2 {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
}
.product-modal-row-1 {
  margin-top: 8px;
}
.product-modal-row-2 + .product-modal-row-2 {
  margin-top: 8px;
}
.product-modal-field {
  display: flex;
  flex-direction: column;
}
.product-modal-field label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 4px;
}
.product-modal .wd-input,
.product-modal textarea.wd-input {
  width: 100%;
  border-radius: 12px;
  padding: 8px 10px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  font-size: 13px;
  outline: none;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.product-modal .wd-input:focus,
.product-modal textarea.wd-input:focus {
  border-color: #4f46e5;
  background: #ffffff;
  box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.12);
}
.product-modal-actions {
  margin-top: 14px;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

/* nút đỏ */
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

/* ===== EDIT PANEL LAYOUT (CẬP NHẬT SÁCH) ===== */
.product-edit-card .wd-input,
.product-edit-card textarea.wd-input {
  width: 100%;
  border-radius: 12px;
  padding: 8px 10px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  font-size: 13px;
  outline: none;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.product-edit-card .wd-input:focus,
.product-edit-card textarea.wd-input:focus {
  border-color: #4f46e5;
  background: #ffffff;
  box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.12);
}

.product-edit-grid {
  margin-top: 4px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.product-edit-row-2 {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
}
.product-edit-row-1 {
  /* full width block */
}
.product-edit-field {
  display: flex;
  flex-direction: column;
}
.product-edit-field .wd-label {
  margin-top: 0;
  margin-bottom: 4px;
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
}
.product-edit-card .wd-token-field {
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  padding: 6px 8px;
  background: #f9fafb;
}
.product-edit-card .wd-chip-list {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 4px;
}
.product-edit-card .wd-chip-selected {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 999px;
  background: #eef2ff;
  font-size: 12px;
}
.product-edit-card .wd-chip-selected button {
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 13px;
  line-height: 1;
}
.product-edit-actions {
  margin-top: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.product-edit-actions-left {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
}
.product-edit-actions-right {
  display: flex;
  gap: 8px;
}
</style>

<div class="product-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Sản phẩm (sách)</h2>
      <p class="admin-section-subtitle">
        Quản lý danh sách sách đang bán trên WiseDecision Bookstore.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreateProduct">
      + Thêm sách
    </button>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="products">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword']); ?>"
             placeholder="Tiêu đề sách..."
             class="wd-input">
    </div>

    <div class="filter-group">
      <label>Danh mục</label>
      <select name="category_id" class="wd-input">
        <option value="0">Tất cả</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id']; ?>"
            <?= $filters['category_id']==$c['id'] ? 'selected' : ''; ?>>
            <?= htmlspecialchars($c['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="filter-group">
      <label>Trạng thái</label>
      <select name="status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="1" <?= $filters['status']==='1'?'selected':''; ?>>Đang bán</option>
        <option value="0" <?= $filters['status']==='0'?'selected':''; ?>>Ẩn</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="wd-btn-secondary">Lọc</button>
    </div>
  </form>

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

  <div class="admin-grid-2 only-list" id="productGrid">

    <!-- BẢNG SÁCH -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách sách (<?= $total; ?>)</span>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblProducts">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Danh mục</th>
            <th>Tác giả</th>
            <th>NXB</th>
            <th>Mô tả ngắn</th>
            <th>Trạng thái</th>
            <th style="width:120px;">Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $b): ?>
            <?php
            $authorNamesFull    = $b['author_names'] ?? '—';
            $publisherNamesFull = $b['publisher_names'] ?? '—';
            $authorNamesShort    = htmlspecialchars(mb_strimwidth($authorNamesFull, 0, 50, '...', 'UTF-8'));
            $publisherNamesShort = htmlspecialchars(mb_strimwidth($publisherNamesFull, 0, 50, '...', 'UTF-8'));
            ?>
            <tr
              data-id="<?= $b['id']; ?>"
              data-title="<?= htmlspecialchars($b['title']); ?>"
              data-slug="<?= htmlspecialchars($b['slug']); ?>"
              data-category-id="<?= (int)$b['category_id']; ?>"
              data-short-desc="<?= htmlspecialchars($b['short_desc']); ?>"
              data-description="<?= htmlspecialchars($b['description'] ?? ''); ?>"
              data-active="<?= (int)$b['is_active']; ?>"
              data-author-ids="<?= htmlspecialchars($b['author_ids'] ?? ''); ?>"
              data-publisher-ids="<?= htmlspecialchars($b['publisher_ids'] ?? ''); ?>"
            >
              <td><?= $b['id']; ?></td>
              <td><?= htmlspecialchars($b['title']); ?></td>
              <td><?= htmlspecialchars($b['category_name'] ?? '—'); ?></td>

              <td title="<?= htmlspecialchars($authorNamesFull); ?>">
                <?= $authorNamesShort; ?>
              </td>

              <td title="<?= htmlspecialchars($publisherNamesFull); ?>">
                <?= $publisherNamesShort; ?>
              </td>

              <td class="text-truncate" title="<?= htmlspecialchars($b['short_desc']); ?>">
                <?= htmlspecialchars(mb_strimwidth($b['short_desc'], 0, 60, '...', 'UTF-8')); ?>
              </td>
              <td>
                <?php if ($b['is_active']): ?>
                  <span class="badge badge-success">Đang bán</span>
                <?php else: ?>
                  <span class="badge badge-muted">Ẩn</span>
                <?php endif; ?>
              </td>
              <td>
                <button type="button"
                        class="wd-icon-btn js-edit-product"
                        title="Chỉnh sửa">
                  ✏️
                </button>
                <button type="button"
                        class="wd-icon-btn danger js-delete-product"
                        title="Xóa sách này">
                  🗑
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($lastPage > 1): ?>
        <div class="admin-pagination">
          <?php for ($p = 1; $p <= $lastPage; $p++): ?>
            <a class="page-link <?= $p === $page ? 'active' : ''; ?>"
               href="index.php?c=products&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&category_id=<?= (int)$filters['category_id']; ?>&status=<?= urlencode($filters['status']); ?>">
              <?= $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL EDIT SÁCH BÊN PHẢI (UI MỚI) -->
    <div class="admin-card product-edit-card" id="productEditCard">
      <div class="product-edit-header">
        <div>
          <h3 id="productFormTitle">Cập nhật sách</h3>
          <div class="product-edit-meta" id="productEditMeta">
            Chọn một dòng ở bảng bên trái để chỉnh sửa sách.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseProductEdit" title="Đóng">
          ✕
        </button>
      </div>

      <form method="post" id="productEditForm" action="index.php?c=products&a=update">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="bookIdEdit">

        <div class="product-edit-grid">
          <!-- Hàng 1: Tiêu đề + Slug -->
          <div class="product-edit-row-2">
            <div class="product-edit-field">
              <label class="wd-label" for="bookTitleEdit">Tiêu đề</label>
              <input type="text" name="title" id="bookTitleEdit" class="wd-input" required>
            </div>
            <div class="product-edit-field">
              <label class="wd-label" for="bookSlugEdit">Slug (nếu bỏ trống sẽ tự tạo)</label>
              <input type="text" name="slug" id="bookSlugEdit"
                     class="wd-input" placeholder="vd: tu-duy-lam-giau">
            </div>
          </div>

          <!-- Hàng 2: Danh mục -->
          <div class="product-edit-row-1">
            <div class="product-edit-field">
              <label class="wd-label" for="bookCategoryEdit">Danh mục</label>
              <select name="category_id" id="bookCategoryEdit" class="wd-input" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= $c['id']; ?>">
                    <?= htmlspecialchars($c['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Hàng 3: Tác giả -->
          <div class="product-edit-row-1">
            <div class="product-edit-field">
              <label class="wd-label">Tác giả</label>
              <div class="wd-token-field">
                <div id="authorChipsEdit" class="wd-chip-list"></div>
                <div id="authorHiddenEdit"></div>
                <input type="text"
                       id="authorSearchEdit"
                       class="wd-input"
                       list="authorDatalistEdit"
                       placeholder="Gõ tên tác giả, chọn từ gợi ý rồi Enter">
                <datalist id="authorDatalistEdit">
                  <?php foreach ($authors as $a): ?>
                    <option value="<?= htmlspecialchars($a['name']); ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>
            </div>
          </div>

          <!-- Hàng 4: Nhà xuất bản -->
          <div class="product-edit-row-1">
            <div class="product-edit-field">
              <label class="wd-label">Nhà xuất bản</label>
              <div class="wd-token-field">
                <div id="publisherChipsEdit" class="wd-chip-list"></div>
                <div id="publisherHiddenEdit"></div>
                <input type="text"
                       id="publisherSearchEdit"
                       class="wd-input"
                       list="publisherDatalistEdit"
                       placeholder="Gõ tên NXB, chọn từ gợi ý rồi Enter">
                <datalist id="publisherDatalistEdit">
                  <?php foreach ($publishers as $p): ?>
                    <option value="<?= htmlspecialchars($p['name']); ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>
            </div>
          </div>

          <!-- Hàng 5: Mô tả ngắn + chi tiết -->
          <div class="product-edit-row-2">
            <div class="product-edit-field">
              <label class="wd-label" for="bookShortDescEdit">Mô tả ngắn</label>
              <textarea name="short_desc" id="bookShortDescEdit"
                        rows="2" class="wd-input"
                        placeholder="Tóm tắt nội dung chính"></textarea>
            </div>
            <div class="product-edit-field">
              <label class="wd-label" for="bookDescriptionEdit">Mô tả chi tiết</label>
              <textarea name="description" id="bookDescriptionEdit"
                        rows="2" class="wd-input"
                        placeholder="Giới thiệu chi tiết về sách"></textarea>
            </div>
          </div>

          <!-- Hàng 6: Đang bán + nút -->
          <div class="product-edit-row-1">
            <div class="product-edit-actions">
              <label class="product-edit-actions-left">
                <input type="checkbox" name="is_active" id="bookActiveEdit" value="1">
                <span>Đang bán</span>
              </label>

              <div class="product-edit-actions-right">
                <button type="submit" class="wd-btn-primary">
                  Cập nhật
                </button>
                <button type="button" class="wd-btn-secondary" id="btnResetBookEdit">
                  Làm mới form
                </button>
              </div>
            </div>
          </div>
        </div><!-- /.product-edit-grid -->
      </form>
    </div>

  </div> <!-- /.admin-grid-2 -->
</div> <!-- /.product-admin-page -->

<!-- MODAL: TẠO MỚI SÁCH -->
<div class="wd-modal" id="createProductModal">
  <div class="wd-modal__backdrop"></div>
  <div class="wd-modal__dialog product-modal">
    <div class="wd-modal__header">
      <h3>Thêm sách mới</h3>
      <button type="button" class="wd-icon-btn" id="btnCloseCreateProduct">✕</button>
    </div>
    <div class="wd-modal__body">
      <form method="post" id="productCreateForm" action="index.php?c=products&a=store">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

        <div class="product-modal-form">
          <div class="product-modal-row-1">
            <div class="product-modal-field">
              <label for="bookTitleCreate">Tiêu đề</label>
              <input type="text" name="title" id="bookTitleCreate"
                     class="wd-input" required placeholder="Tên sách">
            </div>
          </div>

          <div class="product-modal-row-2" style="margin-top:8px;">
            <div class="product-modal-field">
              <label for="bookSlugCreate">Slug (có thể bỏ trống)</label>
              <input type="text" name="slug" id="bookSlugCreate"
                     class="wd-input" placeholder="vd: sach-hat-giong-tam-hon">
            </div>
            <div class="product-modal-field">
              <label for="bookCategoryCreate">Danh mục</label>
              <select name="category_id" id="bookCategoryCreate"
                      class="wd-input" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= $c['id']; ?>">
                    <?= htmlspecialchars($c['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="product-modal-row-1">
            <div class="product-modal-field">
              <label>Tác giả</label>
              <div class="wd-token-field">
                <div id="authorChipsCreate" class="wd-chip-list"></div>
                <div id="authorHiddenCreate"></div>
                <input type="text"
                       id="authorSearchCreate"
                       class="wd-input"
                       list="authorDatalistCreate"
                       placeholder="Gõ tên tác giả, chọn từ gợi ý rồi Enter">
                <datalist id="authorDatalistCreate">
                  <?php foreach ($authors as $a): ?>
                    <option value="<?= htmlspecialchars($a['name']); ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>
            </div>
          </div>

          <div class="product-modal-row-1">
            <div class="product-modal-field">
              <label>Nhà xuất bản</label>
              <div class="wd-token-field">
                <div id="publisherChipsCreate" class="wd-chip-list"></div>
                <div id="publisherHiddenCreate"></div>
                <input type="text"
                       id="publisherSearchCreate"
                       class="wd-input"
                       list="publisherDatalistCreate"
                       placeholder="Gõ tên NXB, chọn từ gợi ý rồi Enter">
                <datalist id="publisherDatalistCreate">
                  <?php foreach ($publishers as $p): ?>
                    <option value="<?= htmlspecialchars($p['name']); ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>
            </div>
          </div>

          <div class="product-modal-row-2">
            <div class="product-modal-field">
              <label for="bookShortDescCreate">Mô tả ngắn</label>
              <textarea name="short_desc" id="bookShortDescCreate"
                        rows="2" class="wd-input"
                        placeholder="Tóm tắt nội dung chính"></textarea>
            </div>
            <div class="product-modal-field">
              <label for="bookDescriptionCreate">Mô tả chi tiết</label>
              <textarea name="description" id="bookDescriptionCreate"
                        rows="2" class="wd-input"
                        placeholder="Giới thiệu chi tiết về sách"></textarea>
            </div>
          </div>

          <div class="product-modal-row-1">
            <label class="product-modal-field" style="flex-direction:row;align-items:center;gap:6px;">
              <input type="checkbox" name="is_active" id="bookActiveCreate" value="1" checked>
              <span>Đang bán</span>
            </label>
          </div>
        </div>

        <div class="product-modal-actions">
          <button type="button" class="wd-btn-secondary" id="btnCancelCreateProduct">
            Hủy
          </button>
          <button type="submit" class="wd-btn-primary">
            Lưu sách
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: XÁC NHẬN XÓA SÁCH -->
<div class="wd-modal" id="deleteProductModal">
  <div class="wd-modal__backdrop"></div>
  <div class="wd-modal__dialog">
    <div class="wd-modal__header">
      <h3>Xóa sách</h3>
      <button type="button" class="wd-icon-btn" id="btnCloseDeleteProduct">✕</button>
    </div>
    <div class="wd-modal__body">
      <p style="font-size:14px;margin-bottom:12px;">
        Bạn có chắc chắn muốn xóa sách này khỏi hệ thống? Hành động này không thể hoàn tác.
      </p>
      <form method="post" action="index.php?c=products&a=delete" id="deleteProductForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="deleteProductId">
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
          <button type="button" class="wd-btn-secondary" id="btnCancelDeleteProduct">
            Hủy
          </button>
          <button type="submit" class="wd-btn-danger">
            Xóa sách
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const WD_AUTHORS    = <?= json_encode($authorMapJs, JSON_UNESCAPED_UNICODE); ?>;
const WD_PUBLISHERS = <?= json_encode($publisherMapJs, JSON_UNESCAPED_UNICODE); ?>;

(function () {
  function openModal(el){ if(el) el.classList.add('is-open'); }
  function closeModal(el){ if(el) el.classList.remove('is-open'); }

  const gridEl   = document.getElementById('productGrid');
  const editCard = document.getElementById('productEditCard');

  function openEditLayout() {
    if (!gridEl || !editCard) return;
    gridEl.classList.remove('only-list');
    gridEl.classList.add('has-edit');
  }

  function closeEditLayout() {
    if (!gridEl || !editCard) return;
    gridEl.classList.remove('has-edit');
    gridEl.classList.add('only-list');
    document.querySelectorAll('#tblProducts tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));
  }

  function clearChildren(el) {
    while (el.firstChild) el.removeChild(el.firstChild);
  }

  function createMultiSelector(map, chipId, hiddenId, searchId, fieldName) {
    const chips  = document.getElementById(chipId);
    const hidden = document.getElementById(hiddenId);
    const search = document.getElementById(searchId);

    function add(id) {
      id = String(id);
      if (!map[id]) return;
      if (hidden.querySelector('input[value="' + id + '"]')) return;

      const chip = document.createElement('span');
      chip.className = 'wd-chip-selected';
      chip.textContent = map[id];

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = '×';
      btn.addEventListener('click', () => {
        chip.remove();
        const h = hidden.querySelector('input[value="' + id + '"]');
        if (h) h.remove();
      });
      chip.appendChild(btn);
      chips.appendChild(chip);

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = fieldName;
      input.value = id;
      hidden.appendChild(input);
    }

    function reset() {
      clearChildren(chips);
      clearChildren(hidden);
      if (search) search.value = '';
    }

    function findIdByName(name) {
      name = name.trim().toLowerCase();
      for (const [id, label] of Object.entries(map)) {
        if (label.toLowerCase() === name) return id;
      }
      return null;
    }

    if (search) {
      search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const name = this.value.trim();
          if (!name) return;
          const id = findIdByName(name);
          if (!id) {
            alert('Không tìm thấy "' + name + '" trong danh sách.');
            return;
          }
          add(id);
          this.value = '';
        }
      });
    }

    function fillFromCsv(csv) {
      reset();
      if (!csv) return;
      csv.split(',').forEach(id => {
        id = id.trim();
        if (id) add(id);
      });
    }

    return { add, reset, fillFromCsv };
  }

  // selectors cho EDIT
  const authorEditSelector = createMultiSelector(
    WD_AUTHORS,
    'authorChipsEdit',
    'authorHiddenEdit',
    'authorSearchEdit',
    'author_ids[]'
  );
  const publisherEditSelector = createMultiSelector(
    WD_PUBLISHERS,
    'publisherChipsEdit',
    'publisherHiddenEdit',
    'publisherSearchEdit',
    'publisher_ids[]'
  );

  // selectors cho CREATE
  const authorCreateSelector = createMultiSelector(
    WD_AUTHORS,
    'authorChipsCreate',
    'authorHiddenCreate',
    'authorSearchCreate',
    'author_ids[]'
  );
  const publisherCreateSelector = createMultiSelector(
    WD_PUBLISHERS,
    'publisherChipsCreate',
    'publisherHiddenCreate',
    'publisherSearchCreate',
    'publisher_ids[]'
  );

  // ====== EDIT PANEL ======
  const idEdit        = document.getElementById('bookIdEdit');
  const titleEdit     = document.getElementById('bookTitleEdit');
  const slugEdit      = document.getElementById('bookSlugEdit');
  const categoryEdit  = document.getElementById('bookCategoryEdit');
  const shortDescEdit = document.getElementById('bookShortDescEdit');
  const descEdit      = document.getElementById('bookDescriptionEdit');
  const activeEdit    = document.getElementById('bookActiveEdit');
  const titleEl       = document.getElementById('productFormTitle');
  const metaEl        = document.getElementById('productEditMeta');

  function resetEditForm() {
    idEdit.value        = '';
    titleEdit.value     = '';
    slugEdit.value      = '';
    categoryEdit.value  = '';
    shortDescEdit.value = '';
    descEdit.value      = '';
    activeEdit.checked  = true;
    authorEditSelector.reset();
    publisherEditSelector.reset();
    titleEl.textContent = 'Cập nhật sách';
    metaEl.textContent  = 'Chọn một dòng ở bảng bên trái để chỉnh sửa sách.';
    document.querySelectorAll('#tblProducts tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));
  }

  document.querySelectorAll('.js-edit-product').forEach(btn => {
    btn.addEventListener('click', function () {
      const tr = this.closest('tr');
      if (!tr) return;

      document.querySelectorAll('#tblProducts tr.is-active-row')
        .forEach(r => r.classList.remove('is-active-row'));
      tr.classList.add('is-active-row');

      idEdit.value        = tr.dataset.id || '';
      titleEdit.value     = tr.dataset.title || '';
      slugEdit.value      = tr.dataset.slug || '';
      categoryEdit.value  = tr.dataset.categoryId || '';
      shortDescEdit.value = tr.dataset.shortDesc || '';
      descEdit.value      = tr.dataset.description || '';
      activeEdit.checked  = tr.dataset.active === '1';

      authorEditSelector.fillFromCsv(tr.dataset.authorIds || '');
      publisherEditSelector.fillFromCsv(tr.dataset.publisherIds || '');

      titleEl.textContent = 'Cập nhật sách #' + (tr.dataset.id || '');
      metaEl.textContent  = 'Đang chỉnh sửa: ' + (tr.dataset.title || '');

      openEditLayout();
    });
  });

  document.getElementById('btnResetBookEdit')?.addEventListener('click', () => {
    resetEditForm();
  });

  document.getElementById('btnCloseProductEdit')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });

  // ====== CREATE MODAL ======
  const createModal        = document.getElementById('createProductModal');
  const btnOpenCreateProd  = document.getElementById('btnOpenCreateProduct');
  const btnCloseCreateProd = document.getElementById('btnCloseCreateProduct');
  const btnCancelCreateProd= document.getElementById('btnCancelCreateProduct');

  const titleCreate     = document.getElementById('bookTitleCreate');
  const slugCreate      = document.getElementById('bookSlugCreate');
  const categoryCreate  = document.getElementById('bookCategoryCreate');
  const shortDescCreate = document.getElementById('bookShortDescCreate');
  const descCreate      = document.getElementById('bookDescriptionCreate');
  const activeCreate    = document.getElementById('bookActiveCreate');

  function resetCreateForm() {
    titleCreate.value     = '';
    slugCreate.value      = '';
    categoryCreate.value  = '';
    shortDescCreate.value = '';
    descCreate.value      = '';
    activeCreate.checked  = true;
    authorCreateSelector.reset();
    publisherCreateSelector.reset();
  }

  btnOpenCreateProd?.addEventListener('click', () => {
    resetCreateForm();
    openModal(createModal);
  });
  btnCloseCreateProd?.addEventListener('click', () => closeModal(createModal));
  btnCancelCreateProd?.addEventListener('click', () => closeModal(createModal));

  // ====== DELETE MODAL ======
  const deleteModal      = document.getElementById('deleteProductModal');
  const deleteIdInput    = document.getElementById('deleteProductId');
  const btnCloseDelete   = document.getElementById('btnCloseDeleteProduct');
  const btnCancelDelete  = document.getElementById('btnCancelDeleteProduct');

  document.querySelectorAll('.js-delete-product').forEach(btn => {
    btn.addEventListener('click', function () {
      const tr = this.closest('tr');
      if (!tr) return;
      deleteIdInput.value = tr.dataset.id || '';
      openModal(deleteModal);
    });
  });

  btnCloseDelete?.addEventListener('click', () => closeModal(deleteModal));
  btnCancelDelete?.addEventListener('click', () => closeModal(deleteModal));
})();
</script>
