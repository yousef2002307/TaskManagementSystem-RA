<?php
$isEdit          = $task !== null;
$pageTitle       = ($isEdit ? 'Edit Task' : 'New Task') . ' — TaskManager';
$metaDescription = $isEdit ? 'Edit your task.' : 'Create a new task.';

$formAction = $isEdit ? 'index.php?action=update' : 'index.php?action=store';

ob_start();
?>

<div class="form-page">
    <div class="form-card">
        <h1 class="form-heading"><?= $isEdit ? 'Edit Task' : 'New Task' ?></h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= $formAction ?>" id="task-form" novalidate>
            <?= CSRF::field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $task['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="<?= htmlspecialchars($task['title'] ?? '') ?>"
                    maxlength="200"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    rows="4"
                ><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="low"    <?= (($task['priority'] ?? '') === 'low')    ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= (($task['priority'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>Medium</option>
                        <option value="high"   <?= (($task['priority'] ?? '') === 'high')   ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending"     <?= (($task['status'] ?? 'pending') === 'pending')     ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= (($task['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed"   <?= (($task['status'] ?? '') === 'completed')   ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input
                    type="date"
                    id="due_date"
                    name="due_date"
                    class="form-control"
                    value="<?= htmlspecialchars($task['due_date'] ?? '') ?>"
                >
            </div>

            <div class="form-actions">
                <a href="index.php?action=dashboard" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Task' : 'Create Task' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
