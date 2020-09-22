<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\V1\ApiBaseController;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use JWTAuth;
use Closure;

class Jwt extends ApiBaseController
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
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->apiErrorResponse(false, 'User Not Found', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'userNotFound');
            }

        } catch (TokenInvalidException $e) {
            return $this->apiErrorResponse(false, 'Invalid Token', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'invalidToken');

        } catch (TokenExpiredException $e) {
            return $this->apiErrorResponse(false, 'Token Expired', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'tokenExpired');
        
        } catch (JWTException $e) {
            return $this->apiErrorResponse(false, 'Token Absent', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'tokenAbsent');
        
        } catch (Exception $e) {
            return $this->apiErrorResponse(false, 'Unknown Error', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'unknownError');
        }

        return $next($request);
    }
}
