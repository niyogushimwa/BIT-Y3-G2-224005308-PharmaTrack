<?php
$isEditing = $supplier !== null;
$value = static fn(string $key): mixed => $old[$key] ?? ($supplier[$key] ?? '');
?>
<section class="page-heading">
    <div><span class="eyebrow">PARTNER DIRECTORY</span><h1><?= $isEditing ? 'Edit supplier' : 'Add a supplier' ?></h1><p class="muted">Add reliable contact information for your suppliers.</p></div>
    <a class="button button-secondary" href="<?= e(url('suppliers')) ?>">← Back to suppliers</a>
</section>
<section class="panel form-panel"><form method="post" action="<?= e(url('supplier-save')) ?>" class="form-stack">
    <?= \App\Core\Csrf::field() ?><?php if ($isEditing): ?><input type="hidden" name="id" value="<?= e($supplier['supplier_id']) ?>"><?php endif; ?>
    <div class="form-grid">
        <label>Supplier name <span class="required">*</span><input name="name" type="text" maxlength="120" required value="<?= e($value('name')) ?>"><?php if (!empty($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?></label>
        <label>Phone number <span class="required">*</span><input name="phone" type="tel" maxlength="30" required value="<?= e($value('phone')) ?>"><?php if (!empty($errors['phone'])): ?><small class="field-error"><?= e($errors['phone']) ?></small><?php endif; ?></label>
        <label>Email address<input name="email" type="email" maxlength="100" value="<?= e($value('email')) ?>"><?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?></label>
        <label>Address<input name="address" type="text" maxlength="255" value="<?= e($value('address')) ?>"><?php if (!empty($errors['address'])): ?><small class="field-error"><?= e($errors['address']) ?></small><?php endif; ?></label>
    </div>
    <div class="form-actions"><a class="button button-quiet" href="<?= e(url('suppliers')) ?>">Cancel</a><button class="button button-primary" type="submit"><?= $isEditing ? 'Save changes' : 'Add supplier' ?></button></div>
</form></section>
