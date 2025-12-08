<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminCouponModel.php';

final class CouponAdminController extends BaseAdminController
{
    public function index(): string
    {
        $list   = AdminCouponModel::all();
        $report = AdminCouponModel::usageReport();
        $csrf   = $this->csrfToken();

        return $this->renderAdmin('admin/coupons/index', [
            'list'   => $list,
            'report' => $report,
            'csrf'   => $csrf,
        ]);
    }

    public function store(): void
    {
        $this->checkCsrf();
        $data = $_POST;
        $ok   = AdminCouponModel::create($data);

        $_SESSION['flash_' . ($ok ? 'success' : 'error')] =
            $ok ? 'Tạo mã giảm giá thành công.' : 'Không thể tạo mã giảm giá.';

        header('Location: index.php?c=coupons&a=index');
        exit;
    }

    public function update(): void
    {
        $this->checkCsrf();
        $id   = (int) ($_POST['id'] ?? 0);
        $data = $_POST;

        $ok = AdminCouponModel::update($id, $data);

        $_SESSION['flash_' . ($ok ? 'success' : 'error')] =
            $ok ? 'Cập nhật mã giảm giá thành công.' : 'Không thể cập nhật mã giảm giá.';

        header('Location: index.php?c=coupons&a=index');
        exit;
    }

    public function delete(): void
    {
        $this->checkCsrf();
        $id = (int) ($_POST['id'] ?? 0);

        if (AdminCouponModel::isUsed($id)) {
            $_SESSION['flash_error'] = 'Không thể xóa: mã giảm giá đã được sử dụng trong đơn hàng.';
        } else {
            $ok = AdminCouponModel::deleteIfUnused($id);
            $_SESSION['flash_' . ($ok ? 'success' : 'error')] =
                $ok ? 'Đã xóa mã giảm giá.' : 'Không thể xóa mã giảm giá.';
        }

        header('Location: index.php?c=coupons&a=index');
        exit;
    }
}
