<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * User Model - Quản lý người dùng và địa chỉ giao hàng
 */
final class UserModel extends BaseModel
{
    /**
     * Tìm người dùng theo email
     * @param string $email Email của người dùng
     * @return array|null Thông tin người dùng
     */
    public static function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT id, name, email, password, role, is_active 
                    FROM users 
                    WHERE email = :email 
                    LIMIT 1";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo người dùng mới
     * @param string $name Tên người dùng
     * @param string $email Email
     * @param string $password Mật khẩu (chưa mã hóa)
     * @return int ID của người dùng mới tạo
     */
    public static function create(string $name, string $email, string $password): int
    {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, role, is_active) 
                    VALUES (:name, :email, :password, 'user', 1)";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash]);
            return (int)self::db()->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tìm người dùng theo ID
     * @param int $id ID người dùng
     * @return array|null Thông tin người dùng
     */
    public static function findById(int $id): ?array
    {
        try {
            $sql = "SELECT id, name, email, password, role, is_active, created_at
                    FROM users 
                    WHERE id = :id 
                    LIMIT 1";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật thông tin hồ sơ
     * @param int $id ID người dùng
     * @param string $name Tên mới
     * @param string $email Email mới
     * @return bool True nếu thành công
     */
    public static function updateProfile(int $id, string $name, string $email): bool
    {
        try {
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':name' => $name, ':email' => $email, ':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật mật khẩu
     * @param int $id ID người dùng
     * @param string $newPassword Mật khẩu mới (chưa mã hóa)
     * @return bool True nếu thành công
     */
    public static function updatePassword(int $id, string $newPassword): bool
    {
        try {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':password' => $hash, ':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra email đã tồn tại cho người dùng khác
     * @param string $email Email cần kiểm tra
     * @param int $selfId ID người dùng hiện tại (bỏ qua)
     * @return bool True nếu email đã tồn tại
     */
    public static function emailExistsForOther(string $email, int $selfId): bool
    {
        try {
            $sql = "SELECT id FROM users WHERE email = :email AND id <> :selfId LIMIT 1";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':email' => $email, ':selfId' => $selfId]);
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error checking email exists: " . $e->getMessage());
            return false;
        }
    }

    // ===================================
    // PHƯƠNG THỨC QUẢN LÝ ĐỊA CHỈ
    // ===================================

    /**
     * Lấy tất cả địa chỉ của người dùng
     * @param int $userId ID người dùng
     * @return array Danh sách địa chỉ
     */
    public static function getAddresses(int $userId): array
    {
        try {
            $sql = "SELECT id, full_address, shipping_phone, is_default 
                    FROM user_address 
                    WHERE user_id = :userId 
                    ORDER BY is_default DESC, id DESC";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error getting addresses: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy 1 địa chỉ theo ID (và user_id để bảo mật)
     * @param int $id ID địa chỉ
     * @param int $userId ID người dùng
     * @return array|null Thông tin địa chỉ
     */
    public static function findAddressById(int $id, int $userId): ?array
    {
        try {
            $sql = "SELECT id, full_address, shipping_phone, is_default 
                    FROM user_address 
                    WHERE id = :id AND user_id = :userId 
                    LIMIT 1";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':id' => $id, ':userId' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error finding address: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Thêm địa chỉ mới
     * @param int $userId ID người dùng
     * @param string $address Địa chỉ đầy đủ
     * @param string $phone Số điện thoại
     * @param bool $isDefault Đặt làm mặc định
     * @return int ID của địa chỉ mới
     */
    public static function createAddress(int $userId, string $address, string $phone, bool $isDefault): int
    {
        try {
            if ($isDefault) {
                self::clearDefaultAddress($userId);
            }
            
            $sql = "INSERT INTO user_address (user_id, full_address, shipping_phone, is_default) 
                    VALUES (:userId, :address, :phone, :isDefault)";
            
            $stmt = self::db()->prepare($sql);
            $stmt->execute([
                ':userId' => $userId, 
                ':address' => $address, 
                ':phone' => $phone, 
                ':isDefault' => $isDefault ? 1 : 0
            ]);
            
            return (int)self::db()->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating address: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Đặt địa chỉ thành mặc định
     * @param int $id ID địa chỉ
     * @param int $userId ID người dùng
     * @return bool True nếu thành công
     */
    public static function setDefaultAddress(int $id, int $userId): bool
    {
        try {
            self::clearDefaultAddress($userId);
            
            $sql = "UPDATE user_address SET is_default = 1 WHERE id = :id AND user_id = :userId";
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':id' => $id, ':userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Error setting default address: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hàm phụ trợ: Hủy đặt mặc định tất cả địa chỉ cũ
     * @param int $userId ID người dùng
     * @return bool True nếu thành công
     */
    private static function clearDefaultAddress(int $userId): bool
    {
        try {
            $sql = "UPDATE user_address SET is_default = 0 WHERE user_id = :userId";
            $stmt = self::db()->prepare($sql);
            return $stmt->execute([':userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Error clearing default address: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa địa chỉ
     * @param int $id ID địa chỉ
     * @param int $userId ID người dùng
     * @return bool True nếu thành công
     */
    public static function deleteAddress(int $id, int $userId): bool
    {
        try {
            $sql = "DELETE FROM user_address WHERE id = :id AND user_id = :userId";
            $stmt = self::db()->prepare($sql);
            return (bool)$stmt->execute([':id' => $id, ':userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting address: " . $e->getMessage());
            return false;
        }
    }

    // Thêm cho checkout
public static function getAddressByIdAndUser(int $addressId, int $userId): ?array
{
    $pdo = self::db();
    $stmt = $pdo->prepare('
        SELECT id, full_address, shipping_phone, is_default
        FROM user_address              -- ✅ ĐÚNG BẢNG
        WHERE id = :id AND user_id = :user_id
        LIMIT 1
    ');
    $stmt->execute([
        ':id'      => $addressId,
        ':user_id' => $userId,
    ]);

    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ?: null;
}


    // ===================================
    // PHƯƠNG THỨC ADMIN QUẢN LÝ USER
    // ===================================

    /**
     * Tạo user mới từ admin panel
     * @param string $name Tên người dùng
     * @param string $email Email
     * @param string $password Mật khẩu
     * @param string $role Role (user/admin)
     * @param bool $isActive Trạng thái active
     * @return int ID người dùng mới
     */
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

    /**
     * Cập nhật thông tin user từ admin panel
     * @param int $id ID người dùng
     * @param string $name Tên mới
     * @param string $email Email mới
     * @param string $role Role mới
     * @param bool $isActive Trạng thái
     * @param string|null $newPassword Mật khẩu mới (nếu có)
     * @return bool True nếu thành công
     */
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

    /**
     * Lấy danh sách user với phân trang và lọc (cho admin)
     * @param array $filters Điều kiện lọc
     * @param int $page Trang hiện tại
     * @param int $perPage Số item mỗi trang
     * @return array Dữ liệu phân trang
     */
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

    // ===================================
    // KHÔI PHỤC MẬT KHẨU
    // ===================================

    /**
     * Tạo token khôi phục mật khẩu (hết hạn 60 phút).
     */
    public static function createResetToken(int $userId): ?string
    {
        try {
            $token = bin2hex(random_bytes(32));
            $sql = "INSERT INTO password_resets (user_id, token, expires_at)
                    VALUES (:uid, :token, :exp)";
            $st = self::db()->prepare($sql);
            $st->execute([
                ':uid'   => $userId,
                ':token' => $token,
                ':exp'   => date('Y-m-d H:i:s', time() + 3600), // 60 phút
            ]);
            return $token;
        } catch (\Throwable $e) {
            error_log("Error createResetToken: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy token hợp lệ (chưa dùng, chưa hết hạn).
     */
    public static function findResetByToken(string $token): ?array
    {
        $sql = "
            SELECT pr.*, u.id AS user_id, u.email, u.name, u.role, u.is_active
            FROM password_resets pr
            JOIN users u ON u.id = pr.user_id
            WHERE pr.token = :token
              AND pr.used_at IS NULL
              AND pr.expires_at >= NOW()
            LIMIT 1
        ";
        $st = self::db()->prepare($sql);
        $st->execute([':token' => $token]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Đánh dấu token đã dùng.
     */
    public static function markResetUsed(string $token): void
    {
        $sql = "UPDATE password_resets SET used_at = NOW() WHERE token = :token";
        self::db()->prepare($sql)->execute([':token' => $token]);
    }
}
