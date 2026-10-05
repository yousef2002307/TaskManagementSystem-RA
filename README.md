# TaskManager

A clean, secure task management system built with vanilla PHP 8, MySQL, and no external frameworks.

---

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- A local server (Laragon, XAMPP, WAMP, or PHP's built-in server)

---

## Setup Instructions

### 1. Place the project

```
d:/laragon/www/rawatask/
```

### 2. Install dependencies

```bash
composer install
```

### 3. Create the database

**Option 1 (Recommended): Import `ticketsystemdb.sql` from the root folder**

- **Via phpMyAdmin**:
  1. Open phpMyAdmin and create a database named `ticketsystemdb`.
  2. Select `ticketsystemdb`, go to the **Import** tab, choose `ticketsystemdb.sql` from the root directory, and click **Import**.
- **Via MySQL CLI**:
  ```bash
  mysql -u root -e "CREATE DATABASE IF NOT EXISTS ticketsystemdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  mysql -u root -p ticketsystemdb < ticketsystemdb.sql
  ```

**Option 2: Run `database/schema.sql`**

```bash
mysql -u root -p < database/schema.sql
```
Or paste the contents of `database/schema.sql` directly into phpMyAdmin's SQL tab.

Both options create the `ticketsystemdb` database, `users` and `tasks` tables, and pre-populate the two test users and sample tasks.

### 4. Configure the database connection

Edit `config/database.php` if your MySQL credentials differ:

```php
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_NAME') || define('DB_NAME', 'ticketsystemdb');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');  // your password
```

### 5. Run the app

With Laragon:

```
http://localhost/rawatask/
```

Or PHP's built-in server:

```bash
php -S localhost:8000 -t d:/laragon/www/rawatask
```

---

## Test Credentials

| Name / Role | Email | Password |
|---|---|---|
| john (Admin One) | admin1@example.com | password123 |
| Admin Two | admin2@example.com | password123 |

> Each user can only see, edit, and delete their own tasks.

---

## Features

- **Authentication** — Login, logout, session handling with CSRF protection
- **Task CRUD** — Create, edit, delete tasks with confirmation
- **Dashboard** — Stats bar (Total / Pending / Completed) + full task table
- **Search & Filter** — Live client-side filtering by title, status, and priority (no page reload)
- **fetch() delete** — Task deletion with confirmation modal via fetch() and automatic reload to refresh dashboard stats
- **Dual validation** — JS validates before submit; PHP re-validates on every POST independently
- **Ownership guard** — Every DB query includes `AND user_id = ?`; users cannot access each other's tasks

---

## Running the Tests

```bash
vendor/bin/phpunit --testdox
```

Tests use a separate `ticketsystemdb_test` database that is created and wiped automatically on each run.

### Test Coverage (36 tests, 61 assertions)

| Suite | File | What it tests |
|-------|------|---------------|
| Unit | `tests/Unit/CSRFTest.php` | Token generation, consistency across multiple forms in session, valid & invalid checks |
| Unit | `tests/Unit/TaskValidatorTest.php` | Title required/max-length, valid priority & status values, date format, multiple errors |
| Integration | `tests/Integration/UserModelTest.php` | create, findByEmail, findById, password hashing, no password leak on findById |
| Integration | `tests/Integration/TaskModelTest.php` | CRUD, ownership guards on find/update/delete, stats counts + user isolation, filters |

---

## Project Structure

```
rawatask/
├── .gitignore
├── composer.json
├── composer.lock
├── index.php                        ← Front controller / router
├── phpunit.xml
├── ticketsystemdb.sql               ← Database export (root)
├── config/
│   └── database.php                 ← PDO singleton
├── src/
│   ├── Core/
│   │   ├── Auth.php                 ← Session lifecycle
│   │   └── CSRF.php                 ← Token generate & validate
│   ├── Models/
│   │   ├── User.php                 ← User DB queries
│   │   └── Task.php                 ← Task CRUD + ownership guard
│   ├── Validators/
│   │   └── TaskValidator.php        ← Reusable backend validation rules
│   └── Controllers/
│       ├── AuthController.php       ← Login / logout
│       └── TaskController.php       ← Task CRUD flow
├── views/
│   ├── layout.php                   ← Shared HTML wrapper
│   ├── auth/login.php
│   └── tasks/
│       ├── dashboard.php            ← Stats + task table
│       └── form.php                 ← Shared create/edit form
├── public/
│   ├── css/style.css                ← Design system (CSS variables, responsive)
│   └── js/app.js                    ← Form validation, live filter, fetch() delete
├── database/
│   └── schema.sql                   ← DDL + seed data
└── tests/
    ├── bootstrap.php
    ├── Unit/
    │   ├── CSRFTest.php
    │   └── TaskValidatorTest.php
    └── Integration/
        ├── UserModelTest.php
        └── TaskModelTest.php
```

---

## Security

| Practice | Implementation |
|---|---|
| SQL injection | PDO prepared statements throughout |
| Password storage | `password_hash(PASSWORD_DEFAULT)` / `password_verify()` |
| CSRF | Token on every POST form, validated before any action |
| XSS | `htmlspecialchars()` on all output |
| Session fixation | `session_regenerate_id(true)` on login |
| Session cookies | `httponly`, `samesite=Lax` |
| Authorization | Every task query includes `AND user_id = ?` |
| Backend validation | Validated in `TaskValidator` before any DB write |
