<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\User;
use App\Services\UserValidator;

final class UserController
{
    public function index(): void
    {
        Auth::requireAdmin();

        View::render('users/index', [
            'title' => 'Users',
            'users' => User::all(),
        ]);
    }

    public function form(): void
    {
        Auth::requireAdmin();

        $id   = (int) ($_GET['id'] ?? 0);
        $user = $id > 0 ? User::find($id) : null;

        if ($id > 0 && $user === null) {
            Flash::set('error', 'User not found.');
            redirect(url('users'));
        }

        View::render('users/form', [
            'title' => $user ? 'Edit user' : 'Add user',
            'user'  => $user,
        ]);
    }

    public function save(): void
    {
        Auth::requireAdmin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('users'));
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $editing = $id > 0 ? User::find($id) : null;

        if ($id > 0 && $editing === null) {
            Flash::set('error', 'User not found.');
            redirect(url('users'));
        }

        [$data, $errors] = UserValidator::validate($_POST, $id > 0, $id > 0 ? $id : null);

        // An administrator cannot remove their own administrator access.
        if ($id > 0 && $id === Auth::id() && $data['role'] !== 'admin') {
            $errors['role'] = 'You cannot remove your own administrator role.';
        }

        if ($errors) {
            Flash::setErrors($errors);
            Flash::setOld($_POST);
            redirect(url('user-form', $id > 0 ? ['id' => $id] : []));
        }

        $payload = [
            ':username' => $data['username'],
            ':email'    => $data['email'],
            ':role'     => $data['role'],
            ':password_hash' => $data['password'] !== ''
                ? password_hash($data['password'], PASSWORD_DEFAULT)
                : null,
        ];

        if ($id > 0) {
            $changed = User::update($id, $payload);
            Flash::set(
                $changed ? 'success' : 'info',
                $changed ? 'User account updated.' : 'No changes were made.'
            );
        } else {
            $newId = User::create($payload);
            Flash::set('success', 'User #' . $newId . ' created.');
        }

        redirect(url('users'));
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('users'));
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id === Auth::id()) {
            Flash::set('error', 'You cannot delete the account you are signed in with.');
            redirect(url('users'));
        }

        $target = $id > 0 ? User::find($id) : null;

        if ($target === null) {
            Flash::set('error', 'User not found.');
            redirect(url('users'));
        }

        if ($target['role'] === 'admin' && User::countAdmins() <= 1) {
            Flash::set('error', 'The last administrator account cannot be deleted.');
            redirect(url('users'));
        }

        User::delete($id);
        Flash::set('success', 'User account deleted.');

        redirect(url('users'));
    }
}
