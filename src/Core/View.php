<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $viewFile = APP_ROOT . '/src/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('View not found: ' . e($view));
        }

        $data += [
            'flash'  => Flash::get(),
            'errors' => Flash::errors(),
            'old'    => Flash::old(),
        ];

        extract($data, EXTR_SKIP);

        require APP_ROOT . '/src/Views/layouts/header.php';
        require $viewFile;
        require APP_ROOT . '/src/Views/layouts/footer.php';
    }
}
