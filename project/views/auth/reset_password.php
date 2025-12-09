<div class="auth-wrapper">
    <section class="auth-card">
        <h1>Đặt lại mật khẩu</h1>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=reset" method="POST">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="form-group">
                <label for="password">Mật khẩu mới</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Nhập mật khẩu mới">
            </div>

            <div class="form-group">
                <label for="password2">Nhập lại mật khẩu</label>
                <input type="password" id="password2" name="password2" class="form-control" required placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="btn-auth">Cập nhật mật khẩu</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:14px; color:#64748b">
            <a href="index.php?controller=auth&action=login" style="color:#0ea5a5; font-weight:600">Quay lại đăng nhập</a>
        </div>
    </section>
</div>
