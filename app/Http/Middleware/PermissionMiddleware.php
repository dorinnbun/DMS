<?php

namespace App\Http\Middleware;

use Spatie\Permission\Middleware\PermissionMiddleware as OriginalPermissionMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class PermissionMiddleware extends OriginalPermissionMiddleware
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
