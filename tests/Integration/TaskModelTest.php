<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    private int $userId;

    protected function setUp(): void
    {
        get_db()->exec('DELETE FROM tasks');
        get_db()->exec('DELETE FROM users');
        get_db()->exec('ALTER TABLE tasks AUTO_INCREMENT = 1');
        get_db()->exec('ALTER TABLE users AUTO_INCREMENT = 1');

        User::create('Task User', 'taskuser@example.com', 'pass');
        $user = User::findByEmail('taskuser@example.com');
        $this->userId = (int) $user['id'];
    }

    // ── Create & Read ─────────────────────────────────────────────────────────

    public function test_create_and_retrieve_task(): void
    {
        Task::create($this->userId, [
            'title'       => 'Write tests',
            'description' => 'Cover all edge cases.',
            'priority'    => 'high',
            'status'      => 'pending',
            'due_date'    => '2026-12-01',
        ]);

        $tasks = Task::allForUser($this->userId);

        $this->assertCount(1, $tasks);
        $this->assertSame('Write tests', $tasks[0]['title']);
        $this->assertSame('high', $tasks[0]['priority']);
        $this->assertSame('pending', $tasks[0]['status']);
    }

    public function test_find_returns_task_for_correct_owner(): void
    {
        Task::create($this->userId, $this->taskData());
        $all  = Task::allForUser($this->userId);
        $task = Task::find((int) $all[0]['id'], $this->userId);

        $this->assertIsArray($task);
        $this->assertSame('Sample Task', $task['title']);
    }

    public function test_find_returns_false_for_wrong_owner(): void
    {
        Task::create($this->userId, $this->taskData());
        $all = Task::allForUser($this->userId);

        $result = Task::find((int) $all[0]['id'], userId: 9999);

        $this->assertFalse($result);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_update_changes_task_fields(): void
    {
        Task::create($this->userId, $this->taskData());
        $id = (int) Task::allForUser($this->userId)[0]['id'];

        Task::update($id, $this->userId, [
            'title'       => 'Updated Title',
            'description' => 'Updated description.',
            'priority'    => 'low',
            'status'      => 'completed',
            'due_date'    => '2026-11-30',
        ]);

        $updated = Task::find($id, $this->userId);

        $this->assertSame('Updated Title', $updated['title']);
        $this->assertSame('low', $updated['priority']);
        $this->assertSame('completed', $updated['status']);
    }

    public function test_update_with_wrong_owner_does_nothing(): void
    {
        Task::create($this->userId, $this->taskData());
        $id = (int) Task::allForUser($this->userId)[0]['id'];

        Task::update($id, userId: 9999, data: [
            'title'       => 'Hijacked',
            'description' => '',
            'priority'    => 'low',
            'status'      => 'completed',
            'due_date'    => '',
        ]);

        $unchanged = Task::find($id, $this->userId);
        $this->assertSame('Sample Task', $unchanged['title']);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_delete_removes_task(): void
    {
        Task::create($this->userId, $this->taskData());
        $id = (int) Task::allForUser($this->userId)[0]['id'];

        Task::delete($id, $this->userId);

        $this->assertFalse(Task::find($id, $this->userId));
        $this->assertEmpty(Task::allForUser($this->userId));
    }

    public function test_delete_with_wrong_owner_does_nothing(): void
    {
        Task::create($this->userId, $this->taskData());
        $id = (int) Task::allForUser($this->userId)[0]['id'];

        Task::delete($id, userId: 9999);

        $this->assertNotFalse(Task::find($id, $this->userId));
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    public function test_stats_returns_correct_counts(): void
    {
        Task::create($this->userId, $this->taskData(['status' => 'pending']));
        Task::create($this->userId, $this->taskData(['status' => 'pending']));
        Task::create($this->userId, $this->taskData(['status' => 'completed']));
        Task::create($this->userId, $this->taskData(['status' => 'in_progress']));

        $stats = Task::stats($this->userId);

        $this->assertSame(4, (int) $stats['total']);
        $this->assertSame(2, (int) $stats['pending']);
        $this->assertSame(1, (int) $stats['completed']);
    }

    public function test_stats_are_isolated_per_user(): void
    {
        User::create('Other', 'other@example.com', 'pass');
        $other = User::findByEmail('other@example.com');
        $otherId = (int) $other['id'];

        Task::create($this->userId, $this->taskData());
        Task::create($otherId,      $this->taskData());
        Task::create($otherId,      $this->taskData());

        $stats = Task::stats($this->userId);

        $this->assertSame(1, (int) $stats['total']);
    }

    // ── Filters ───────────────────────────────────────────────────────────────

    public function test_filter_by_status_returns_matching_tasks_only(): void
    {
        Task::create($this->userId, $this->taskData(['status' => 'pending']));
        Task::create($this->userId, $this->taskData(['status' => 'completed']));

        $pending = Task::allForUser($this->userId, ['status' => 'pending']);

        $this->assertCount(1, $pending);
        $this->assertSame('pending', $pending[0]['status']);
    }

    public function test_filter_by_priority_returns_matching_tasks_only(): void
    {
        Task::create($this->userId, $this->taskData(['priority' => 'high']));
        Task::create($this->userId, $this->taskData(['priority' => 'low']));

        $high = Task::allForUser($this->userId, ['priority' => 'high']);

        $this->assertCount(1, $high);
        $this->assertSame('high', $high[0]['priority']);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function taskData(array $overrides = []): array
    {
        return array_merge([
            'title'       => 'Sample Task',
            'description' => 'A test task.',
            'priority'    => 'medium',
            'status'      => 'pending',
            'due_date'    => '2026-12-01',
        ], $overrides);
    }
}
