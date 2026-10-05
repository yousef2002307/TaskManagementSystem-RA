<?php

declare(strict_types=1);

class Task
{
    public static function allForUser(int $userId, array $filters = []): array
    {
        $sql    = 'SELECT * FROM tasks WHERE user_id = ?';
        $params = [$userId];

        if (!empty($filters['status'])) {
            $sql     .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql     .= ' AND priority = ?';
            $params[] = $filters['priority'];
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = get_db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Returns false when the task doesn't exist or belongs to another user. */
    public static function find(int $id, int $userId): array|false
    {
        $stmt = get_db()->prepare(
            'SELECT * FROM tasks WHERE id = ? AND user_id = ? LIMIT 1'
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create(int $userId, array $data): void
    {
        $stmt = get_db()->prepare(
            'INSERT INTO tasks (user_id, title, description, priority, status, due_date)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $data['title'],
            $data['description'] ?? null,
            $data['priority'],
            $data['status'],
            $data['due_date'] ?: null,
        ]);
    }

    public static function update(int $id, int $userId, array $data): void
    {
        $stmt = get_db()->prepare(
            'UPDATE tasks
             SET title = ?, description = ?, priority = ?, status = ?, due_date = ?
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['priority'],
            $data['status'],
            $data['due_date'] ?: null,
            $id,
            $userId,
        ]);
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = get_db()->prepare(
            'DELETE FROM tasks WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$id, $userId]);
    }

    public static function stats(int $userId): array
    {
        $stmt = get_db()->prepare(
            'SELECT
                COUNT(*)                                        AS total,
                SUM(status = "pending")                         AS pending,
                SUM(status = "in_progress")                     AS in_progress,
                SUM(status = "completed")                       AS completed
             FROM tasks WHERE user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
