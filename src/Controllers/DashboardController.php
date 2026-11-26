<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Medicine;
use App\Models\StockAlert;

final class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $created = StockAlert::sync();
        if ($created > 0) {
            \App\Core\Flash::set('info', $created . ' new stock alert' . ($created === 1 ? '' : 's') . ' generated automatically.');
        }

        View::render('dashboard/index', [
            'title'       => 'Dashboard',
            'stats'       => Medicine::dashboardStats(),
            'alertCounts' => StockAlert::counts(),
            'lowStock'    => Medicine::lowStock(6),
            'expiring'    => Medicine::expiringSoon(30, 6),
            'recent'      => StockAlert::recent(6),
            'categories'  => Medicine::byCategory(),
        ]);
    }
}
