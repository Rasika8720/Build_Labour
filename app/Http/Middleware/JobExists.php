<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\V1\ApiBaseController;
use App\Models\Companies\Job;
use Closure;

class JobExists extends ApiBaseController
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

        $job = Job::where(['id' => $request->id])->exists();

        if (!$job) {

            return $this->apiErrorResponse(false, 'job does not exist', self::INTERNAL_SERVER_ERROR, 'internalServerError');

        }

        return $next($request);
    }

    public static function parseToken($method = 'bearer', $header = 'authorization', $query = 'token')
    {
        return JWTAuth::parseToken($method, $header, $query);
    }
}
