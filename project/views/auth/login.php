<div class="auth-wrapper">
    <section class="auth-card">
        <h1>Đăng nhập</h1>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div style="background:#dcfce7; color:#166534; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=login" method="POST">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Nhập email của bạn...">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Nhập mật khẩu...">
            </div>

            <button type="submit" class="btn-auth">Đăng nhập</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:14px; color:#64748b">
            <div style="margin-bottom:8px;">
                <a href="index.php?controller=auth&action=forgot" style="color:#0ea5a5; font-weight:600">Quên mật khẩu?</a>
            </div>
            Chưa có tài khoản? <a href="index.php?controller=auth&action=register" style="color:#0ea5a5; font-weight:600">Đăng ký ngay</a>
        </div>
    </section>
</div>