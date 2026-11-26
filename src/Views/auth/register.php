<div class="auth-layout">
    <?php require __DIR__ . '/hero.php'; ?>
    <div class="auth-form-side">
        <section class="auth-card">
            <span class="eyebrow">GET STARTED</span>
            <h1>Create a pharmacist account</h1>
            <p class="muted">Join the workspace to manage medicines, suppliers and stock alerts.</p>
            <form method="post" action="<?= e(url('register')) ?>" class="form-stack">
                <?= \App\Core\Csrf::field() ?>
                <label>Username
                    <input name="username" type="text" autocomplete="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9_]{3,50}" required value="<?= e($old['username'] ?? '') ?>">
                    <?php if (!empty($errors['username'])): ?><small class="field-error"><?= e($errors['username']) ?></small><?php endif; ?>
                </label>
                <label>Email
                    <input name="email" type="email" autocomplete="email" maxlength="100" required value="<?= e($old['email'] ?? '') ?>">
                    <?php if (!empty($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
                </label>
                <div class="form-grid">
                    <label>Password
                        <input name="password" type="password" autocomplete="new-password" minlength="8" required>
                        <?php if (!empty($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?>
                    </label>
                    <label>Confirm password
                        <input name="password_confirm" type="password" autocomplete="new-password" minlength="8" required>
                        <?php if (!empty($errors['password_confirm'])): ?><small class="field-error"><?= e($errors['password_confirm']) ?></small><?php endif; ?>
                    </label>
                </div>
                <button class="button button-primary button-wide" type="submit">Create account <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-footnote">Already have an account? <a href="<?= e(url('login')) ?>">Sign in</a></p>
        </section>
    </div>
</div>
