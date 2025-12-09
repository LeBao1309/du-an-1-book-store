<?php
// project/models/admin/AdminCategoryModel.php
require_once __DIR__ . '/../BaseModel.php';

final class AdminCategoryModel extends BaseModel
{
    public static function paginate(array $filters, int $page, int $perPage): array
    {
        $where  = ["c.is_deleted = :deleted"];
        $params = [':deleted' => (int)$filters['deleted']];

        if ($filters['keyword'] !== '') {
            $where[] = "c.name LIKE :kw";
            $params[':kw'] = "%" . $filters['keyword'] . "%";
        }

        if ($filters['status'] !== '') {
            $where[] = "c.is_active = :active";
            $params[':active'] = (int)$filters['status'];
        }

        $whereSql = "WHERE " . implode(" AND ", $where);

        /* Count */
        $sqlCount = "SELECT COUNT(*) FROM categories c {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        /* Items */
        $sql = "
            SELECT c.*, parent.name AS parent_name
            FROM categories c
            LEFT JOIN categories parent ON parent.id = c.parent_id
            {$whereSql}
            ORDER BY c.id DESC
            LIMIT :limit OFFSET :offset
        ";
        $st = self::db()->prepare($sql);

        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,   \PDO::PARAM_INT);

        $st->execute();

        return [
            'items'     => $st->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }


    public static function allActive(): array
    {
        $sql = "SELECT id, name, slug FROM categories 
                WHERE is_deleted = 0 AND is_active = 1
                ORDER BY name ASC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function getLevel1(): array
    {
        $sql = "SELECT id, name FROM categories 
                WHERE parent_id IS NULL AND is_deleted = 0 AND is_active = 1
                ORDER BY name ASC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM categories 
                WHERE id = :id AND is_deleted = 0 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findDeleted(int $id): ?array
    {
        $sql = "SELECT * FROM categories 
                WHERE id = :id AND is_deleted = 1 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function isSlugExist(string $slug, ?int $ignore = null): bool
    {
        $sql = "SELECT id FROM categories WHERE slug = :slug AND is_deleted = 0";
        $params = [':slug' => $slug];

        if ($ignore) {
            $sql .= " AND id != :id";
            $params[':id'] = $ignore;
        }

        $st = self::db()->prepare($sql);
        $st->execute($params);
        return (bool)$st->fetch();
    }

    public static function hasChildren(int $id): bool
    {
        $sql = "SELECT id FROM categories 
                WHERE parent_id = :id AND is_deleted = 0 LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        return (bool)$st->fetch();
    }

    public static function hasBooks(int $id): bool
    {
        $sql = "SELECT id FROM books 
                WHERE category_id = :id AND is_deleted = 0 LIMIT 1";
        $st = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        return (bool)$st->fetch();
    }

    public static function create(string $name, string $slug, ?int $parentId, bool $isActive): int
    {
        $sql = "INSERT INTO categories (name, slug, parent_id, is_active)
                VALUES (:name, :slug, :parent_id, :active)";

        $st = self::db()->prepare($sql);
        $st->execute([
            ':name'      => $name,
            ':slug'      => $slug,
            ':parent_id' => $parentId,
            ':active'    => $isActive ? 1 : 0,
        ]);

        return (int)self::db()->lastInsertId();
    }

    public static function update(int $id, string $name, string $slug, ?int $parentId, bool $isActive): bool
    {
        $sql = "UPDATE categories 
                SET name=:name, slug=:slug, parent_id=:parent_id, is_active=:active
                WHERE id=:id AND is_deleted = 0";

        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'        => $id,
            ':name'      => $name,
            ':slug'      => $slug,
            ':parent_id' => $parentId,
            ':active'    => $isActive ? 1 : 0,
        ]);
    }

    public static function softDelete(int $id): bool
    {
        $sql = "UPDATE categories 
                SET is_deleted = 1, deleted_at = NOW()
                WHERE id = :id AND is_deleted = 0";

        return self::db()->prepare($sql)->execute([':id' => $id]);
    }

    public static function restore(int $id): bool
    {
        $sql = "UPDATE categories 
                SET is_deleted = 0, deleted_at = NULL
                WHERE id = :id AND is_deleted = 1";

        return self::db()->prepare($sql)->execute([':id' => $id]);
    }
}
