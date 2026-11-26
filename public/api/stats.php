<?php
declare(strict_types=1);

/**
 * JSON API: live dashboard counters + alert scan.
 * GET /api/stats.php
 */

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use App\Core\Auth;
use App\Models\Medicine;
use App\Models\StockAlert;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized. Please sign in.']);
    exit;
}

try {
    $created = StockAlert::sync();

    echo json_encode([
        'ok'       => true,
        'created'  => $created,
        'medicine' => Medicine::dashboardStats(),
        'alerts'   => StockAlert::counts(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    error_log('Stats API failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not load statistics.']);
}
