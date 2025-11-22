<?php $active = 'profile'; ?>
<section class="container my-4">
  <div class="row g-4">
    <div class="col-12 col-lg-3">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </div>

    <div class="col-12 col-lg-9">
      <div class="card shadow-sm border-0 acc-content">
        <div class="acc-content__head">
          <h4 class="mb-0 text-white fw-bold">Thông Tin Cá Nhân</h4>
          <div class="text-white-50 small">Quản lý thông tin hồ sơ của bạn</div>
        </div>

        <div class="card-body p-4">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="?controller=account&action=updateProfile">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Họ và tên</label>
                <input name="name" class="form-control acc-input"
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input name="email" type="email" class="form-control acc-input"
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                <div class="small text-muted mt-1">Email có thể thay đổi</div>
              </div>
            </div>

            <div class="mt-4">
              <button class="btn btn-success px-4 acc-btn">Lưu Thay Đổi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
/* HEADER SIDEBAR */
.acc-sidebar__head{
  background: linear-gradient(180deg, var(--brand), var(--brand-2));
  padding: 22px 20px;
  color: #fff;
}

/* MENU ITEMS */
.acc-menu .acc-item{
  padding: 14px 16px;
  font-weight: 500;
  border: 0;
  background: var(--card);
  color: var(--text);
}

.acc-menu .acc-item.active{
  background: rgba(14,165,165,0.12); /* nhấn nhẹ brand */
  color: var(--brand);
  border-left: 4px solid var(--brand);
}

.acc-menu .acc-item:hover{
  background: rgba(14,165,165,0.08);
}

/* CARD CONTENT VÙNG PHẢI */
.acc-content{
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--card);
  box-shadow: var(--shadow);
}

.acc-content__head{
  background: linear-gradient(180deg, var(--brand), var(--brand-2));
  padding: 20px 22px;
  color: #fff;
}

/* INPUT */
.acc-input{
  height: 46px;
  border-radius: 12px;
  border: 1px solid var(--line);
  color: var(--text);
  background: var(--card);
}

/* BUTTON */
.acc-btn{
  height: 48px;
  border-radius: 12px;
  font-weight: 600;
  background: var(--brand);
  border: none;
  color: #fff;
}

.acc-btn:hover{
  background: var(--brand-2);
}

</style>
