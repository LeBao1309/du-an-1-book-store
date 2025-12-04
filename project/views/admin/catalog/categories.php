<?php
/** @var array $pagination */
/** @var array $filters */
/** @var string $csrf */
/** @var array $allCategories */

$items     = $pagination['items'] ?? [];
$page      = $pagination['page'] ?? 1;
$lastPage  = $pagination['last_page'] ?? 1;
$total     = $pagination['total'] ?? 0;
?>
<style>
  /* ===== UI riêng cho trang quản lý danh mục ===== */
  .category-admin-page {
    margin-top: 4px;
  }

  .category-admin-page .admin-grid-2 {
    display: grid;
    gap: 18px;
    align-items: flex-start;
    transition: grid-template-columns 0.25s ease;
  }

  /* Mặc định chỉ có bảng */
  .category-admin-page .admin-grid-2.only-list {
    grid-template-columns: minmax(0, 1fr);
  }

  /* Khi có form update */
  .category-admin-page .admin-grid-2.has-edit {
    grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
  }

  .category-admin-page .category-edit-card {
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 120px);
    overflow: auto;

    opacity: 0;
    transform: translateX(16px);
    transition: opacity 0.22s ease, transform 0.22s ease;
  }

  .category-admin-page .admin-grid-2.only-list .category-edit-card {
    display: none;
  }

  .category-admin-page .admin-grid-2.has-edit .category-edit-card {
    display: block;
  }

  .category-admin-page .category-edit-card.is-visible {
    opacity: 1;
    transform: translateX(0);
  }

  .category-admin-page .category-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }

  .category-admin-page .category-edit-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
  }

  .category-admin-page .category-edit-meta {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
  }

  #tblCategories tr.is-active-row {
    background: #e5e7ff;
  }

  /* Modal dùng chung (create + disable) */
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
    border-radius: 18px;
    padding: 18px 20px 20px;
    width: 420px;
    max-width: calc(100% - 32px);
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
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
  .wd-btn-danger:hover {
    background: #b91c1c;
  }

  .wd-input {
    width: 100%;
    padding: 8px 10px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    font-size: 14px;
  }
  .wd-label {
    display: block;
    font-size: 13px;
    margin-top: 8px;
    margin-bottom: 4px;
  }
</style>

<div class="category-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Danh mục sách</h2>
      <p class="admin-section-subtitle">
        Quản lý cấu trúc catalog (danh mục cha – con) cho WiseDecision Bookstore.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreateCategory">
      + Thêm danh mục
    </button>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="catalog">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword']); ?>"
             placeholder="Tên danh mục..."
             class="wd-input">
    </div>

    <div class="filter-group">
      <label>Trạng thái</label>
      <select name="status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="1" <?= $filters['status']==='1'?'selected':''; ?>>Đang hoạt động</option>
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

  <!-- GRID chính: table + panel update (ẩn/hiện giống user admin) -->
  <div class="admin-grid-2 only-list" id="categoryGrid">

    <!-- BẢNG DANH MỤC -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách danh mục (<?= (int)$total; ?>)</span>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblCategories">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Trạng thái</th>
            <th style="width:120px;">Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $cat): ?>
            <?php
            $parentName = $cat['parent_name'] ?? '—';
            $bookCount  = $cat['book_count'] ?? null;
            $childCount = $cat['child_count'] ?? null;
            $createdAt  = $cat['created_at'] ?? '';
            ?>
            <tr
              data-id="<?= $cat['id']; ?>"
              data-name="<?= htmlspecialchars($cat['name']); ?>"
              data-slug="<?= htmlspecialchars($cat['slug']); ?>"
              data-parent-id="<?= (int)$cat['parent_id']; ?>"
              data-parent-name="<?= htmlspecialchars($parentName); ?>"
              data-active="<?= (int)$cat['is_active']; ?>"
              data-book-count="<?= $bookCount !== null ? (int)$bookCount : 0; ?>"
              data-child-count="<?= $childCount !== null ? (int)$childCount : 0; ?>"
              data-created-at="<?= htmlspecialchars($createdAt); ?>"
            >
              <td><?= $cat['id']; ?></td>
              <td><?= htmlspecialchars($cat['name']); ?></td>
              <td><?= htmlspecialchars($cat['slug']); ?></td>
              <td><?= htmlspecialchars($parentName); ?></td>
              <td>
                <?php if ($cat['is_active']): ?>
                  <span class="badge badge-success">Hoạt động</span>
                <?php else: ?>
                  <span class="badge badge-muted">Ẩn</span>
                <?php endif; ?>
              </td>
              <td>
                <button type="button"
                        class="wd-icon-btn js-edit-category"
                        title="Chỉnh sửa">
                  ✏️
                </button>
                <button type="button"
                        class="wd-icon-btn danger js-disable-category"
                        title="Vô hiệu hóa">
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
               href="index.php?c=catalog&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&status=<?= urlencode($filters['status']); ?>">
              <?= $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL CẬP NHẬT DANH MỤC (bên phải) -->
    <div class="admin-card category-edit-card" id="categoryEditCard">
      <div class="category-edit-header">
        <div>
          <h3 id="categoryEditTitle">Cập nhật danh mục</h3>
          <div class="category-edit-meta" id="categoryEditMeta">
            Chọn một danh mục ở bảng bên trái để chỉnh sửa.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseEditCategory">✕</button>
      </div>

      <form method="post" action="index.php?c=catalog&a=update" id="categoryEditForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="catId">

        <label class="wd-label">Tên danh mục</label>
        <input type="text" name="name" id="catName" class="wd-input" required>

        <label class="wd-label">Slug (nếu bỏ trống sẽ tự tạo)</label>
        <input type="text" name="slug" id="catSlug" class="wd-input">

        <label class="wd-label">Danh mục cha</label>
        <select name="parent_id" id="catParent" class="wd-input">
          <option value="0">Không có</option>
          <?php foreach ($allCategories as $c): ?>
            <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>

        <label class="wd-label" style="margin-top:8px;">
          <input type="checkbox" name="is_active" id="catActive" value="1" checked>
          Đang hoạt động
        </label>

        <div style="margin-top:16px;display:flex;gap:8px;">
          <button type="submit" class="wd-btn-primary">Lưu thay đổi</button>
          <button type="button" class="wd-btn-secondary" id="btnClearEditCategory">
            Hủy / trở về
          </button>
        </div>
      </form>

      <hr style="margin:14px 0;border:none;border-top:1px solid #e5e7eb;">

      <div style="font-size:12px;color:#6b7280;">
        <div><strong>Ghi chú:</strong></div>
        <ul style="padding-left:16px;margin-top:4px;">
          <li>Không thể chọn chính nó làm danh mục cha (chặn loop cơ bản).</li>
          <li>Nên tránh chuyển danh mục đã có sách sang danh mục con không phù hợp.</li>
        </ul>
      </div>
    </div>
  </div> <!-- /.admin-grid-2 -->

  <!-- MODAL: TẠO DANH MỤC MỚI -->
  <div class="wd-modal" id="createCategoryModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Thêm danh mục mới</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseCreateCategory">✕</button>
      </div>
      <div class="wd-modal__body">
        <form method="post" action="index.php?c=catalog&a=store">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

          <label class="wd-label">Tên danh mục</label>
          <input type="text" name="name" class="wd-input" required>

          <label class="wd-label">Slug (nếu bỏ trống sẽ tự tạo)</label>
          <input type="text" name="slug" class="wd-input">

          <label class="wd-label">Danh mục cha</label>
          <select name="parent_id" class="wd-input">
            <option value="0">Không có</option>
            <?php foreach ($allCategories as $c): ?>
              <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
          </select>

          <label class="wd-label" style="margin-top:8px;">
            <input type="checkbox" name="is_active" value="1" checked>
            Đang hoạt động
          </label>

          <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" class="wd-btn-secondary" id="btnCancelCreateCategory">
              Hủy
            </button>
            <button type="submit" class="wd-btn-primary">
              Lưu danh mục
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: VÔ HIỆU HÓA (SOFT DELETE) -->
  <div class="wd-modal" id="disableCategoryModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Vô hiệu hóa danh mục</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseDisableCategory">✕</button>
      </div>
      <div class="wd-modal__body">
        <p style="font-size:14px;margin-bottom:12px;" id="disableCategoryText">
          Bạn có chắc chắn muốn vô hiệu hóa danh mục này?
        </p>
        <form method="post" action="index.php?c=catalog&a=disable">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
          <input type="hidden" name="id" id="disableCategoryId">
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
            <button type="button" class="wd-btn-secondary" id="btnCancelDisableCategory">Hủy</button>
            <button type="submit" class="wd-btn-danger">Vô hiệu hóa</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div> <!-- /.category-admin-page -->

<script>
  // ===== helpers modal =====
  function openModal(el){ if (el) el.classList.add('is-open'); }
  function closeModal(el){ if (el) el.classList.remove('is-open'); }

  const createModal  = document.getElementById('createCategoryModal');
  const disableModal = document.getElementById('disableCategoryModal');

  // ===== layout edit giống user admin =====
  const gridEl   = document.getElementById('categoryGrid');
  const editCard = document.getElementById('categoryEditCard');

  const titleEl  = document.getElementById('categoryEditTitle');
  const metaEl   = document.getElementById('categoryEditMeta');

  const idInput    = document.getElementById('catId');
  const nameInput  = document.getElementById('catName');
  const slugInput  = document.getElementById('catSlug');
  const parentSelect = document.getElementById('catParent');
  const activeInput  = document.getElementById('catActive');

  function openEditLayout() {
    gridEl.classList.remove('only-list');
    gridEl.classList.add('has-edit');
    editCard.classList.remove('is-visible');
    requestAnimationFrame(() => {
      editCard.classList.add('is-visible');
    });
  }

  function closeEditLayout() {
    editCard.classList.remove('is-visible');
    setTimeout(() => {
      gridEl.classList.remove('has-edit');
      gridEl.classList.add('only-list');
    }, 240);
  }

  function resetEditForm() {
    idInput.value = '';
    nameInput.value = '';
    slugInput.value = '';
    parentSelect.value = '0';
    activeInput.checked = true;

    document.querySelectorAll('#tblCategories tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));

    titleEl.textContent = 'Cập nhật danh mục';
    metaEl.textContent  = 'Chọn một danh mục ở bảng bên trái để chỉnh sửa.';
  }

  // Không cho chọn chính nó làm danh mục cha (chặn loop cơ bản)
  function updateParentSelectDisabled(currentId) {
    const options = parentSelect.querySelectorAll('option');
    options.forEach(opt => {
      if (opt.value === String(currentId)) {
        opt.disabled = true;
      } else {
        opt.disabled = false;
      }
    });
  }

  // click Sửa
  document.querySelectorAll('.js-edit-category').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr = btn.closest('tr');
      document.querySelectorAll('#tblCategories tr.is-active-row')
        .forEach(r => r.classList.remove('is-active-row'));
      tr.classList.add('is-active-row');

      const id    = tr.dataset.id;
      const name  = tr.dataset.name;
      const slug  = tr.dataset.slug;
      const pid   = tr.dataset.parentId || '0';
      const active= tr.dataset.active === '1';
      const parentName = tr.dataset.parentName || '—';
      const bookCount  = parseInt(tr.dataset.bookCount || '0', 10);
      const childCount = parseInt(tr.dataset.childCount || '0', 10);
      const createdAt  = tr.dataset.createdAt || '';

      idInput.value    = id;
      nameInput.value  = name;
      slugInput.value  = slug;
      parentSelect.value = pid;
      activeInput.checked = active;

      updateParentSelectDisabled(id);

      titleEl.textContent = 'Cập nhật danh mục #' + id;
      let meta = 'Danh mục: ' + name;
      meta += ' • Cha: ' + parentName;
      meta += ' • Sách: ' + bookCount;
      meta += ' • Danh mục con: ' + childCount;
      if (createdAt) meta += ' • Tạo lúc: ' + createdAt;
      metaEl.textContent = meta;

      openEditLayout();
    });
  });

  document.getElementById('btnClearEditCategory')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });
  document.getElementById('btnCloseEditCategory')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });

  // ===== create modal =====
  document.getElementById('btnOpenCreateCategory')?.addEventListener('click', () => {
    openModal(createModal);
  });
  document.getElementById('btnCloseCreateCategory')?.addEventListener('click', () => closeModal(createModal));
  document.getElementById('btnCancelCreateCategory')?.addEventListener('click', () => closeModal(createModal));

  // ===== disable (soft delete) modal =====
  document.querySelectorAll('.js-disable-category').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr = btn.closest('tr');
      const id = tr.dataset.id;
      const name = tr.dataset.name;
      document.getElementById('disableCategoryId').value = id;
      const txt = document.getElementById('disableCategoryText');
      if (txt) {
        txt.textContent = 'Bạn có chắc chắn muốn vô hiệu hóa danh mục "' + name + '" (#' + id + ')?';
      }
      openModal(disableModal);
    });
  });
  document.getElementById('btnCloseDisableCategory')?.addEventListener('click', () => closeModal(disableModal));
  document.getElementById('btnCancelDisableCategory')?.addEventListener('click', () => closeModal(disableModal));
</script>
