<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido1'], 401);
        }

        return $next($request);
    }
}
