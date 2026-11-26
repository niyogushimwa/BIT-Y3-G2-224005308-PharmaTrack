<div class="auth-layout">
    <?php require __DIR__ . '/hero.php'; ?>
    <div class="auth-form-side">
        <section class="auth-card">
            <span class="eyebrow">WELCOME BACK</span>
            <h1>Sign in to your workspace</h1>
            <p class="muted">Keep your medicine stock and expiry dates under control.</p>
            <?php if (!empty($errors['login'])): ?><div class="form-error" role="alert"><?= e($errors['login']) ?></div><?php endif; ?>
            <form method="post" action="<?= e(url('login')) ?>" class="form-stack">
                <?= \App\Core\Csrf::field() ?>
                <label>Username or email
                    <input name="username" type="text" autocomplete="username" maxlength="100" required value="<?= e($old['username'] ?? '') ?>">
                    <?php if (!empty($errors['username'])): ?><small class="field-error"><?= e($errors['username']) ?></small><?php endif; ?>
                </label>
                <label>Password
                    <input name="password" type="password" autocomplete="current-password" required>
                    <?php if (!empty($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?>
                </label>
                <button class="button button-primary button-wide" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-footnote">New to PharmaTrack? <a href="<?= e(url('register')) ?>">Create a pharmacist account</a></p>
        </section>
    </div>
</div>
