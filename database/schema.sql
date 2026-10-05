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
INSERT INTO users (name, email, password) VALUES
    ('Alice Admin',   'alice@example.com', '$2y$12$YHb6MnqmxYWlMWV9w.lGOeY8S6UH3Z1jHXSmJX2fWNdRAFnbMfFzu'),
    ('Bob Standard',  'bob@example.com',   '$2y$12$YHb6MnqmxYWlMWV9w.lGOeY8S6UH3Z1jHXSmJX2fWNdRAFnbMfFzu');

-- Seed: tasks for Alice (user_id = 1)
INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES
    (1, 'Set up project repo',    'Initialize Git repository and base structure.', 'high',   'completed',  '2026-09-01'),
    (1, 'Design database schema', 'Create ERD and write migration SQL.',           'high',   'completed',  '2026-09-05'),
    (1, 'Build auth module',      'Login, logout, session handling.',              'high',   'in_progress','2026-10-10'),
    (1, 'Write unit tests',       'PHPUnit tests for models and auth.',            'medium', 'pending',    '2026-10-20'),
    (1, 'Deploy to staging',      'Push to staging server and smoke-test.',        'low',    'pending',    '2026-11-01');

-- Seed: tasks for Bob (user_id = 2)
INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES
    (2, 'Review requirements',    'Read and annotate the project spec.',           'medium', 'completed',  '2026-09-10'),
    (2, 'Create wireframes',      'Sketch UI flow for login and dashboard.',       'medium', 'in_progress','2026-10-08'),
    (2, 'Implement task list',    'CRUD operations for tasks.',                    'high',   'pending',    '2026-10-15'),
    (2, 'Add filtering',          'Filter tasks by status and priority.',          'low',    'pending',    '2026-10-22');
