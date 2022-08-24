<?php

namespace App\Http\Middleware;

use App\Jwt\JwtUser;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticateRefresh
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected Auth $auth;
    protected JwtUser $jwtUser;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, JwtUser $jwtUser)
    {
        $this->auth = $auth;
        $this->jwtUser = $jwtUser;
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
        if (!$this->jwtUser->checkToken($this->jwtUser->parseToken($header), true)) {
            throw new HttpException(401, 'Unauthorized.');
        }

        return $next($request);
    }
}
