<section class="page-heading">
    <div><span class="eyebrow">YOUR PHARMACY, AT A GLANCE</span><h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 17 ? 'afternoon' : 'evening') ?>, <?= e(\App\Core\Auth::user()['username'] ?? 'Pharmacist') ?>.</h1><p class="muted">A clear view of your inventory and what needs attention.</p></div>
    <a class="button button-primary" href="<?= e(url('medicine-form')) ?>">＋ Add medicine</a>
</section>
<section class="stat-grid" aria-label="Inventory summary" data-stats-url="<?= e(api_url('stats.php')) ?>">
    <article class="stat-card"><span class="stat-label">Total medicines</span><strong data-stat="total"><?= e($stats['total'] ?? 0) ?></strong><span class="stat-note">products in your catalog</span></article>
    <article class="stat-card stat-warn"><span class="stat-label">Low stock</span><strong data-stat="low"><?= e($stats['low'] ?? 0) ?></strong><span class="stat-note">at or below reorder level</span></article>
    <article class="stat-card stat-danger"><span class="stat-label">Expired</span><strong data-stat="expired"><?= e($stats['expired'] ?? 0) ?></strong><span class="stat-note">remove from dispensary stock</span></article>
    <article class="stat-card stat-accent"><span class="stat-label">Active alerts</span><strong data-stat="active"><?= e($alertCounts['active'] ?? 0) ?></strong><span class="stat-note"><span data-stat="units"><?= e(number_format($stats['units'] ?? 0)) ?></span> units on hand</span></article>
    <p class="sr-only" data-stats-status aria-live="polite"></p>
</section>
<section class="dashboard-grid">
    <article class="panel">
        <div class="panel-heading"><div><span class="eyebrow">NEEDS ATTENTION</span><h2>Low stock medicines</h2></div><a class="text-link" href="<?= e(url('medicines', ['status' => 'low'])) ?>">View all →</a></div>
        <?php if (!$lowStock): ?><p class="empty-inline">Everything is above its reorder level.</p><?php else: ?>
            <div class="compact-list"><?php foreach ($lowStock as $medicine): ?><a class="compact-row" href="<?= e(url('medicine-form', ['id' => $medicine['medicine_id']])) ?>"><span class="medicine-symbol" aria-hidden="true">Rx</span><span class="compact-main"><strong><?= e($medicine['name']) ?></strong><small><?= e($medicine['category']) ?></small></span><span class="quantity-low"><?= e($medicine['stock']) ?> <small>/ <?= e($medicine['reorder_level']) ?></small></span></a><?php endforeach; ?></div>
        <?php endif; ?>
    </article>
    <article class="panel">
        <div class="panel-heading"><div><span class="eyebrow">COMING UP</span><h2>Expiry watch</h2></div><a class="text-link" href="<?= e(url('medicines', ['status' => 'expiring'])) ?>">View all →</a></div>
        <?php if (!$expiring): ?><p class="empty-inline">No medicines expire in the next 30 days.</p><?php else: ?>
            <div class="compact-list"><?php foreach ($expiring as $medicine): ?><div class="compact-row"><span class="medicine-symbol symbol-amber" aria-hidden="true">◷</span><span class="compact-main"><strong><?= e($medicine['name']) ?></strong><small><?= e($medicine['category']) ?></small></span><span class="date-value"><?= e(date('M j, Y', strtotime($medicine['expiry_date']))) ?></span></div><?php endforeach; ?></div>
        <?php endif; ?>
    </article>
    <article class="panel panel-wide">
        <div class="panel-heading"><div><span class="eyebrow">LATEST ACTIVITY</span><h2>Recent stock alerts</h2></div><a class="text-link" href="<?= e(url('alerts')) ?>">All alerts →</a></div>
        <?php if (!$recent): ?><p class="empty-inline">No alerts yet. Your inventory is looking good.</p><?php else: ?>
            <div class="table-wrap"><table><thead><tr><th>Medicine</th><th>Alert</th><th>Triggered</th><th>Status</th></tr></thead><tbody><?php foreach ($recent as $alert): ?><tr><td class="table-strong"><?= e($alert['medicine_name']) ?></td><td><span class="badge <?= $alert['alert_type'] === 'expired' ? 'badge-danger' : 'badge-warn' ?>"><?= $alert['alert_type'] === 'expired' ? 'Expired' : 'Low stock' ?></span></td><td><?= e(date('M j, Y · H:i', strtotime($alert['triggered_at']))) ?></td><td><?= $alert['resolved'] ? '<span class="badge badge-neutral">Resolved</span>' : '<span class="badge badge-success">Active</span>' ?></td></tr><?php endforeach; ?></tbody></table></div>
        <?php endif; ?>
    </article>
    <?php if ($categories): ?>
        <article class="panel panel-wide">
            <div class="panel-heading"><div><span class="eyebrow">CATALOG BREAKDOWN</span><h2>Stock by category</h2></div><a class="text-link" href="<?= e(url('medicines')) ?>">Browse catalog →</a></div>
            <div class="category-grid"><?php $maxCategory = max(array_map(static fn(array $item): int => (int) $item['total'], $categories)); foreach ($categories as $category): ?><div class="category-item"><div><strong><?= e($category['category']) ?></strong><span><?= e($category['total']) ?> medicines · <?= e(number_format((int) $category['units'])) ?> units</span></div><span class="category-track"><span style="width: <?= e((int) round(((int) $category['total'] / $maxCategory) * 100)) ?>%"></span></span></div><?php endforeach; ?></div>
        </article>
    <?php endif; ?>
</section>
