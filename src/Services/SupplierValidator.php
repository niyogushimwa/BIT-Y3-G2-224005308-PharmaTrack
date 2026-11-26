<?php
declare(strict_types=1);

namespace App\Services;

final class SupplierValidator
{
    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public static function validate(array $input): array
    {
        $errors = [];

        $name    = trim((string) ($input['name'] ?? ''));
        $phone   = trim((string) ($input['phone'] ?? ''));
        $email   = trim((string) ($input['email'] ?? ''));
        $address = trim((string) ($input['address'] ?? ''));

        if ($name === '') {
            $errors['name'] = 'Supplier name is required.';
        } elseif (text_length($name) < 2 || text_length($name) > 120) {
            $errors['name'] = 'Supplier name must be between 2 and 120 characters.';
        }

        if ($phone === '') {
            $errors['phone'] = 'Phone number is required.';
        } elseif (!preg_match('/^[0-9+()\-.\s]{7,30}$/', $phone)) {
            $errors['phone'] = 'Phone may only contain digits, spaces and + ( ) - . and must be 7–30 characters.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (text_length($email) > 100) {
            $errors['email'] = 'Email cannot exceed 100 characters.';
        }

        if (text_length($address) > 255) {
            $errors['address'] = 'Address cannot exceed 255 characters.';
        }

        $data = [
            ':name'    => $name,
            ':phone'   => $phone,
            ':email'   => $email === '' ? null : $email,
            ':address' => $address === '' ? null : $address,
        ];

        return [$data, $errors];
    }
}
