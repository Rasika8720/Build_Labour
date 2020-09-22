<?php

namespace App\Http\Middleware;

use App\Models\Companies\Job;
use App\Models\Companies\JobStat;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Token;

class CompanyHasViewUserProfile
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
        // prev job is job id
        // cView is tag as company view a profile

        try {

            if (isset($request->prevJob) && isset($request->cView) && $job = Job::find($request->prevJob)) {


                // check if its already viewed

                $page = \Route::current()->getName();

                $workerUserId = $request->id;

                $jobStatExist = JobStat::where(['scored_to' => $workerUserId, 'category' => 'company_viewed_profile', 'job_id' => $job->id])->exists();

                if (!$jobStatExist) {

                    $token = $_COOKIE['bl_token'];

                    $rawToken = substr($token, 1, -1);
                    $token = new Token($rawToken);
                    $payload = JWTAuth::decode($token);

                    $user = Auth::loginUsingId($payload['sub']);


                    JobStat::create([
                        'job_id' => $job->id,
                        'scored_to' => $workerUserId,
                        'performed_by' => $user->id,
                        'category' => 'company_viewed_profile',
                        'created_at' => Carbon::now()
                    ]);
                }
            }


        } catch (\Exception $e) {

            return $next($request);
        }

        return $next($request);
    }


}
