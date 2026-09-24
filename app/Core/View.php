<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

/**
 * Plain-PHP templates. 'home' → resources/views/home.php; 'admin:projects/index' → admin/views/projects/index.php.
 * Layouts receive the rendered view as $content.
 */
final class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $content = self::capture(self::path($view), $data);
        if ($layout === null) {
            return $content;
        }
        return self::capture(self::path($layout), ['content' => $content] + $data);
    }

    public static function partial(string $name, array $data = []): string
    {
        return self::capture(BASE_PATH . '/resources/partials/' . $name . '.php', $data);
    }

    public static function path(string $view): string
    {
        if (str_starts_with($view, 'admin:')) {
            return BASE_PATH . '/admin/views/' . substr($view, 6) . '.php';
        }
        return BASE_PATH . '/resources/views/' . $view . '.php';
    }

    private static function capture(string $__file, array $__data): string
    {
        if (!is_file($__file) || str_contains($__file, '..')) {
            throw new RuntimeException('View not found: ' . basename($__file));
        }
        extract($__data, EXTR_SKIP);
        ob_start();
        try {
            require $__file;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}
