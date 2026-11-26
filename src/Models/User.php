<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;

final class User
{
    public static function all(): array
    {
        return Database::pdo()
            ->query('SELECT user_id, username, email, role, created_at FROM users ORDER BY username')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT user_id, username, email, password_hash, role, created_at FROM users WHERE user_id = :id'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function findByLogin(string $login): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1'
        );
        $stmt->execute([':username' => $login, ':email' => $login]);

        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (username, email, password_hash, role)
             VALUES (:username, :email, :password_hash, :role)'
        );
        $stmt->execute($data);

        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): int
    {
        if (!empty($data[':password_hash'])) {
            $sql = 'UPDATE users
                    SET username = :username, email = :email, role = :role, password_hash = :password_hash
                    WHERE user_id = :id';
        } else {
            unset($data[':password_hash']);
            $sql = 'UPDATE users
                    SET username = :username, email = :email, role = :role
                    WHERE user_id = :id';
        }

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($data + [':id' => $id]);

        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::pdo()->prepare('DELETE FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount();
    }

    public static function countAdmins(): int
    {
        return (int) Database::pdo()
            ->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")
            ->fetchColumn();
    }

    public static function usernameTaken(string $username, ?int $exceptId = null): bool
    {
        return self::valueTaken('username', $username, $exceptId);
    }

    public static function emailTaken(string $email, ?int $exceptId = null): bool
    {
        return self::valueTaken('email', $email, $exceptId);
    }

    private static function valueTaken(string $column, string $value, ?int $exceptId): bool
    {
        $sql = "SELECT 1 FROM users WHERE {$column} = :value";
        $params = [':value' => $value];

        if ($exceptId !== null) {
            $sql .= ' AND user_id <> :id';
            $params[':id'] = $exceptId;
        }

        $stmt = Database::pdo()->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }
}
