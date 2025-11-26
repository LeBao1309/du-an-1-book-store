<?php $active = 'address'; ?>
<section class="container my-4">
    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
            <div class="card shadow-sm border-0 acc-content">
                <div class="acc-content__head">
                    <h4 class="mb-0 text-white fw-bold">Địa Chỉ Giao Hàng</h4>
                    <div class="text-white-50 small">Quản lý các địa chỉ nhận hàng của bạn</div>
                </div>

                <div class="card-body p-4">
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-info">Bạn chưa có địa chỉ giao hàng nào.</div>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-item border p-3 rounded mb-3 <?= $addr['is_default'] ? 'border-success bg-light-green' : '' ?>">
                                <p class="fw-bold mb-1">
                                    <?= htmlspecialchars($addr['shipping_phone']) ?> 
                                    <?php if ($addr['is_default']): ?>
                                        <span class="badge bg-success ms-2">Mặc định</span>
                                    <?php endif; ?>
                                </p>
                                <p class="mb-2"><?= htmlspecialchars($addr['full_address']) ?></p>
                                
                                <div class="address-actions">
                                    <?php if (!$addr['is_default']): ?>
                                        <a href="?controller=account&action=setDefaultAddress&id=<?= $addr['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary me-2">
                                            Đặt làm Mặc định
                                        </a>
                                    <?php endif; ?>
                                    <a href="?controller=account&action=deleteAddress&id=<?= $addr['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                                        Xóa
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr class="my-4">

                    <h5 class="mb-3 fw-bold" style="color:var(--brand)">Thêm Địa Chỉ Mới</h5>
                    
                    <form id="addAddressForm" method="POST" action="?controller=account&action=addAddress">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ chi tiết</label>
                            <textarea name="full_address" id="full_address" required class="form-control acc-input" rows="3" placeholder="Số nhà, tên đường, Xã/Phường, Quận/Huyện..."></textarea>
                            <div id="addressError" class="js-error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số điện thoại nhận hàng</label>
                            <input name="shipping_phone" id="shipping_phone" type="text" required class="form-control acc-input" placeholder="Ví dụ: 0912345678">
                            <div id="phoneError" class="js-error"></div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" id="isDefaultCheck">
                            <label class="form-check-label" for="isDefaultCheck">
                                Đặt làm địa chỉ mặc định
                            </label>
                        </div>

                        <button type="submit" class="btn w-100 acc-btn">Thêm Địa Chỉ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('addAddressForm').addEventListener('submit', function(e) {
        // 1. Lấy giá trị
        const phone = document.getElementById('shipping_phone').value.trim();
        const address = document.getElementById('full_address').value.trim();
        
        const phoneError = document.getElementById('phoneError');
        const addressError = document.getElementById('addressError');

        // Reset lỗi cũ
        phoneError.style.display = 'none';
        addressError.style.display = 'none';
        
        let hasError = false;

        // 2. KIỂM TRA SỐ ĐIỆN THOẠI
        // ^0     : Bắt đầu bằng số 0
        // \d{9}  : Theo sau là 9 chữ số bất kỳ
        // $      : Kết thúc (tổng cộng đúng 10 số)
        const phoneRegex = /^0\d{9}$/;

        if (!phoneRegex.test(phone)) {
            phoneError.innerText = 'Số điện thoại không hợp lệ (Phải bắt đầu bằng số 0 và có đúng 10 số).';
            phoneError.style.display = 'block';
            hasError = true;
        }

        // Kiểm tra địa chỉ không được để trống (hoặc quá ngắn)
        if (address.length < 10) {
            addressError.innerText = 'Vui lòng nhập địa chỉ cụ thể hơn (ít nhất 10 ký tự).';
            addressError.style.display = 'block';
            hasError = true;
        }

        // 3. Nếu có lỗi thì chặn gửi form
        if (hasError) {
            e.preventDefault();
        }
    });
</script>