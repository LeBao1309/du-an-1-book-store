<div class="auth-wrapper">
    <section class="auth-card">
        <h1>Quên mật khẩu</h1>
        <p style="color:#64748b;margin-top:4px;">Nhập email để nhận liên kết đặt lại mật khẩu.</p>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div style="background:#dcfce7; color:#166534; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=forgot" method="POST">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Nhập email đã đăng ký">
            </div>

            <button type="submit" class="btn-auth">Gửi liên kết đặt lại</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:14px; color:#64748b">
            Nhớ mật khẩu? <a href="index.php?controller=auth&action=login" style="color:#0ea5a5; font-weight:600">Đăng nhập</a>
        </div>
    </section>
</div>
