<style>
/* Màu chính của web */
:root {
    --primary-color: #0fbfbf;
    --primary-hover: #0aa5a5;
    --primary-light: #e0f9f9;
    --primary-dark: #088a8a;
}

.contact-page {
    padding: 40px 0;
    background: linear-gradient(to bottom, #ffffff 0%, var(--primary-light) 100%);
    min-height: 80vh;
}

.contact-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 30px 0;
}

.contact-header h1 {
    font-size: 42px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.contact-header h1:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
    border-radius: 2px;
}

.contact-header p {
    font-size: 18px;
    color: #666;
    margin-top: 25px;
    line-height: 1.8;
}

.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.contact-info {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(15, 191, 191, 0.1);
    border: 2px solid var(--primary-light);
}

.contact-info h2 {
    font-size: 28px;
    color: var(--primary-color);
    margin-bottom: 25px;
    font-weight: bold;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 25px;
    padding: 20px;
    background: var(--primary-light);
    border-radius: 10px;
    transition: all 0.3s;
}

.info-item:hover {
    background: white;
    border: 2px solid var(--primary-color);
    transform: translateX(5px);
}

.info-icon {
    font-size: 32px;
    min-width: 40px;
}

.info-content h3 {
    font-size: 18px;
    color: var(--primary-dark);
    margin-bottom: 5px;
    font-weight: bold;
}

.info-content p {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.contact-form {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(15, 191, 191, 0.1);
    border: 2px solid var(--primary-light);
}

.contact-form h2 {
    font-size: 28px;
    color: var(--primary-color);
    margin-bottom: 25px;
    font-weight: bold;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-group label span {
    color: red;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(15, 191, 191, 0.1);
}

.form-control.error {
    border-color: #dc3545;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.3);
}

.btn-submit:hover {
    background: linear-gradient(135deg, var(--primary-hover), var(--primary-dark));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 191, 191, 0.4);
}

.btn-submit:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 15px;
    display: none;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert.show {
    display: block;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.map-section {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(15, 191, 191, 0.1);
    border: 2px solid var(--primary-light);
    margin-top: 40px;
}

.map-section h2 {
    font-size: 28px;
    color: var(--primary-color);
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}

.map-container {
    width: 100%;
    height: 400px;
    border-radius: 10px;
    overflow: hidden;
    border: 3px solid var(--primary-light);
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

@media (max-width: 768px) {
    .contact-header h1 {
        font-size: 32px;
    }
    
    .contact-container {
        grid-template-columns: 1fr;
    }
    
    .contact-info,
    .contact-form,
    .map-section {
        padding: 25px;
    }
}
</style>

<div class="contact-page">
    <div class="container">
        <!-- Header -->
        <div class="contact-header">
            <h1>📞 Liên Hệ Với Chúng Tôi</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>

        <!-- Contact Container -->
        <div class="contact-container">
            <!-- Thông tin liên hệ -->
            <div class="contact-info">
                <h2>Thông tin liên hệ</h2>
                
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <h3>Địa chỉ</h3>
                        <p>Công viên Phần mềm Quang Trung<br>Quận 12, TP. Hồ Chí Minh</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <h3>Số điện thoại</h3>
                        <p>Hotline: 1900 1234<br>Di động: 0366 842 947</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p>toanvotruong276@gmail.com<br>support@bookstore.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🕐</div>
                    <div class="info-content">
                        <h3>Giờ làm việc</h3>
                        <p>Thứ 2 - Thứ 7: 8:00 - 20:00<br>Chủ nhật: 9:00 - 18:00</p>
                    </div>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="contact-form">
                <h2>Gửi tin nhắn cho chúng tôi</h2>
                
                <div id="alertMessage" class="alert"></div>

                <form id="contactForm">
                    <div class="form-group">
                        <label for="name">Họ và tên <span>*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Nhập họ tên của bạn" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span>*</span></label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="example@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="0901234567">
                    </div>

                    <div class="form-group">
                        <label for="subject">Tiêu đề <span>*</span></label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Tiêu đề tin nhắn" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Nội dung <span>*</span></label>
                        <textarea id="message" name="message" class="form-control" placeholder="Nhập nội dung tin nhắn của bạn..." required></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="btn-submit">
                        📨 Gửi tin nhắn
                    </button>
                </form>
            </div>
        </div>

        <!-- Bản đồ -->
        <div class="map-section">
            <h2>🗺️ Vị trí của chúng tôi</h2>
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.4545437049034!2d106.62535187570655!3d10.850622889300393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752a20d8555e69%3A0x743b1e9558fb89e0!2zQ8O0bmcgdmnDqm4gUGjhuqduIG3hu4FtIFF1YW5nIFRydW5n!5e0!3m2!1svi!2s!4v1733058000000!5m2!1svi!2s" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý form gửi liên hệ
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const alertMessage = document.getElementById('alertMessage');
    
    // Disable nút submit
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Đang gửi...';
    
    // Lấy dữ liệu form
    const formData = new FormData(this);
    
    // Gửi AJAX request
    fetch('index.php?controller=contact&action=send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Hiển thị thông báo
        alertMessage.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger') + ' show';
        alertMessage.textContent = data.message;
        
        // Reset form nếu thành công
        if (data.success) {
            document.getElementById('contactForm').reset();
        }
        
        // Ẩn thông báo sau 5 giây
        setTimeout(() => {
            alertMessage.classList.remove('show');
        }, 5000);
    })
    .catch(error => {
        console.error('Error:', error);
        alertMessage.className = 'alert alert-danger show';
        alertMessage.textContent = 'Có lỗi xảy ra. Vui lòng thử lại sau!';
    })
    .finally(() => {
        // Enable lại nút submit
        submitBtn.disabled = false;
        submitBtn.textContent = '📨 Gửi tin nhắn';
    });
});

// Validate email realtime
document.getElementById('email').addEventListener('blur', function() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (this.value && !emailRegex.test(this.value)) {
        this.classList.add('error');
    } else {
        this.classList.remove('error');
    }
});

// Validate số điện thoại realtime
document.getElementById('phone').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }
});
</script>
