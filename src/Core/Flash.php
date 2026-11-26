<?php
declare(strict_types=1);

namespace App\Core;

final class Flash
{
    private const FLASH  = '_flash';
    private const ERRORS = '_errors';
    private const OLD    = '_old';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::FLASH] = ['type' => $type, 'message' => $message];
    }

    public static function get(): ?array
    {
        $flash = $_SESSION[self::FLASH] ?? null;
        unset($_SESSION[self::FLASH]);

        return is_array($flash) ? $flash : null;
    }

    public static function setErrors(array $errors): void
    {
        $_SESSION[self::ERRORS] = $errors;
    }

    public static function errors(): array
    {
        $errors = $_SESSION[self::ERRORS] ?? [];
        unset($_SESSION[self::ERRORS]);

        return is_array($errors) ? $errors : [];
    }

    public static function setOld(array $old): void
    {
        unset($old['password'], $old['password_confirm'], $old['csrf_token']);
        $_SESSION[self::OLD] = $old;
    }

    public static function old(): array
    {
        $old = $_SESSION[self::OLD] ?? [];
        unset($_SESSION[self::OLD]);

        return is_array($old) ? $old : [];
    }
}
