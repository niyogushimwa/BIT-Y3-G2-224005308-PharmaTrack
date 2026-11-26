<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Services\MedicineValidator;

final class MedicineController
{
    public function index(): void
    {
        Auth::requireLogin();

        $query  = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        if (!in_array($status, ['ok', 'low', 'expiring', 'expired'], true)) {
            $status = '';
        }

        $medicines = Medicine::all($query, $status);

        View::render('medicines/index', [
            'title'      => 'Medicines',
            'medicines'  => $medicines,
            'query'      => $query,
            'status'     => $status,
            'categories' => Medicine::categories(),
        ]);
    }

    public function form(): void
    {
        Auth::requireLogin();

        $id       = (int) ($_GET['id'] ?? 0);
        $medicine = $id > 0 ? Medicine::find($id) : null;

        if ($id > 0 && $medicine === null) {
            Flash::set('error', 'Medicine not found.');
            redirect(url('medicines'));
        }

        View::render('medicines/form', [
            'title'     => $medicine ? 'Edit medicine' : 'Add medicine',
            'medicine'  => $medicine,
            'suppliers' => Supplier::options(),
            'categories' => Medicine::categories(),
        ]);
    }

    public function save(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('medicines'));
        }

        $id = (int) ($_POST['id'] ?? 0);
        [$data, $errors] = MedicineValidator::validate($_POST);

        if ($errors) {
            Flash::setErrors($errors);
            Flash::setOld($_POST);
            redirect(url('medicine-form', $id > 0 ? ['id' => $id] : []));
        }

        if ($id > 0) {
            if (Medicine::find($id) === null) {
                Flash::set('error', 'Medicine not found.');
                redirect(url('medicines'));
            }

            $changed = Medicine::update($id, $data);
            Flash::set(
                $changed ? 'success' : 'info',
                $changed ? 'Medicine updated successfully.' : 'No changes were made.'
            );
        } else {
            $newId = Medicine::create($data);
            Flash::set('success', 'Medicine #' . $newId . ' added successfully.');
        }

        redirect(url('medicines'));
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('medicines'));
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $removed = $id > 0 ? Medicine::delete($id) : 0;

        Flash::set(
            $removed ? 'success' : 'error',
            $removed ? 'Medicine deleted. Its stock alerts were removed too.' : 'Medicine not found.'
        );

        redirect(url('medicines'));
    }
}
