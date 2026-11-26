<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

final class Medicine
{
    /**
     * Shared SELECT with the computed stock status used across the app.
     */
    private static function selectSql(): string
    {
        return "
            SELECT m.*, s.name AS supplier_name,
                   CASE
                       WHEN m.expiry_date < CURDATE() THEN 'expired'
                       WHEN m.stock <= m.reorder_level THEN 'low'
                       WHEN m.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring'
                       ELSE 'ok'
                   END AS status
            FROM medicines m
            LEFT JOIN suppliers s ON s.supplier_id = m.supplier_id
        ";
    }

    public static function all(string $query = '', string $status = ''): array
    {
        $sql    = 'SELECT * FROM (' . self::selectSql() . ') AS t';
        $where  = [];
        $params = [];

        if ($query !== '') {
            $where[]        = '(t.name LIKE :q1 OR t.category LIKE :q2 OR t.supplier_name LIKE :q3)';
            $params[':q1']  = '%' . $query . '%';
            $params[':q2']  = '%' . $query . '%';
            $params[':q3']  = '%' . $query . '%';
        }

        if (in_array($status, ['ok', 'low', 'expiring', 'expired'], true)) {
            $where[]         = 't.status = :status';
            $params[':status'] = $status;
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY FIELD(t.status, 'expired', 'low', 'expiring', 'ok'), t.name";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM (' . self::selectSql() . ') AS t WHERE t.medicine_id = :id'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO medicines (name, category, stock, reorder_level, expiry_date, supplier_id)
             VALUES (:name, :category, :stock, :reorder_level, :expiry_date, :supplier_id)'
        );
        $stmt->execute($data);

        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE medicines
             SET name = :name,
                 category = :category,
                 stock = :stock,
                 reorder_level = :reorder_level,
                 expiry_date = :expiry_date,
                 supplier_id = :supplier_id
             WHERE medicine_id = :id'
        );
        $stmt->execute($data + [':id' => $id]);

        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::pdo()->prepare('DELETE FROM medicines WHERE medicine_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount();
    }

    public static function search(string $query = '', int $limit = 100): array
    {
        $limit = max(1, min(200, $limit));
        $sql   = 'SELECT * FROM (' . self::selectSql() . ') AS t';
        $params = [];

        if ($query !== '') {
            $sql .= ' WHERE t.name LIKE :q1 OR t.category LIKE :q2 OR t.supplier_name LIKE :q3';
            $params[':q1'] = '%' . $query . '%';
            $params[':q2'] = '%' . $query . '%';
            $params[':q3'] = '%' . $query . '%';
        }

        $sql .= " ORDER BY FIELD(t.status, 'expired', 'low', 'expiring', 'ok'), t.name LIMIT {$limit}";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function dashboardStats(): array
    {
        $row = Database::pdo()->query("
            SELECT COUNT(*)                                                               AS total,
                   COALESCE(SUM(expiry_date < CURDATE()), 0)                              AS expired,
                   COALESCE(SUM(stock <= reorder_level), 0)                               AS low,
                   COALESCE(SUM(expiry_date >= CURDATE()
                       AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)), 0)       AS expiring,
                   COALESCE(SUM(stock), 0)                                                AS units
            FROM medicines
        ")->fetch();

        return array_map('intval', $row ?: []);
    }

    public static function lowStock(int $limit = 8): array
    {
        $limit = max(1, min(50, $limit));
        $stmt  = Database::pdo()->query(
            'SELECT * FROM (' . self::selectSql() . ') AS t
             WHERE t.stock <= t.reorder_level
             ORDER BY t.stock ASC, t.name ASC
             LIMIT ' . $limit
        );

        return $stmt->fetchAll();
    }

    public static function expiringSoon(int $days = 30, int $limit = 8): array
    {
        $days  = max(1, min(365, $days));
        $limit = max(1, min(50, $limit));

        $stmt = Database::pdo()->query(
            'SELECT * FROM (' . self::selectSql() . ') AS t
             WHERE t.expiry_date >= CURDATE()
               AND t.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ' . $days . ' DAY)
             ORDER BY t.expiry_date ASC, t.name ASC
             LIMIT ' . $limit
        );

        return $stmt->fetchAll();
    }

    public static function byCategory(): array
    {
        return Database::pdo()->query("
            SELECT category,
                   COUNT(*)              AS total,
                   COALESCE(SUM(stock),0) AS units
            FROM medicines
            GROUP BY category
            ORDER BY total DESC, category ASC
        ")->fetchAll();
    }

    public static function categories(): array
    {
        return Database::pdo()
            ->query('SELECT DISTINCT category FROM medicines ORDER BY category')
            ->fetchAll(PDO::FETCH_COLUMN);
    }
}
