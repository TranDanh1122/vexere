<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
