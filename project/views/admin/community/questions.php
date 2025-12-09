<?php
/** @var array $pagination */
/** @var array $filters */
/** @var string $csrf */

$items    = $pagination['items'] ?? [];
$page     = $pagination['page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$total    = $pagination['total'] ?? 0;
?>
<div class="admin-section-header">
  <div>
    <h2 class="admin-section-title">Hỏi đáp sản phẩm</h2>
    <p class="admin-section-subtitle">Quản lý câu hỏi của khách và phản hồi từ shop.</p>
  </div>
</div>

<form class="admin-filter-bar" method="get" action="index.php">
  <input type="hidden" name="c" value="qa">
  <input type="hidden" name="a" value="index">

  <div class="filter-group">
    <label>Từ khóa</label>
    <input type="text" name="keyword" class="wd-input" placeholder="Nội dung, sách, người dùng..."
           value="<?= htmlspecialchars($filters['keyword']); ?>">
  </div>

  <div class="filter-group">
    <label>Trạng thái</label>
    <select name="answered" class="wd-input">
      <option value="">Tất cả</option>
      <option value="0" <?= $filters['answered']==='0' ? 'selected' : ''; ?>>Chưa trả lời</option>
      <option value="1" <?= $filters['answered']==='1' ? 'selected' : ''; ?>>Đã trả lời</option>
    </select>
  </div>

  <div class="filter-actions">
    <button type="submit" class="wd-btn-secondary">Lọc</button>
  </div>
</form>

<div class="admin-card">
  <div class="admin-card-header">
    <span>Danh sách câu hỏi (<?= (int)$total; ?>)</span>
  </div>
  <div class="admin-table-wrapper">
    <table class="admin-table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Sách</th>
        <th>Khách hỏi</th>
        <th>Nội dung</th>
        <th>Trạng thái</th>
        <th>Trả lời</th>
        <th>Thời gian</th>
        <th style="width:130px;">Thao tác</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $q): ?>
        <tr>
          <td><?= (int)$q['id']; ?></td>
          <td><?= htmlspecialchars($q['book_title']); ?></td>
          <td><?= htmlspecialchars($q['user_name']); ?></td>
          <td><?= nl2br(htmlspecialchars($q['question'])); ?></td>
          <td>
            <?php if ((int)$q['is_answered'] === 1): ?>
              <span class="badge badge-success">Đã trả lời</span>
            <?php else: ?>
              <span class="badge badge-warning">Chưa trả lời</span>
            <?php endif; ?>
          </td>
          <td><?= (int)$q['answer_count']; ?></td>
          <td><?= htmlspecialchars($q['created_at']); ?></td>
          <td>
            <a class="wd-btn-secondary" style="padding:6px 10px;"
               href="index.php?c=qa&a=detail&id=<?= (int)$q['id']; ?>">Xem</a>
            <form method="post" action="index.php?c=qa&a=deleteQuestion"
                  onsubmit="return confirm('Xóa câu hỏi này?');" style="display:inline;">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
              <input type="hidden" name="id" value="<?= (int)$q['id']; ?>">
              <button type="submit" class="wd-btn-danger" style="padding:6px 10px;">Xóa</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <tr><td colspan="8" style="text-align:center;">Chưa có câu hỏi.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($lastPage > 1): ?>
    <div class="admin-pagination">
      <?php for ($p = 1; $p <= $lastPage; $p++): ?>
        <a class="page-link <?= $p === (int)$page ? 'active' : ''; ?>"
           href="index.php?c=qa&a=index&page=<?= $p; ?>&keyword=<?= urlencode($filters['keyword']); ?>&answered=<?= urlencode($filters['answered']); ?>">
          <?= $p; ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
