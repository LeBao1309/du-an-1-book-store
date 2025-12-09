<?php
require_once __DIR__ . '/../BaseModel.php';

final class AdminAuthorModel extends BaseModel
{
    public static function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $where  = ['is_deleted = :deleted'];
        $params = [':deleted' => (int)($filters['deleted'] ?? 0)];

        if (($filters['keyword'] ?? '') !== '') {
            $where[]       = 'name LIKE :kw';
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }

        if (($filters['status'] ?? '') !== '') {
            $where[]          = 'is_active = :active';
            $params[':active'] = (int)$filters['status'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $sqlCount = "SELECT COUNT(*) FROM authors {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int) $st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT id, name, slug, is_active
            FROM authors
            {$whereSql}
            ORDER BY id DESC
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

    public static function allActive(): array
    {
        $sql = "SELECT id, name, slug FROM authors WHERE is_deleted = 0 AND is_active = 1 ORDER BY name ASC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM authors WHERE id = :id AND is_deleted = 0 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findDeleted(int $id): ?array
    {
        $sql = "SELECT * FROM authors WHERE id = :id AND is_deleted = 1 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function isSlugExist(string $slug, int $ignoreId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM authors WHERE slug = :slug AND is_deleted = 0 AND id != :id";
        $st  = self::db()->prepare($sql);
        $st->execute([
            ':slug' => $slug,
            ':id'   => $ignoreId,
        ]);
        return (bool)$st->fetchColumn();
    }

    public static function create(string $name, string $slug, bool $isActive = true): int
    {
        $sql = "INSERT INTO authors (name, slug, is_active)
                VALUES (:name, :slug, :active)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':name'   => $name,
            ':slug'   => $slug,
            ':active' => $isActive ? 1 : 0,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, string $name, string $slug, bool $isActive = true): bool
    {
        $sql = "UPDATE authors
                SET name = :name, slug = :slug, is_active = :active
                WHERE id = :id AND is_deleted = 0";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'     => $id,
            ':name'   => $name,
            ':slug'   => $slug,
            ':active' => $isActive ? 1 : 0,
        ]);
    }

    public static function softDelete(int $id): bool
    {
        $sql = "UPDATE authors
                SET is_deleted = 1, deleted_at = NOW(), is_active = 0
                WHERE id = :id AND is_deleted = 0";
        $st  = self::db()->prepare($sql);
        return $st->execute([':id' => $id]);
    }

    public static function restore(int $id): bool
    {
        $sql = "UPDATE authors
                SET is_deleted = 0, deleted_at = NULL
                WHERE id = :id AND is_deleted = 1";
        $st  = self::db()->prepare($sql);
        return $st->execute([':id' => $id]);
    }

    public static function countUsedInBooks(int $authorId): int
    {
        $sql = "SELECT COUNT(DISTINCT book_id) FROM book_authors WHERE author_id = :id";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $authorId]);
        return (int)$st->fetchColumn();
    }
}
