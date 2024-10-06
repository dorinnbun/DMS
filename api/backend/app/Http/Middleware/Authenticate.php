<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Traits\HasHttpResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use HasHttpResponse;

    protected function redirectTo(Request $request)
        {
            return $request->expectsJson() ? null : route('login');
        }
    

    protected function unauthenticated($request, array $guards)
    {
        // throw new AuthenticationException(
        //     'Unauthenticated.', $guards, $this->redirectTo($request)
        // ); // Original code
        $response = $this
        ->httpResponse()
        ->setError(true)
        ->setStatus(401)
        ->setMessage("Unauthenticated")
        ->toApiResponse();
        throw new HttpResponseException($response);
    }
}
