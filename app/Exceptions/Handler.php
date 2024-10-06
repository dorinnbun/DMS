<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Database\QueryException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (QueryException $e, $request) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate entry)
                return response()->json([
                    'message' => 'Duplicate entry detected!'
                ], 400); // Custom error response
            }
        });
    }
}
