<?php
require_once __DIR__ . '/../BaseModel.php';

final class AdminPublisherModel extends BaseModel
{
    public static function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $where  = [];
        $params = [];

        if ($filters['keyword'] !== '') {
            $where[]       = 'name LIKE :kw';
            $params[':kw'] = '%' . $filters['keyword'] . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sqlCount = "SELECT COUNT(*) FROM publisher {$whereSql}";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int) $st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT id, name, slug
            FROM publisher
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

    public static function all(): array
    {
        $sql = "SELECT id, name, slug FROM publisher ORDER BY name ASC";
        return self::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM publisher WHERE id = :id LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $slug): int
    {
        $sql = "INSERT INTO publisher (name, slug)
                VALUES (:name, :slug)";
        $st = self::db()->prepare($sql);
        $st->execute([
            ':name' => $name,
            ':slug' => $slug,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, string $name, string $slug): bool
    {
        $sql = "UPDATE publisher
                SET name = :name, slug = :slug
                WHERE id = :id";
        $st = self::db()->prepare($sql);
        return $st->execute([
            ':id'   => $id,
            ':name' => $name,
            ':slug' => $slug,
        ]);
    }

   public static function delete(int $id): bool
   {
      $sql = "DELETE FROM publisher WHERE id = :id";
      $st  = self::db()->prepare($sql);
      return $st->execute([':id' => $id]);
   }

    public static function countUsedInBooks(int $publisherId): int
{
    $sql = "SELECT COUNT(DISTINCT book_id) FROM book_publisher WHERE publisher_id = :id";
    $st  = self::db()->prepare($sql);
    $st->execute([':id' => $publisherId]);
    return (int)$st->fetchColumn();
}

}
