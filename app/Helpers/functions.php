<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Env;
use App\Core\HttpException;
use App\Core\Session;
use App\Core\View;
use App\Models\Setting;

function env(string $key, mixed $default = null): mixed
{
    return Env::get($key, $default);
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/** Escape for HTML text and attribute context. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

/** Absolute URL on the canonical host. */
function url(string $path = '/'): string
{
    return config('app.url') . '/' . ltrim($path, '/');
}

/** Cache-busted asset URL (relative, same host). */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = BASE_PATH . '/public/assets/' . $path;
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return '/assets/' . $path . '?v=' . $v;
}

/** Site setting from DB with fallback. Safe when the DB is unavailable. */
function setting(string $key, ?string $default = null): ?string
{
    try {
        return Setting::get($key) ?? $default;
    } catch (Throwable) {
        return $default;
    }
}

/** Business NAP value: DB setting "business.<key>" overrides config/business.php. */
function business(string $key): mixed
{
    $fallback = config('business.' . $key);
    if (is_array($fallback)) {
        return $fallback;
    }
    $value = setting('business.' . $key, $fallback === null ? null : (string) $fallback);
    return $value === '' ? null : $value;
}

function tel_href(): string
{
    return 'tel:' . business('phone_e164');
}

function whatsapp_href(?string $message = null): string
{
    $message ??= setting('whatsapp_default_message', 'Hello Dubai Towing Experts, I need recovery assistance in Dubai. My location is: ');
    return 'https://wa.me/' . business('whatsapp') . '?text=' . rawurlencode((string) $message);
}

function csrf_field(): string
{
    return Csrf::field();
}

/** Previously submitted value after a failed validation redirect. */
function old(string $key, string $default = ''): string
{
    $old = Session::getFlash('old', []);
    return is_array($old) && isset($old[$key]) && is_string($old[$key]) ? $old[$key] : $default;
}

/** @return array<string, string> */
function errors(): array
{
    $errors = Session::getFlash('errors', []);
    return is_array($errors) ? $errors : [];
}

function partial(string $name, array $data = []): string
{
    return View::partial($name, $data);
}

function redirect(string $to, int $status = 302): never
{
    header('Location: ' . $to, true, $status);
    exit;
}

/** Redirect back to a same-site path only (prevents open redirects). */
function redirect_back(string $fallback = '/'): never
{
    $ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $path = (string) parse_url($ref, PHP_URL_PATH);
    $host = (string) parse_url($ref, PHP_URL_HOST);
    $ownHost = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $ownHost = explode(':', $ownHost)[0];
    $target = ($path !== '' && str_starts_with($path, '/') && !str_starts_with($path, '//') && ($host === '' || $host === $ownHost)) ? $path : $fallback;
    $query = (string) parse_url($ref, PHP_URL_QUERY);
    redirect($target . ($query !== '' && $target === $path ? '?' . $query : ''));
}

function abort(int $status, string $message = ''): never
{
    throw new HttpException($status, $message);
}

function slugify(string $text, int $max = 80): string
{
    $text = mb_strtolower(trim($text));
    $text = (string) preg_replace('/[^a-z0-9]+/u', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text);
    $text = trim($text, '-');
    if (strlen($text) > $max) {
        $text = rtrim(substr($text, 0, $max), '-');
    }
    return $text;
}

function str_limit(string $text, int $limit): string
{
    $text = trim((string) preg_replace('/\s+/', ' ', strip_tags($text)));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    $cut = mb_substr($text, 0, $limit - 1);
    $space = mb_strrpos($cut, ' ');
    return rtrim($space !== false && $space > $limit * 0.6 ? mb_substr($cut, 0, $space) : $cut, ' ,.;:') . '…';
}

function format_date(?string $date, string $format = 'j M Y'): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $ts = strtotime($date);
    return $ts === false ? '' : date($format, $ts);
}

/** Per-request CSP nonce. */
function csp_nonce(): string
{
    static $nonce = null;
    return $nonce ??= base64_encode(random_bytes(16));
}

/** Current request path (used for nav state and canonical defaults). */
function current_path(): string
{
    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    return $path === '' ? '/' : $path;
}

function is_active(string $prefix): bool
{
    $path = current_path();
    return $prefix === '/' ? $path === '/' : str_starts_with($path, $prefix);
}

/** Published services for global navigation/footer (memoised; empty if DB unavailable). */
function nav_services(): array
{
    static $items = null;
    try {
        return $items ??= \App\Models\Service::published();
    } catch (Throwable) {
        return [];
    }
}

function nav_areas(): array
{
    static $items = null;
    try {
        return $items ??= \App\Models\Area::published();
    } catch (Throwable) {
        return [];
    }
}

/** Dubai skyline silhouette used as a decorative layer on dark sections (inlined once per request). */
function skyline(): string
{
    static $svg = null;
    return $svg ??= (string) @file_get_contents(BASE_PATH . '/public/assets/img/skyline.svg');
}

/** Inline SVG icon from the sprite in resources/partials/icons.php. */
function icon(string $name, string $class = 'icon'): string
{
    $fill = in_array($name, ['star', 'whatsapp', 'zap', 'facebook', 'heart'], true) ? ' icon--fill' : '';
    return '<svg class="' . e($class . $fill) . '" aria-hidden="true" focusable="false"><use href="#i-' . e($name) . '"></use></svg>';
}

/** Privacy-preserving IP fingerprint for abuse control. */
function ip_hash(string $ip): string
{
    return hash_hmac('sha256', $ip, (string) config('app.key', 'dre'));
}

/** Responsive <img> for a media row (from App\Models\Media). */
function media_img(?array $media, string $sizes = '100vw', array $attrs = []): string
{
    if ($media === null) {
        return '';
    }
    $widths = array_map('intval', explode(',', (string) $media['widths']));
    sort($widths);
    $srcset = [];
    foreach ($widths as $w) {
        $srcset[] = e('/uploads/' . $media['path'] . '-' . $w . '.webp') . ' ' . $w . 'w';
    }
    $largest = end($widths);
    $default = $widths[min(1, count($widths) - 1)];
    $attrs += ['loading' => 'lazy', 'decoding' => 'async'];
    $extra = '';
    foreach ($attrs as $k => $v) {
        $extra .= ' ' . e($k) . '="' . e($v) . '"';
    }
    $height = (int) round((int) $media['height'] * ($largest / max(1, (int) $media['width'])));
    return sprintf(
        '<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s"%s>',
        e('/uploads/' . $media['path'] . '-' . $default . '.webp'),
        implode(', ', $srcset),
        e($sizes),
        $largest,
        $height,
        e($media['alt_text']),
        $extra
    );
}

function media_url(?array $media, int $width = 1600): ?string
{
    if ($media === null) {
        return null;
    }
    $widths = array_map('intval', explode(',', (string) $media['widths']));
    $pick = max(array_filter($widths, static fn (int $w): bool => $w <= $width) ?: [min($widths)]);
    return url('/uploads/' . $media['path'] . '-' . $pick . '.webp');
}
