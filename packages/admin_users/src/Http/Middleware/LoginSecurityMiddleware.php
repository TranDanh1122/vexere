<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use DreamTeam\AdminUser\Support\Google2FAAuthenticator;
use Closure;
use Illuminate\Support\Facades\Auth;

class LoginSecurityMiddleware
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
        $option = getOption('googleAuthenticate', '', false);
        $isEnabled = $option['enabled'] ?? 0;
        $auth = Auth::guard('admin')->user();
        if(!$isEnabled || !$auth->enabel_google2fa) {
            return $next($request);
        }
        $authenticator = app(Google2FAAuthenticator::class)->boot($request);
        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }
        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
