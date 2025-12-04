<?php
// project/models/admin/AdminCategoryModel.php
require_once __DIR__ . '/../BaseModel.php';

final class AdminCategoryModel extends BaseModel
{
    public static function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $where  = [];
        $params = [];

        if ($filters['keyword'] !== '') {
            $where[] = 'name LIKE :kw';
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }

        if ($filters['status'] !== '') {
            $where[] = 'is_active = :active';
            $params[':active'] = (int)$filters['status'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sqlCount = "SELECT COUNT(*) FROM categories {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT c.*, parent.name AS parent_name
            FROM categories c
            LEFT JOIN categories parent ON parent.id = c.parent_id
            {$whereSql}
            ORDER BY c.id DESC
            LIMIT :limit OFFSET :offset
        ";
        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $st->execute();

        return [
            'items'     => $st->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function all(): array
    {
        $sql = "SELECT id, name, slug, parent_id, is_active 
                FROM categories ORDER BY name ASC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $slug, ?int $parentId, bool $isActive): int
    {
        $sql = "INSERT INTO categories (name, slug, parent_id, is_active)
                VALUES (:name, :slug, :parent_id, :active)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':name'      => $name,
            ':slug'      => $slug,
            ':parent_id' => $parentId ?: null,
            ':active'    => $isActive ? 1 : 0,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(
        int $id,
        string $name,
        string $slug,
        ?int $parentId,
        bool $isActive
    ): bool {
        $sql = "UPDATE categories
                SET name=:name, slug=:slug, parent_id=:parent_id, is_active=:active
                WHERE id=:id";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'        => $id,
            ':name'      => $name,
            ':slug'      => $slug,
            ':parent_id' => $parentId ?: null,
            ':active'    => $isActive ? 1 : 0,
        ]);
    }

    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM categories WHERE id=:id";
        $st = self::db()->prepare($sql);
        return $st->execute([':id' => $id]);
    }
}
