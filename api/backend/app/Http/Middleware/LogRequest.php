<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class LogRequest
{

    public function handle($request, Closure $next)
    {
        $user_id = 0;
        if ($request->user()) {
            $user_id = $request->user()->id;
        }
        App::setLocale($request->lang ?? 'kh');
        log_error("================================================");
        log_infos("Request ==> ", [
            "url"     => $request->fullUrl(),
            "method"  => $request->method(),
            "header"  => $request->header(),
            "payload" => $request->all(),
            "ip"      => $request->ip(),
            "user"    => $user_id,
        ]);

        return $next($request);
    }
}
