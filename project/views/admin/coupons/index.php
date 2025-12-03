<?php
/** @var array  $list */
/** @var array  $report */
/** @var string $csrf */
?>
<style>
.coupon-admin-page {
  margin-top: 4px;
}

.coupon-admin-page .admin-grid-2 {
  display: grid;
  gap: 18px;
  align-items: flex-start;
}
.coupon-admin-page .admin-grid-2.only-list {
  grid-template-columns: minmax(0, 1fr);
}
.coupon-admin-page .admin-grid-2.has-edit {
  grid-template-columns: minmax(0, 2fr) minmax(360px, 1.4fr);
}

.coupon-admin-page .coupon-edit-card {
  position: sticky;
  top: 10px;
  max-height: calc(100vh - 120px);
  overflow: auto;
  display: none;
}
.coupon-admin-page .admin-grid-2.has-edit .coupon-edit-card {
  display: block;
}

.coupon-admin-page .coupon-edit-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.coupon-admin-page .coupon-edit-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}
.coupon-admin-page .coupon-edit-meta {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}

/* FORM LAYOUT TRONG PANEL (grid 2 cột) */
.coupon-form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  column-gap: 12px;
  row-gap: 8px;
  margin-top: 4px;
}
.coupon-form-grid .field-span-2 {
  grid-column: span 2;
}
.coupon-form-grid .wd-label {
  margin-top: 0;
  margin-bottom: 4px;
  font-size: 13px;
}
.coupon-form-grid .wd-input {
  width: 100%;
  padding: 7px 10px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  font-size: 13px;
}
.coupon-form-actions {
  margin-top: 14px;
  display: flex;
  gap: 8px;
}

/* HIGHLIGHT ROW ĐANG EDIT */
#tblCoupons tr.is-active-row {
  background: #e5e7ff;
}

/* ICON BUTTON giống user-admin */
.coupon-admin-page .wd-icon-btn {
  border: none;
  background: #eef2ff;
  color: #4f46e5;
  width: 30px;
  height: 30px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  margin-right: 4px;
  transition: background 0.15s ease, transform 0.1s ease;
  text-decoration: none;
}
.coupon-admin-page .wd-icon-btn:hover {
  background: #e0e7ff;
  transform: translateY(-1px);
}
.coupon-admin-page .wd-icon-btn.danger {
  background: #fee2e2;
  color: #b91c1c;
}
.coupon-admin-page .wd-icon-btn.danger:hover {
  background: #fecaca;
}

/* BADGE */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 12px;
}
.badge-success {
  background: #ecfdf3;
  color: #16a34a;
}
.badge-muted {
  background: #e5e7eb;
  color: #4b5563;
}

.wd-modal {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 60;
}
.wd-modal.is-open {
  display: flex;
}
.wd-modal__backdrop {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.35);
}

/* ===== MODAL TẠO COUPON — UI ĐẸP HƠN ===== */
.wd-modal__dialog.coupon-modal {
  position: relative;
  background: #ffffff;
  border-radius: 24px;
  padding: 18px 20px 20px;
  width: 560px;
  max-width: calc(100% - 32px);
  box-shadow: 0 24px 80px rgba(15, 23, 42, 0.35);
  z-index: 1;
}

.coupon-modal .wd-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.coupon-modal .wd-modal__header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}
.coupon-modal .wd-modal__body {
  font-size: 14px;
}

/* layout form trong modal */
.coupon-modal-form {
  margin-top: 4px;
}

/* Hàng 3 cột: Code | Loại | Giá trị */
.coupon-modal-row-3 {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr;
  gap: 8px;
}

/* Hàng 2 cột: các cặp field */
.coupon-modal-row-2 {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
  margin-top: 8px;
}

/* Hàng full width */
.coupon-modal-row-1 {
  margin-top: 8px;
}

/* group label + input */
.coupon-field {
  display: flex;
  flex-direction: column;
}
.coupon-field label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 4px;
}

/* input trong modal: bo tròn, nền nhẹ */
.coupon-modal .wd-input {
  width: 100%;
  border-radius: 999px;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  font-size: 13px;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}
.coupon-modal .wd-input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.15);
  background: #ffffff;
}

/* checkbox dòng cuối */
.coupon-modal-checkbox {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  margin-top: 10px;
}

/* nút ở footer */
.coupon-modal-actions {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>

<div class="coupon-admin-page">

  <div class="admin-section-header">
    <div>
      <h2 class="admin-section-title">Mã giảm giá</h2>
      <p class="admin-section-subtitle">
        Quản lý mã khuyến mãi áp dụng cho đơn hàng.
      </p>
    </div>
    <button type="button" class="wd-btn-primary" id="btnOpenCreateCoupon">
      + Tạo mã
    </button>
  </div>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="wd-alert wd-alert-error">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="wd-alert wd-alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>

  <div class="admin-grid-2 only-list" id="couponGrid">

    <!-- DANH SÁCH COUPON -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span>Danh sách mã</span>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table" id="tblCoupons">
          <thead>
          <tr>
            <th>Code</th>
            <th>Loại</th>
            <th>Giá trị</th>
            <th>Giới hạn</th>
            <th>Đã dùng</th>
            <th>Thời gian</th>
            <th>Hoạt động</th>
            <th>Thao tác</th>
          </tr>
          </thead>
          <tbody>
          <?php if (!$list): ?>
            <tr>
              <td colspan="8" style="text-align:center;padding:16px;color:#6b7280;">
                Chưa có mã giảm giá.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($list as $c): ?>
              <tr
                data-id="<?= (int)$c['id']; ?>"
                data-code="<?= htmlspecialchars($c['code']); ?>"
                data-type="<?= htmlspecialchars($c['type']); ?>"
                data-value="<?= htmlspecialchars($c['value']); ?>"
                data-max-discount="<?= htmlspecialchars($c['max_discount']); ?>"
                data-min-total="<?= htmlspecialchars($c['min_order_total']); ?>"
                data-usage-limit="<?= htmlspecialchars($c['usage_limit']); ?>"
                data-max-user="<?= htmlspecialchars($c['max_uses_per_user']); ?>"
                data-starts-at="<?= htmlspecialchars($c['starts_at']); ?>"
                data-ends-at="<?= htmlspecialchars($c['ends_at']); ?>"
                data-active="<?= (int)$c['is_active']; ?>"
              >
                <td><?= htmlspecialchars($c['code']); ?></td>
                <td><?= $c['type']==='percent' ? 'Phần trăm' : 'Số tiền'; ?></td>
                <td>
                  <?php if ($c['type']==='percent'): ?>
                    <?= (float)$c['value']; ?>%
                  <?php else: ?>
                    <?= number_format((float)$c['value'], 0, ',', '.'); ?>đ
                  <?php endif; ?>
                </td>
                <td>
                  <?= $c['usage_limit'] !== null ? (int)$c['usage_limit'] : '∞'; ?>
                </td>
                <td><?= (int)($c['used_count'] ?? 0); ?></td>
                <td>
                  <?php if ($c['starts_at'] || $c['ends_at']): ?>
                    <span class="text-sm">
                      <?= htmlspecialchars($c['starts_at'] ?? '—'); ?> →
                      <?= htmlspecialchars($c['ends_at'] ?? '—'); ?>
                    </span>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($c['is_active']): ?>
                    <span class="badge badge-success">Đang bật</span>
                  <?php else: ?>
                    <span class="badge badge-muted">Tắt</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button type="button"
                          class="wd-icon-btn js-edit-coupon"
                          title="Chỉnh sửa mã">
                    ✏️
                  </button>

                  <button type="button"
                          class="wd-icon-btn danger js-delete-coupon"
                          title="Xóa mã">
                    🗑
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($report): ?>
        <div class="admin-card-body" style="margin-top:16px;">
          <h3 style="font-size:14px;font-weight:600;margin-bottom:8px;">
            Báo cáo sử dụng (tổng quan)
          </h3>
          <table class="admin-table">
            <thead>
            <tr>
              <th>Code</th>
              <th>Số đơn đã áp</th>
              <th>Tổng tiền đã giảm</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($report as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['code']); ?></td>
                <td><?= (int)$r['used_orders']; ?></td>
                <td><?= number_format((float)$r['total_discount'], 0, ',', '.'); ?>đ</td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- PANEL EDIT BÊN PHẢI (CHỈ DÙNG CHO SỬA) -->
    <div class="admin-card coupon-edit-card" id="couponEditCard">
      <div class="coupon-edit-header">
        <div>
          <h3 id="couponFormTitle">Cập nhật mã giảm giá</h3>
          <div class="coupon-edit-meta" id="couponEditMeta">
            Chọn một mã ở bảng bên trái để chỉnh sửa.
          </div>
        </div>
        <button type="button" class="wd-icon-btn" id="btnCloseCouponEdit" title="Đóng">
          ✕
        </button>
      </div>

      <form method="post" id="couponEditForm" action="index.php?c=coupons&a=update">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="couponId">

        <div class="coupon-form-grid">
          <div class="field-span-2">
            <label class="wd-label">Code</label>
            <input type="text" name="code" id="couponCode"
                   class="wd-input" required>
          </div>

          <div>
            <label class="wd-label">Loại</label>
            <select name="type" id="couponType" class="wd-input">
              <option value="percent">Phần trăm (%)</option>
              <option value="fixed">Số tiền (đ)</option>
            </select>
          </div>

          <div>
            <label class="wd-label">Giá trị</label>
            <input type="number" step="0.01" min="0" name="value"
                   id="couponValue" class="wd-input" required>
          </div>

          <div>
            <label class="wd-label">Giảm tối đa (max_discount, nếu %)</label>
            <input type="number" step="0.01" min="0" name="max_discount"
                   id="couponMaxDiscount" class="wd-input">
          </div>

          <div>
            <label class="wd-label">Đơn tối thiểu (min_order_total)</label>
            <input type="number" step="0.01" min="0" name="min_order_total"
                   id="couponMinTotal" class="wd-input" value="0">
          </div>

          <div>
            <label class="wd-label">Giới hạn sử dụng (usage_limit)</label>
            <input type="number" min="0" name="usage_limit"
                   id="couponUsageLimit" class="wd-input"
                   placeholder="Để trống = không giới hạn">
          </div>

          <div>
            <label class="wd-label">Giới hạn / user (max_uses_per_user)</label>
            <input type="number" min="0" name="max_uses_per_user"
                   id="couponMaxPerUser" class="wd-input"
                   placeholder="Để trống = không giới hạn">
          </div>

          <div>
            <label class="wd-label">Bắt đầu từ</label>
            <input type="datetime-local" name="starts_at"
                   id="couponStarts" class="wd-input">
          </div>

          <div>
            <label class="wd-label">Kết thúc</label>
            <input type="datetime-local" name="ends_at"
                   id="couponEnds" class="wd-input">
          </div>

          <div class="field-span-2">
            <label class="wd-label">
              <input type="checkbox" name="is_active" id="couponActive" value="1">
              Đang hoạt động
            </label>
          </div>
        </div>

        <div class="coupon-form-actions">
          <button type="submit" class="wd-btn-primary">
            Cập nhật
          </button>
          <button type="button" class="wd-btn-secondary" id="btnCouponReset">
            Làm mới form
          </button>
        </div>
      </form>
    </div>

  </div> <!-- /.admin-grid-2 -->
</div> 

<div class="wd-modal" id="createCouponModal">
  <div class="wd-modal__backdrop"></div>

  <div class="wd-modal__dialog coupon-modal">
    <div class="wd-modal__header">
      <h3>Tạo mã giảm giá mới</h3>
      <button type="button" class="wd-icon-btn" id="btnCloseCreateCoupon">✕</button>
    </div>

    <div class="wd-modal__body">
      <form method="post" id="createCouponForm" action="index.php?c=coupons&a=store">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">

        <div class="coupon-modal-form">
          <div class="coupon-modal-row-3">
            <div class="coupon-field">
              <label for="couponCodeCreate">Code</label>
              <input type="text"
                     name="code"
                     id="couponCodeCreate"
                     class="wd-input"
                     required
                     placeholder="VD: WDSPRING2025">
            </div>

            <div class="coupon-field">
              <label for="couponTypeCreate">Loại</label>
              <select name="type" id="couponTypeCreate" class="wd-input">
                <option value="percent">Phần trăm (%)</option>
                <option value="fixed">Số tiền (đ)</option>
              </select>
            </div>

            <div class="coupon-field">
              <label for="couponValueCreate">Giá trị</label>
              <input type="number"
                     step="0.01"
                     min="0"
                     name="value"
                     id="couponValueCreate"
                     class="wd-input"
                     required
                     placeholder="VD: 10 (10%)">
            </div>
          </div>


          <div class="coupon-modal-row-2">
            <div class="coupon-field">
              <label for="couponMaxDiscountCreate">Giảm tối đa (max_discount, nếu %)</label>
              <input type="number"
                     step="0.01"
                     min="0"
                     name="max_discount"
                     id="couponMaxDiscountCreate"
                     class="wd-input"
                     placeholder="Đơn vị: đ (có thể để trống)">
            </div>

            <div class="coupon-field">
              <label for="couponMinTotalCreate">Đơn tối thiểu (min_order_total)</label>
              <input type="number"
                     step="0.01"
                     min="0"
                     name="min_order_total"
                     id="couponMinTotalCreate"
                     class="wd-input"
                     value="0">
            </div>
          </div>


          <div class="coupon-modal-row-2">
            <div class="coupon-field">
              <label for="couponUsageLimitCreate">Giới hạn sử dụng (usage_limit)</label>
              <input type="number"
                     min="0"
                     name="usage_limit"
                     id="couponUsageLimitCreate"
                     class="wd-input"
                     placeholder="Để trống = không giới hạn">
            </div>

            <div class="coupon-field">
              <label for="couponMaxPerUserCreate">Giới hạn / user (max_uses_per_user)</label>
              <input type="number"
                     min="0"
                     name="max_uses_per_user"
                     id="couponMaxPerUserCreate"
                     class="wd-input"
                     placeholder="Để trống = không giới hạn">
            </div>
          </div>


          <div class="coupon-modal-row-2">
            <div class="coupon-field">
              <label for="couponStartsCreate">Bắt đầu từ</label>
              <input type="datetime-local"
                     name="starts_at"
                     id="couponStartsCreate"
                     class="wd-input">
            </div>

            <div class="coupon-field">
              <label for="couponEndsCreate">Kết thúc</label>
              <input type="datetime-local"
                     name="ends_at"
                     id="couponEndsCreate"
                     class="wd-input">
            </div>
          </div>


          <div class="coupon-modal-row-1">
            <label class="coupon-modal-checkbox">
              <input type="checkbox" name="is_active" id="couponActiveCreate" value="1" checked>
              Đang hoạt động
            </label>
          </div>
        </div>

        <div class="coupon-modal-actions">
          <button type="button" class="wd-btn-secondary" id="btnCancelCreateCoupon">
            Hủy
          </button>
          <button type="submit" class="wd-btn-primary">
            Lưu mã
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- MODAL: XÁC NHẬN XÓA -->
<div class="wd-modal" id="deleteCouponModal">
  <div class="wd-modal__backdrop"></div>
  <div class="wd-modal__dialog">
    <div class="wd-modal__header">
      <h3>Xóa mã giảm giá</h3>
      <button type="button" class="wd-icon-btn" id="btnCloseDeleteCoupon">✕</button>
    </div>
    <div class="wd-modal__body">
      <p style="font-size:14px;margin-bottom:12px;">
        Bạn có chắc chắn muốn xóa mã giảm giá này? Hành động này không thể hoàn tác.
      </p>
      <form method="post" action="index.php?c=coupons&a=delete" id="deleteCouponForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf); ?>">
        <input type="hidden" name="id" id="deleteCouponId">
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px;">
          <button type="button" class="wd-btn-secondary" id="btnCancelDeleteCoupon">
            Hủy
          </button>
          <button type="submit" class="wd-btn-danger">
            Xóa mã
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function () {
  // helper modal
  function openModal(el){ if(el) el.classList.add('is-open'); }
  function closeModal(el){ if(el) el.classList.remove('is-open'); }

  const gridEl       = document.getElementById('couponGrid');
  const editCard     = document.getElementById('couponEditCard');

  const editForm     = document.getElementById('couponEditForm');
  const formTitleEl  = document.getElementById('couponFormTitle');
  const formMetaEl   = document.getElementById('couponEditMeta');
  const btnCloseEdit = document.getElementById('btnCloseCouponEdit');
  const btnResetEdit = document.getElementById('btnCouponReset');

  const idInput          = document.getElementById('couponId');
  const codeInput        = document.getElementById('couponCode');
  const typeSelect       = document.getElementById('couponType');
  const valueInput       = document.getElementById('couponValue');
  const maxDiscountInput = document.getElementById('couponMaxDiscount');
  const minTotalInput    = document.getElementById('couponMinTotal');
  const usageLimitInput  = document.getElementById('couponUsageLimit');
  const maxPerUserInput  = document.getElementById('couponMaxPerUser');
  const startsInput      = document.getElementById('couponStarts');
  const endsInput        = document.getElementById('couponEnds');
  const activeInput      = document.getElementById('couponActive');

  function openEditLayout() {
    if (!gridEl || !editCard) return;
    gridEl.classList.remove('only-list');
    gridEl.classList.add('has-edit');
  }

  function closeEditLayout() {
    if (!gridEl || !editCard) return;
    gridEl.classList.remove('has-edit');
    gridEl.classList.add('only-list');

    document.querySelectorAll('#tblCoupons tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));
  }

  function resetEditForm() {
    idInput.value          = '';
    codeInput.value        = '';
    typeSelect.value       = 'percent';
    valueInput.value       = '';
    maxDiscountInput.value = '';
    minTotalInput.value    = '0';
    usageLimitInput.value  = '';
    maxPerUserInput.value  = '';
    startsInput.value      = '';
    endsInput.value        = '';
    activeInput.checked    = true;

    formTitleEl.textContent = 'Cập nhật mã giảm giá';
    formMetaEl.textContent  = 'Chọn một mã ở bảng bên trái để chỉnh sửa.';

    document.querySelectorAll('#tblCoupons tr.is-active-row')
      .forEach(r => r.classList.remove('is-active-row'));
  }

  if (btnResetEdit) {
    btnResetEdit.addEventListener('click', resetEditForm);
  }
  if (btnCloseEdit) {
    btnCloseEdit.addEventListener('click', () => {
      resetEditForm();
      closeEditLayout();
    });
  }

  // click Sửa → mở panel
  document.querySelectorAll('.js-edit-coupon').forEach(btn => {
    btn.addEventListener('click', function () {
      const tr = this.closest('tr');
      if (!tr) return;

      document.querySelectorAll('#tblCoupons tr.is-active-row')
        .forEach(r => r.classList.remove('is-active-row'));
      tr.classList.add('is-active-row');

      editForm.action = 'index.php?c=coupons&a=update';

      idInput.value          = tr.dataset.id || '';
      codeInput.value        = tr.dataset.code || '';
      typeSelect.value       = tr.dataset.type || 'percent';
      valueInput.value       = tr.dataset.value || '';
      maxDiscountInput.value = tr.dataset.maxDiscount || '';
      minTotalInput.value    = tr.dataset.minTotal || '0';
      usageLimitInput.value  = tr.dataset.usageLimit || '';
      maxPerUserInput.value  = tr.dataset.maxUser || '';
      startsInput.value      = tr.dataset.startsAt || '';
      endsInput.value        = tr.dataset.endsAt || '';
      activeInput.checked    = tr.dataset.active === '1';

      formTitleEl.textContent = 'Cập nhật mã giảm giá';
      formMetaEl.textContent  = 'Đang chỉnh sửa mã: ' + (tr.dataset.code || '');

      openEditLayout();
    });
  });

  // ===== MODAL CREATE =====
  const createModal   = document.getElementById('createCouponModal');
  const btnOpenCreate = document.getElementById('btnOpenCreateCoupon');
  const btnCloseCreate= document.getElementById('btnCloseCreateCoupon');
  const btnCancelCreate = document.getElementById('btnCancelCreateCoupon');

  const codeCreate        = document.getElementById('couponCodeCreate');
  const typeCreate        = document.getElementById('couponTypeCreate');
  const valueCreate       = document.getElementById('couponValueCreate');
  const maxDiscountCreate = document.getElementById('couponMaxDiscountCreate');
  const minTotalCreate    = document.getElementById('couponMinTotalCreate');
  const usageLimitCreate  = document.getElementById('couponUsageLimitCreate');
  const maxPerUserCreate  = document.getElementById('couponMaxPerUserCreate');
  const startsCreate      = document.getElementById('couponStartsCreate');
  const endsCreate        = document.getElementById('couponEndsCreate');
  const activeCreate      = document.getElementById('couponActiveCreate');

  function resetCreateForm() {
    codeCreate.value        = '';
    typeCreate.value        = 'percent';
    valueCreate.value       = '';
    maxDiscountCreate.value = '';
    minTotalCreate.value    = '0';
    usageLimitCreate.value  = '';
    maxPerUserCreate.value  = '';
    startsCreate.value      = '';
    endsCreate.value        = '';
    activeCreate.checked    = true;
  }

  if (btnOpenCreate) {
    btnOpenCreate.addEventListener('click', () => {
      resetCreateForm();
      openModal(createModal);
    });
  }
  [btnCloseCreate, btnCancelCreate].forEach(btn => {
    if (!btn) return;
    btn.addEventListener('click', () => {
      closeModal(createModal);
    });
  });

  // ===== MODAL DELETE =====
  const deleteModal    = document.getElementById('deleteCouponModal');
  const deleteIdInput  = document.getElementById('deleteCouponId');
  const btnCloseDelete = document.getElementById('btnCloseDeleteCoupon');
  const btnCancelDelete= document.getElementById('btnCancelDeleteCoupon');

  document.querySelectorAll('.js-delete-coupon').forEach(btn => {
    btn.addEventListener('click', function () {
      const tr = this.closest('tr');
      if (!tr) return;
      deleteIdInput.value = tr.dataset.id || '';
      openModal(deleteModal);
    });
  });

  [btnCloseDelete, btnCancelDelete].forEach(btn => {
    if (!btn) return;
    btn.addEventListener('click', () => {
      closeModal(deleteModal);
    });
  });
})();
</script>
