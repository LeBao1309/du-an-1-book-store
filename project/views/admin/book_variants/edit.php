<?php
/** @var array $variant */
/** @var array $books */
/** @var string $csrf */
$bookTitle = '';
foreach ($books as $book) {
    if ($book['id'] == $variant['book_id']) {
        $bookTitle = $book['title'];
        break;
    }
}
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
    <h2 class="admin-section-title">Chỉnh sửa biến thể #<?= (int)$variant['id']; ?></h2>
    <p class="admin-section-subtitle">
      <?= htmlspecialchars($variant['format']); ?> • <?= htmlspecialchars($bookTitle); ?>
    </p>
  </div>
  <a href="index.php?c=book_variants&a=index" class="wd-btn-secondary">← Quay lại</a>
</div>

<div class="admin-card variant-form-card">
  <form method="post" action="index.php?c=book_variants&a=update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
    <input type="hidden" name="id" value="<?= (int)$variant['id']; ?>">

    <label class="wd-label">Sách</label>
    <select name="book_id" class="wd-input" required>
      <?php foreach ($books as $book): ?>
        <option value="<?= $book['id']; ?>" <?= $variant['book_id']==$book['id']?'selected':''; ?>>
          <?= htmlspecialchars($book['title']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label class="wd-label">Định dạng</label>
    <input type="text" name="format" class="wd-input" required value="<?= htmlspecialchars($variant['format']); ?>">

    <div class="field-row">
      <div>
        <label class="wd-label">Giá gốc (VND)</label>
        <input type="number" min="0" name="price" class="wd-input" required value="<?= (float)$variant['price']; ?>">
      </div>
      <div>
        <label class="wd-label">Giá khuyến mãi</label>
        <input type="number" min="0" name="sale_price" class="wd-input"
               value="<?= $variant['sale_price'] !== null ? (float)$variant['sale_price'] : ''; ?>">
      </div>
    </div>

    <label class="wd-label">Tồn kho</label>
    <input type="number" min="0" name="stock" class="wd-input" required value="<?= (int)$variant['stock']; ?>">

    <label class="wd-label" style="margin-top:10px;">
      <input type="checkbox" name="is_active" value="1" <?= (int)$variant['is_active']===1?'checked':''; ?>>
      Đang hoạt động
    </label>

    <div style="margin-top:20px; display:flex; gap:12px;">
      <button type="submit" class="wd-btn-primary">Lưu thay đổi</button>
      <a href="index.php?c=book_variants&a=index" class="wd-btn-secondary">Hủy</a>
    </div>
  </form>
</div>
