<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Token;

class CheckIfAdmin
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

        if (!$token) {

            if ($page == 'admin.login') {

                return $next($request);
            }
        }

        try {

            if ( !$token ) {

                $user = JWTAuth::parseToken()->authenticate();

            } else {

                $rawToken = substr($token, 1, -1);
                $token = new Token($rawToken);
                $payload = JWTAuth::decode($token);

                $user = Auth::loginUsingId($payload['sub']);
            }


            if (! $user->isAdmin()) {

                JWTAuth::invalidate();

                setcookie('bl_token', null, time() + (86400 * 30), '/');

                return redirect(route('login'));
            }
        
        } catch(\Exception $e) {

            setcookie('bl_token', null, time() + (86400 * 30), '/');
            
            return redirect(route('login'));
        }

        if ($token && $page == 'admin.login') {

            return redirect(route('admin.index'));
        }

        return $next($request);
    }

    public static function parseToken($method = 'bearer', $header = 'authorization', $query = 'token')
    {
        return JWTAuth::parseToken($method, $header, $query);
    }
}
