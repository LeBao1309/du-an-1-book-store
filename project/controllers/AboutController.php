<?php

require_once __DIR__ . '/BaseController.php';

class AboutController extends BaseController {
    
    public function index() {
        // Hiển thị trang giới thiệu
        echo $this->render('page/about');
    }
}
