<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class UserValidator
{
    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public static function validate(array $input, bool $isUpdate = false, ?int $userId = null): array
    {
        $errors = [];

        $username = trim((string) ($input['username'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));
        $role     = trim((string) ($input['role'] ?? 'pharmacist'));
        $password = (string) ($input['password'] ?? '');
        $confirm  = (string) ($input['password_confirm'] ?? '');

        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
            $errors['username'] = 'Username must be 3–50 characters: letters, numbers or underscore only.';
        } elseif (User::usernameTaken($username, $userId)) {
            $errors['username'] = 'That username is already taken.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($email) > 100) {
            $errors['email'] = 'Enter a valid email address (max 100 characters).';
        } elseif (User::emailTaken($email, $userId)) {
            $errors['email'] = 'That email is already registered.';
        }

        if (!in_array($role, ['admin', 'pharmacist'], true)) {
            $errors['role'] = 'Choose a valid role.';
        }

        if (!$isUpdate || $password !== '') {
            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters.';
            } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $errors['password'] = 'Password must contain at least one letter and one number.';
            }

            if ($password !== $confirm) {
                $errors['password_confirm'] = 'Passwords do not match.';
            }
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'password' => $password,
        ];

        return [$data, $errors];
    }

    /**
     * Registration is always for a pharmacist; role is never taken from the form.
     */
    public static function validateRegistration(array $input): array
    {
        $input['role'] = 'pharmacist';

        return self::validate($input, false, null);
    }
}
