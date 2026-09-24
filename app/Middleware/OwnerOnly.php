<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;

/** Restricts a route to admins with the "owner" role. Use after Authenticate. */
final class OwnerOnly
{
    public function handle(Request $request): void
    {
        if (!Auth::isOwner()) {
            abort(403, 'Only the site owner can access this section.');
        }
    }
}
