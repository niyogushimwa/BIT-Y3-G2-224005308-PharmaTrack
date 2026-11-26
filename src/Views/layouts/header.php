<?php
use App\Core\Auth;

$currentUser = Auth::user();
$isGuest = $currentUser === null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f8">
    <title><?= e(($title ?? APP_NAME) . ' · ' . APP_NAME) ?></title>
    <link rel="icon" type="image/png" href="<?= e(BASE_URL) ?>/assets/images/logo-mark.png">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/app.css?v=<?= e((string) filemtime(APP_ROOT . '/public/assets/app.css')) ?>">
    <script src="<?= e(BASE_URL) ?>/assets/app.js" defer></script>
</head>
<body class="<?= !empty($authLayout) ? 'auth-page' : '' ?>">
<header class="site-header">
    <a class="brand" href="<?= e(url($isGuest ? 'login' : 'dashboard')) ?>">
        <img class="brand-logo" src="<?= e(BASE_URL) ?>/assets/images/logo-mark.png" alt="" width="40" height="40">
        <span>Pharma<span class="brand-light">Track</span><small>PHARMACY INVENTORY</small></span>
    </a>
    <?php if (!$isGuest): ?>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">☰ <span>Menu</span></button>
        <div class="account">
            <span class="account-avatar" aria-hidden="true"><?= e(strtoupper(substr($currentUser['username'], 0, 1))) ?></span>
            <span class="account-name"><strong><?= e($currentUser['username']) ?></strong><small><?= e(ucfirst($currentUser['role'])) ?></small></span>
            <form method="post" action="<?= e(url('logout')) ?>">
                <?= \App\Core\Csrf::field() ?>
                <button class="button button-primary button-small sign-out-button" type="submit">Sign out</button>
            </form>
        </div>
    <?php endif; ?>
</header>
<?php if (!$isGuest): ?>
    <div class="app-layout">
        <aside class="sidebar" id="primary-nav">
            <span class="sidebar-label">WORKSPACE</span>
            <nav class="primary-nav" aria-label="Main navigation">
                <a href="<?= e(url('dashboard')) ?>" class="<?= ($title ?? '') === 'Dashboard' ? 'active' : '' ?>"><span class="nav-icon" aria-hidden="true">◫</span>Overview</a>
                <a href="<?= e(url('medicines')) ?>" class="<?= str_starts_with((string) ($title ?? ''), 'Medicine') ? 'active' : '' ?>"><span class="nav-icon" aria-hidden="true">✚</span>Medicines</a>
                <a href="<?= e(url('suppliers')) ?>" class="<?= str_starts_with((string) ($title ?? ''), 'Supplier') ? 'active' : '' ?>"><span class="nav-icon" aria-hidden="true">▤</span>Suppliers</a>
                <a href="<?= e(url('alerts')) ?>" class="<?= str_starts_with((string) ($title ?? ''), 'Stock alert') || ($title ?? '') === 'Create alert' ? 'active' : '' ?>"><span class="nav-icon" aria-hidden="true">◷</span>Alerts</a>
                <a href="<?= e(url('users')) ?>" class="<?= str_starts_with((string) ($title ?? ''), 'User') ? 'active' : '' ?>"><span class="nav-icon" aria-hidden="true">♙</span>Users</a>
            </nav>
        </aside>
        <main class="page-shell">
<?php else: ?>
<main class="page-shell<?= $isGuest ? ' guest-shell' : '' ?><?= !empty($authLayout) ? ' auth-shell' : '' ?>">
<?php endif; ?>
    <?php if (!empty($flash)): ?>
        <div class="flash flash-<?= e($flash['type'] ?? 'info') ?>" role="status"><?= e($flash['message'] ?? '') ?></div>
    <?php endif; ?>
