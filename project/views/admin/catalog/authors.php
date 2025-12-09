<?php
/** @var array $pagination */
/** @var array $filters */
/** @var string $csrf */

$items    = $pagination['items'] ?? [];
$page     = $pagination['page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$total    = $pagination['total'] ?? 0;
?>
<style>
  /* ===== UI riêng cho trang quản lý tác giả (giống pattern user/category) ===== */
  .author-admin-page {
    margin-top: 4px;
  }

  .author-admin-page .admin-grid-2 {
    display: grid;
    gap: 18px;
    align-items: flex-start;
    transition: grid-template-columns 0.25s ease;
  }

  /* Mặc định chỉ có bảng */
  .author-admin-page .admin-grid-2.only-list {
    grid-template-columns: minmax(0, 1fr);
  }

  /* Khi đang chỉnh sửa */
  .author-admin-page .admin-grid-2.has-edit {
    grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
  }

  .author-admin-page .author-edit-card {
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 120px);
    overflow: auto;

    opacity: 0;
    transform: translateX(16px);
    transition: opacity 0.22s ease, transform 0.22s ease;
  }

  .author-admin-page .admin-grid-2.only-list .author-edit-card {
    display: none;
  }

  .author-admin-page .admin-grid-2.has-edit .author-edit-card {
    display: block;
  }

  .author-admin-page .author-edit-card.is-visible {
    opacity: 1;
    transform: translateX(0);
  }

  .author-admin-page .author-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }

  .author-admin-page .author-edit-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
  }

  .author-admin-page .author-edit-meta {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
  }

  #tblAuthors tr.is-active-row {
    background: #e5e7ff;
  }

  /* Modal dùng chung (create + delete) */
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

<div class="author-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Tác giả</h2>
      <p class="admin-section-subtitle">
        Quản lý danh sách tác giả cho WiseDecision Bookstore.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreateAuthor">
      + Thêm tác giả
    </button>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="authors">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword']); ?>"
             placeholder="Tên tác giả..." class="wd-input">
    </div>

    <div class="filter-group">
      <label>Trạng thái</label>
      <select name="status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="1" <?= $filters['status']==='1' ? 'selected' : ''; ?>>Hiển thị</option>
        <option value="0" <?= $filters['status']==='0' ? 'selected' : ''; ?>>Ẩn</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Loại bản ghi</label>
      <select name="deleted" class="wd-input">
        <option value="0" <?= (int)$filters['deleted'] === 0 ? 'selected' : ''; ?>>Đang hoạt động</option>
        <option value="1" <?= (int)$filters['deleted'] === 1 ? 'selected' : ''; ?>>Đã xóa (thùng rác)</option>
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

  <!-- GRID chính: bảng + panel edit (ẩn/hiện giống user/category) -->
  <div class="admin-grid-2 only-list" id="authorGrid">

    <!-- BẢNG TÁC GIẢ -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách tác giả (<?= (int)$total; ?>)</span>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblAuthors">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tên tác giả</th>
            <th>Slug</th>
            <th>Trạng thái</th>
            <th style="width:120px;">Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $a): ?>
            <?php
            $bookCount = $a['book_count'] ?? 0;
            $createdAt = $a['created_at'] ?? '';
            ?>
            <tr
              data-id="<?= $a['id']; ?>"
              data-name="<?= htmlspecialchars($a['name']); ?>"
              data-slug="<?= htmlspecialchars($a['slug']); ?>"
              data-active="<?= (int)($a['is_active'] ?? 0); ?>"
              data-book-count="<?= (int)$bookCount; ?>"
              data-created-at="<?= htmlspecialchars($createdAt); ?>"
            >
              <td><?= $a['id']; ?></td>
              <td><?= htmlspecialchars($a['name']); ?></td>
              <td><?= htmlspecialchars($a['slug']); ?></td>
              <td>
                <?php if ((int)($filters['deleted'] ?? 0) === 1): ?>
                  <span class="badge badge-danger">Đã xóa</span>
                <?php else: ?>
                  <?php if (!empty($a['is_active'])): ?>
                    <span class="badge badge-success">Hiển thị</span>
                  <?php else: ?>
                    <span class="badge badge-muted">Ẩn</span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if ((int)($filters['deleted'] ?? 0) === 1): ?>
                  <form method="post" action="index.php?c=authors&a=restore" style="display:inline;">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                    <input type="hidden" name="id" value="<?= $a['id']; ?>">
                    <button type="submit" class="wd-icon-btn" title="Khôi phục"
                            onclick="return confirm('Khôi phục tác giả này?');">
                      ⟳
                    </button>
                  </form>
                <?php else: ?>
                  <button type="button"
                          class="wd-icon-btn js-edit-author"
                          title="Chỉnh sửa">
                    ✏️
                  </button>
                  <button type="button"
                          class="wd-icon-btn danger js-delete-author"
                          title="Xóa">
                    🗑
                  </button>
                <?php endif; ?>
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
               href="index.php?c=authors&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&status=<?= urlencode($filters['status']); ?>&deleted=<?= (int)$filters['deleted']; ?>">
              <?= $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL CẬP NHẬT TÁC GIẢ -->
    <div class="admin-card author-edit-card" id="authorEditCard">
      <div class="author-edit-header">
        <div>
          <h3 id="authorEditTitle">Cập nhật tác giả</h3>
          <div class="author-edit-meta" id="authorEditMeta">
            Chọn một tác giả ở bảng bên trái để chỉnh sửa.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseEditAuthor">✕</button>
      </div>

      <form method="post" action="index.php?c=authors&a=update" id="authorEditForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="authorId">

        <label class="wd-label">Tên tác giả</label>
        <input type="text" name="name" id="authorName" class="wd-input" required>

        <label class="wd-label">Slug (bỏ trống sẽ tự tạo)</label>
        <input type="text" name="slug" id="authorSlug" class="wd-input">

        <label class="wd-label" style="margin-top:10px;">
          <input type="checkbox" name="is_active" id="authorActive" value="1">
          <span>Hiển thị</span>
        </label>

        <div style="margin-top:16px;display:flex;gap:8px;">
          <button type="submit" class="wd-btn-primary">Lưu thay đổi</button>
          <button type="button" class="wd-btn-secondary" id="btnCancelEditAuthor">
            Hủy / trở về
          </button>
        </div>
      </form>

      <hr style="margin:14px 0;border:none;border-top:1px solid #e5e7eb;">

      <div style="font-size:12px;color:#6b7280;">
        <div><strong>Ghi chú:</strong></div>
        <ul style="padding-left:16px;margin-top:4px;">
          <li>Nếu tác giả đang được dùng ở nhiều sách, thao tác xóa sẽ bị chặn từ server.</li>
          <li>Nên giữ slug ổn định để tránh lỗi khi seed / mapping dữ liệu.</li>
        </ul>
      </div>
    </div>
  </div> <!-- /.admin-grid-2 -->

  <!-- MODAL: TẠO TÁC GIẢ MỚI -->
  <div class="wd-modal" id="createAuthorModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Thêm tác giả mới</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseCreateAuthor">✕</button>
      </div>
      <div class="wd-modal__body">
        <form method="post" action="index.php?c=authors&a=store">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

          <label class="wd-label">Tên tác giả</label>
          <input type="text" name="name" class="wd-input" required>

          <label class="wd-label">Slug (bỏ trống sẽ tự tạo)</label>
          <input type="text" name="slug" class="wd-input">

          <label class="wd-label" style="margin-top:10px;">
            <input type="checkbox" name="is_active" value="1" checked>
            <span>Hiển thị</span>
          </label>

          <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" class="wd-btn-secondary" id="btnCancelCreateAuthor">
              Hủy
            </button>
            <button type="submit" class="wd-btn-primary">
              Lưu tác giả
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: XÓA TÁC GIẢ (có thể bị chặn nếu đang được dùng) -->
  <div class="wd-modal" id="deleteAuthorModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Xóa tác giả</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseDeleteAuthor">✕</button>
      </div>
      <div class="wd-modal__body">
        <p style="font-size:14px;margin-bottom:12px;" id="deleteAuthorText">
          Bạn có chắc chắn muốn xóa tác giả này?
        </p>
        <form method="post" action="index.php?c=authors&a=delete">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
          <input type="hidden" name="id" id="deleteAuthorId">
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
            <button type="button" class="wd-btn-secondary" id="btnCancelDeleteAuthor">Hủy</button>
            <button type="submit" class="wd-btn-danger">Xóa</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div> <!-- /.author-admin-page -->

<script>
  function openModal(el){ if (el) el.classList.add('is-open'); }
  function closeModal(el){ if (el) el.classList.remove('is-open'); }

  const createModal  = document.getElementById('createAuthorModal');
  const deleteModal  = document.getElementById('deleteAuthorModal');

  const gridEl   = document.getElementById('authorGrid');
  const editCard = document.getElementById('authorEditCard');

  const titleEl  = document.getElementById('authorEditTitle');
  const metaEl   = document.getElementById('authorEditMeta');

  const idInput   = document.getElementById('authorId');
  const nameInput = document.getElementById('authorName');
  const slugInput = document.getElementById('authorSlug');

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
    document.getElementById('authorActive').checked = true;

    document.querySelectorAll('#tblAuthors tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));

    titleEl.textContent = 'Cập nhật tác giả';
    metaEl.textContent  = 'Chọn một tác giả ở bảng bên trái để chỉnh sửa.';
  }

  // mở panel update khi bấm "Sửa"
  document.querySelectorAll('.js-edit-author').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr = btn.closest('tr');
      document.querySelectorAll('#tblAuthors tr.is-active-row')
        .forEach(r => r.classList.remove('is-active-row'));
      tr.classList.add('is-active-row');

      const id   = tr.dataset.id;
      const name = tr.dataset.name;
      const slug = tr.dataset.slug;
      const active = tr.dataset.active === '1';
      const bookCount = parseInt(tr.dataset.bookCount || '0', 10);
      const createdAt = tr.dataset.createdAt || '';

      idInput.value   = id;
      nameInput.value = name;
      slugInput.value = slug;
      document.getElementById('authorActive').checked = active;

      titleEl.textContent = 'Cập nhật tác giả #' + id;
      let meta = 'Tác giả: ' + name;
      meta += ' • Sách sử dụng: ' + bookCount;
      if (createdAt) meta += ' • Tạo lúc: ' + createdAt;
      metaEl.textContent = meta;

      openEditLayout();
    });
  });

  document.getElementById('btnCancelEditAuthor')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });
  document.getElementById('btnCloseEditAuthor')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });

  // tạo mới: modal
  document.getElementById('btnOpenCreateAuthor')?.addEventListener('click', () => {
    openModal(createModal);
  });
  document.getElementById('btnCloseCreateAuthor')?.addEventListener('click', () => closeModal(createModal));
  document.getElementById('btnCancelCreateAuthor')?.addEventListener('click', () => closeModal(createModal));

  // xóa: modal confirm (controller sẽ chặn nếu đang được dùng)
  document.querySelectorAll('.js-delete-author').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr   = btn.closest('tr');
      const id   = tr.dataset.id;
      const name = tr.dataset.name;
      document.getElementById('deleteAuthorId').value = id;
      const txt = document.getElementById('deleteAuthorText');
      if (txt) {
        txt.textContent = 'Bạn có chắc chắn muốn xóa tác giả "' + name + '" (#' + id + ')?';
      }
      openModal(deleteModal);
    });
  });
  document.getElementById('btnCloseDeleteAuthor')?.addEventListener('click', () => closeModal(deleteModal));
  document.getElementById('btnCancelDeleteAuthor')?.addEventListener('click', () => closeModal(deleteModal));
</script>
