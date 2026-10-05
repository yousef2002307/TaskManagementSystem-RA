<?php

declare(strict_types=1);

class User
{
    public static function findByEmail(string $email): array|false
    {
        $stmt = get_db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function findById(int $id): array|false
    {
        $stmt = get_db()->prepare('SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(string $name, string $email, string $password): void
    {
        $stmt = get_db()->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }
}
