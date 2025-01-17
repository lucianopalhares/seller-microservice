<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\StatusCodeEnum;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {


        return $next($request);
    }
}
