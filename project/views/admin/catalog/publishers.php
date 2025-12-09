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
  /* ===== UI riêng cho trang quản lý NXB (giống pattern user/author) ===== */
  .publisher-admin-page {
    margin-top: 4px;
  }

  .publisher-admin-page .admin-grid-2 {
    display: grid;
    gap: 18px;
    align-items: flex-start;
    transition: grid-template-columns 0.25s ease;
  }

  /* Mặc định chỉ có bảng */
  .publisher-admin-page .admin-grid-2.only-list {
    grid-template-columns: minmax(0, 1fr);
  }

  /* Khi đang chỉnh sửa */
  .publisher-admin-page .admin-grid-2.has-edit {
    grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
  }

  .publisher-admin-page .publisher-edit-card {
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 120px);
    overflow: auto;

    opacity: 0;
    transform: translateX(16px);
    transition: opacity 0.22s ease, transform 0.22s ease;
  }

  .publisher-admin-page .admin-grid-2.only-list .publisher-edit-card {
    display: none;
  }

  .publisher-admin-page .admin-grid-2.has-edit .publisher-edit-card {
    display: block;
  }

  .publisher-admin-page .publisher-edit-card.is-visible {
    opacity: 1;
    transform: translateX(0);
  }

  .publisher-admin-page .publisher-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }

  .publisher-admin-page .publisher-edit-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
  }

  .publisher-admin-page .publisher-edit-meta {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
  }

  #tblPublishers tr.is-active-row {
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

<div class="publisher-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Nhà xuất bản</h2>
      <p class="admin-section-subtitle">
        Quản lý danh sách nhà xuất bản.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreatePublisher">
      + Thêm nhà xuất bản
    </button>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="publishers">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword']); ?>"
             placeholder="Tên NXB..." class="wd-input">
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

  <!-- GRID chính: bảng + panel edit -->
  <div class="admin-grid-2 only-list" id="publisherGrid">

    <!-- BẢNG NXB -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách NXB (<?= (int)$total; ?>)</span>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblPublishers">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tên NXB</th>
            <th>Slug</th>
            <th>Trạng thái</th>
            <th style="width:120px;">Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $p): ?>
            <?php
            $bookCount = $p['book_count'] ?? 0;
            $createdAt = $p['created_at'] ?? '';
            ?>
            <tr
              data-id="<?= $p['id']; ?>"
              data-name="<?= htmlspecialchars($p['name']); ?>"
              data-slug="<?= htmlspecialchars($p['slug']); ?>"
              data-active="<?= (int)($p['is_active'] ?? 0); ?>"
              data-book-count="<?= (int)$bookCount; ?>"
              data-created-at="<?= htmlspecialchars($createdAt); ?>"
            >
              <td><?= $p['id']; ?></td>
              <td><?= htmlspecialchars($p['name']); ?></td>
              <td><?= htmlspecialchars($p['slug']); ?></td>
              <td>
                <?php if ((int)($filters['deleted'] ?? 0) === 1): ?>
                  <span class="badge badge-danger">Đã xóa</span>
                <?php else: ?>
                  <?php if (!empty($p['is_active'])): ?>
                    <span class="badge badge-success">Hiển thị</span>
                  <?php else: ?>
                    <span class="badge badge-muted">Ẩn</span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if ((int)($filters['deleted'] ?? 0) === 1): ?>
                  <form method="post" action="index.php?c=publishers&a=restore" style="display:inline;">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                    <input type="hidden" name="id" value="<?= $p['id']; ?>">
                    <button type="submit" class="wd-icon-btn" title="Khôi phục"
                            onclick="return confirm('Khôi phục nhà xuất bản này?');">
                      ⟳
                    </button>
                  </form>
                <?php else: ?>
                  <button type="button"
                          class="wd-icon-btn js-edit-publisher"
                          title="Chỉnh sửa">
                    ✏️
                  </button>
                  <button type="button"
                          class="wd-icon-btn danger js-delete-publisher"
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
          <?php for ($pg = 1; $pg <= $lastPage; $pg++): ?>
            <a class="page-link <?= $pg === $page ? 'active' : ''; ?>"
               href="index.php?c=publishers&a=index&page=<?= $pg; ?>&keyword=<?= urlencode($filters['keyword']); ?>&status=<?= urlencode($filters['status']); ?>&deleted=<?= (int)$filters['deleted']; ?>">
              <?= $pg; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL CẬP NHẬT NXB -->
    <div class="admin-card publisher-edit-card" id="publisherEditCard">
      <div class="publisher-edit-header">
        <div>
          <h3 id="publisherEditTitle">Cập nhật nhà xuất bản</h3>
          <div class="publisher-edit-meta" id="publisherEditMeta">
            Chọn một NXB ở bảng bên trái để chỉnh sửa.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseEditPublisher">✕</button>
      </div>

      <form method="post" action="index.php?c=publishers&a=update" id="publisherEditForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="pubId">

        <label class="wd-label">Tên NXB</label>
        <input type="text" name="name" id="pubName" class="wd-input" required>

        <label class="wd-label">Slug (bỏ trống sẽ tự tạo)</label>
        <input type="text" name="slug" id="pubSlug" class="wd-input">

        <label class="wd-label" style="margin-top:10px;">
          <input type="checkbox" name="is_active" id="pubActive" value="1">
          <span>Hiển thị</span>
        </label>

        <div style="margin-top:16px;display:flex;gap:8px;">
          <button type="submit" class="wd-btn-primary">Lưu thay đổi</button>
          <button type="button" class="wd-btn-secondary" id="btnCancelEditPublisher">
            Hủy / trở về
          </button>
        </div>
      </form>

      <hr style="margin:14px 0;border:none;border-top:1px solid #e5e7eb;">

      <div style="font-size:12px;color:#6b7280;">
        <div><strong>Ghi chú:</strong></div>
        <ul style="padding-left:16px;margin-top:4px;">
          <li>Nếu NXB đang được dùng ở nhiều sách, thao tác xóa sẽ bị chặn từ server.</li>
          <li>Nên giữ slug ổn định để tránh lỗi khi seed / mapping dữ liệu.</li>
        </ul>
      </div>
    </div>
  </div> <!-- /.admin-grid-2 -->

  <!-- MODAL: TẠO NXB MỚI -->
  <div class="wd-modal" id="createPublisherModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Thêm nhà xuất bản mới</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseCreatePublisher">✕</button>
      </div>
      <div class="wd-modal__body">
        <form method="post" action="index.php?c=publishers&a=store">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

          <label class="wd-label">Tên NXB</label>
          <input type="text" name="name" class="wd-input" required>

          <label class="wd-label">Slug (bỏ trống sẽ tự tạo)</label>
          <input type="text" name="slug" class="wd-input">

          <label class="wd-label" style="margin-top:10px;">
            <input type="checkbox" name="is_active" value="1" checked>
            <span>Hiển thị</span>
          </label>

          <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" class="wd-btn-secondary" id="btnCancelCreatePublisher">
              Hủy
            </button>
            <button type="submit" class="wd-btn-primary">
              Lưu NXB
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: XÓA NXB -->
  <div class="wd-modal" id="deletePublisherModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Xóa nhà xuất bản</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseDeletePublisher">✕</button>
      </div>
      <div class="wd-modal__body">
        <p style="font-size:14px;margin-bottom:12px;" id="deletePublisherText">
          Bạn có chắc chắn muốn xóa nhà xuất bản này?
        </p>
        <form method="post" action="index.php?c=publishers&a=delete">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
          <input type="hidden" name="id" id="deletePublisherId">
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
            <button type="button" class="wd-btn-secondary" id="btnCancelDeletePublisher">Hủy</button>
            <button type="submit" class="wd-btn-danger">Xóa</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div> <!-- /.publisher-admin-page -->

<script>
  function openModal(el){ if (el) el.classList.add('is-open'); }
  function closeModal(el){ if (el) el.classList.remove('is-open'); }

  const createModal  = document.getElementById('createPublisherModal');
  const deleteModal  = document.getElementById('deletePublisherModal');

  const gridEl   = document.getElementById('publisherGrid');
  const editCard = document.getElementById('publisherEditCard');

  const titleEl  = document.getElementById('publisherEditTitle');
  const metaEl   = document.getElementById('publisherEditMeta');

  const idInput   = document.getElementById('pubId');
  const nameInput = document.getElementById('pubName');
  const slugInput = document.getElementById('pubSlug');
  const activeInput = document.getElementById('pubActive');

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
    if (activeInput) activeInput.checked = true;

    document.querySelectorAll('#tblPublishers tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));

    titleEl.textContent = 'Cập nhật nhà xuất bản';
    metaEl.textContent  = 'Chọn một NXB ở bảng bên trái để chỉnh sửa.';
  }

  // mở panel update khi bấm "Sửa"
  document.querySelectorAll('.js-edit-publisher').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr = btn.closest('tr');
      document.querySelectorAll('#tblPublishers tr.is-active-row')
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
      if (activeInput) activeInput.checked = active;

      titleEl.textContent = 'Cập nhật NXB #' + id;
      let meta = 'NXB: ' + name;
      meta += ' • Sách sử dụng: ' + bookCount;
      if (createdAt) meta += ' • Tạo lúc: ' + createdAt;
      metaEl.textContent = meta;

      openEditLayout();
    });
  });

  document.getElementById('btnCancelEditPublisher')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });
  document.getElementById('btnCloseEditPublisher')?.addEventListener('click', () => {
    resetEditForm();
    closeEditLayout();
  });

  // tạo mới: modal
  document.getElementById('btnOpenCreatePublisher')?.addEventListener('click', () => {
    openModal(createModal);
  });
  document.getElementById('btnCloseCreatePublisher')?.addEventListener('click', () => closeModal(createModal));
  document.getElementById('btnCancelCreatePublisher')?.addEventListener('click', () => closeModal(createModal));

  // xóa: modal confirm (backend sẽ chặn nếu đang được dùng ở sách)
  document.querySelectorAll('.js-delete-publisher').forEach(btn => {
    btn.addEventListener('click', () => {
      const tr   = btn.closest('tr');
      const id   = tr.dataset.id;
      const name = tr.dataset.name;
      document.getElementById('deletePublisherId').value = id;
      const txt = document.getElementById('deletePublisherText');
      if (txt) {
        txt.textContent = 'Bạn có chắc chắn muốn xóa nhà xuất bản "' + name + '" (#' + id + ')?';
      }
      openModal(deleteModal);
    });
  });

  document.getElementById('btnCloseDeletePublisher')?.addEventListener('click', () => closeModal(deleteModal));
  document.getElementById('btnCancelDeletePublisher')?.addEventListener('click', () => closeModal(deleteModal));
</script>
