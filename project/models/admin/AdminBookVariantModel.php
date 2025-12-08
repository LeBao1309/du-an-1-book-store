<?php
require_once __DIR__ . '/../BaseModel.php';

final class AdminBookVariantModel extends BaseModel
{
    public static function paginate(array $filters, int $page, int $perPage): array
    {
        $where  = ["v.is_deleted = :deleted", "b.is_deleted = 0"];
        $params = [':deleted' => (int)$filters['deleted']];

        if ($filters['keyword'] !== '') {
            $where[] = "v.format LIKE :kw";
            $params[':kw'] = "%" . $filters['keyword'] . "%";
        }

        if (!empty($filters['book_id'])) {
            $where[] = "v.book_id = :bid";
            $params[':bid'] = (int)$filters['book_id'];
        }

        if ($filters['status'] !== '') {
            $where[] = "v.is_active = :active";
            $params[':active'] = (int)$filters['status'];
        }

        $whereSQL = "WHERE " . implode(" AND ", $where);

        $sqlCount = "
            SELECT COUNT(*)
            FROM book_variants v
            {$whereSQL}
        ";
        $st = self::db()->prepare($sqlCount);
        $st->execute($params);
        $total = (int)$st->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT v.*, b.title AS book_title
            FROM book_variants v
            JOIN books b ON b.id = v.book_id
            {$whereSQL}
            ORDER BY v.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $st = self::db()->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset,   \PDO::PARAM_INT);

        $st->execute();

        return [
            'items'     => $st->fetchAll(),
            'total'     => $total,
            'page'      => $page,
            'last_page' => max(1, ceil($total / $perPage)),
        ];
    }


    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM book_variants WHERE id=:id AND is_deleted=0 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public static function findDeleted(int $id): ?array
    {
        $sql = "SELECT * FROM book_variants WHERE id=:id AND is_deleted=1 LIMIT 1";
        $st  = self::db()->prepare($sql);
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public static function create(array $d): int
    {
        $sql = "
            INSERT INTO book_variants
            (book_id, format, price, sale_price, stock, is_active)
            VALUES (:book_id, :format, :price, :sale_price, :stock, :active)
        ";

        $st = self::db()->prepare($sql);
        $st->execute([
            ':book_id'    => $d['book_id'],
            ':format'     => $d['format'],
            ':price'      => $d['price'],
            ':sale_price' => $d['sale_price'],
            ':stock'      => $d['stock'],
            ':active'     => $d['is_active'],
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function updateRecord(int $id, array $d): bool
    {
        $sql = "
            UPDATE book_variants
            SET book_id=:book_id, format=:format, price=:price,
                sale_price=:sale_price, stock=:stock, is_active=:active
            WHERE id=:id AND is_deleted=0
        ";

        return self::db()->prepare($sql)->execute([
            ':id'         => $id,
            ':book_id'    => $d['book_id'],
            ':format'     => $d['format'],
            ':price'      => $d['price'],
            ':sale_price' => $d['sale_price'],
            ':stock'      => $d['stock'],
            ':active'     => $d['is_active'],
        ]);
    }

    public static function softDelete(int $id): bool
    {
        $sql = "
            UPDATE book_variants 
            SET is_deleted=1, deleted_at=NOW(), is_active=0
            WHERE id=:id
        ";
        return self::db()->prepare($sql)->execute([':id' => $id]);
    }

    public static function restore(int $id): bool
    {
        $sql = "
            UPDATE book_variants 
            SET is_deleted=0, deleted_at=NULL
            WHERE id=:id
        ";
        return self::db()->prepare($sql)->execute([':id' => $id]);
    }
}
