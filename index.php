<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Core/Auth.php';
require_once __DIR__ . '/src/Core/CSRF.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Task.php';
require_once __DIR__ . '/src/Validators/TaskValidator.php';
require_once __DIR__ . '/src/Controllers/AuthController.php';
require_once __DIR__ . '/src/Controllers/TaskController.php';

Auth::start();

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    match ($action) {
        'login'  => (new AuthController())->login(),
        'logout' => (new AuthController())->logout(),
        'store'  => (new TaskController())->store(),
        'update' => (new TaskController())->update(),
        'delete' => (new TaskController())->delete(),
        default  => (function () { http_response_code(405); exit('Method not allowed.'); })(),
    };
    exit; // never fall through to GET routing on a POST request
}

match ($action) {
    '', 'login' => (new AuthController())->showLogin(),
    'logout'    => (new AuthController())->logout(),
    'dashboard' => (new TaskController())->dashboard(),
    'create'    => (new TaskController())->create(),
    'edit'      => (new TaskController())->edit(),
    default     => (function () { http_response_code(404); exit('Page not found.'); })(),
};
