<?php
declare(strict_types=1);

final class HomeController extends BaseController
{
    public function index(): string
    {
      $name = $_SESSION['user']['name'] ?? 'Khách';
      $message = "Xin chào {$name}, chúc bạn một ngày tốt lành!";
        return $this->render('home/index', compact('message'));
    }

    public function profile(): string
    {
        $this->requireAuth();
        $user = $_SESSION['user'];
        return $this->render('home/profile', compact('user'));
    }
}
