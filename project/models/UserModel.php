<?php
require_once __DIR__ . '/BaseModel.php';

final class UserModel extends BaseModel
{
    public static function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, name, email, password, role, is_active FROM users WHERE email = :email LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':email' => $email]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role, is_active) VALUES (:name, :email, :password, 'user', 1)";
        $st = self::db()->prepare($sql);
        $st->execute([':name'=>$name, ':email'=>$email, ':password'=>$hash]);
        return (int) self::db()->lastInsertId();
    }

    // Đã sửa để lấy password cho chức năng Đổi Mật Khẩu
    public static function findById(int $id): ? array {
        $sql = "SELECT id, name, email, password, role, is_active, created_at
                FROM users WHERE id = :id LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':id'=>$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function updateProfile(int $id, string $name, string $email): bool
    {
        $sql = "UPDATE users SET name=:name, email=:email WHERE id=:id";
        $st = self::db()->prepare($sql);
        return $st->execute([':name'=>$name, ':email'=>$email, ':id'=>$id]);
    }

    public static function updatePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT); 
        $sql = "UPDATE users SET password=:password WHERE id=:id";
        $st = self::db()->prepare($sql);
        return $st->execute([':password' => $hash, ':id' => $id]);
    }

    public static function emailExistsForOther(string $email, int $selfId): bool
    {
        $sql = "SELECT id FROM users WHERE email=:email AND id<>:selfId LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':email'=>$email, ':selfId'=>$selfId]);
        return (bool)$st->fetch();
    }

// ===================================
// PHƯƠNG THỨC QUẢN LÝ ĐỊA CHỈ
// ===================================

    // Lấy tất cả địa chỉ của người dùng
    public static function getAddresses(int $userId): array
    {
        $sql = "SELECT id, full_address, shipping_phone, is_default 
                FROM user_address 
                WHERE user_id = :userId 
                ORDER BY is_default DESC, id DESC";
        $st = self::db()->prepare($sql);
        $st->execute([':userId' => $userId]);
        return $st->fetchAll();
    }
    
    // Lấy 1 địa chỉ theo ID (và user_id để bảo mật)
    public static function findAddressById(int $id, int $userId): ?array
    {
        $sql = "SELECT id, full_address, shipping_phone, is_default 
                FROM user_address 
                WHERE id = :id AND user_id = :userId";
        $st = self::db()->prepare($sql);
        $st->execute([':id' => $id, ':userId' => $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    // Thêm địa chỉ mới
    public static function createAddress(int $userId, string $address, string $phone, bool $isDefault): int
    {
        if ($isDefault) {
            self::clearDefaultAddress($userId);
        }
        
        $sql = "INSERT INTO user_address (user_id, full_address, shipping_phone, is_default) 
                VALUES (:userId, :address, :phone, :isDefault)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':userId' => $userId, 
            ':address' => $address, 
            ':phone' => $phone, 
            ':isDefault' => $isDefault ? 1 : 0
        ]);
        return (int) self::db()->lastInsertId();
    }
    
    // Đặt địa chỉ thành mặc định
    public static function setDefaultAddress(int $id, int $userId): bool
    {
        self::clearDefaultAddress($userId);
        
        $sql = "UPDATE user_address SET is_default = 1 WHERE id = :id AND user_id = :userId";
        $st = self::db()->prepare($sql);
        return $st->execute([':id' => $id, ':userId' => $userId]);
    }
    
    // Hàm phụ trợ: Hủy đặt mặc định tất cả địa chỉ cũ
    private static function clearDefaultAddress(int $userId): bool
    {
        $sql = "UPDATE user_address SET is_default = 0 WHERE user_id = :userId";
        $st = self::db()->prepare($sql);
        return $st->execute([':userId' => $userId]);
    }

    // Xóa địa chỉ
    public static function deleteAddress(int $id, int $userId): bool
    {
        $sql = "DELETE FROM user_address WHERE id = :id AND user_id = :userId";
        $st = self::db()->prepare($sql);
        return (bool) $st->execute([':id' => $id, ':userId' => $userId]);
    }
}