<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo View::render($view, $data, $layout);
    }

    /** @return array{page: int, pages: int, perPage: int} */
    protected function pagination(int $page, int $total, int $perPage): array
    {
        $pages = max(1, (int) ceil($total / $perPage));
        if ($page < 1 || $page > $pages) {
            abort(404);
        }
        return ['page' => $page, 'pages' => $pages, 'perPage' => $perPage];
    }
}
