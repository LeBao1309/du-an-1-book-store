<style>
/* Màu chính của web */
:root {
    --primary-color: #0fbfbf;
    --primary-hover: #0aa5a5;
    --primary-light: #e0f9f9;
    --primary-dark: #088a8a;
}

.about-page {
    padding: 40px 0;
    background: linear-gradient(to bottom, #ffffff 0%, var(--primary-light) 100%);
    min-height: 80vh;
}

.about-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 30px 0;
}

.about-header h1 {
    font-size: 42px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.about-header h1:after {
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

.about-header p {
    font-size: 18px;
    color: #666;
    margin-top: 25px;
    line-height: 1.8;
}

.about-section {
    background: white;
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(15, 191, 191, 0.1);
    border: 2px solid var(--primary-light);
    transition: all 0.3s;
}

.about-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(15, 191, 191, 0.2);
}

.about-section h2 {
    font-size: 28px;
    color: var(--primary-color);
    margin-bottom: 20px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
}

.about-section h2:before {
    content: '📚';
    font-size: 32px;
}

.about-section p {
    font-size: 16px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 15px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.feature-card {
    background: var(--primary-light);
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.feature-card:hover {
    background: white;
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(15, 191, 191, 0.2);
}

.feature-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.feature-card h3 {
    font-size: 20px;
    color: var(--primary-dark);
    margin-bottom: 10px;
    font-weight: bold;
}

.feature-card p {
    font-size: 15px;
    color: #666;
    line-height: 1.6;
}

.stats-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    padding: 50px 40px;
    border-radius: 15px;
    margin: 40px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-item h3 {
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-item p {
    font-size: 18px;
    opacity: 0.9;
}

.team-section {
    margin-top: 40px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.team-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    border: 2px solid var(--primary-light);
    transition: all 0.3s;
}

.team-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(15, 191, 191, 0.2);
}

.team-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    border-radius: 50%;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
}

.team-card h4 {
    font-size: 20px;
    color: var(--primary-dark);
    margin-bottom: 5px;
    font-weight: bold;
}

.team-card .role {
    color: var(--primary-color);
    font-size: 14px;
    margin-bottom: 10px;
}

.team-card p {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
}

.cta-section {
    background: var(--primary-light);
    padding: 50px;
    border-radius: 15px;
    text-align: center;
    margin-top: 40px;
    border: 3px solid var(--primary-color);
}

.cta-section h2 {
    font-size: 32px;
    color: var(--primary-dark);
    margin-bottom: 20px;
}

.cta-section p {
    font-size: 18px;
    color: #666;
    margin-bottom: 30px;
}

.btn-cta {
    display: inline-block;
    padding: 15px 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-size: 18px;
    font-weight: bold;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(15, 191, 191, 0.3);
}

.btn-cta:hover {
    background: linear-gradient(135deg, var(--primary-hover), var(--primary-dark));
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(15, 191, 191, 0.4);
    color: white;
}

@media (max-width: 768px) {
    .about-header h1 {
        font-size: 32px;
    }
    
    .about-section {
        padding: 25px;
    }
    
    .stats-grid,
    .team-grid,
    .feature-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="about-page">
    <div class="container">
        <!-- Header -->
        <div class="about-header">
            <h1>📖 Về Chúng Tôi</h1>
            <p>Nơi kết nối bạn với tri thức - Hành trình khám phá thế giới sách</p>
        </div>

        <!-- Giới thiệu chung -->
        <div class="about-section">
            <h2>Câu chuyện của chúng tôi</h2>
            <p>
                <strong>Book Store</strong> được thành lập với niềm đam mê chia sẻ tri thức và tình yêu sách. 
                Chúng tôi tin rằng mỗi cuốn sách là một hành trình, mỗi trang giấy là một câu chuyện đang chờ được khám phá.
            </p>
            <p>
                Từ những ngày đầu khiêm tốn, chúng tôi đã phát triển thành một trong những nhà sách trực tuyến 
                uy tín nhất, với hàng ngàn đầu sách đa dạng từ văn học, khoa học, kinh tế đến kỹ năng sống.
            </p>
            <p>
                Sứ mệnh của chúng tôi không chỉ là bán sách, mà là tạo ra một cộng đồng yêu sách, 
                nơi mọi người có thể tìm thấy nguồn cảm hứng, kiến thức và niềm vui trong từng trang sách.
            </p>
        </div>

        <!-- Thống kê -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>10,000+</h3>
                    <p>Đầu sách</p>
                </div>
                <div class="stat-item">
                    <h3>50,000+</h3>
                    <p>Khách hàng</p>
                </div>
                <div class="stat-item">
                    <h3>99%</h3>
                    <p>Hài lòng</p>
                </div>
                <div class="stat-item">
                    <h3>24/7</h3>
                    <p>Hỗ trợ</p>
                </div>
            </div>
        </div>

        <!-- Tính năng nổi bật -->
        <div class="about-section">
            <h2>Tại sao chọn chúng tôi?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>Giao hàng nhanh</h3>
                    <p>Giao hàng toàn quốc, cam kết đúng hẹn trong 2-5 ngày</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💯</div>
                    <h3>Chính hãng 100%</h3>
                    <p>Tất cả sách đều chính hãng, nguồn gốc rõ ràng</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Giá tốt nhất</h3>
                    <p>Cam kết giá cạnh tranh, nhiều ưu đãi hấp dẫn</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎁</div>
                    <h3>Quà tặng miễn phí</h3>
                    <p>Bookmark độc quyền và voucher cho đơn hàng tiếp theo</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3>Đổi trả dễ dàng</h3>
                    <p>Chính sách đổi trả linh hoạt trong 7 ngày</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Tư vấn nhiệt tình</h3>
                    <p>Đội ngũ tư vấn chuyên nghiệp, am hiểu sách</p>
                </div>
            </div>
        </div>

        <!-- Đội ngũ -->
        <div class="about-section team-section">
            <h2>Đội ngũ của chúng tôi</h2>
            <p>Những người đam mê sách, tận tâm mang đến trải nghiệm tốt nhất cho bạn</p>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">👨‍🎓</div>
                    <h4>Lê Quang Gia Bảo</h4>
                    <p class="role">Sinh viên</p>
                    <p>Chuyên ngành lập trình web</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🎓</div>
                    <h4>Lê Thành Vinh</h4>
                    <p class="role">Sinh viên</p>
                    <p>Chuyên ngành lập trình web</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🎓</div>
                    <h4>Huỳnh Văn Tài</h4>
                    <p class="role">Sinh viên</p>
                    <p>Chuyên ngành lập trình web</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🎓</div>
                    <h4>Võ Trường Toản</h4>
                    <p class="role">Sinh viên</p>
                    <p>Chuyên ngành lập trình web</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-section">
            <h2>🎯 Bắt đầu hành trình khám phá</h2>
            <p>Hàng ngàn đầu sách hay đang chờ bạn. Đừng bỏ lỡ!</p>
            <a href="index.php?controller=home&action=index" class="btn-cta">
                Khám phá ngay →
            </a>
        </div>
    </div>
</div>
