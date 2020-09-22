<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\V1\ApiBaseController;
use App\Interfaces\ChatServices\Drivers\FirebaseChatService;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use JWTAuth;
use Closure;

class Chat
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
        \App::bind( 'ChatServiceInterface', function( $app )
        {
            switch( env( 'CHAT_SERVICE' ) ){
                case 'Firebase':
                default:
                    return new FirebaseChatService();
                break;
            }
        });
    }
}
