<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OnlyDev
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
            if (Auth::guard('admin')->user()->id == 1) {
                return $next($request);
            } else {
                return redirect(route('admin.home'))->with([
                    'type' => 'danger',
                    'message' => 'Core::admin.role.no_permission',
                ]);
            }
        } else {
            return redirect(route('admin.login'));
        }
    }
}
