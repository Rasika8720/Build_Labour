<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Token;

class CheckToken
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
        $page = \Route::current()->getName();

        $token = isset($_COOKIE['bl_token']) ? $_COOKIE['bl_token'] : null;

        if (! $token) {
            if ($page == 'login' || $page == 'register' || $page == 'register_company'
                || $page == 'password_request' || $page == 'password_reset') {

                return $next($request);
            }

            return redirect('login');
        }

        if ($page == 'login' || $page == 'register' || $page == 'register_company'
            || $page == 'password_request' || $page == 'password_reset') {

            return redirect('/user/profile');
        }

        return $next($request);
    }

    public static function parseToken($method = 'bearer', $header = 'authorization', $query = 'token')
    {
        return JWTAuth::parseToken($method, $header, $query);
    }
}
