<?php
/** @var array $stats */
/** @var array $chartMonths */
/** @var array $statusStats */
/** @var string $range */

$stats       = $stats ?? [];
$chartMonths = $chartMonths ?? [];
$statusStats = $statusStats ?? [];
$range       = $range ?? 'last_12_months';

// Data cho chart (PHP → JS)
$labels = [];
$values = []; // sẽ dùng đơn vị "triệu" cho dễ nhìn

foreach ($chartMonths as $row) {
    $labels[] = $row['label'];                      // Jan, Feb, ...
    $values[] = round(((float)$row['total']) / 1_000_000, 2);
}

// Donut: trạng thái đơn hàng
$labelMap = [
    'pending'    => 'Chờ xử lý',
    'processing' => 'Đang xử lý',
    'shipped'    => 'Đang giao',
    'delivered'  => 'Đã giao',
    'cancelled'  => 'Đã hủy',
];

$donutLabels = [];
$donutValues = [];
foreach ($labelMap as $key => $label) {
    $donutLabels[] = $label;
    $donutValues[] = (int)($statusStats[$key] ?? 0);
}

// Helper text cho filter
$rangeText = [
    'all'            => 'Toàn bộ thời gian',
    'this_month'     => 'Tháng này',
    'this_year'      => 'Năm nay',
    'last_12_months' => '12 tháng gần nhất',
][$range] ?? '12 tháng gần nhất';
?>

<div class="admin-kpi-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
  <div>
    <h1 class="admin-page-title">Bảng điều khiển</h1>
    <p class="admin-page-subtitle">
      Tổng quan nhanh về sách, đơn hàng, người dùng
      <span style="color:#6b7280;">(<?= htmlspecialchars($rangeText) ?>)</span>
    </p>
  </div>
  <form method="get" style="display:flex;gap:8px;align-items:center;">
    <input type="hidden" name="c" value="dashboard">
    <input type="hidden" name="a" value="index">
    <select name="range" style="padding:6px 10px;border-radius:999px;border:1px solid #e5e7eb;font-size:13px;">
      <option value="last_12_months" <?= $range==='last_12_months'?'selected':''; ?>>12 tháng gần nhất</option>
      <option value="this_year"      <?= $range==='this_year'?'selected':''; ?>>Năm nay</option>
      <option value="this_month"     <?= $range==='this_month'?'selected':''; ?>>Tháng này</option>
      <option value="all"            <?= $range==='all'?'selected':''; ?>>Toàn bộ</option>
    </select>
    <button type="submit" style="padding:6px 12px;border-radius:999px;border:none;background:#3b82f6;color:#fff;font-size:13px;cursor:pointer;">
      Lọc
    </button>
  </form>
</div>

<div class="admin-kpi-grid">
  <div class="admin-card kpi kpi-pink">
    <div class="admin-kpi-label">Doanh thu (đơn đã giao)</div>
    <div class="admin-kpi-value">
      <?= number_format($stats['totalRevenue'] ?? 0, 0, ',', '.'); ?>đ
    </div>
    <div class="admin-kpi-trend <?= ($stats['growthRevenue'] ?? 0) >= 0 ? 'up' : 'down'; ?>">
      <?= ($stats['growthRevenue'] ?? 0) >= 0 ? 'Tăng' : 'Giảm'; ?>
      <?= abs($stats['growthRevenue'] ?? 0); ?>% so với tuần trước
    </div>
  </div>

  <div class="admin-card kpi kpi-blue">
    <div class="admin-kpi-label">Đơn hàng (theo filter)</div>
    <div class="admin-kpi-value"><?= (int)($stats['totalOrders'] ?? 0); ?></div>
    <div class="admin-kpi-trend <?= ($stats['growthOrders'] ?? 0) >= 0 ? 'up' : 'down'; ?>">
      Tuần này: <?= (int)($stats['ordersThisWeek'] ?? 0); ?> đơn
    </div>
  </div>

  <div class="admin-card kpi kpi-green">
    <div class="admin-kpi-label">Catalog & người dùng</div>
    <div class="admin-kpi-value">
      <?= (int)($stats['totalBooks'] ?? 0); ?> sách ·
      <?= (int)($stats['totalCategories'] ?? 0); ?> danh mục
    </div>
    <div class="admin-kpi-trend up">
      User: <?= (int)($stats['totalUsers'] ?? 0); ?> · Admin: <?= (int)($stats['totalAdmins'] ?? 0); ?>
    </div>
  </div>
</div>

<div class="admin-chart-grid">
  <div class="admin-card">
    <h3 style="margin-top:0;font-size:15px;">Doanh thu theo tháng (đơn vị: triệu VND)</h3>
    <canvas id="chartSales" height="140"></canvas>
  </div>

  <div class="admin-card">
    <h3 style="margin-top:0;font-size:15px;">Trạng thái đơn hàng</h3>
    <canvas id="chartStatus" height="140"></canvas>
  </div>
</div>

<script>
  // Bar chart doanh thu
  const salesLabels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
  const salesData   = <?= json_encode($values) ?>;

  const ctxSales = document.getElementById('chartSales');
  if (ctxSales) {
    new Chart(ctxSales, {
      type: 'bar',
      data: {
        labels: salesLabels,
        datasets: [{
          label: 'Doanh thu (triệu)',
          data: salesData,
        }]
      },
      options: {
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
      }
    });
  }

  // Donut chart trạng thái đơn hàng
  const statusLabels = <?= json_encode($donutLabels, JSON_UNESCAPED_UNICODE) ?>;
  const statusData   = <?= json_encode($donutValues) ?>;

  const ctxStatus = document.getElementById('chartStatus');
  if (ctxStatus) {
    new Chart(ctxStatus, {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusData,
        }]
      },
      options: {
        plugins:{legend:{position:'bottom'}}
      }
    });
  }
</script>
