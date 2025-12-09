<?php
require_once __DIR__ . '/BaseAdminController.php';
require_once __DIR__ . '/../../models/admin/AdminQuestionModel.php';

final class QuestionAdminController extends BaseAdminController
{
    public function index(): string
    {
        $filters = [
            'keyword'  => trim($_GET['keyword'] ?? ''),
            'answered' => $_GET['answered'] ?? '',
            'book_id'  => $_GET['book_id'] ?? '',
        ];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $data = AdminQuestionModel::paginate($filters, $page, 10);

        return $this->renderAdmin('admin/community/questions', [
            'pagination' => $data,
            'filters'    => $filters,
            'csrf'       => $this->csrfToken(),
        ]);
    }

    public function detail(): string
    {
        $id = (int)($_GET['id'] ?? 0);
        $question = AdminQuestionModel::getQuestion($id);
        if (!$question) {
            $this->flashError('Câu hỏi không tồn tại.');
            $this->redirect('index.php?c=qa&a=index');
        }

        return $this->renderAdmin('admin/community/question_detail', [
            'question' => $question,
            'answers'  => AdminQuestionModel::getAnswers($id),
            'csrf'     => $this->csrfToken(),
        ]);
    }

    public function answer(): void
    {
        $this->checkCsrf();
        $qid = (int)($_POST['question_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        if ($qid <= 0 || $content === '') {
            $this->flashError('Dữ liệu không hợp lệ.');
            $this->redirect('index.php?c=qa&a=index');
        }

        $admin = $this->currentAdmin();
        $uid = (int)($admin['id'] ?? 0);
        $ok = AdminQuestionModel::addAnswer($qid, $uid, $content, true);
        $ok ? $this->flashSuccess('Đã trả lời câu hỏi.') : $this->flashError('Trả lời thất bại.');
        $this->redirect('index.php?c=qa&a=detail&id=' . $qid);
    }

    public function deleteQuestion(): void
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $ok = $id > 0 ? AdminQuestionModel::deleteQuestion($id) : false;
        $ok ? $this->flashSuccess('Đã xóa câu hỏi.') : $this->flashError('Xóa câu hỏi thất bại.');
        $this->redirect('index.php?c=qa&a=index');
    }

    public function deleteAnswer(): void
    {
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $qid = (int)($_POST['question_id'] ?? 0);
        $ok = $id > 0 ? AdminQuestionModel::deleteAnswer($id) : false;
        $ok ? $this->flashSuccess('Đã xóa câu trả lời.') : $this->flashError('Xóa câu trả lời thất bại.');
        $redirectId = $qid > 0 ? $qid : 0;
        $this->redirect($redirectId ? 'index.php?c=qa&a=detail&id=' . $redirectId : 'index.php?c=qa&a=index');
    }
}
