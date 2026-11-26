<?php
declare(strict_types=1);

/**
 * JSON API: live medicine search.
 * GET /api/medicines.php?q=para
 */

require dirname(__DIR__, 2) . '/src/bootstrap.php';

use App\Core\Auth;
use App\Models\Medicine;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized. Please sign in.']);
    exit;
}

$query = isset($_GET['q']) && is_string($_GET['q'])
    ? trim($_GET['q'])
    : '';

if (text_length($query) > 100) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Search query is too long.']);
    exit;
}

try {
    $rows = Medicine::search($query, 200);

    $labels = [
        'ok'       => 'In stock',
        'low'      => 'Low stock',
        'expiring' => 'Expiring soon',
        'expired'  => 'Expired',
    ];

    $results = array_map(static function (array $row) use ($labels): array {
        return [
            'id'            => (int) $row['medicine_id'],
            'name'          => $row['name'],
            'category'      => $row['category'],
            'stock'         => (int) $row['stock'],
            'reorder_level' => (int) $row['reorder_level'],
            'expiry_date'   => $row['expiry_date'],
            'supplier'      => $row['supplier_name'] ?? '—',
            'status'        => $row['status'],
            'status_label'  => $labels[$row['status']] ?? $row['status'],
        ];
    }, $rows);

    echo json_encode([
        'ok'         => true,
        'query'      => $query,
        'count'      => count($results),
        'results'    => $results,
        'generated'  => date(DATE_ATOM),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    error_log('Medicine search API failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not load medicines.']);
}
