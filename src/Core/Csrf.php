<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        $sessionToken = $_SESSION[self::KEY] ?? '';

        return is_string($sessionToken)
            && $sessionToken !== ''
            && is_string($token)
            && hash_equals($sessionToken, $token);
    }

    /**
     * Stop the request unless the POST carries a valid token.
     */
    public static function requireValid(): void
    {
        if (!self::verify($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            exit('419 — Invalid or expired CSRF token. Go back, refresh the page and try again.');
        }
    }
}
