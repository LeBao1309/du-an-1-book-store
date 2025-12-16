<?php
/** @var array $question */
/** @var array $answers */
/** @var string $csrf */
?>
<style>
  .qa-detail {
    margin-top: 4px;
  }
  .qa-detail .admin-card {
    border-radius: 20px;
    box-shadow: 0 18px 48px rgba(15,23,42,0.08);
  }
  .qa-detail .badge {
    padding:3px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
  }
  .qa-detail .badge-warning { background:#fef3c7; color:#92400e; }
  .qa-detail .badge-success { background:#d1fae5; color:#065f46; }
  .wd-icon-btn {
    border: none;
    background: #eef2ff;
    color: #4f46e5;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    margin-right: 4px;
    transition: background 0.15s ease, transform 0.1s ease;
  }
  .wd-icon-btn:hover {
    background: #e0e7ff;
    transform: translateY(-1px);
  }
  .wd-icon-btn.danger {
    background: #fee2e2;
    color: #b91c1c;
  }
  .wd-icon-btn.danger:hover {
    background: #fecaca;
  }
</style>

<div class="qa-detail">
  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Chi tiết Q&A #<?= (int)$question['id']; ?></h2>
      <p class="admin-section-subtitle">
        Sách: <?= htmlspecialchars($question['book_title']); ?> • Khách: <?= htmlspecialchars($question['user_name']); ?>
      </p>
    </div>
    <a href="index.php?c=qa&a=index" class="wd-btn-secondary">← Quay lại</a>
  </div>

  <div class="admin-grid-2">
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Câu hỏi</span>
      </div>
      <div class="admin-card-body">
        <p><strong>Nội dung:</strong></p>
        <div style="padding:12px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;">
          <?= nl2br(htmlspecialchars($question['question'])); ?>
        </div>
        <p style="margin-top:10px;color:#6b7280;">Tạo lúc: <?= htmlspecialchars($question['created_at']); ?></p>
        <?= (int)$question['is_answered'] === 1
            ? '<span class="badge badge-success">Đã trả lời</span>'
            : '<span class="badge badge-warning">Chưa trả lời</span>'; ?>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card-header">
        <span>Trả lời từ shop</span>
      </div>
      <div class="admin-card-body">
        <form method="post" action="index.php?c=qa&a=answer">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
          <input type="hidden" name="question_id" value="<?= (int)$question['id']; ?>">
          <label class="wd-label">Nội dung trả lời</label>
          <textarea name="content" class="wd-input" rows="4" required></textarea>
          <div style="margin-top:10px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="submit" class="wd-btn-primary">Gửi trả lời</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="admin-card" style="margin-top:16px;">
    <div class="admin-card-header">
      <span>Danh sách trả lời (<?= count($answers); ?>)</span>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Người trả lời</th>
        <th>Vai trò</th>
        <th>Nội dung</th>
        <th>Thời gian</th>
        <th style="width:110px;">Thao tác</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($answers as $a): ?>
        <tr>
          <td><?= (int)$a['id']; ?></td>
          <td><?= htmlspecialchars($a['user_name']); ?></td>
          <td><?= htmlspecialchars($a['role']); ?><?= ((int)$a['is_shop_answer'] === 1) ? ' (Shop)' : ''; ?></td>
          <td><?= nl2br(htmlspecialchars($a['answer'])); ?></td>
          <td><?= htmlspecialchars($a['created_at']); ?></td>
          <td>
            <form method="post" action="index.php?c=qa&a=deleteAnswer"
                  onsubmit="return confirm('Xóa câu trả lời này?');" style="display:inline;">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
              <input type="hidden" name="id" value="<?= (int)$a['id']; ?>">
              <input type="hidden" name="question_id" value="<?= (int)$question['id']; ?>">
              <button type="submit" class="wd-icon-btn danger" title="Xóa trả lời">🗑</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
        <?php if (empty($answers)): ?>
          <tr><td colspan="6" style="text-align:center;">Chưa có trả lời.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
