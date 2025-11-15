<section class="card" style="max-width:520px; padding:16px">
  <h1>Đăng nhập</h1>

  <?php if (!empty($error)): ?>
    <p class="form-message error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <?php if (!empty($success)):  ?>
    <p class="form-message success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <form action="index.php?controller=auth&action=login" method="POST" ...>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required class="text" style="width:100%; margin-top:5px;">

    <label for="password" style="display:block; margin-top:10px;">Mật khẩu</label>
    <input type="password" id="password" name="password" required class="text" style="width:100%; margin-top:5px;">

    <button class="btn solid" style="margin-top:10px; width:100%">Đăng nhập</button>
  </form>

  <hr/>
  <p>Chưa có tài khoản? <a href="index.php?controller=auth&action=register">Đăng ký ngay</a></p>
</section>