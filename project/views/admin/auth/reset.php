<div class="admin-login-wrapper">
  <div class="admin-card" style="max-width:420px;margin:40px auto;">
    <h2 style="margin-top:0;margin-bottom:8px;">Đặt lại mật khẩu (Admin)</h2>
    <p style="margin-top:0;font-size:13px;color:#6b7280;">
      Nhập mật khẩu mới cho tài khoản quản trị.
    </p>

    <?php if (!empty($error)): ?>
      <div style="background:#fee2e2;border-radius:10px;padding:8px 10px;
                  font-size:13px;color:#b91c1c;margin-bottom:10px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="index.php?c=auth&a=reset">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

      <label style="display:block;font-size:13px;margin-top:8px;">Mật khẩu mới</label>
      <input type="password" name="password" required
             style="width:100%;padding:8px 10px;border-radius:10px;
                    border:1px solid #e5e7eb;font-size:14px;">

      <label style="display:block;font-size:13px;margin-top:8px;">Nhập lại mật khẩu</label>
      <input type="password" name="password2" required
             style="width:100%;padding:8px 10px;border-radius:10px;
                    border:1px solid #e5e7eb;font-size:14px;">

      <button type="submit"
              style="margin-top:14px;width:100%;padding:9px 10px;border-radius:999px;
                     border:none;background:linear-gradient(135deg,#ec4899,#6366f1);
                     color:#fff;font-weight:600;font-size:14px;cursor:pointer;">
        Cập nhật mật khẩu
      </button>
    </form>

    <div style="text-align:center;margin-top:14px;font-size:13px;color:#6b7280;">
      <a href="index.php?c=auth&a=login" style="color:#4f46e5;font-weight:600;">Quay lại đăng nhập</a>
    </div>
  </div>
</div>
