<?php

namespace DreamTeam\AdminUser\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
class CheckLicense
{
    public function handle($request, Closure $next)
    {
        $settings = DB::table('settings')->where('key','dreamteam')->first();
        if (!$settings || $settings->value != md5(config('app.url', env('APP_URL')))) {
            exit();
        }
        return $next($request);
    }
}
