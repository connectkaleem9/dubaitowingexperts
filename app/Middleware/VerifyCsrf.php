<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;

/** Applied globally in public/index.php to every POST. */
final class VerifyCsrf
{
    public function handle(Request $request): void
    {
        if ($request->isPost() && !Csrf::valid($request->input('_token'))) {
            abort(419, 'Your session expired. Please go back, refresh the page and try again.');
        }
    }
}
