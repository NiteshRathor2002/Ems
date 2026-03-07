<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected array $config)
    {
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $config = $this->config;
        require __DIR__ . '/../Views/layout/header.php';
        require __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../Views/layout/footer.php';
    }
}
