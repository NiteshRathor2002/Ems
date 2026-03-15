<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected array $config)
    {
    }

    protected function render(string $view, array $data = [], string $layout = 'default'): void
    {
        extract($data);
        $config = $this->config;

        $layoutHeader = __DIR__ . '/../Views/layout/header.php';
        $layoutFooter = __DIR__ . '/../Views/layout/footer.php';
        if ($layout === 'admin') {
            $layoutHeader = __DIR__ . '/../Views/layout/admin_header.php';
            $layoutFooter = __DIR__ . '/../Views/layout/admin_footer.php';
        }
        if ($layout === 'employee') {
            $layoutHeader = __DIR__ . '/../Views/layout/employee_header.php';
            $layoutFooter = __DIR__ . '/../Views/layout/employee_footer.php';
        }

        require $layoutHeader;
        require __DIR__ . '/../Views/' . $view . '.php';
        require $layoutFooter;
    }

    protected function basePath(): string
    {
        $base = (string) ($this->config['base_path'] ?? '');
        return $base !== '' ? $base : (parse_url((string) ($this->config['app_url'] ?? ''), PHP_URL_PATH) ?: '');
    }
}
