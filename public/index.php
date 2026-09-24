<?php

declare(strict_types=1);

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Middleware\SecurityHeaders;
use App\Middleware\VerifyCsrf;
use App\Services\Seo;

require dirname(__DIR__) . '/app/bootstrap.php';

$request = Request::capture();
(new SecurityHeaders())->handle($request);

$path = $request->path();

// 1. Permanent redirects from config/redirects.php (legacy / merged URLs).
$redirects = (array) require BASE_PATH . '/config/redirects.php';
if (isset($redirects[$path])) {
    redirect($redirects[$path], 301);
}

// 2. Canonical URL shape: /index.php → /, and add trailing slash to extension-less GET paths.
if ($path === '/index.php') {
    redirect('/', 301);
}
if ($request->method() === 'GET' && $path !== '/' && !str_ends_with($path, '/') && !preg_match('/\.[a-z0-9]{2,5}$/i', $path)) {
    $qs = $request->queryString();
    redirect($path . '/' . ($qs !== '' ? '?' . $qs : ''), 301);
}

Session::start($request->isSecure());

// 3. Remember ad attribution (last click) so leads can be tied to campaigns.
if ($request->method() === 'GET') {
    $attribution = [];
    foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'gclid'] as $key) {
        $value = mb_substr($request->query($key), 0, $key === 'gclid' ? 255 : 150);
        if ($value !== '') {
            $attribution[$key] = $value;
        }
    }
    if ($attribution !== []) {
        Session::put('attribution', $attribution);
    }
}

$router = new Router();
require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/admin.php';

try {
    (new VerifyCsrf())->handle($request);
    $router->dispatch($request);
} catch (HttpException $e) {
    $status = in_array($e->status, [403, 404, 405, 419, 429], true) ? $e->status : 400;
    http_response_code($status);
    $titles = [403 => 'Access denied', 404 => 'Page not found', 405 => 'Method not allowed', 419 => 'Session expired', 429 => 'Too many requests', 400 => 'Bad request'];
    $message = $e->getMessage() !== (string) $e->status ? $e->getMessage() : '';
    echo View::render('errors/http', [
        'status' => $status,
        'heading' => $titles[$status],
        'message' => $message,
        'seo' => Seo::simple($titles[$status], $path, 'noindex,follow'),
    ]);
}
