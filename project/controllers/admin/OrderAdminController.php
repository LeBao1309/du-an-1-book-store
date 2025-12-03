<?php
require_once __DIR__ . '/AdminBaseController.php';
require_once __DIR__ . '/../../models/admin/AdminOrderModel.php';

final class OrderAdminController extends AdminBaseController{
        public function index(): string
    {
        $page = max(1, (int)($_GET['page'] ?? 1));

        $filters = [
            'keyword'         => trim($_GET['keyword'] ?? ''),
            'shipping_status' => $_GET['shipping_status'] ?? '',
            'payment_status'  => $_GET['payment_status'] ?? '',
            'payment_method'  => $_GET['payment_method'] ?? '',
            'channel'         => $_GET['channel'] ?? '',
            'from_date'       => $_GET['from_date'] ?? '',
            'to_date'         => $_GET['to_date'] ?? '',
        ];

        $pagination = AdminOrderModel::filter($filters, $page, 20);   // 👈

        return $this->renderAdmin('admin/orders/index', [
            'filters'     => $filters,
            'pagination'  => $pagination,
            'csrf'        => $this->csrfToken(),
            'order'       => null,
            'itemsDetail' => [],
        ]);
    }

    public function show(): string
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?c=orders&a=index');
            return '';
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'keyword'         => trim($_GET['keyword'] ?? ''),
            'shipping_status' => $_GET['shipping_status'] ?? '',
            'payment_status'  => $_GET['payment_status'] ?? '',
            'payment_method'  => $_GET['payment_method'] ?? '',
            'channel'         => $_GET['channel'] ?? '',
            'from_date'       => $_GET['from_date'] ?? '',
            'to_date'         => $_GET['to_date'] ?? '',
        ];

        $pagination = AdminOrderModel::filter($filters, $page, 20);
        $order      = AdminOrderModel::find($id);  
        $items      = AdminOrderModel::items($id);             

        return $this->renderAdmin('admin/orders/index', [
            'filters'     => $filters,
            'pagination'  => $pagination,
            'csrf'        => $this->csrfToken(),
            'order'       => $order,
            'itemsDetail' => $items,
        ]);
    }

    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=orders&a=index');
            return;
        }

        $this->checkCsrf();

        $id      = (int)($_POST['id'] ?? 0);
        $status  = (string)($_POST['shipping_status'] ?? '');
        $reason  = (string)($_POST['reason'] ?? '');
        $adminId = $_SESSION['admin']['id'] ?? null;

        if ($id <= 0 || !in_array($status, ['pending','processing','shipped','cancelled'], true)) {
            $_SESSION['flash_error'] = 'Trạng thái không hợp lệ.';
            header('Location: index.php?c=orders&a=index');
            return;
        }

        $ok = AdminOrderModel::updateShippingStatus($id, $status, $adminId, $reason);  // 👈

        $_SESSION['flash_'.($ok ? 'success' : 'error')] =
            $ok ? 'Cập nhật trạng thái đơn hàng thành công.'
                : 'Không thể cập nhật trạng thái đơn hàng.';

        header('Location: index.php?c=orders&a=show&id='.$id);
    }
}
