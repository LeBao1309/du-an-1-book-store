<?php

abstract class AdminBaseController
{
    // Middleware bảo vệ admin
    public function __construct()
    {
        $isAuthController = ($this instanceof AdminAuthController);

        if (!$isAuthController && empty($_SESSION['admin'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
    }

    protected function render($viewPath, $data = []): string
    {
        extract($data);

        $file = __DIR__ . '/../../views/' . $viewPath . '.php';
        if (!file_exists($file)) die("View not found: $file");

        ob_start();
        require $file;
        return ob_get_clean();
    }

    protected function renderAdmin($view, $data = []): string
    {
        $content = $this->render($view, $data);

        ob_start();
        require __DIR__ . '/../../views/admin/__layout_admin.php';
        return ob_get_clean();
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf'];
    }

    protected function checkCsrf(): void
    {
        if (($_POST['_csrf'] ?? '') !== ($_SESSION['_csrf'] ?? '')) {
            die('CSRF validation failed');
        }
    }
        protected function currentAdmin(): ?array
    {
        return $_SESSION['admin'] ?? null;
    }
}
