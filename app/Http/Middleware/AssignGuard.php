<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AssignGuard
{
    /*
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */


    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($guard != null)
            auth()->shouldUse($guard);
        return $next($request);
    }


}
