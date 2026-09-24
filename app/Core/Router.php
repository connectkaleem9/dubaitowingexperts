<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Pattern router. Routes are written with trailing slashes: '/services/{slug}/'.
 * Placeholders match [a-z0-9-]+ (slugs) unless the name ends in "id" ({id} matches digits).
 */
final class Router
{
    /** @var list<array{method:string, regex:string, handler:array{0:class-string,1:string}, middleware:list<string>}> */
    private array $routes = [];

    /** @var list<string> */
    private array $groupMiddleware = [];

    public function get(string $pattern, array $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function group(array $middleware, callable $define): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);
        $define($this);
        $this->groupMiddleware = $previous;
    }

    private function add(string $method, string $pattern, array $handler, array $middleware): void
    {
        $regex = preg_replace_callback('/\{([a-z_]+)\}/', static function (array $m): string {
            $name = $m[1];
            $class = str_ends_with($name, 'id') ? '[0-9]+' : '[a-z0-9]+(?:-[a-z0-9]+)*';
            return '(?P<' . $name . '>' . $class . ')';
        }, $pattern);
        $this->routes[] = [
            'method' => $method,
            'regex' => '#^' . $regex . '$#',
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(Request $request): void
    {
        $path = $request->path();
        $allowed = [];
        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $m)) {
                continue;
            }
            if ($route['method'] !== $request->method() && !($route['method'] === 'GET' && $request->method() === 'HEAD')) {
                $allowed[] = $route['method'];
                continue;
            }
            $request->params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            foreach ($route['middleware'] as $mw) {
                (new $mw())->handle($request);
            }
            [$class, $method] = $route['handler'];
            (new $class())->{$method}($request);
            return;
        }
        if ($allowed !== []) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            throw new HttpException(405);
        }
        throw new HttpException(404);
    }
}
