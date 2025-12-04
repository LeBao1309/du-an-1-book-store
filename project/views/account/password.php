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
          <div class="text-white-50 small">Cập nhật mật khẩu mới để bảo vệ tài khoản</div>
        </div>

        <div class="card-body p-4">
          
          <?php if (!empty($error)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:20px;">
                <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
            <div style="background:#d1fae5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:20px;">
                <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>

          <form id="changePassForm" method="POST" action="?controller=account&action=changePassword">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                <input name="old_password" id="old_password" type="password" required class="form-control acc-input" placeholder="Nhập mật khẩu đang dùng...">
              </div>

              <hr class="my-3" style="opacity:0.1">
              
              <div class="col-md-6">
                <label class="form-label fw-semibold">Mật khẩu mới</label>
                <input name="new_password" id="new_password" type="password" required class="form-control acc-input" placeholder="Nhập mật khẩu mới...">
                
                <small class="password-note">
                    ⚠️ Mật khẩu cần: Ít nhất 6 ký tự, bao gồm chữ <b>Hoa</b>, chữ <b>thường</b> và <b>ký tự đặc biệt</b> (!@#...).
                </small>
                <div id="newPassError" class="js-error"></div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                <input name="confirm_password" id="confirm_password" type="password" required class="form-control acc-input" placeholder="Nhập lại mật khẩu mới...">
                <div id="confirmPassError" class="js-error"></div>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn w-100 acc-btn">Cập Nhật Mật Khẩu</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
    document.getElementById('changePassForm').addEventListener('submit', function(e) {
        // 1. Lấy giá trị
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        
        const newPassError = document.getElementById('newPassError');
        const confirmPassError = document.getElementById('confirmPassError');
        
        // Reset lỗi cũ
        newPassError.style.display = 'none';
        confirmPassError.style.display = 'none';
        
        let hasError = false;

        // 2. Kiểm tra độ mạnh mật khẩu (Hoa, Thường, Đặc biệt, >=6 ký tự)
        const strongPassRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{6,}$/;
        
        if (!strongPassRegex.test(newPass)) {
            newPassError.innerText = 'Mật khẩu mới chưa đủ mạnh (Cần chữ Hoa, thường & ký tự đặc biệt)';
            newPassError.style.display = 'block';
            hasError = true;
        }

        // 3. Kiểm tra khớp mật khẩu
        if (newPass !== confirmPass) {
            confirmPassError.innerText = 'Mật khẩu xác nhận không khớp!';
            confirmPassError.style.display = 'block';
            hasError = true;
        }

        // 4. Chặn gửi form nếu có lỗi
        if (hasError) {
            e.preventDefault();
        }
    });
</script> 
