<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Token;
use JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAuthFromToken()
    {
        $token = isset($_COOKIE['bl_token']) ? $_COOKIE['bl_token'] : null;

        if (! $token) {
            return JWTAuth::parseToken()->authenticate();
        }

        $rawToken = substr($token, 1, -1);
        $token = new Token($rawToken);
        $payload = JWTAuth::decode($token);

        return Auth::loginUsingId($payload['sub']);
    }

    public function clearAuthToken()
    {
        setcookie('bl_token', null, time() + (86400 * 30), '/');

        return redirect(route('login'));
    }
}
