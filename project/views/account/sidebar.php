<?php
$active = $active ?? 'profile';
?>
<aside class="acc-sidebar card shadow-sm border-0">
  <div class="acc-sidebar__head">
    <h5 class="mb-1 fw-bold text-white">Tài khoản của bạn</h5>
    <div class="text-white-50 small">Chào mừng trở lại!</div>
  </div>

  <div class="list-group list-group-flush acc-menu">
    <a class="list-group-item acc-item <?= $active==='profile'?'active':'' ?>"
       href="?controller=account&action=profile">
      👤 Thông tin cá nhân
    </a>
    <a class="list-group-item acc-item <?= $active==='password'?'active':'' ?>"
       href="?controller=account&action=password">
      🔒 Đổi mật khẩu
    </a>
    <a class="list-group-item acc-item <?= $active==='orders'?'active':'' ?>"
       href="?controller=account&action=orders">
      📦 Lịch sử đơn hàng
    </a>
    <a class="list-group-item acc-item <?= $active==='address'?'active':'' ?>"
       href="?controller=account&action=address">
      📍 Địa chỉ giao hàng
    </a>
    <a class="list-group-item acc-item <?= $active==='wishlist'?'active':'' ?>"
       href="?controller=account&action=wishlist">
      ❤️ Yêu thích
    </a>
  </div>

  <div class="p-3">
    <a class="btn btn-outline-danger w-100" href="?controller=auth&action=logout">
      Đăng xuất
    </a>
  </div>
</aside>
