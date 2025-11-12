<section class="card" style="max-width:520px; padding:16px">
  <h1>Tạo tài khoản</h1>

  <?php if (!empty($error)): ?>
    <p class="form-message error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="?c=auth&a=register" method="POST" autocomplete="on">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

    <label for="name">Tên của bạn</label>
    <input type="text" id="name" name="name" required class="text" style="width:100%; margin-top:5px;">

    <label for="email" style="display:block; margin-top:10px;">Email</label>
    <input type="email" id="email" name="email" required class="text" style="width:100%; margin-top:5px;">

    <label for="password" style="display:block; margin-top:10px;">Mật khẩu (ít nhất 6 ký tự)</label>
    <input type="password" id="password" name="password" required minlength="6" class="text" style="width:100%; margin-top:5px;">

    <label for="confirm_password" style="display:block; margin-top:10px;">Xác nhận mật khẩu</label>
    <input type="password" id="confirm_password" name="confirm_password" required minlength="6" class="text" style="width:100%; margin-top:5px;">

    <button class="btn solid" style="margin-top:10px; width:100%">Tạo tài khoản</button>
  </form>

  <hr/>
  <p>Đã có tài khoản? <a href="?c=auth&a=login" style="color:var(--brand);">Đăng nhập</a></p>
</section>
