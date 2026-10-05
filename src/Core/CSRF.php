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

    public static function getToken(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            return self::generate();
        }
        return (string) $_SESSION[self::TOKEN_KEY];
    }

    public static function validate(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[self::TOKEN_KEY])) {
            return false;
        }
        return hash_equals((string) $_SESSION[self::TOKEN_KEY], $token);
    }

    /** Returns a ready-to-embed hidden input field. */
    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
