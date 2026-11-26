<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\User;
use App\Services\UserValidator;

final class AuthController
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect(url('dashboard'));
        }

        if (is_post()) {
            Csrf::requireValid();

            $login    = post_string('username');
            $password = (string) ($_POST['password'] ?? '');
            $errors   = [];

            if ($login === '') {
                $errors['username'] = 'Username or email is required.';
            }

            if ($password === '') {
                $errors['password'] = 'Password is required.';
            }

            if (!$errors) {
                $user = User::findByLogin($login);

                if ($user === null || !password_verify($password, $user['password_hash'])) {
                    usleep(250000);
                    $errors['login'] = 'Invalid username/email or password.';
                } else {
                    Auth::login($user);
                    Flash::set('success', 'Welcome back, ' . $user['username'] . '!');
                    redirect(url('dashboard'));
                }
            }

            Flash::setErrors($errors);
            Flash::setOld(['username' => $login]);
        }

        View::render('auth/login', ['title' => 'Sign in', 'authLayout' => true]);
    }

    public function register(): void
    {
        if (Auth::check()) {
            redirect(url('dashboard'));
        }

        if (is_post()) {
            Csrf::requireValid();

            [$data, $errors] = UserValidator::validateRegistration($_POST);

            if (!$errors) {
                $user = User::create([
                    ':username'      => $data['username'],
                    ':email'         => $data['email'],
                    ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                    ':role'          => 'pharmacist',
                ]);

                Auth::login(User::find($user) ?? []);
                Flash::set('success', 'Account created. Welcome to PharmaTrack!');
                redirect(url('dashboard'));
            }

            Flash::setErrors($errors);
            Flash::setOld($_POST);
        }

        View::render('auth/register', ['title' => 'Create account', 'authLayout' => true]);
    }

    public function logout(): void
    {
        if (!is_post()) {
            redirect(url('dashboard'));
        }

        Csrf::requireValid();
        Auth::logout();

        Flash::set('info', 'You have been signed out.');
        redirect(url('login'));
    }
}
