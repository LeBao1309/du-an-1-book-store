<?php
/** @var array $books */
/** @var string $csrf */
?>
<style>
  .variant-form-card {
    max-width:720px;
    margin:auto;
    border-radius:22px;
    box-shadow:0 25px 70px rgba(15,23,42,0.12);
    padding:24px;
  }
  .variant-form-card .field-row {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:16px;
  }
</style>

<div class="admin-section-header">
  <div>
    <h2 class="admin-section-title">Thêm biến thể</h2>
    <p class="admin-section-subtitle">Tạo định dạng mới cho sách, đồng bộ với UI danh mục.</p>
  </div>
  <a href="index.php?c=book_variants&a=index" class="wd-btn-secondary">← Quay lại</a>
</div>

<div class="admin-card variant-form-card">
  <form method="post" action="index.php?c=book_variants&a=store">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

    <label class="wd-label">Sách</label>
    <select name="book_id" class="wd-input" required>
      <option value="">-- Chọn sách --</option>
      <?php foreach ($books as $book): ?>
        <option value="<?= $book['id']; ?>"><?= htmlspecialchars($book['title']); ?></option>
      <?php endforeach; ?>
    </select>

    <label class="wd-label">Định dạng</label>
    <input type="text" name="format" class="wd-input" placeholder="Bìa cứng / Ebook..." required>

    <div class="field-row">
      <div>
        <label class="wd-label">Giá gốc (VND)</label>
        <input type="number" min="0" name="price" class="wd-input" required>
      </div>
      <div>
        <label class="wd-label">Giá khuyến mãi</label>
        <input type="number" min="0" name="sale_price" class="wd-input" placeholder="Tùy chọn">
      </div>
    </div>

    <label class="wd-label">Tồn kho</label>
    <input type="number" min="0" name="stock" class="wd-input" value="0" required>

    <label class="wd-label" style="margin-top:10px;">
      <input type="checkbox" name="is_active" value="1" checked>
      Đang hoạt động
    </label>

    <div style="margin-top:20px; display:flex; gap:12px;">
      <button type="submit" class="wd-btn-primary">Lưu biến thể</button>
      <a href="index.php?c=book_variants&a=index" class="wd-btn-secondary">Hủy</a>
    </div>
  </form>
</div>
