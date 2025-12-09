<?php
/** @var array $book */
/** @var array $images */
/** @var string $csrf */
?>
<style>
  .gallery-page { margin-top:8px; }
  .gallery-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
  .gallery-grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:12px; }
  .gallery-card { border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; background:#fff; }
  .gallery-card img { width:100%; height:150px; object-fit:cover; display:block; }
  .gallery-card .meta { padding:10px; font-size:13px; display:flex; justify-content:space-between; align-items:center; }
  .gallery-card form { margin:0; }
</style>

<div class="gallery-page">
  <div class="gallery-header">
    <div>
      <h2 class="admin-section-title" style="margin:0;">Ảnh sách: <?= htmlspecialchars($book['title']); ?></h2>
      <p class="admin-section-subtitle" style="margin:0;">Quản lý gallery ảnh cho sách.</p>
    </div>
    <a href="index.php?c=products&a=index" class="wd-btn-secondary">← Quay lại danh sách sách</a>
  </div>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="wd-alert wd-alert-error">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="wd-alert wd-alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>

  <div class="admin-card" style="margin-bottom:14px;">
    <h3 style="margin-top:0;font-size:15px;">Thêm ảnh mới</h3>
    <form method="post" action="index.php?c=products&a=addImage">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
      <input type="hidden" name="book_id" value="<?= (int)$book['id']; ?>">

      <label class="wd-label">URL ảnh (mỗi dòng một URL, hỗ trợ nhiều ảnh)</label>
      <textarea name="image_urls" class="wd-input" rows="3" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"></textarea>

      <label class="wd-label" style="margin-top:8px;">Thứ tự hiển thị bắt đầu từ</label>
      <input type="number" name="sort_order" class="wd-input" value="0" min="0" style="max-width:160px;">

      <div style="margin-top:10px;">
        <button type="submit" class="wd-btn-primary">Thêm ảnh</button>
      </div>
    </form>
  </div>

  <div class="admin-card">
    <h3 style="margin-top:0;font-size:15px;">Danh sách ảnh (<?= count($images); ?>)</h3>
    <?php if (empty($images)): ?>
      <p style="color:#6b7280;">Chưa có ảnh cho sách này.</p>
    <?php else: ?>
      <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
          <div class="gallery-card">
            <img src="<?= htmlspecialchars($img['image_url']); ?>" alt="">
            <div class="meta">
              <span>#<?= (int)$img['sort_order']; ?></span>
              <form method="post" action="index.php?c=products&a=deleteImage" onsubmit="return confirm('Xóa ảnh này?');">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
                <input type="hidden" name="id" value="<?= (int)$img['id']; ?>">
                <input type="hidden" name="book_id" value="<?= (int)$book['id']; ?>">
                <button type="submit" class="wd-icon-btn danger" title="Xóa ảnh">🗑</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
