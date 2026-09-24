<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;

final class Authenticate
{
    public function handle(Request $request): void
    {
        if (Auth::user() === null) {
            if ($request->method() === 'GET') {
                Session::put('intended', $request->path());
            }
            redirect('/admin/login/');
        }
        header('Cache-Control: no-store, private');
        header('X-Robots-Tag: noindex, nofollow');
    }
}
