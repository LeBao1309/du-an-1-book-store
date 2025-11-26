<div class="auth-wrapper">
    <section class="auth-card">
        <h1>Đăng ký thành viên</h1>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="index.php?controller=auth&action=register" method="POST">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
                <div id="emailError" class="js-error"></div>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Tạo mật khẩu...">
                <small class="password-note">
                    ⚠️ Mật khẩu cần: Ít nhất 6 ký tự, bao gồm chữ <b>Hoa</b>, chữ <b>thường</b> và <b>ký tự đặc biệt</b> (!@#...).
                </small>
                <div id="passError" class="js-error"></div>
            </div>

            <div class="form-group">
                <label>Nhập lại mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Xác nhận mật khẩu...">
                <div id="confirmError" class="js-error"></div>
            </div>

            <button type="submit" class="btn-auth">Tạo tài khoản</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:14px; color:#64748b">
            Đã có tài khoản? <a href="index.php?controller=auth&action=login" style="color:#0ea5a5; font-weight:600">Đăng nhập ngay</a>
        </div>
    </section>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        // Lấy giá trị
        const email = document.getElementById('email').value.trim();
        const pass = document.getElementById('password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        
        // Các thẻ báo lỗi
        const emailError = document.getElementById('emailError');
        const passError = document.getElementById('passError');
        const confirmError = document.getElementById('confirmError');
        
        // Reset lỗi (ẩn đi trước khi kiểm tra)
        emailError.style.display = 'none';
        passError.style.display = 'none';
        confirmError.style.display = 'none';

        let hasError = false;

        // 1. KIỂM TRA EMAIL (Định dạng chung: chuoi@chuoi.chuoi)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            emailError.innerText = 'Email không hợp lệ (Ví dụ: abc@gmail.com)';
            emailError.style.display = 'block';
            hasError = true;
        }

        // 2. KIỂM TRA MẬT KHẨU (Hoa + Thường + Đặc biệt + Dài >=6)
        const strongPassRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{6,}$/;
        if (!strongPassRegex.test(pass)) {
            passError.innerText = 'Mật khẩu phải có chữ Hoa, thường & ký tự đặc biệt (!@#)';
            passError.style.display = 'block';
            hasError = true;
        }

        // 3. KIỂM TRA KHỚP MẬT KHẨU
        if (pass !== confirmPass) {
            confirmError.innerText = 'Mật khẩu nhập lại không khớp!';
            confirmError.style.display = 'block';
            hasError = true;
        }

        if (hasError) {
            e.preventDefault(); // Chặn form nếu có lỗi
        }
    });
</script>