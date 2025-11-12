<section class="hero">
  <div class="container grid hero-inner">
    <div class="hero-text">
      <span class="chip">Special Offer</span>
      <h1>There is nothing<br/>better than to read</h1>
      <p>Tìm món quà hoàn hảo cho mọi người trong danh sách của bạn.</p>
      <div class="hero-actions">
        <a href="?c=home&a=index#category" class="btn btn-light">Mua ngay</a>
        <a href="?c=home&a=index#explore" class="btn btn-ghost">Khám phá</a>
      </div>
    </div>
    <div class="hero-art">
      <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200" alt="books"/>
    </div>
  </div>
</section>

<section class="container section" id="category">
  <div class="section-head">
    <h2>Danh mục nổi bật</h2>
    <div class="dots">
      <button class="dot prev" data-target="#catTrack" aria-label="Prev">‹</button>
      <button class="dot next" data-target="#catTrack" aria-label="Next">›</button>
    </div>
  </div>
  <div class="track" id="catTrack">
    <a class="pill" href="<?= $BASE ?? '' ?>/category/trinh-tham">Trinh thám</a>
    <a class="pill" href="<?= $BASE ?? '' ?>/category/self-help">Self-help</a>
    <a class="pill" href="<?= $BASE ?? '' ?>/category/kinh-doanh">Kinh doanh</a>
    <a class="pill" href="<?= $BASE ?? '' ?>/category/thieu-nhi">Thiếu nhi</a>
  </div>
</section>

<section class="container section">
  <div class="section-head">
    <h2>Đang thịnh hành</h2>
    <a class="see-all" href="<?= $BASE ?? '' ?>/category/trending">Xem tất cả</a>
  </div>
  <div class="grid cards-5" id="homeTrending"><p>Đang tải sách...</p></div>
</section>

<section class="container section">
  <div class="section-head">
    <h2>Bán chạy</h2>
    <a class="see-all" href="<?= $BASE ?? '' ?>/category/bestseller">Xem tất cả</a>
  </div>
  <div class="grid cards-6" id="homeBestseller"></div>
</section>

<section class="container features">
  <div class="feature"><span>🚚</span> Free Shipping</div>
  <div class="feature"><span>🛡️</span> Money Guarantee</div>
  <div class="feature"><span>💬</span> Online Support</div>
  <div class="feature"><span>💳</span> Flexible Payment</div>
</section>
