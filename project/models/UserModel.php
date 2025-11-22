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

    public static function findById(int $id): ? array {
        $sql = "SELECT id, name, email, role, is_active, created_at
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


    public static function emailExistsForOther(string $email, int $selfId): bool
    {
        $sql = "SELECT id FROM users WHERE email=:email AND id<>:selfId LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':email'=>$email, ':selfId'=>$selfId]);
        return (bool)$st->fetch();
    }
}