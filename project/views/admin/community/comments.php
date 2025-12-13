<?php
/** @var array $pagination */
/** @var array $filters */
/** @var string $csrf */

$items    = $pagination['items'] ?? [];
$page     = $pagination['page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$total    = $pagination['total'] ?? 0;
?>
<style>
  .community-page .badge-rating {
    display:inline-flex;
    align-items:center;
    gap:3px;
    background:#fef3c7;
    color:#92400e;
    padding:3px 8px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
  }
  .community-page .comment-content {
    max-width:360px;
    color:#374151;
  }
</style>

<div class="community-page">
  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Bình luận & Đánh giá</h2>
      <p class="admin-section-subtitle">Giao diện đồng bộ với danh mục, dễ lọc và kiểm duyệt.</p>
    </div>
  </div>

  <form class="admin-filter-bar" method="get" action="index.php">
    <input type="hidden" name="c" value="comments">
    <input type="hidden" name="a" value="index">

    <div class="filter-group">
      <label>Từ khóa</label>
      <input type="text" name="keyword" class="wd-input" placeholder="Nội dung, sách, người dùng..."
             value="<?= htmlspecialchars($filters['keyword']); ?>">
    </div>

    <div class="filter-group">
      <label>Điểm</label>
      <select name="rating" class="wd-input">
        <option value="">Tất cả</option>
        <?php for ($r = 1; $r <= 5; $r++): ?>
          <option value="<?= $r; ?>" <?= ($filters['rating'] === (string)$r) ? 'selected' : ''; ?>><?= $r; ?> ⭐</option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="wd-btn-secondary">Lọc</button>
    </div>
  </form>

  <div class="admin-card">
    <div class="admin-card-header">
      <span>Danh sách bình luận (<?= (int)$total; ?>)</span>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Sách</th>
        <th>Người dùng</th>
        <th>Điểm</th>
        <th>Nội dung</th>
        <th>Thời gian</th>
        <th style="width:110px;">Thao tác</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $c): ?>
        <tr>
          <td><?= (int)$c['id']; ?></td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($c['book_title']); ?></div>
            <div style="font-size:12px;color:#94a3b8;">#<?= (int)$c['book_id']; ?></div>
          </td>
          <td><?= htmlspecialchars($c['user_name']); ?></td>
          <td>
            <?= $c['rating'] ? '<span class="badge-rating">' . (int)$c['rating'] . ' ⭐</span>' : '—'; ?>
          </td>
          <td class="comment-content"><?= nl2br(htmlspecialchars($c['content'])); ?></td>
          <td><?= htmlspecialchars($c['created_at']); ?></td>
          <td>
            <form method="post" action="index.php?c=comments&a=delete" onsubmit="return confirm('Xóa bình luận này?');">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
              <input type="hidden" name="id" value="<?= (int)$c['id']; ?>">
              <button type="submit" class="wd-icon-btn danger" title="Xóa bình luận">
                🗑
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <tr><td colspan="7" style="text-align:center;">Chưa có bình luận.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

    <?php if ($lastPage > 1): ?>
      <div class="admin-pagination">
        <?php for ($p = 1; $p <= $lastPage; $p++): ?>
          <a class="page-link <?= $p === (int)$page ? 'active' : ''; ?>"
             href="index.php?c=comments&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&rating=<?= urlencode($filters['rating']); ?>">
            <?= $p; ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
