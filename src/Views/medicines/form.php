<?php
$isEditing = $medicine !== null;
$value = static fn(string $key, mixed $fallback = ''): mixed => $old[$key] ?? ($medicine[$key] ?? $fallback);
?>
<section class="page-heading">
    <div><span class="eyebrow">INVENTORY CATALOG</span><h1><?= $isEditing ? 'Edit medicine' : 'Add a medicine' ?></h1><p class="muted">Keep product, stock and expiry information up to date.</p></div>
    <a class="button button-secondary" href="<?= e(url('medicines')) ?>">← Back to medicines</a>
</section>
<section class="panel form-panel">
    <form method="post" action="<?= e(url('medicine-save')) ?>" class="form-stack">
        <?= \App\Core\Csrf::field() ?>
        <?php if ($isEditing): ?><input type="hidden" name="id" value="<?= e($medicine['medicine_id']) ?>"><?php endif; ?>
        <div class="form-grid">
            <label>Medicine name <span class="required">*</span><input name="name" type="text" maxlength="150" required value="<?= e($value('name')) ?>"><?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?></label>
            <label>Category <span class="required">*</span><input name="category" type="text" maxlength="100" list="medicine-categories" required value="<?= e($value('category')) ?>"><datalist id="medicine-categories"><?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?php endforeach; ?></datalist><?php if (!empty($errors['category'])): ?><small class="field-error"><?= e($errors['category']) ?></small><?php endif; ?></label>
            <label>Units in stock <span class="required">*</span><input name="stock" type="number" min="0" step="1" required value="<?= e($value('stock', 0)) ?>"><?php if (!empty($errors['stock'])): ?><small class="field-error"><?= e($errors['stock']) ?></small><?php endif; ?></label>
            <label>Reorder level <span class="required">*</span><input name="reorder_level" type="number" min="0" step="1" required value="<?= e($value('reorder_level', 10)) ?>"><?php if (!empty($errors['reorder_level'])): ?><small class="field-error"><?= e($errors['reorder_level']) ?></small><?php endif; ?></label>
            <label>Expiry date <span class="required">*</span><input name="expiry_date" type="date" required value="<?= e($value('expiry_date')) ?>"><?php if (!empty($errors['expiry_date'])): ?><small class="field-error"><?= e($errors['expiry_date']) ?></small><?php endif; ?></label>
            <label>Supplier<select name="supplier_id"><option value="">No supplier assigned</option><?php foreach ($suppliers as $supplier): ?><option value="<?= e($supplier['supplier_id']) ?>" <?= (string) $value('supplier_id') === (string) $supplier['supplier_id'] ? 'selected' : '' ?>><?= e($supplier['name']) ?></option><?php endforeach; ?></select><?php if (!empty($errors['supplier_id'])): ?><small class="field-error"><?= e($errors['supplier_id']) ?></small><?php endif; ?></label>
        </div>
        <div class="form-actions"><a class="button button-quiet" href="<?= e(url('medicines')) ?>">Cancel</a><button class="button button-primary" type="submit"><?= $isEditing ? 'Save changes' : 'Add medicine' ?></button></div>
    </form>
</section>
