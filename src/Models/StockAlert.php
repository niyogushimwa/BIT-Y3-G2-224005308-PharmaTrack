<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class StockAlert
{
    public static function all(string $status = 'active', string $type = ''): array
    {
        $sql    = "
            SELECT a.*, m.name AS medicine_name, m.stock, m.reorder_level, m.expiry_date
            FROM stock_alerts a
            JOIN medicines m ON m.medicine_id = a.medicine_id
        ";
        $where  = [];
        $params = [];

        if ($status === 'active') {
            $where[] = 'a.resolved = 0';
        } elseif ($status === 'resolved') {
            $where[] = 'a.resolved = 1';
        }

        if (in_array($type, ['low', 'expired'], true)) {
            $where[]        = 'a.alert_type = :type';
            $params[':type'] = $type;
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY a.resolved ASC, a.triggered_at DESC, a.alert_id DESC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM stock_alerts WHERE alert_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function recent(int $limit = 6): array
    {
        $limit = max(1, min(20, $limit));

        return Database::pdo()->query("
            SELECT a.*, m.name AS medicine_name
            FROM stock_alerts a
            JOIN medicines m ON m.medicine_id = a.medicine_id
            ORDER BY a.resolved ASC, a.triggered_at DESC
            LIMIT {$limit}
        ")->fetchAll();
    }

    public static function create(int $medicineId, string $type): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO stock_alerts (medicine_id, alert_type, triggered_at, resolved)
             VALUES (:medicine_id, :alert_type, NOW(), 0)'
        );
        $stmt->execute([':medicine_id' => $medicineId, ':alert_type' => $type]);

        return (int) Database::pdo()->lastInsertId();
    }

    public static function resolve(int $id): int
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE stock_alerts
             SET resolved = 1, resolved_at = NOW()
             WHERE alert_id = :id AND resolved = 0'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::pdo()->prepare('DELETE FROM stock_alerts WHERE alert_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount();
    }

    public static function hasActive(int $medicineId, string $type): bool
    {
        $stmt = Database::pdo()->prepare(
            'SELECT 1 FROM stock_alerts
             WHERE medicine_id = :medicine_id AND alert_type = :type AND resolved = 0
             LIMIT 1'
        );
        $stmt->execute([':medicine_id' => $medicineId, ':type' => $type]);

        return (bool) $stmt->fetchColumn();
    }

    public static function counts(): array
    {
        $row = Database::pdo()->query("
            SELECT COUNT(*)                                          AS active,
                   COALESCE(SUM(alert_type = 'low'), 0)              AS low,
                   COALESCE(SUM(alert_type = 'expired'), 0)          AS expired
            FROM stock_alerts
            WHERE resolved = 0
        ")->fetch();

        return array_map('intval', $row ?: ['active' => 0, 'low' => 0, 'expired' => 0]);
    }

    /**
     * Scan the medicines table and create alerts for conditions that do not
     * already have an active alert. A 24-hour cool-down prevents an alert from
     * immediately reappearing after the pharmacist resolves it.
     */
    public static function sync(): int
    {
        $pdo = Database::pdo();

        $candidates = [
            'low'     => $pdo->query('SELECT medicine_id FROM medicines WHERE stock <= reorder_level')->fetchAll(),
            'expired' => $pdo->query('SELECT medicine_id FROM medicines WHERE expiry_date < CURDATE()')->fetchAll(),
        ];

        $created = 0;

        foreach ($candidates as $type => $rows) {
            foreach ($rows as $row) {
                $medicineId = (int) $row['medicine_id'];

                if (self::hasRecent($medicineId, $type)) {
                    continue;
                }

                self::create($medicineId, $type);
                $created++;
            }
        }

        return $created;
    }

    private static function hasRecent(int $medicineId, string $type): bool
    {
        $stmt = Database::pdo()->prepare(
            'SELECT 1 FROM stock_alerts
             WHERE medicine_id = :medicine_id
               AND alert_type = :type
               AND (resolved = 0 OR triggered_at >= DATE_SUB(NOW(), INTERVAL 1 DAY))
             LIMIT 1'
        );
        $stmt->execute([':medicine_id' => $medicineId, ':type' => $type]);

        return (bool) $stmt->fetchColumn();
    }
}
