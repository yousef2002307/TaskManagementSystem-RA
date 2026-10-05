<?php

declare(strict_types=1);

class CSRF
{
    private const TOKEN_KEY = 'csrf_token';

    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_KEY] = $token;
        return $token;
    }

    public static function validate(string $token): bool
    {
        $stored = $_SESSION[self::TOKEN_KEY] ?? '';
        unset($_SESSION[self::TOKEN_KEY]);
        return hash_equals($stored, $token);
    }

    /** Returns a ready-to-embed hidden input field. */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
