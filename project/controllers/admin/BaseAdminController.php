<?php

abstract class BaseAdminController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => $secure,
            ]);
            session_start();
        }

        $isAuthController = ($this instanceof AdminAuthController);

        if (!$isAuthController && empty($_SESSION['admin'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        // Kiểm tra fingerprint phiên để hạn chế đánh cắp session
        if (!empty($_SESSION['admin'])) {
            $fingerprint = $this->fingerprint();
            if (empty($_SESSION['admin_fingerprint']) || $_SESSION['admin_fingerprint'] !== $fingerprint) {
                unset($_SESSION['admin'], $_SESSION['admin_fingerprint']);
                header('Location: index.php?c=auth&a=login');
                exit;
            }
        }
    }

    protected function render(string $viewPath, array $data = []): string
    {
        extract($data);

        $file = __DIR__ . '/../../views/' . $viewPath . '.php';
        if (!file_exists($file)) {
            die("View not found: $file");
        }

        ob_start();
        require $file;
        return ob_get_clean();
    }

    protected function renderAdmin(string $view, array $data = []): string
    {
        $content = $this->render($view, $data);

        ob_start();
        require __DIR__ . '/../../views/admin/__layout_admin.php';
        return ob_get_clean();
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    protected function flashError(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function slugify(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim($text, '-');

        return $text !== '' ? $text : 'n-a';
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

    protected function fingerprint(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return hash('sha256', $ua . '|' . $ip);
    }

    protected function redirectBack(): void
{
    $url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: {$url}");
    exit;
}

}
