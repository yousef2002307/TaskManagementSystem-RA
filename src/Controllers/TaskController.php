<?php

declare(strict_types=1);

class TaskController
{
    private int $userId;

    public function __construct()
    {
        Auth::requireLogin();
        $this->userId = (int) Auth::user()['id'];
    }

    public function dashboard(): void
    {
        $tasks = Task::allForUser($this->userId);
        $stats = Task::stats($this->userId);

        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        require __DIR__ . '/../../views/tasks/dashboard.php';
    }

    public function create(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $task = null; // new form — no prefill
        require __DIR__ . '/../../views/tasks/form.php';
    }

    public function store(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Invalid form submission.';
            header('Location: index.php?action=create');
            exit;
        }

        $data = $this->extractTaskData();
        $errors = $this->validateTaskData($data);

        if ($errors) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            header('Location: index.php?action=create');
            exit;
        }

        Task::create($this->userId, $data);
        $_SESSION['flash_success'] = 'Task created successfully.';
        header('Location: index.php?action=dashboard');
        exit;
    }

    public function edit(): void
    {
        $task = $this->findOwnedTaskOrAbort((int) ($_GET['id'] ?? 0));

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../../views/tasks/form.php';
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Invalid form submission.';
            header("Location: index.php?action=edit&id={$id}");
            exit;
        }

        $this->findOwnedTaskOrAbort($id); // confirm ownership before update

        $data   = $this->extractTaskData();
        $errors = $this->validateTaskData($data);

        if ($errors) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            header("Location: index.php?action=edit&id={$id}");
            exit;
        }

        Task::update($id, $this->userId, $data);
        $_SESSION['flash_success'] = 'Task updated successfully.';
        header('Location: index.php?action=dashboard');
        exit;
    }

    public function delete(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(false, 'CSRF validation failed.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        Task::delete($id, $this->userId);

        $this->jsonResponse(true, 'Task deleted.');
    }

    // -------------------------------------------------------------------------

    /**
     * Responds with JSON when called via fetch(), otherwise redirects.
     * fetch() requests are identified by the X-Requested-With header.
     */
    private function jsonResponse(bool $success, string $message): never
    {
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $message]);
            exit;
        }

        if ($success) {
            $_SESSION['flash_success'] = $message;
        } else {
            $_SESSION['flash_error'] = $message;
        }
        header('Location: index.php?action=dashboard');
        exit;
    }

    private function findOwnedTaskOrAbort(int $id): array
    {
        $task = Task::find($id, $this->userId);
        if (!$task) {
            http_response_code(403);
            exit('Task not found or access denied.');
        }
        return $task;
    }

    private function extractTaskData(): array
    {
        return [
            'title'       => trim($_POST['title']       ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'priority'    => $_POST['priority']          ?? 'medium',
            'status'      => $_POST['status']            ?? 'pending',
            'due_date'    => $_POST['due_date']          ?? '',
        ];
    }

    private function validateTaskData(array $data): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        } elseif (mb_strlen($data['title']) > 200) {
            $errors[] = 'Title must not exceed 200 characters.';
        }

        if (!in_array($data['priority'], ['low', 'medium', 'high'], true)) {
            $errors[] = 'Invalid priority value.';
        }

        if (!in_array($data['status'], ['pending', 'in_progress', 'completed'], true)) {
            $errors[] = 'Invalid status value.';
        }

        if ($data['due_date'] !== '' && !strtotime($data['due_date'])) {
            $errors[] = 'Invalid due date format.';
        }

        return $errors;
    }
}
