<?php

declare(strict_types=1);

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../../views/auth/login.php';
    }

    public function login(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Invalid form submission. Please try again.';
            header('Location: index.php');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Email and password are required.';
            header('Location: index.php');
            exit;
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Invalid email or password.';
            header('Location: index.php');
            exit;
        }

        Auth::login($user);
        header('Location: index.php?action=dashboard');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: index.php');
        exit;
    }
}
