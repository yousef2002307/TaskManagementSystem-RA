# TaskManager

A simple, clean task management system built with vanilla PHP 8, MySQL, and no external frameworks.

---

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- A local server (Laragon, XAMPP, WAMP, or PHP's built-in server)

---

## Setup Instructions

### 1. Clone / place the project

```
d:/laragon/www/rawatask/
```

### 2. Create the database

Open your MySQL client (phpMyAdmin, TablePlus, or CLI) and run:

```bash
mysql -u root -p < database/schema.sql
```

Or paste the contents of `database/schema.sql` directly into phpMyAdmin's SQL tab.

This will:
- Create the `ticketsystemdb` database
- Create the `users` and `tasks` tables
- Insert two test users and sample tasks

### 3. Configure the database connection

Edit `config/database.php` and update the constants if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ticketsystemdb');
define('DB_USER', 'root');
define('DB_PASS', '');   // your MySQL password
```

### 4. Access the app

With Laragon running, visit:

```
http://localhost/rawatask/
```

Or use PHP's built-in server:

```bash
php -S localhost:8000 -t d:/laragon/www/rawatask
```

Then visit `http://localhost:8000/`

---

## Test Credentials

| User       | Email                  | Password      |
|------------|------------------------|---------------|
| Admin One  | admin1@example.com     | password123   |
| Admin Two  | admin2@example.com     | password123   |

> Each user can only see, edit, and delete their own tasks.

---

## Project Structure

```
rawatask/
├── index.php                    ← Front controller / router
├── config/
│   └── database.php             ← PDO connection
├── src/
│   ├── Core/
│   │   ├── Auth.php             ← Session management
│   │   └── CSRF.php             ← CSRF token generation & validation
│   ├── Models/
│   │   ├── User.php             ← User DB queries
│   │   └── Task.php             ← Task CRUD + ownership guard
│   └── Controllers/
│       ├── AuthController.php   ← Login / logout flow
│       └── TaskController.php   ← Task CRUD flow
├── views/
│   ├── layout.php               ← Shared HTML wrapper
│   ├── auth/login.php
│   └── tasks/
│       ├── dashboard.php
│       └── form.php             ← Shared create/edit form
├── public/
│   ├── css/style.css
│   └── js/app.js
└── database/
    └── schema.sql
```

---

## Security Features

- PDO prepared statements (no raw SQL concatenation)
- Passwords hashed with `password_hash(PASSWORD_DEFAULT)`
- CSRF token on every POST form
- `session_regenerate_id(true)` on login
- `httponly` session cookie flag
- Ownership guard: every task query includes `AND user_id = ?`
- All output escaped with `htmlspecialchars()`
- Server-side validation on all inputs
