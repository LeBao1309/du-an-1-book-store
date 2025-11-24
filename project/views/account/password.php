<?php 
// Đặt biến active để sidebar biết menu nào đang được chọn
$active = 'password'; 
?>
<section class="container my-4">
  <div class="row g-4">
    <div class="col-12 col-lg-3">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </div>

    <div class="col-12 col-lg-9">
      <div class="card shadow-sm border-0 acc-content">
        <div class="acc-content__head">
          <h4 class="mb-0 text-white fw-bold">Đổi Mật Khẩu</h4>
          <div class="text-white-50 small">Thay đổi mật khẩu tài khoản của bạn</div>
        </div>

        <div class="card-body p-4">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="?controller=account&action=changePassword">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Mật khẩu cũ</label>
                <input name="old_password" type="password" required class="form-control acc-input">
              </div>

              <hr class="my-3">
              
              <div class="col-md-6">
                <label class="form-label fw-semibold">Mật khẩu mới (Tối thiểu 6 ký tự)</label>
                <input name="new_password" type="password" required minlength="6" class="form-control acc-input">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                <input name="confirm_password" type="password" required minlength="6" class="form-control acc-input">
              </div>
            </div>

            <div class="mt-4">
              <button class="btn btn-success px-4 acc-btn">Cập Nhật Mật Khẩu</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
/* ... CSS styles for acc-sidebar, acc-content, acc-input, acc-btn ... */
</style>