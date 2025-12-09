<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminCommentModel.php';

final class CommentAdminController extends BaseAdminController
{
    public function index(): string
    {
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'rating'  => $_GET['rating'] ?? '',
            'book_id' => $_GET['book_id'] ?? '',
        ];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $data = AdminCommentModel::paginate($filters, $page, 10);

        return $this->renderAdmin('admin/community/comments', [
            'pagination' => $data,
            'filters'    => $filters,
            'csrf'       => $this->csrfToken(),
        ]);
    }

    public function delete(): void
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->flashError('Bình luận không hợp lệ.');
            $this->redirect('index.php?c=comments&a=index');
        }

        $ok = AdminCommentModel::delete($id);
        $ok ? $this->flashSuccess('Đã xóa bình luận.') : $this->flashError('Xóa bình luận thất bại.');
        $this->redirect('index.php?c=comments&a=index');
    }
}
