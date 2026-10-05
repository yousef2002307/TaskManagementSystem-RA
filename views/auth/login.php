<?php
$pageTitle       = 'Login — TaskManager';
$metaDescription = 'Sign in to your TaskManager account.';

ob_start();
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">&#10003; TaskManager</h1>
        <p class="auth-subtitle">Sign in to manage your tasks</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login" id="login-form" novalidate>
            <?= CSRF::field() ?>

            <div class="form-group">
                <label for="email">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
