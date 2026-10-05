<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class CSRFTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_get_token_generates_token_if_empty(): void
    {
        $token = CSRF::getToken();
        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token)); // 32 random bytes as hex
    }

    public function test_get_token_is_consistent_across_calls(): void
    {
        $first  = CSRF::getToken();
        $second = CSRF::getToken();
        $this->assertSame($first, $second);
    }

    public function test_field_contains_matching_token(): void
    {
        $field = CSRF::field();
        $token = CSRF::getToken();

        $this->assertStringContainsString('name="csrf_token"', $field);
        $this->assertStringContainsString('value="' . $token . '"', $field);
    }

    public function test_multiple_fields_share_same_token(): void
    {
        $field1 = CSRF::field();
        $field2 = CSRF::field();

        $this->assertSame($field1, $field2);
    }

    public function test_validate_passes_for_valid_token(): void
    {
        $token = CSRF::getToken();
        $this->assertTrue(CSRF::validate($token));
        // Token remains valid for concurrent/multiple operations
        $this->assertTrue(CSRF::validate($token));
    }

    public function test_validate_fails_for_invalid_token(): void
    {
        CSRF::getToken();
        $this->assertFalse(CSRF::validate('wrong-token'));
        $this->assertFalse(CSRF::validate(''));
        $this->assertFalse(CSRF::validate(null));
    }
}
