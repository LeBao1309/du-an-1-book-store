<?php
declare(strict_types=1);

abstract class BaseController
{
    protected function render(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) { http_response_code(500); return "View not found: {$view}"; }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // inject layout data
        $currentUser = $_SESSION['user'] ?? null;
        $flash = $_SESSION['flash'] ?? null; // ['type'=>'success|error','message'=>'...']
        unset($_SESSION['flash']);

        $layout = __DIR__ . '/../views/layouts/main.php';
        if (file_exists($layout)) {
            ob_start();
            include $layout;
            return ob_get_clean();
        }
        return $content;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) $this->redirect('?c=auth&a=login');
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
        return $_SESSION['csrf'];
    }

    protected function checkCsrf(): void
    {
        $ok = isset($_POST['_csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['_csrf']);
        if (!$ok) { http_response_code(400); exit('CSRF token invalid'); }
    }
}
