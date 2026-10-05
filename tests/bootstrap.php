<?php

declare(strict_types=1);

// Define test-database constants BEFORE loading config so get_db() connects here
define('DB_HOST',    'localhost');
define('DB_NAME',    'ticketsystemdb_test');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// Load all src classes
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/CSRF.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Task.php';
require_once __DIR__ . '/../src/Validators/TaskValidator.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/TaskController.php';

// Bootstrap the test database schema
$pdo = get_db();
$pdo->exec("DROP TABLE IF EXISTS tasks");
$pdo->exec("DROP TABLE IF EXISTS users");
$pdo->exec("
    CREATE TABLE users (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100)  NOT NULL,
        email      VARCHAR(150)  NOT NULL UNIQUE,
        password   VARCHAR(255)  NOT NULL,
        created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
    )
");
$pdo->exec("
    CREATE TABLE tasks (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT          NOT NULL,
        title       VARCHAR(200) NOT NULL,
        description TEXT,
        priority    ENUM('low','medium','high')               DEFAULT 'medium',
        status      ENUM('pending','in_progress','completed') DEFAULT 'pending',
        due_date    DATE,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");
