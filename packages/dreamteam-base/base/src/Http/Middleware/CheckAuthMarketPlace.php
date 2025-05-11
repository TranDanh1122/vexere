<?php

namespace DreamTeam\Base\Http\Middleware;

use Closure;

class CheckAuthMarketPlace
{
    public function handle($request, Closure $next)
    {
        $config = config('app');
        $marketPlaceToken = $config['marketplace_token'] ?? '';
        $authToken = $request->header('token');
        if (!$authToken || $authToken != $marketPlaceToken) {
            return response()->json(['message' => 'Unauthorized: You are not allowed to access this resource.'], 401);
        }
        return $next($request);
    }
}
