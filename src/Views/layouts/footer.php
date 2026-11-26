</main>
<?php if (\App\Core\Auth::check()): ?>
</div>
<?php endif; ?>
<footer class="site-footer">
    <span class="footer-brand">
        <img src="<?= e(BASE_URL) ?>/assets/images/logo-mark.png" alt="" width="34" height="34">
        <span>PharmaTrack · Pharmacy stock, expiry and supplier management</span>
    </span>
    <span>Inventory records should be verified before dispensing.</span>
</footer>
</body>
</html>
