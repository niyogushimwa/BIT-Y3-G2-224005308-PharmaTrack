<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\Medicine;
use App\Models\StockAlert;
use App\Services\AlertValidator;

final class AlertController
{
    public function index(): void
    {
        Auth::requireLogin();

        StockAlert::sync();

        $status = (string) ($_GET['status'] ?? 'active');
        if (!in_array($status, ['active', 'resolved', 'all'], true)) {
            $status = 'active';
        }

        $type = (string) ($_GET['type'] ?? '');
        if (!in_array($type, ['low', 'expired'], true)) {
            $type = '';
        }

        View::render('alerts/index', [
            'title'     => 'Stock alerts',
            'alerts'    => StockAlert::all($status, $type),
            'counts'    => StockAlert::counts(),
            'status'    => $status,
            'type'      => $type,
            'medicines' => Medicine::search('', 200),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('alerts'));
        }

        [$data, $errors] = AlertValidator::validate($_POST);

        if (!$errors && StockAlert::hasActive($data['medicine_id'], $data['alert_type'])) {
            $errors['alert'] = 'An active alert of that type already exists for this medicine.';
        }

        if ($errors) {
            Flash::setErrors($errors);
            Flash::setOld($_POST);
            redirect(url('alerts'));
        }

        StockAlert::create($data['medicine_id'], $data['alert_type']);
        Flash::set('success', 'Alert created successfully.');

        redirect(url('alerts'));
    }

    public function form(): void
    {
        Auth::requireLogin();

        View::render('alerts/form', [
            'title'     => 'Create alert',
            'medicines' => Medicine::search('', 200),
        ]);
    }

    public function resolve(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('alerts'));
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $changed = $id > 0 ? StockAlert::resolve($id) : 0;

        Flash::set(
            $changed ? 'success' : 'info',
            $changed ? 'Alert marked as resolved.' : 'Alert was not found or was already resolved.'
        );

        redirect(url('alerts', ['status' => 'active']));
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('alerts'));
        }

        $id      = (int) ($_POST['id'] ?? 0);
        $removed = $id > 0 ? StockAlert::delete($id) : 0;

        Flash::set(
            $removed ? 'success' : 'error',
            $removed ? 'Alert deleted permanently.' : 'Alert not found.'
        );

        redirect(url('alerts', ['status' => 'all']));
    }

    public function sync(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        if (!is_post()) {
            redirect(url('alerts'));
        }

        $created = StockAlert::sync();
        Flash::set(
            'success',
            $created > 0
                ? $created . ' new alert' . ($created === 1 ? '' : 's') . ' generated.'
                : 'All current stock issues already have an alert.'
        );

        redirect(url('alerts'));
    }
}
