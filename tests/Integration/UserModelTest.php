<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    protected function setUp(): void
    {
        get_db()->exec('DELETE FROM tasks');
        get_db()->exec('DELETE FROM users');
        get_db()->exec('ALTER TABLE users AUTO_INCREMENT = 1');
    }

    public function test_create_and_find_by_email(): void
    {
        User::create('Test User', 'test@example.com', 'secret');

        $user = User::findByEmail('test@example.com');

        $this->assertIsArray($user);
        $this->assertSame('Test User', $user['name']);
        $this->assertSame('test@example.com', $user['email']);
    }

    public function test_password_is_hashed_on_create(): void
    {
        User::create('Hash Test', 'hash@example.com', 'plaintext');

        $user = User::findByEmail('hash@example.com');

        $this->assertNotSame('plaintext', $user['password']);
        $this->assertTrue(password_verify('plaintext', $user['password']));
    }

    public function test_find_by_email_returns_false_for_unknown(): void
    {
        $result = User::findByEmail('nobody@example.com');
        $this->assertFalse($result);
    }

    public function test_find_by_id_returns_correct_user(): void
    {
        User::create('ID Test', 'id@example.com', 'pass');
        $created = User::findByEmail('id@example.com');

        $found = User::findById((int) $created['id']);

        $this->assertIsArray($found);
        $this->assertSame('ID Test', $found['name']);
        // password column must not be exposed by findById
        $this->assertArrayNotHasKey('password', $found);
    }

    public function test_find_by_id_returns_false_for_unknown(): void
    {
        $result = User::findById(9999);
        $this->assertFalse($result);
    }
}
