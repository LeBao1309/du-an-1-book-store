<?php
// Giả sử BaseModel.php đã được tải
// (Bạn có thể cần require_once 'BaseModel.php' ở đây
// hoặc file index.php của bạn đã xử lý)

/**
 * Class User (Model Người dùng)
 *
 * Kế thừa từ BaseModel và tự động có tất cả các hàm CRUD.
 * Chứa các logic nghiệp vụ (business logic) riêng của User
 * như: băm mật khẩu, kiểm tra mật khẩu.
 */
class User extends BaseModel {
    
    /**
     * BẮT BUỘC: Khai báo tên bảng
     */
    protected $table = 'users';
    
    /**
     * BẮT BUỘC: Khai báo khóa chính
     */
    protected $primaryKey = 'id';

    /**
     * Tìm một người dùng bằng địa chỉ email.
     *
     * @param string $email Email cần tìm
     * @return array|null Trả về mảng thông tin user nếu tìm thấy, ngược lại là null.
     */
    public function findByEmail($email) {
        // Tận dụng hàm findOne() có sẵn từ BaseModel
        // Tương đương: SELECT * FROM users WHERE email = ? LIMIT 1
        return $this->findOne(['email' => $email]);
    }
    
    /**
     * Tạo một người dùng mới.
     *
     * @param string $name Tên người dùng
     * @param string $email Email
     * @param string $password Mật khẩu (chưa băm)
     * @return bool True nếu tạo thành công, false nếu thất bại.
     */
    public function create($name, $email, $password) {
        // 1. Băm mật khẩu để bảo mật
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        
        // 2. Tận dụng hàm insert() có sẵn từ BaseModel
        return $this->insert([
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
            'role' => 'user' // Mặc định role là 'user'
        ]);
    }
    
    /**
     * Xác thực mật khẩu người dùng nhập vào.
     *
     * @param string $inputPass Mật khẩu người dùng nhập (vd: '123456')
     * @param string $hashedPass Mật khẩu đã băm trong CSDL
     * @return bool True nếu mật khẩu khớp, false nếu sai.
     */
    public function verifyPassword($inputPass, $hashedPass) {
        return password_verify($inputPass, $hashedPass);
    }
}
