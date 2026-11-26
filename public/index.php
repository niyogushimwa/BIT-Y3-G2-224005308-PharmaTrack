<?php
declare(strict_types=1);

/**
 * Front controller — every HTML request enters here.
 * Routes are selected with ?r=route-name.
 */

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Controllers\AlertController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MedicineController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Core\Auth;
use App\Core\View;

$routes = [
    'login'          => [AuthController::class, 'login'],
    'register'       => [AuthController::class, 'register'],
    'logout'         => [AuthController::class, 'logout'],

    'dashboard'      => [DashboardController::class, 'index'],

    'medicines'      => [MedicineController::class, 'index'],
    'medicine-form'  => [MedicineController::class, 'form'],
    'medicine-save'  => [MedicineController::class, 'save'],
    'medicine-delete'=> [MedicineController::class, 'delete'],

    'suppliers'      => [SupplierController::class, 'index'],
    'supplier-form'  => [SupplierController::class, 'form'],
    'supplier-save'  => [SupplierController::class, 'save'],
    'supplier-delete'=> [SupplierController::class, 'delete'],

    'alerts'         => [AlertController::class, 'index'],
    'alert-create'   => [AlertController::class, 'create'],
    'alert-form'     => [AlertController::class, 'form'],
    'alert-resolve'  => [AlertController::class, 'resolve'],
    'alert-delete'   => [AlertController::class, 'delete'],
    'alert-sync'     => [AlertController::class, 'sync'],

    'users'          => [UserController::class, 'index'],
    'user-form'      => [UserController::class, 'form'],
    'user-save'      => [UserController::class, 'save'],
    'user-delete'    => [UserController::class, 'delete'],
];

$route = isset($_GET['r']) && is_string($_GET['r'])
    ? trim($_GET['r'])
    : (Auth::check() ? 'dashboard' : 'login');

if (!isset($routes[$route])) {
    http_response_code(404);
    View::render('errors/404', ['title' => 'Page not found']);
    exit;
}

[$controllerClass, $method] = $routes[$route];

try {
    (new $controllerClass())->{$method}();
} catch (Throwable $exception) {
    error_log(sprintf(
        'Unhandled %s: %s in %s:%d',
        $exception::class,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    http_response_code(500);
    View::render('errors/500', ['title' => 'Server error']);
}
