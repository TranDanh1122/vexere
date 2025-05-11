<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            if (Auth::guard('admin')->user()->status != 1) {
                Auth::guard('admin')->logout();
                return redirect(route('admin.login'));
            }
            return $next($request);
        } else {
            return redirect(route('admin.login'));
        }
    }
}
