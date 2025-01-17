<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('api')->check()) {
            return response()->json([
                'message' => StatusCodeEnum::UNAUTHORIZED->message(),
            ], StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}
