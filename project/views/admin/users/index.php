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
/* ====== ONLY FOR USER ADMIN PAGE ====== */
.user-admin-page {
  margin-top: 4px;
}

/* grid tổng: trạng thái 1 cột (only-list) hoặc 2 cột (has-edit) */
.user-admin-page .admin-grid-2 {
  display: grid;
  gap: 18px;
  align-items: flex-start;
}

/* Chỉ có bảng (mặc định) */
.user-admin-page .admin-grid-2.only-list {
  grid-template-columns: minmax(0, 1fr);
}

/* Có form edit */
.user-admin-page .admin-grid-2.has-edit {
  grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
}

/* card form bên phải sticky khi hiện */
.user-admin-page .user-edit-card {
  position: sticky;
  top: 10px;
  max-height: calc(100vh - 120px);
  overflow: auto;
  display: none; /* Ẩn mặc định */
}

/* Khi có edit thì hiện */
.user-admin-page .admin-grid-2.has-edit .user-edit-card {
  display: block;
}

/* header form bên phải */
.user-admin-page .user-edit-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.user-admin-page .user-edit-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

/* trạng thái khi chưa chọn user */
.user-admin-page .user-edit-meta {
  font-size: 11px;
  color:#6b7280;
  margin-top:4px;
}

/* nhấn row đang chỉnh sửa */
#tblUsers tr.is-active-row {
  background: #e5e7ff;
}

/* nút icon nhỏ trong bảng */
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

/* badge quyền / trạng thái */
.badge-admin,
.badge-user-status,
.badge-admin-status {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 12px;
}

.badge-admin {
  background: #eef2ff;
  color: #4338ca;
}

.badge-user-status {
  background: #ecfdf3;
  color: #16a34a;
}

.badge-admin-status {
  background: #fee2e2;
  color: #b91c1c;
}

/* modal create / disable */
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
  border-radius: 16px;
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
.wd-btn-danger:hover {
  background: #b91c1c;
}

/* form trong card bên phải */
.user-admin-page .wd-input,
.user-admin-page .wd-label {
  font-size: 13px;
}
.user-admin-page .wd-input {
  width: 100%;
  padding: 7px 10px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
}

.user-admin-page .wd-label {
  display:block;
  margin-top: 8px;
  margin-bottom: 4px;
  font-weight: 500;
}
</style>

<div class="user-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Quản lý người dùng</h2>
      <p class="admin-section-subtitle">
        Xem và quản lý tài khoản người dùng trong hệ thống.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreateUser">
      + Tạo tài khoản
    </button>
  </div>

  <!-- FILTER BAR -->
  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="users">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Tìm kiếm</label>
      <input
        type="text"
        name="keyword"
        class="wd-input"
        placeholder="Tên hoặc email…"
        value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
    </div>

    <div class="filter-group">
      <label>Quyền</label>
      <select name="role" class="wd-input">
        <option value="">Tất cả quyền</option>
        <option value="user"  <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Trạng thái</label>
      <select name="status" class="wd-input">
        <option value="">Tất cả</option>
        <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Hoạt động</option>
        <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Vô hiệu hóa</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="wd-btn-secondary">Lọc</button>
    </div>
  </form>

  <!-- FLASH MESSAGE -->
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

  <!-- MAIN GRID: TABLE (LEFT) + EDIT FORM (RIGHT - HIDDEN INIT) -->
  <div class="admin-grid-2 only-list" id="userGrid">

    <!-- BẢNG NGƯỜI DÙNG -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách người dùng (<?= (int)$total; ?>)</span>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblUsers">
          <thead>
          <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Quyền</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $u): ?>
            <tr
              data-id="<?= $u['id']; ?>"
              data-name="<?= htmlspecialchars($u['name']); ?>"
              data-email="<?= htmlspecialchars($u['email']); ?>"
              data-role="<?= htmlspecialchars($u['role']); ?>"
              data-active="<?= (int)$u['is_active']; ?>"
            >
              <td>#<?= $u['id']; ?></td>
              <td><?= htmlspecialchars($u['name']); ?></td>
              <td><?= htmlspecialchars($u['email']); ?></td>
              <td>
                <?php if ($u['role'] === 'admin'): ?>
                  <span class="badge-admin">Admin</span>
                <?php else: ?>
                  <span class="badge-user">User</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u['is_active']): ?>
                  <span class="badge-user-status">Hoạt động</span>
                <?php else: ?>
                  <span class="badge-admin-status">Vô hiệu</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($u['created_at']); ?></td>
              <td>
                <button type="button"
                        class="wd-icon-btn js-edit-user"
                        title="Chỉnh sửa">
                  ✏️
                </button>
                <button type="button"
                        class="wd-icon-btn danger js-disable-user"
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
               href="index.php?c=users&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword'] ?? ''); ?>&role=<?= urlencode($filters['role'] ?? ''); ?>&status=<?= urlencode($filters['status'] ?? ''); ?>">
              <?= $p; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- FORM CẬP NHẬT BÊN PHẢI (ẩn cho tới khi click sửa) -->
    <div class="admin-card user-edit-card" id="userEditCard">
      <div class="user-edit-header">
        <div>
          <h3 id="userEditTitle">Cập nhật người dùng</h3>
          <div class="user-edit-meta" id="userEditMeta">
            Chọn một người dùng ở bảng bên trái để chỉnh sửa.
          </div>
        </div>
      </div>

      <form method="post" action="index.php?c=users&a=update">" id="userEditForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="editUserId">

        <label class="wd-label">Tên</label>
        <input type="text" class="wd-input" name="name" id="editUserName" required>

        <label class="wd-label">Email</label>
        <input type="email" class="wd-input" name="email" id="editUserEmail" required>

        <label class="wd-label">Mật khẩu mới (để trống nếu không đổi)</label>
        <input type="password" class="wd-input" name="password">

        <label class="wd-label">Quyền</label>
        <select class="wd-input" name="role" id="editUserRole">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>

        <label class="wd-label">
          <input type="checkbox" name="is_active" id="editUserActive" value="1">
          Hoạt động
        </label>

        <div style="margin-top:14px;display:flex;gap:8px;">
          <button type="submit" class="wd-btn-primary">Cập nhật</button>
          <button type="button" class="wd-btn-secondary" id="btnClearEdit">Làm mới form</button>
        </div>
      </form>
    </div>

  </div> <!-- /.admin-grid-2 -->

  <!-- MODAL: CREATE USER -->
  <div class="wd-modal" id="createUserModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Tạo tài khoản mới</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseCreateModal">✕</button>
      </div>
      <div class="wd-modal__body">
        <form method="post" action="index.php?c=users&a=store">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

          <label class="wd-label">Tên</label>
          <input type="text" class="wd-input" name="name" required>

          <label class="wd-label" style="margin-top:10px;">Email</label>
          <input type="email" class="wd-input" name="email" required>

          <label class="wd-label" style="margin-top:10px;">Mật khẩu</label>
          <input type="password" class="wd-input" name="password" required>

          <label class="wd-label" style="margin-top:10px;">Quyền</label>
          <select class="wd-input" name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>

          <label class="wd-label" style="margin-top:10px;">
            <input type="checkbox" name="is_active" value="1" checked>
            Hoạt động
          </label>

          <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" class="wd-btn-secondary" id="btnCancelCreate">
              Hủy
            </button>
            <button type="submit" class="wd-btn-primary">
              Lưu tài khoản
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: CONFIRM DISABLE -->
  <div class="wd-modal" id="disableUserModal">
    <div class="wd-modal__backdrop"></div>
    <div class="wd-modal__dialog">
      <div class="wd-modal__header">
        <h3>Vô hiệu hóa tài khoản</h3>
        <button type="button" class="wd-icon-btn" id="btnCloseDisableModal">✕</button>
      </div>
      <div class="wd-modal__body">
        <p style="font-size:14px;margin-bottom:12px;">
          Bạn có chắc chắn muốn vô hiệu hóa tài khoản này?
        </p>
        <form method="post" action="index.php?c=users&a=disable">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
          <input type="hidden" name="id" id="disableUserId">
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
            <button type="button" class="wd-btn-secondary" id="btnCancelDisable">Hủy</button>
            <button type="submit" class="wd-btn-danger">Vô hiệu hóa</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div> <!-- /.user-admin-page -->

<script>
// ===== helper modal =====
function openModal(el){ if(el) el.classList.add('is-open'); }
function closeModal(el){ if(el) el.classList.remove('is-open'); }

const createModal  = document.getElementById('createUserModal');
const disableModal = document.getElementById('disableUserModal');

// ===== Edit user in side card =====
const rows     = document.querySelectorAll('#tblUsers tbody tr');
const titleEl  = document.getElementById('userEditTitle');
const metaEl   = document.getElementById('userEditMeta');
const gridEl   = document.getElementById('userGrid');
const editCard = document.getElementById('userEditCard');

const idInput    = document.getElementById('editUserId');
const nameInput  = document.getElementById('editUserName');
const emailInput = document.getElementById('editUserEmail');
const roleInput  = document.getElementById('editUserRole');
const activeInput= document.getElementById('editUserActive');

// mở layout 2 cột + animate card bên phải
function openEditLayout() {
  gridEl.classList.remove('only-list');
  gridEl.classList.add('has-edit');

  // reset trạng thái animation rồi add is-visible ở frame tiếp theo
  editCard.classList.remove('is-visible');
  requestAnimationFrame(() => {
    editCard.classList.add('is-visible');
  });
}

// đóng layout 2 cột + animate ẩn card
function closeEditLayout() {
  // fade-out card trước
  editCard.classList.remove('is-visible');

  // sau khi animation kết thúc (0.22s) mới trả về only-list
  setTimeout(() => {
    gridEl.classList.remove('has-edit');
    gridEl.classList.add('only-list');
  }, 240);
}

// reset dữ liệu form + bỏ highlight row
function resetEditForm() {
  idInput.value = '';
  nameInput.value = '';
  emailInput.value = '';
  roleInput.value = 'user';
  activeInput.checked = true;

  document.querySelectorAll('#tblUsers tr.is-active-row')
    .forEach(r => r.classList.remove('is-active-row'));

  titleEl.textContent = 'Cập nhật người dùng';
  metaEl.textContent  = 'Chọn một người dùng ở bảng bên trái để chỉnh sửa.';
}

// click row → mở panel + đổ data
rows.forEach(tr => {
  const editBtn = tr.querySelector('.js-edit-user');
  editBtn.addEventListener('click', () => {
    // highlight row
    document.querySelectorAll('#tblUsers tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));
    tr.classList.add('is-active-row');

    // set form values
    idInput.value    = tr.dataset.id;
    nameInput.value  = tr.dataset.name;
    emailInput.value = tr.dataset.email;
    roleInput.value  = tr.dataset.role;
    activeInput.checked = tr.dataset.active === '1';

    titleEl.textContent = 'Cập nhật người dùng #' + tr.dataset.id;
    metaEl.textContent  = 'Đang chỉnh sửa: ' + tr.dataset.name + ' (' + tr.dataset.email + ')';

    openEditLayout();
  });
});

// nút "Làm mới form" → đóng panel & reset
document.getElementById('btnClearEdit')?.addEventListener('click', () => {
  resetEditForm();
  closeEditLayout();  
});

// nút ✕ trên header panel → giống "cancel"
document.getElementById('btnCloseEditCard')?.addEventListener('click', () => {
  resetEditForm();
  closeEditLayout();
});

// ===== Create user modal =====
document.getElementById('btnOpenCreateUser')?.addEventListener('click', () => {
  openModal(createModal);
});
document.getElementById('btnCloseCreateModal')?.addEventListener('click', () => closeModal(createModal));
document.getElementById('btnCancelCreate')?.addEventListener('click', () => closeModal(createModal));

// ===== Disable user modal =====
document.querySelectorAll('.js-disable-user').forEach(btn => {
  btn.addEventListener('click', () => {
    const tr = btn.closest('tr');
    document.getElementById('disableUserId').value = tr.dataset.id;
    openModal(disableModal);
  });
});
document.getElementById('btnCloseDisableModal')?.addEventListener('click', () => closeModal(disableModal));
document.getElementById('btnCancelDisable')?.addEventListener('click', () => closeModal(disableModal));
</script>
