<?php
$pageTitle       = 'Dashboard — TaskManager';
$metaDescription = 'View and manage all your tasks.';

$statusLabels   = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'];
$priorityLabels = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];

ob_start();
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Stats -->
<section class="stats-bar" aria-label="Task statistics">
    <div class="stat-card">
        <span class="stat-number"><?= (int) $stats['total'] ?></span>
        <span class="stat-label">Total Tasks</span>
    </div>
    <div class="stat-card stat-pending">
        <span class="stat-number"><?= (int) $stats['pending'] ?></span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-card stat-done">
        <span class="stat-number"><?= (int) $stats['completed'] ?></span>
        <span class="stat-label">Completed</span>
    </div>
</section>

<!-- Search & Filters (handled entirely by JS) -->
<section class="filters" aria-label="Search and filter tasks">
    <div class="filter-row">
        <input
            type="text"
            id="search-input"
            class="form-control search-input"
            placeholder="Search by title…"
            aria-label="Search tasks by title"
        >

        <select id="filter-status" class="form-control filter-select" aria-label="Filter by status">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>

        <select id="filter-priority" class="form-control filter-select" aria-label="Filter by priority">
            <option value="">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>

        <button id="filter-clear" class="btn btn-ghost" style="display:none" aria-label="Clear all filters">
            Clear
        </button>
    </div>
</section>

<!-- Task Table -->
<section class="task-section" aria-label="Tasks list">
    <?php if (empty($tasks)): ?>
        <div class="empty-state" id="empty-state">
            <p>No tasks yet. <a href="index.php?action=create">Create your first task →</a></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="task-table" id="task-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                    <tr
                        data-title="<?= htmlspecialchars(mb_strtolower($task['title'])) ?>"
                        data-status="<?= htmlspecialchars($task['status']) ?>"
                        data-priority="<?= htmlspecialchars($task['priority']) ?>"
                    >
                        <td class="task-title"><?= htmlspecialchars($task['title']) ?></td>
                        <td class="task-desc">
                            <?= htmlspecialchars(mb_strimwidth($task['description'] ?? '', 0, 80, '…')) ?>
                        </td>
                        <td>
                            <span class="badge badge-priority-<?= $task['priority'] ?>">
                                <?= $priorityLabels[$task['priority']] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-status-<?= str_replace('_', '-', $task['status']) ?>">
                                <?= $statusLabels[$task['status']] ?>
                            </span>
                        </td>
                        <td><?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '—' ?></td>
                        <td class="task-actions">
                            <a href="index.php?action=edit&id=<?= $task['id'] ?>"
                               class="btn btn-sm btn-ghost">Edit</a>

                            <form method="POST" action="index.php?action=delete" class="delete-form">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Shown by JS when all rows are filtered out -->
                    <tr id="no-results-row" style="display:none">
                        <td colspan="6" class="no-results-cell">No tasks match your search.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
