<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Task Management System') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'TaskManager') ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php if (Auth::isLoggedIn()): ?>
<header class="site-header">
    <div class="container header-inner">
        <span class="logo">&#10003; TaskManager</span>
        <nav class="header-nav">
            <span class="welcome-text">Welcome, <?= htmlspecialchars(Auth::user()['name']) ?></span>
            <a href="index.php?action=dashboard" class="btn btn-ghost">Dashboard</a>
            <a href="index.php?action=create" class="btn btn-primary">+ New Task</a>
            <form method="POST" action="index.php?action=logout" style="display:inline">
                <?= CSRF::field() ?>
                <button type="submit" class="btn btn-ghost btn-logout">Logout</button>
            </form>
        </nav>
    </div>
</header>
<?php endif; ?>

<main class="main-content">
    <div class="container">
        <?= $content ?? '' ?>
    </div>
</main>

<script src="public/js/app.js"></script>
</body>
</html>
