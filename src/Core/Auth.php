<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    private static ?array $cachedUser = null;
    private static bool $loaded = false;

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        if (self::$loaded) {
            return self::$cachedUser;
        }

        self::$loaded = true;

        $id = (int) ($_SESSION['user_id'] ?? 0);
        if ($id <= 0) {
            return self::$cachedUser = null;
        }

        $user = User::find($id);
        if ($user === null) {
            unset($_SESSION['user_id']);
            return self::$cachedUser = null;
        }

        return self::$cachedUser = $user;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user ? (int) $user['user_id'] : null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && $user['role'] === 'admin';
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id']  = (int) $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        self::$loaded     = false;
        self::$cachedUser = null;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();

        self::$loaded     = false;
        self::$cachedUser = null;
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }

        Flash::set('error', 'Please sign in to continue.');
        redirect(url('login'));
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (self::isAdmin()) {
            return;
        }

        http_response_code(403);
        Flash::set('error', 'Only administrators can manage user accounts.');
        redirect(url('dashboard'));
    }
}
