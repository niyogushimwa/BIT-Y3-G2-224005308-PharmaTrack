<section class="page-heading">
    <div><span class="eyebrow">INVENTORY CATALOG</span><h1>Medicines</h1><p class="muted">Track quantities, reorder points, suppliers and expiry dates.</p></div>
    <a class="button button-primary" href="<?= e(url('medicine-form')) ?>">＋ Add medicine</a>
</section>
<section class="panel">
    <form class="filter-bar" method="get" action="<?= e(BASE_URL) ?>/index.php">
        <input type="hidden" name="r" value="medicines">
        <label class="search-field"><span aria-hidden="true">⌕</span><input type="search" name="q" value="<?= e($query) ?>" placeholder="Search medicines, categories or suppliers" maxlength="100" aria-label="Search medicines"></label>
        <label class="filter-select"><span class="sr-only">Stock status</span><select name="status"><option value="">All statuses</option><option value="ok" <?= $status === 'ok' ? 'selected' : '' ?>>In stock</option><option value="low" <?= $status === 'low' ? 'selected' : '' ?>>Low stock</option><option value="expiring" <?= $status === 'expiring' ? 'selected' : '' ?>>Expiring soon</option><option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expired</option></select></label>
        <button class="button button-secondary" type="submit">Filter</button>
        <?php if ($query !== '' || $status !== ''): ?><a class="button button-quiet" href="<?= e(url('medicines')) ?>">Clear</a><?php endif; ?>
    </form>
    <p class="result-count"><?= count($medicines) ?> medicine<?= count($medicines) === 1 ? '' : 's' ?><?= $query !== '' ? ' matching “' . e($query) . '”' : '' ?></p>
    <?php if (!$medicines): ?><div class="empty-state empty-compact"><h2>No medicines found</h2><p class="muted">Try another search or add a medicine to your catalog.</p><a class="button button-secondary" href="<?= e(url('medicine-form')) ?>">Add medicine</a></div><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Medicine</th><th>Category</th><th>Stock</th><th>Reorder at</th><th>Expiry date</th><th>Supplier</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody><?php foreach ($medicines as $medicine): $statusLabels = ['ok' => 'In stock', 'low' => 'Low stock', 'expiring' => 'Expiring soon', 'expired' => 'Expired']; ?><tr><td class="table-strong"><?= e($medicine['name']) ?></td><td><?= e($medicine['category']) ?></td><td class="<?= $medicine['status'] === 'low' ? 'quantity-low' : '' ?>"><?= e($medicine['stock']) ?></td><td><?= e($medicine['reorder_level']) ?></td><td><?= e(date('M j, Y', strtotime($medicine['expiry_date']))) ?></td><td><?= e($medicine['supplier_name'] ?? '—') ?></td><td><span class="badge badge-<?= e($medicine['status']) ?>"><?= e($statusLabels[$medicine['status']] ?? $medicine['status']) ?></span></td><td><div class="row-actions"><a class="button button-quiet button-small" href="<?= e(url('medicine-form', ['id' => $medicine['medicine_id']])) ?>">Edit</a><form method="post" action="<?= e(url('medicine-delete')) ?>" data-confirm="Delete this medicine and its alert history?"><?= \App\Core\Csrf::field() ?><input type="hidden" name="id" value="<?= e($medicine['medicine_id']) ?>"><button class="button button-quiet button-small button-danger-text" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?></tbody></table></div>
    <?php endif; ?>
</section>
<section class="panel async-search" data-medicine-search data-api-url="<?= e(api_url('medicines.php')) ?>">
    <div class="panel-heading"><div><span class="eyebrow">QUICK LOOKUP</span><h2>Live medicine search</h2></div><span class="live-indicator">● Live</span></div>
    <label class="search-field search-wide"><span aria-hidden="true">⌕</span><input type="search" data-live-search placeholder="Type a medicine or category..." maxlength="100" autocomplete="off"></label>
    <p class="muted async-status" data-search-status aria-live="polite">Start typing to search your catalog.</p>
    <div class="live-results" data-search-results></div>
</section>
