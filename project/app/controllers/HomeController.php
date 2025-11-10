<?php
/**
 * HomeController
 *
 * Xử lý các trang CÔNG KHAI (public) của website.
 * Bất kỳ ai cũng có thể truy cập.
 */
class HomeController extends BaseController {

    /**
     * Hàm khởi tạo (Constructor)
     *
     * KHÔNG gọi "requireLogin" ở đây.
     */
    public function __construct() {
        // (Bạn có thể tải model Book, v.v. ở đây)
    }

    /**
     * Hiển thị trang chủ chính
     * URL: (GET) /
     */
    public function index() {
        
        // 1. Lấy thông tin user (nếu họ ĐÃ đăng nhập)
        // $currentUser sẽ là MỘT MẢNG (array) nếu đã đăng nhập
        // $currentUser sẽ là NULL nếu là khách
        $currentUser = $this->currentUser(); 

        // 2. Lấy tin nhắn flash (ví dụ: 'Đăng nhập thành công')
        $successMsg = $this->getFlash('success');

        // 3. Render view, và truyền CẢ MẢNG $currentUser (hoặc null)
        //
        // *** ĐÂY LÀ ĐOẠN SỬA LỖI ***
        // Chúng ta truyền biến 'currentUser' (khớp với Lỗi 2)
        // Và chúng ta truyền $currentUser (dù là mảng hay null),
        // sẽ không gây ra lỗi "access array offset" (sửa Lỗi 1).
        //
        $this->view('home/index', [
            'currentUser' => $currentUser, // Truyền TOÀN BỘ user (sửa cả 2 lỗi)
            'successMsg' => $successMsg
        ]);
    }
}
?>