<?php
$isEditing = $user !== null;
$value = static fn(string $key, mixed $fallback = ''): mixed => $old[$key] ?? ($user[$key] ?? $fallback);
?>
<section class="page-heading"><div><span class="eyebrow">ACCESS MANAGEMENT</span><h1><?= $isEditing ? 'Edit user account' : 'Add a user account' ?></h1><p class="muted"><?= $isEditing ? 'Update profile details, access level or password.' : 'Create a new account for a pharmacist or administrator.' ?></p></div><a class="button button-secondary" href="<?= e(url('users')) ?>">← Back to users</a></section>
<section class="panel form-panel"><form method="post" action="<?= e(url('user-save')) ?>" class="form-stack">
    <?= \App\Core\Csrf::field() ?><?php if ($isEditing): ?><input type="hidden" name="id" value="<?= e($user['user_id']) ?>"><?php endif; ?>
    <div class="form-grid">
        <label>Username <span class="required">*</span><input name="username" type="text" minlength="3" maxlength="50" pattern="[A-Za-z0-9_]{3,50}" required value="<?= e($value('username')) ?>"><?php if (!empty($errors['username'])): ?><small class="field-error"><?= e($errors['username']) ?></small><?php endif; ?></label>
        <label>Email <span class="required">*</span><input name="email" type="email" maxlength="100" required value="<?= e($value('email')) ?>"><?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?></label>
        <label>Role <span class="required">*</span><select name="role" required><option value="pharmacist" <?= $value('role', 'pharmacist') === 'pharmacist' ? 'selected' : '' ?>>Pharmacist</option><option value="admin" <?= $value('role', 'pharmacist') === 'admin' ? 'selected' : '' ?>>Administrator</option></select><?php if (!empty($errors['role'])): ?><small class="field-error"><?= e($errors['role']) ?></small><?php endif; ?></label>
        <label>Password <?= $isEditing ? '' : '<span class="required">*</span>' ?><input name="password" type="password" minlength="8" autocomplete="new-password" <?= $isEditing ? '' : 'required' ?>><?php if ($isEditing): ?><small class="field-hint">Leave blank to keep the current password.</small><?php endif; ?><?php if (!empty($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?></label>
        <label>Confirm password <?= $isEditing ? '' : '<span class="required">*</span>' ?><input name="password_confirm" type="password" minlength="8" autocomplete="new-password" <?= $isEditing ? '' : 'required' ?>><?php if (!empty($errors['password_confirm'])): ?><small class="field-error"><?= e($errors['password_confirm']) ?></small><?php endif; ?></label>
    </div>
    <div class="form-actions"><a class="button button-quiet" href="<?= e(url('users')) ?>">Cancel</a><button class="button button-primary" type="submit"><?= $isEditing ? 'Save changes' : 'Create account' ?></button></div>
</form></section>
