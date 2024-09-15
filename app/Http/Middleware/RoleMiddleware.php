<?php

namespace App\Http\Middleware;

use Spatie\Permission\Middleware\PermissionMiddleware as OriginalRoleMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class RoleMiddleware extends OriginalRoleMiddleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
