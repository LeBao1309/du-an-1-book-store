<?php $active = 'orders'; ?>
<section class="container my-4">
    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
            <div class="card shadow-sm border-0 acc-content">
                <div class="acc-content__head">
                    <h4 class="mb-0 text-white fw-bold">Lịch Sử Đơn Hàng</h4>
                </div>

                <div class="card-body p-4">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <div class="mb-3" style="font-size: 40px;">📦</div>
                            <p class="text-muted">Bạn chưa mua đơn hàng nào.</p>
                            <a href="index.php" class="btn btn-outline-primary mt-2">Dạo một vòng mua sắm</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td class="fw-bold text-primary">#<?= $o['id'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                                            <td class="fw-bold text-danger"><?= number_format($o['total']) ?>₫</td>
                                            <td>
                                                <?php 
                                                    // Xử lý màu sắc trạng thái
                                                    $s = $o['shipping_status'];
                                                    $color = 'secondary';
                                                    $text  = $s;

                                                    if ($s === 'pending') { $color = 'warning text-dark'; $text = 'Chờ xử lý'; }
                                                    elseif ($s === 'processing') { $color = 'info text-dark'; $text = 'Đang xử lý'; }
                                                    elseif ($s === 'shipped') { $color = 'primary'; $text = 'Đang giao'; }
                                                    elseif ($s === 'delivered') { $color = 'success'; $text = 'Đã giao'; }
                                                    elseif ($s === 'cancelled') { $color = 'danger'; $text = 'Đã hủy'; }
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= $text ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="index.php?controller=account&action=orderDetail&id=<?= $o['id'] ?>" 
                                                   class="btn btn-sm btn-outline-dark mb-1" title="Xem chi tiết">
                                                    Chi tiết
                                                </a>
                                                
                                                <?php if ($o['shipping_status'] === 'pending'): ?>
                                                    <a href="index.php?controller=account&action=cancelOrder&id=<?= $o['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger mb-1"
                                                       onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= $o['id'] ?> này không?');">
                                                        Hủy đơn
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($o['shipping_status'] === 'shipped'): ?>
                                                    <a href="index.php?controller=account&action=confirmReceived&id=<?= $o['id'] ?>" 
                                                       class="btn btn-sm btn-success text-white mb-1"
                                                       onclick="return confirm('Xác nhận bạn đã nhận được hàng và kiện hàng nguyên vẹn?');">
                                                        Đã nhận hàng
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>