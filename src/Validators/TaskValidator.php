<?php

declare(strict_types=1);

/**
 * Validates raw task input data.
 * Kept separate from TaskController so it can be unit-tested without HTTP context.
 */
class TaskValidator
{
    private const VALID_PRIORITIES = ['low', 'medium', 'high'];
    private const VALID_STATUSES   = ['pending', 'in_progress', 'completed'];
    private const MAX_TITLE_LENGTH = 200;

    /** @return string[] List of error messages; empty array means valid. */
    public function validate(array $data): array
    {
        $errors = [];

        $errors = array_merge($errors, $this->validateTitle($data['title'] ?? ''));
        $errors = array_merge($errors, $this->validatePriority($data['priority'] ?? ''));
        $errors = array_merge($errors, $this->validateStatus($data['status'] ?? ''));
        $errors = array_merge($errors, $this->validateDueDate($data['due_date'] ?? ''));

        return $errors;
    }

    private function validateTitle(string $title): array
    {
        if (trim($title) === '') {
            return ['Title is required.'];
        }

        if (mb_strlen(trim($title)) > self::MAX_TITLE_LENGTH) {
            return ['Title must not exceed ' . self::MAX_TITLE_LENGTH . ' characters.'];
        }

        return [];
    }

    private function validatePriority(string $priority): array
    {
        if (!in_array($priority, self::VALID_PRIORITIES, true)) {
            return ['Invalid priority value.'];
        }

        return [];
    }

    private function validateStatus(string $status): array
    {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            return ['Invalid status value.'];
        }

        return [];
    }

    private function validateDueDate(string $date): array
    {
        if ($date !== '' && !strtotime($date)) {
            return ['Invalid due date format.'];
        }

        return [];
    }
}
