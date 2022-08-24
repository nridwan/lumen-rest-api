<?php

namespace App\Http\Middleware;

use Closure;

class JsonResponseOnly
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
        if ($request->prefers('application/json') || $request->expectsJson())
        {
            return $next($request);
        }
        return abort(404);
    }
}
