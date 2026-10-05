<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TaskValidatorTest extends TestCase
{
    private TaskValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new TaskValidator();
    }

    // ── Title ─────────────────────────────────────────────────────────────────

    public function test_valid_title_passes(): void
    {
        $errors = $this->validator->validate($this->baseData(['title' => 'Fix login bug']));
        $this->assertEmpty($errors);
    }

    public function test_empty_title_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['title' => '']));
        $this->assertContains('Title is required.', $errors);
    }

    public function test_whitespace_only_title_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['title' => '   ']));
        $this->assertContains('Title is required.', $errors);
    }

    public function test_title_exceeding_200_chars_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['title' => str_repeat('a', 201)]));
        $this->assertContains('Title must not exceed 200 characters.', $errors);
    }

    public function test_title_of_exactly_200_chars_passes(): void
    {
        $errors = $this->validator->validate($this->baseData(['title' => str_repeat('a', 200)]));
        $this->assertEmpty($errors);
    }

    // ── Priority ──────────────────────────────────────────────────────────────

    public function test_valid_priorities_pass(): void
    {
        foreach (['low', 'medium', 'high'] as $priority) {
            $errors = $this->validator->validate($this->baseData(['priority' => $priority]));
            $this->assertEmpty($errors, "Priority '{$priority}' should be valid.");
        }
    }

    public function test_invalid_priority_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['priority' => 'critical']));
        $this->assertContains('Invalid priority value.', $errors);
    }

    public function test_empty_priority_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['priority' => '']));
        $this->assertContains('Invalid priority value.', $errors);
    }

    // ── Status ────────────────────────────────────────────────────────────────

    public function test_valid_statuses_pass(): void
    {
        foreach (['pending', 'in_progress', 'completed'] as $status) {
            $errors = $this->validator->validate($this->baseData(['status' => $status]));
            $this->assertEmpty($errors, "Status '{$status}' should be valid.");
        }
    }

    public function test_invalid_status_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['status' => 'archived']));
        $this->assertContains('Invalid status value.', $errors);
    }

    // ── Due Date ──────────────────────────────────────────────────────────────

    public function test_valid_date_passes(): void
    {
        $errors = $this->validator->validate($this->baseData(['due_date' => '2026-12-31']));
        $this->assertEmpty($errors);
    }

    public function test_empty_due_date_passes(): void
    {
        $errors = $this->validator->validate($this->baseData(['due_date' => '']));
        $this->assertEmpty($errors);
    }

    public function test_invalid_due_date_fails(): void
    {
        $errors = $this->validator->validate($this->baseData(['due_date' => 'not-a-date']));
        $this->assertContains('Invalid due date format.', $errors);
    }

    // ── Multiple errors ───────────────────────────────────────────────────────

    public function test_multiple_invalid_fields_return_all_errors(): void
    {
        $errors = $this->validator->validate([
            'title'    => '',
            'priority' => 'bad',
            'status'   => 'bad',
            'due_date' => 'bad',
        ]);

        $this->assertCount(4, $errors);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function baseData(array $overrides = []): array
    {
        return array_merge([
            'title'    => 'Default title',
            'priority' => 'medium',
            'status'   => 'pending',
            'due_date' => '',
        ], $overrides);
    }
}
