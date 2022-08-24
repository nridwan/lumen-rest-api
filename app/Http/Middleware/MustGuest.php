<?php

namespace App\Http\Middleware;

use App\Jwt\JwtApp;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MustGuest
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected Auth $auth;
    protected JwtApp $jwtApp;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, JwtApp $jwtApp)
    {
        $this->auth = $auth;
        $this->jwtApp = $jwtApp;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $header = $request->header('Authorization');
        $auth = $this->auth->guard($guard);
        if (!$this->jwtApp->checkToken($this->jwtApp->parseToken($header), false)) {
            throw new HttpException(401, 'Unauthorized.');
        }

        return $next($request);
    }
}
