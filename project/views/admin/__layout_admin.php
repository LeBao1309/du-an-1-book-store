<?php
/** @var string $content */
/** @var array|null $currentAdmin */
$ASSET = $ASSET ?? 'public';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>WiseDecision Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="../<?= $ASSET; ?>/css/styles.css">
  <link rel="stylesheet" href="../<?= $ASSET; ?>/css/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</head>
<body class="admin-body">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-brand">
        <span class="admin-logo">WD</span>
        <div class="admin-brand-text">
          <strong>WiseDecision</strong>
          <small>Bookstore Admin</small>
        </div>
      </div>

    <nav class="admin-menu">
      <a href="index.php?c=dashboard&a=index" class="menu-item">
        <span class="icon">📊</span> <span>Dashboard</span>
      </a>

      <a href="#" class="menu-section">QUẢN LÝ CATALOG</a>
      <a href="index.php?c=catalog&a=index" class="menu-item">
        <span class="icon">📂</span> <span>DANH MỤC</span>
      </a>
      <a href="index.php?c=products&a=index" class="menu-item">
        <span class="icon">📘</span> <span>Sách</span>
      </a>
      <a href="index.php?c=authors&a=index" class="menu-item">
        <span class="icon">✍️</span> <span>Tác giả</span>
      </a>
      <a href="index.php?c=publishers&a=index" class="menu-item">
        <span class="icon">🏢</span> <span>Nhà xuất bản</span>
      </a>

      <a href="#" class="menu-section">BÁN HÀNG</a>
      <a href="index.php?c=orders&a=index" class="menu-item">
        <span class="icon">🧾</span> <span>Đơn hàng</span>
      </a>
      <a href="index.php?c=coupons&a=index" class="menu-item">
        <span class="icon">🎟️</span> <span>Mã giảm giá</span>
      </a>

      <a href="#" class="menu-section">NGƯỜI DÙNG</a>
      <a href="index.php?c=users&a=index" class="menu-item">
        <span class="icon">👥</span> <span>Tài khoản</span>
      </a>
    </nav>


      <?php if (!empty($currentAdmin)): ?>
        <div class="admin-user">
          <div class="avatar-circle"><?= strtoupper($currentAdmin['name'][0] ?? 'A'); ?></div>
          <div class="info">
            <div class="name"><?= htmlspecialchars($currentAdmin['name']); ?></div>
            <div class="role">Admin</div>
          </div>
          <a class="logout" href="index.php?c=auth&a=logout" title="Đăng xuất">⏻</a>
        </div>
      <?php endif; ?>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <?php if (!empty($currentAdmin)): ?>
          <div class="admin-top-meta">
            <span>Xin chào, <?= htmlspecialchars($currentAdmin['name']); ?></span>
          </div>
        <?php endif; ?>
      </header>

      <div class="admin-content">
        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="wd-alert wd-alert-error">
            <?= htmlspecialchars($_SESSION['flash_error']); ?>
          </div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="wd-alert wd-alert-success">
            <?= htmlspecialchars($_SESSION['flash_success']); ?>
          </div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?= $content ?>
      </div>
    </main>
  </div>
</body>
</html>
