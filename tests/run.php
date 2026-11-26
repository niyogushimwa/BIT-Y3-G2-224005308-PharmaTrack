<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$passed = 0;
$failed = 0;

$test = static function (string $name, bool $result) use (&$passed, &$failed): void {
    if ($result) {
        $passed++;
        echo "PASS: {$name}\n";
        return;
    }

    $failed++;
    fwrite(STDERR, "FAIL: {$name}\n");
};

$test(
    'HTML output is escaped',
    e('<script>alert("x")</script>') === '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;'
);
$test('UTF-8 text length counts characters', text_length('PharmaTrack') === 11 && text_length('薬局') === 2);

$token = \App\Core\Csrf::token();
$test('CSRF token is generated with sufficient entropy', preg_match('/^[a-f0-9]{64}$/', $token) === 1);
$test('CSRF verification accepts the session token', \App\Core\Csrf::verify($token));
$test('CSRF verification rejects an incorrect token', !\App\Core\Csrf::verify(str_repeat('0', 64)));
$test('CSRF verification rejects a missing token', !\App\Core\Csrf::verify(null));

echo "\n{$passed} passed; {$failed} failed.\n";
exit($failed === 0 ? 0 : 1);
