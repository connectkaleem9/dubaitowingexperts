<?php

declare(strict_types=1);

namespace App\Core;

/** The only place that reads request superglobals. */
final class Request
{
    /** @var array<string, string> */
    public array $params = [];

    private function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $files,
        private readonly array $server,
    ) {
    }

    public static function capture(): self
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = rawurldecode((string) parse_url($uri, PHP_URL_PATH));
        $path = '/' . ltrim(preg_replace('#/{2,}#', '/', $path) ?? '/', '/');
        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            $path,
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /** Trimmed string from POST, or default. Arrays are rejected (returns default). */
    public function input(string $key, string $default = ''): string
    {
        $v = $this->post[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    /** @return list<string> */
    public function inputArray(string $key): array
    {
        $v = $this->post[$key] ?? [];
        return is_array($v) ? array_values(array_filter($v, 'is_string')) : [];
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->post;
    }

    public function query(string $key, string $default = ''): string
    {
        $v = $this->query[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    public function queryInt(string $key, int $default = 0): int
    {
        $v = filter_var($this->query[$key] ?? null, FILTER_VALIDATE_INT);
        return $v === false || $v === null ? $default : $v;
    }

    /** @return array<string, mixed>|null Single uploaded file entry (not multi). */
    public function file(string $key): ?array
    {
        $f = $this->files[$key] ?? null;
        if (!is_array($f) || !isset($f['error']) || is_array($f['error']) || $f['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return $f;
    }

    /** @return list<array<string, mixed>> Normalised multiple-file input. */
    public function files(string $key): array
    {
        $f = $this->files[$key] ?? null;
        if (!is_array($f) || !is_array($f['name'] ?? null)) {
            $single = $this->file($key);
            return $single ? [$single] : [];
        }
        $out = [];
        foreach ($f['name'] as $i => $name) {
            if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name' => $name,
                'type' => $f['type'][$i] ?? '',
                'tmp_name' => $f['tmp_name'][$i] ?? '',
                'error' => $f['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $f['size'][$i] ?? 0,
            ];
        }
        return $out;
    }

    public function ip(): string
    {
        // Trust only REMOTE_ADDR. If behind Cloudflare, configure the server to restore the real IP.
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function userAgent(): string
    {
        return mb_substr((string) ($this->server['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }

    public function referer(): string
    {
        return (string) ($this->server['HTTP_REFERER'] ?? '');
    }

    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || (($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }

    public function queryString(): string
    {
        return (string) ($this->server['QUERY_STRING'] ?? '');
    }

    public function param(string $key): string
    {
        return $this->params[$key] ?? '';
    }
}
