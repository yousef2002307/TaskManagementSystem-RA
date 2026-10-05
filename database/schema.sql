CREATE DATABASE IF NOT EXISTS ticketsystemdb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ticketsystemdb;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT,
    priority    ENUM('low', 'medium', 'high')                    DEFAULT 'medium',
    status      ENUM('pending', 'in_progress', 'completed')      DEFAULT 'pending',
    due_date    DATE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed: two test users (password = "password123" for both)
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password) VALUES
    ('john', 'admin1@example.com', '$2y$10$0QYHm9XmKuFILB0O/T0BdeSOet7e5QeOyd5wAmvv9x/FOeZ9rth4a'),
    ('Admin Two', 'admin2@example.com', '$2y$10$0QYHm9XmKuFILB0O/T0BdeSOet7e5QeOyd5wAmvv9x/FOeZ9rth4a');

-- Seed: tasks for Admin One (user_id = 1) — 10 tasks: 6 completed, 4 pending
INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES
    (1, 'Set up project repo',       'Initialize Git repository and base structure.',        'high',   'completed', '2026-09-01'),
    (1, 'Design database schema',    'Create ERD and write migration SQL.',                  'high',   'completed', '2026-09-05'),
    (1, 'Build auth module',         'Login, logout, and session handling.',                 'high',   'completed', '2026-09-12'),
    (1, 'Create task model',         'CRUD operations with ownership guards.',               'medium', 'completed', '2026-09-18'),
    (1, 'Build dashboard view',      'Stats bar and task table.',                            'medium', 'completed', '2026-09-25'),
    (1, 'Style the UI',              'CSS design system, responsive layout.',                'low',    'completed', '2026-09-30'),
    (1, 'Write unit tests',          'PHPUnit tests for models and auth.',                   'high',   'pending',   '2026-10-20'),
    (1, 'Add input validation',      'Server-side validation for all POST handlers.',        'medium', 'pending',   '2026-10-22'),
    (1, 'Security audit',            'Review prepared statements and XSS escaping.',         'high',   'pending',   '2026-10-28'),
    (1, 'Deploy to staging',         'Push to staging server and run smoke tests.',          'low',    'pending',   '2026-11-01');

-- Seed: tasks for Admin Two (user_id = 2) — 6 tasks: 4 completed, 2 pending
INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES
    (2, 'Review requirements',       'Read and annotate the project specification.',         'medium', 'completed', '2026-09-10'),
    (2, 'Create wireframes',         'Sketch UI flow for login and dashboard.',              'medium', 'completed', '2026-09-15'),
    (2, 'Set up local environment',  'Configure Laragon, PHP, and MySQL.',                   'high',   'completed', '2026-09-18'),
    (2, 'Implement task list',       'CRUD operations for tasks.',                           'high',   'completed', '2026-09-28'),
    (2, 'Add filtering feature',     'Filter tasks by status and priority.',                 'medium', 'pending',   '2026-10-15'),
    (2, 'Write API documentation',   'Document all endpoints and expected responses.',       'low',    'pending',   '2026-10-25');

