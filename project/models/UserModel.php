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


    public static function adminCreate(
        string $name,
        string $email,
        string $password,
        string $role = 'user',
        bool $isActive = true
    ): int {
        if (!in_array($role, ['user','admin'], true)) {
            $role = 'user';
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, role, is_active)
                VALUES (:name, :email, :password, :role, :is_active)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':name'      => $name,
            ':email'     => $email,
            ':password'  => $hash,
            ':role'      => $role,
            ':is_active' => $isActive ? 1 : 0,
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function adminUpdate(
        int $id,
        string $name,
        string $email,
        string $role,
        bool $isActive,
        ?string $newPassword = null
    ): bool {
        if (!in_array($role, ['user','admin'], true)) {
            $role = 'user';
        }

        $params = [
            ':id'        => $id,
            ':name'      => $name,
            ':email'     => $email,
            ':role'      => $role,
            ':is_active' => $isActive ? 1 : 0,
        ];

        $set = "name = :name, email = :email, role = :role, is_active = :is_active";

        if ($newPassword !== null && $newPassword !== '') {
            $params[':password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $set .= ", password = :password";
        }

        $sql = "UPDATE users SET {$set} WHERE id = :id";
        $st = self::db()->prepare($sql);
        return $st->execute($params);
    }

    public static function paginateForAdmin(array $filters, int $page, int $perPage = 10): array
    {
        $where  = [];
        $params = [];

        if ($filters['keyword'] !== '') {
            $where[] = '(name LIKE :kw OR email LIKE :kw)';
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }
        if ($filters['role'] !== '') {
            $where[] = 'role = :role';
            $params[':role'] = $filters['role'];
        }
        if ($filters['status'] !== '') {
            $where[] = 'is_active = :status';
            $params[':status'] = (int)$filters['status'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sqlCount = "SELECT COUNT(*) FROM users {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, name, email, role, is_active, created_at
                FROM users
                {$whereSql}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $st->execute();

        return [
            'items'    => $st->fetchAll(),
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'lastPage' => max(1, (int)ceil($total / $perPage)),
        ];
    }

}