<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class Supplier
{
    public static function all(): array
    {
        return Database::pdo()->query("
            SELECT s.*,
                   COUNT(m.medicine_id)        AS medicine_count,
                   COALESCE(SUM(m.stock), 0)   AS total_stock
            FROM suppliers s
            LEFT JOIN medicines m ON m.supplier_id = s.supplier_id
            GROUP BY s.supplier_id
            ORDER BY s.name ASC
        ")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM suppliers WHERE supplier_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function options(): array
    {
        return Database::pdo()
            ->query('SELECT supplier_id, name FROM suppliers ORDER BY name')
            ->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO suppliers (name, phone, email, address)
             VALUES (:name, :phone, :email, :address)'
        );
        $stmt->execute($data);

        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE suppliers
             SET name = :name, phone = :phone, email = :email, address = :address
             WHERE supplier_id = :id'
        );
        $stmt->execute($data + [':id' => $id]);

        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::pdo()->prepare('DELETE FROM suppliers WHERE supplier_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount();
    }
}
