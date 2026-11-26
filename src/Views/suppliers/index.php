<section class="page-heading">
    <div><span class="eyebrow">PARTNER DIRECTORY</span><h1>Suppliers</h1><p class="muted">Keep contact details and supplier relationships organized.</p></div>
    <a class="button button-primary" href="<?= e(url('supplier-form')) ?>">＋ Add supplier</a>
</section>
<section class="panel">
    <p class="result-count"><?= count($suppliers) ?> supplier<?= count($suppliers) === 1 ? '' : 's' ?> in your directory</p>
    <?php if (!$suppliers): ?><div class="empty-state empty-compact"><h2>No suppliers yet</h2><p class="muted">Add a supplier to connect medicines with their source.</p><a class="button button-secondary" href="<?= e(url('supplier-form')) ?>">Add supplier</a></div><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Supplier</th><th>Phone</th><th>Email</th><th>Address</th><th>Medicines</th><th>Total units</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody><?php foreach ($suppliers as $supplier): ?><tr><td class="table-strong"><?= e($supplier['name']) ?></td><td><?= e($supplier['phone']) ?></td><td><?= $supplier['email'] ? e($supplier['email']) : '—' ?></td><td><?= $supplier['address'] ? e($supplier['address']) : '—' ?></td><td><?= e($supplier['medicine_count']) ?></td><td><?= e(number_format((int) $supplier['total_stock'])) ?></td><td><div class="row-actions"><a class="button button-quiet button-small" href="<?= e(url('supplier-form', ['id' => $supplier['supplier_id']])) ?>">Edit</a><form method="post" action="<?= e(url('supplier-delete')) ?>" data-confirm="Delete this supplier? Linked medicines will remain but become unassigned."><?= \App\Core\Csrf::field() ?><input type="hidden" name="id" value="<?= e($supplier['supplier_id']) ?>"><button class="button button-quiet button-small button-danger-text" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?></tbody></table></div>
    <?php endif; ?>
</section>
