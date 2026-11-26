<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\Supplier;
use App\Services\SupplierValidator;

final class SupplierController
{
    public function index(): void
    {
        Auth::requireLogin();

        View::render('suppliers/index', [
            'title'     => 'Suppliers',
            'suppliers' => Supplier::all(),
        ]);
    }

    public function form(): void
    {
        Auth::requireLogin();

        $id       = (int) ($_GET['id'] ?? 0);
        $supplier = $id > 0 ? Supplier::find($id) : null;

        if ($id > 0 && $supplier === null) {
            Flash::set('error', 'Supplier not found.');
            redirect(url('suppliers'));
        }

        View::render('suppliers/form', [
            'title'    => $supplier ? 'Edit supplier' : 'Add supplier',
            'supplier' => $supplier,
        ]);
    }

    public function save(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('suppliers'));
        }

        $id = (int) ($_POST['id'] ?? 0);
        [$data, $errors] = SupplierValidator::validate($_POST);

        if ($errors) {
            Flash::setErrors($errors);
            Flash::setOld($_POST);
            redirect(url('supplier-form', $id > 0 ? ['id' => $id] : []));
        }

        if ($id > 0) {
            if (Supplier::find($id) === null) {
                Flash::set('error', 'Supplier not found.');
                redirect(url('suppliers'));
            }

            $changed = Supplier::update($id, $data);
            Flash::set(
                $changed ? 'success' : 'info',
                $changed ? 'Supplier updated successfully.' : 'No changes were made.'
            );
        } else {
            $newId = Supplier::create($data);
            Flash::set('success', 'Supplier #' . $newId . ' added successfully.');
        }

        redirect(url('suppliers'));
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('suppliers'));
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $removed = $id > 0 ? Supplier::delete($id) : 0;

        Flash::set(
            $removed ? 'success' : 'error',
            $removed ? 'Supplier deleted. Linked medicines are now unassigned.' : 'Supplier not found.'
        );

        redirect(url('suppliers'));
    }
}
