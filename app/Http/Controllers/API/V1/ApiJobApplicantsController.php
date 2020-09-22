<?php

namespace App\Http\Controllers\API\V1;

use App\Mails\SendWorkerJobApplicationEmail;
use App\Models\Companies\Company;
use App\Models\Companies\Job;
use App\Models\Companies\JobApplicant;
use App\Models\Companies\JobStat;
use App\Notifications\WorkerAppliesToJob;
use App\Notifications\CompanyAssignsUserToRejectOrShortlist;
use App\Repositories\JobRepository;
use App\User;
use App\Models\Users\Users;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Http\Request;

class ApiJobApplicantsController extends ApiBaseController
{

    /**
     * @OA\Get(
     *      path="/job/{id}/apply",
     *      tags={"Applicant"},
     *      summary="A worker apply a job",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="job id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function apply(Request $request) {

        DB::beginTransaction();

        $user = JWTAuth::toUser();
        $job = Job::find($request->id);

        try {

            if (!$user || !$job) {

                throw new \Exception('could not find user or job.');
            }

            $company = Company::find($job->company_id);

            if (!$company || !$company->CreatedBy) {

                throw new \Exception('could not find company.');
            }

            // check if its already applied
            $applicant = JobApplicant::where('user_id', $user->id)->where('job_id', $job->id)->exists();

            // already applied for the job
            if ($applicant) {

                $msg = 'You already applied for this job.';
                return $this->apiErrorResponse(
                    false,
                    $msg,
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    ['job_post_applicant' => $msg]
                );
            }

            $companyAdmin = $company->CreatedBy;

            // apply applicant

            $jobApplicant = JobApplicant::create([
                'job_id' => $request->id,
                'user_id' => $user->id,
                'applied_at' => Carbon::now(),
                'selected' => 'Pending'
            ]);

            $job->company = $company;
            $job->companyAdmin = $companyAdmin;
            $job->jobApplicantUser = $jobApplicant->User;

            if ($job->title || ($job->job_role && $job->job_role->job_role_name)) {

                $company_user = (new Users)->f($company->created_by);
//                return $this->apiErrorResponse(false, $company_user, self::INTERNAL_SERVER_ERROR, 'internalServerError');

                $company_user->notify(new WorkerAppliesToJob($job, $user));
                // email company for applicant applied job
                \Mail::to( $companyAdmin->email )->send( new SendWorkerJobApplicationEmail( $job ) );
            }

            JobStat::create([
                'job_id' => $request->id,
                'scored_to' => $user->id,
                'performed_by' => $user->id,
                'category' => 'viewed',
                'created_at' => Carbon::now()
            ]);

        } catch(\Exception $e) {

            DB::rollBack();

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        DB::commit();

        return $this->apiSuccessResponse( [], true, 'Successfully applied.', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/job/{id}/applicants",
     *      tags={"Applicant"},
     *      summary="Get applicants in a job",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="job id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function applicants(Request $request) {

        $sortBy = !empty(trim($request->sort)) ? strtolower(trim($request->sort)) : null;

        $applicants = JobApplicant::query();

        $applicants = $applicants->select(
            'job_post_applicants.*',
            'jp.status',
            'job_post_applicants.selected',
            "w.profile_description",
            "w.has_whitecard",
            "w.params",
            "u.*",
            "work_experience.job_role",
            "work_experience.company_id",
            "work_experience.company_name",
            \DB::raw("CONCAT(first_name, ' ', last_name) AS full_name")
        );

        $applicants = $applicants->with('User');
        $applicants = $applicants->join('users as u', 'u.id', '=', 'job_post_applicants.user_id');
        $applicants = $applicants->join('worker_details as w', 'w.user_id', '=', 'job_post_applicants.user_id');
        $applicants = $applicants->join('job_posts as jp', 'jp.id', '=', 'job_post_applicants.job_id');
//        $applicants = $applicants->leftJoin("work_experience as we", 'u.id', '=', 'job_post_applicants.user_id' ,function($query) {
//            $query->on('u.id','=','we.user_id')
//                ->whereRaw('we.id IN (select MAX(a2.id) from answers as a2 join users as u2 on u2.id = a2.user_id group by u2.id) LIMIT 1');
//        });
        $applicants = $applicants->leftJoin('work_experience', function($query) {
            $query->on('u.id','=','work_experience.user_id')
                ->whereRaw('work_experience.id IN (select MAX(a2.id) from work_experience as a2 join users as u2 on u2.id = a2.user_id group by u2.id)');
        });

      //  $applicants = $applicants->leftJoin(DB::raw("work_experience we on u.id = we.user_id"));
        if (!empty(trim($request->keyword))) {

            $applicants = $applicants->where('first_name', 'like', "%{$request->keyword}%")
                            ->orWhere('last_name', 'like', "%{$request->keyword}%");
        }

        $applicants = $applicants->where('job_id', $request->id);

        if($request->userIds)
            $applicants = $applicants->whereIn('u.id', $request->userIds);

        if ($sortBy) {

            switch ($sortBy)
            {

                case 'most recent':

                    $sortBy = 'desc';

                    break;

                default:

                    $sortBy = 'asc';
            }

            $applicants = $applicants->orderBy('applied_at', $sortBy);
        }

        $applicants = $applicants->groupBy('user_id')->get();

        $applicants = $applicants->map(function ($applicant){

            $companyName = $applicant->company_name;

            if ($applicant->company_id && $company = Company::find($applicant->company_id)) {

                $companyName = $company->name;
            }

            return collect([
                'id' => $applicant->id,
                'status' => $applicant->status,
                'selected' => $applicant->selected,
                'company_id' => $applicant->company_id,
                'user_id' => $applicant->user_id,
                'full_name' => $applicant->full_name,
                'job_role' => $applicant->job_role,
                'company_name' => $companyName,
                'applied_at_proper' => Carbon::parse($applicant->applied_at)->diffForHumans(),
                'experiences' => $applicant->user->experiences,
                'educations' =>  $applicant->user->educations,
                'tickets' => $applicant->user->tickets,
                'skills' => $applicant->user->skills,
                'latest_exp' => $applicant->user->workerDetail->getLatestExperience(),
                'profile_description' => $applicant->profile_description,
                'sectors' => $applicant->user->workerDetail->sectors,
                'tiers' => $applicant->user->workerDetail->tiers,
                'english_skill' => $applicant->user->workerDetail->english_skill,
                'drivers_license' => $applicant->user->workerDetail->drivers_license,
                'params' => $applicant->user->workerDetail->params,
                'right_to_work' => $applicant->user->workerDetail->right_to_work,
                'ideal_next_role' => $applicant->user->workerDetail->introduction,
                'max_distance' => $applicant->user->workerDetail->max_distance,
                'states' => $applicant->user->workerDetail->state,
                'has_whitecard' => $applicant->user->workerDetail->has_whitecard
            ]);
        });

        return $this->apiSuccessResponse( compact('applicants'), true, 'Successfully retrieved applicants.', self::HTTP_STATUS_REQUEST_OK);

    }

    public function applicantsByType(Request $request) {

        $sortBy = !empty(trim($request->sort)) ? strtolower(trim($request->sort)) : null;

        $applicants = JobApplicant::query();

        $applicants = $applicants->select(
            'job_post_applicants.*',
            "w.profile_description",
            "u.*",
            "work_experience.job_role",
            "work_experience.company_id",
            "work_experience.company_name",
            \DB::raw("CONCAT(first_name, ' ', last_name) AS full_name")
        );

        $applicants = $applicants->with('User');
        $applicants = $applicants->join('users as u', 'u.id', '=', 'job_post_applicants.user_id');
        $applicants = $applicants->join('worker_details as w', 'w.user_id', '=', 'job_post_applicants.user_id');

        if( $request->applicantType != 'all')
            $applicants = $applicants->leftJoin('job_post_stats as j', 'j.scored_to', '=', 'job_post_applicants.user_id');

//        $applicants = $applicants->leftJoin("work_experience as we", 'u.id', '=', 'job_post_applicants.user_id' ,function($query) {
//            $query->on('u.id','=','we.user_id')
//                ->whereRaw('we.id IN (select MAX(a2.id) from answers as a2 join users as u2 on u2.id = a2.user_id group by u2.id) LIMIT 1');
//        });
        $applicants = $applicants->leftJoin('work_experience', function($query) {
            $query->on('u.id','=','work_experience.user_id')
                ->whereRaw('work_experience.id IN (select MAX(a2.id) from work_experience as a2 join users as u2 on u2.id = a2.user_id group by u2.id)');
        });

      //  $applicants = $applicants->leftJoin(DB::raw("work_experience we on u.id = we.user_id"));
        if (!empty(trim($request->keyword))) {

            $applicants = $applicants->where('first_name', 'like', "%{$request->keyword}%")
                            ->orWhere('last_name', 'like', "%{$request->keyword}%");
        }

        $applicants = $applicants->where('job_post_applicants.job_id', $request->id);

        if( $request->applicantType != 'all')
            $applicants = $applicants->where('j.category', $request->applicantType);

        if ($sortBy) {

            switch ($sortBy)
            {

                case 'most recent':

                    $sortBy = 'desc';

                    break;

                default:

                    $sortBy = 'asc';
            }

            $applicants = $applicants->orderBy('applied_at', $sortBy);
        }

        $applicants = $applicants->groupBy('user_id')->get();

        $applicants = $applicants->map(function ($applicant){

            $companyName = $applicant->company_name;

            if ($applicant->company_id && $company = Company::find($applicant->company_id)) {

                $companyName = $company->name;
            }

            return collect([
                'id' => $applicant->id,
                'company_id' => $applicant->company_id,
                'user_id' => $applicant->user_id,
                'full_name' => $applicant->full_name,
                'job_role' => $applicant->job_role,
                'company_name' => $companyName,
                'applied_at_proper' => Carbon::parse($applicant->applied_at)->diffForHumans(),
                'experiences' => $applicant->user->experiences,
                'educations' =>  $applicant->user->educations,
                'tickets' => $applicant->user->tickets,
                'skills' => $applicant->user->skills,
                'latest_exp' => $applicant->user->workerDetail->getLatestExperience(),
                'profile_description' => $applicant->profile_description,
                'sectors' => $applicant->user->workerDetail->sectors,
                'tiers' => $applicant->user->workerDetail->tiers,
                'english_skill' => $applicant->user->workerDetail->english_skill,
                'drivers_license' => $applicant->user->workerDetail->drivers_license,
                'right_to_work' => $applicant->user->workerDetail->right_to_work,
                'ideal_next_role' => $applicant->user->workerDetail->introduction,
                'max_distance' => $applicant->user->workerDetail->max_distance,
                'states' => $applicant->user->workerDetail->state
            ]);
        });

        return $this->apiSuccessResponse( compact('applicants'), true, 'Successfully retrieved applicants.', self::HTTP_STATUS_REQUEST_OK);

    }

    public function singleApplicant(Request $request)
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        $applicants = JobApplicant::query();

        $applicants = $applicants->select(
            'job_post_applicants.*'
        );

        $applicants = $applicants->with('User');
        $applicants = $applicants->join('job_posts', 'job_posts.id', '=', 'job_post_applicants.job_id')
            ->where('job_posts.created_by', $user->id)->where('job_post_applicants.user_id', $request->applicantId)
            ->where('job_post_applicants.selected', '!=', 'Declined')->count();

        return $this->apiSuccessResponse( $applicants, true, 'Successfully retrieved applicants.', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Post(
     *      path="/job/{id}/do-score",
     *      tags={"Applicant"},
     *      summary="Score a stats by different catories (favourite, invited, not suitable)",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="job id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="scored_to",
     *                      description="Score an applicant (user_id)",
     *                      type="integer",
     *                      example=3
     *                  ),
     *                  @OA\Property(
     *                      property="category",
     *                      description="categories (viewed, invited, favourite, not_suitable)",
     *                      type="string",
     *                      example="favourite"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function postDoScore( Request $request )
    {

        DB::beginTransaction();

        $user = JWTAuth::toUser();

        $data = [
            'subscribed' => true
        ];

        try {

            $rules = [
                'category'  => 'required|in:viewed,invited,favourite,not_suitable',
                'scored_to' => 'required',
            ];

            $validator = \Validator::make( $request->all() , $rules);

            if( $validator->fails() ){

                return $this->apiErrorResponse(
                    false,
                    $validator->errors(),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $validator->errors()->toArray()
                );

            }

            // check if the scored_to or user exist

            $scoredTo = User::find($request->scored_to);

            if (!$scoredTo) {

                throw new \Exception();
            }

            // check if its already exists

            /** TODO complete this section to only use a single line for suitable and unsuitable users */
            if( ($request->category == 'favourite' || $request->category == 'not_suitable') ) {
                $stat = DB::table('job_post_stats')
                    ->where('scored_to', $scoredTo->id)
                    ->where('job_id', $request->id)
                    ->whereIn('category', ['favourite', 'not_suitable'])
                    ->first();

                if($request->deleted == true && $stat) {
                    DB::table('job_post_stats')
                        ->where('scored_to', $scoredTo->id)
                        ->where('job_id', $request->id)
                        ->whereIn('category', ['favourite', 'not_suitable'])
                        ->delete();
                    $data['subscribed'] = false;
                } elseif($stat) {
                    DB::table('job_post_stats')
                        ->where('scored_to', $scoredTo->id)
                        ->where('job_id', $request->id)
                        ->whereIn('category', ['favourite', 'not_suitable'])
                        ->update(['category' =>  $request->category]);
                } else {
                    JobStat::create([
                        'job_id' => $request->id,
                        'scored_to' => $scoredTo->id,
                        'performed_by' => $user->id,
                        'category' => $request->category,
                        'created_at' => Carbon::now()
                    ]);

                    $data['subscribed'] = true;
                }

                $user = (new Users)->f($scoredTo->id);
//                return $this->apiErrorResponse(false, $company_user, self::INTERNAL_SERVER_ERROR, 'internalServerError');
                $job = (new JobRepository)->getJob($request->id);
                $user->notify(new CompanyAssignsUserToRejectOrShortlist($job, $user, $request->category));

            } else {
                $stat = JobStat::where(['job_id' => $request->id, 'scored_to' => $scoredTo->id, 'category' => $request->category])->first();

                if ($stat) {

                    $stat->delete();
                    $data['subscribed'] = false;

                } else {

                    JobStat::create([
                        'job_id' => $request->id,
                        'scored_to' => $scoredTo->id,
                        'performed_by' => $user->id,
                        'category' => $request->category,
                        'created_at' => Carbon::now()
                    ]);

                    $data['subscribed'] = true;
                }
            }

        } catch(\Exception $e) {

            DB::rollback();
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        DB::commit();

        $not_suitable = JobStat::where(['job_id' => $request->id, 'category' => 'not_suitable'])->pluck('scored_to');
        $favourites = JobStat::where(['job_id' => $request->id, 'category' => 'favourite'])->pluck('scored_to');

        $data['not_suitable'] = $not_suitable;
        $data['favourites'] = $favourites;

        return $this->apiSuccessResponse( $data, true, 'Successfully added a score.', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Get(
     *      path="/job/{id}/search-applicants?keyword=John",
     *      tags={"Applicant"},
     *      summary="Search in a job applicants by keyword",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="job id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function searchJobApplicants( Request $request )
    {

        $keyword = empty(trim($request->keyword)) ? '' : trim($request->keyword);

        $users = User::query();

        $users = $users->select(
            'users.*',
            'ja.user_id',
            'ja.job_id',
            'ja.applied_at',
            'ja.selected'
        );

        $users = $users->join('job_post_applicants as ja', 'users.id', 'ja.user_id');
        $users = $users->with([
            'WorkerDetail',
            'WorkerDetail.Areas',
            'WorkerDetail.Sectors',
            'WorkerDetail.Tiers',
            'Educations',
            'Tickets',
            'Skills'
        ]);

        $applicants = $users->where('first_name', 'like', "%{$keyword}%")->where('ja.job_id', $request->id)->get();

        return $this->apiSuccessResponse( compact('applicants', 'users'), true, '', self::HTTP_STATUS_REQUEST_OK);
    }
}
