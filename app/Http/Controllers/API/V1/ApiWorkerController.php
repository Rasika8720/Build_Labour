<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Skills\Level;
use App\Models\Users\Education;
use App\Models\Users\WorkExperience;
use App\Repositories\SkillRepository;
use App\Repositories\WorkerRepository;
use Illuminate\Http\Request;
use JWTAuth;

class ApiWorkerController extends ApiBaseController
{
    /**
     * @var WorkerRepository
     */
    protected $workerRepo;

    public function __construct(WorkerRepository $repository) {

        $this->workerRepo = $repository;
    }

    /**
     * @OA\Post(
     *      path="/worker/next-role",
     *      tags={"Worker"},
     *      summary="Update worker next role or Step 6",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="introduction",
     *                      description="Ideal next role",
     *                      type="string",
     *                      example="My next target is as CEO"
     *                  ),
     *                  @OA\Property(
     *                      property="when",
     *                      description="When or what date",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="max_distance",
     *                      description="Distance from home",
     *                      type="integer",
     *                      example=20
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      description="Address",
     *                      type="string",
     *                      example="21st corner st conwalum"
     *                  ),
     *                  @OA\Property(
     *                      property="state",
     *                      description="State",
     *                      type="string",
     *                      example="VIC"
     *                  ),
     *                  @OA\Property(
     *                      property="right_to_work",
     *                      description="right to work in Australia?",
     *                      type="integer",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="willing_to_relocate",
     *                      type="boolean",
     *                      example=true
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
    public function updateNextRole( Request $request )
    {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            if( !$user->workerDetail->store($request) ){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }


            // if (isset($request->countries)) {

                $user->country = $request->countries;
                $user->save();
            // }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( [ 'worker_detail' => $user->workerDetail ], true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function deleteNextRole( Request $request )
    {

        try {

            $this->workerRepo->deleteIdealRole();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }


        return $this->apiSuccessResponse( [], true, 'Successfully deleted ideal role', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/worker/about-me",
     *      tags={"Worker"},
     *      summary="Update worker about me",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="profile_description",
     *                      description="About me",
     *                      type="string",
     *                      example="Experienced Senior Project Manager; demonstrated history of working on a wide range of construction projects for leading companies."
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
    public function updateAboutMe( Request $request )
    {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            if( !$user->workerDetail->store($request) ){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }


        return $this->apiSuccessResponse( [ 'worker_detail' => $user->workerDetail ], true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function deleteAboutMe( Request $request )
    {

        try {

            $this->workerRepo->deleteAboutMe();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }


        return $this->apiSuccessResponse( [], true, 'Successfully deleted about me information', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/worker/optional",
     *      tags={"Worker"},
     *      summary="Update Worker optional information",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="first_name",
     *                      description="First Name",
     *                      type="string",
     *                      example="Chris"
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      description="Last Name",
     *                      type="string",
     *                      example="Webber"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      description="Address",
     *                      type="string",
     *                      example="200 St. Southern Lane Avenue."
     *                  ),
     *                  @OA\Property(
     *                      property="profile_description",
     *                      description="Profile Description",
     *                      type="string",
     *                      example="Good worker"
     *                  ),
     *                  @OA\Property(
     *                      property="gender",
     *                      description="Gender",
     *                      type="string",
     *                      example="Male"
     *                  ),
     *                  @OA\Property(
     *                      property="date_of_birth",
     *                      description="Date of Birth",
     *                      type="string",
     *                      example="1988-10-08"
     *                  ),
     *                  @OA\Property(
     *                      property="marital_status",
     *                      description="Marital Status",
     *                      type="string",
     *                      example="Married"
     *                  ),
     *                  @OA\Property(
     *                      property="english_skill",
     *                      description="english_skill",
     *                      type="string",
     *                      example="Excellent in English"
     *                  ),
     *                  @OA\Property(
     *                      property="countries",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="china"
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="drivers_license",
     *                      description="Drivers License",
     *                      type="string",
     *                      example="I have a professional drivers license."
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
    public function updateOptional( Request $request )
    {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            if( !$user->workerDetail->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }

            $user->isOptionalTransaction = true;
            $request->request->add(['id' => $user->id]);

            if( !$user->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $optional = $request->all();

        return $this->apiSuccessResponse( compact('optional'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function updateIntroduction( Request $request )
    {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            $user->workerDetail->isIntroductionUpdate = true;

            if( !$user->workerDetail->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }

            $user->isForIntroduction = true;
            $request->request->add(['id' => $user->id]);

            if( !$user->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $introduction = $request->all() + ['education' => $user->WorkerDetail->Education ];

        return $this->apiSuccessResponse( compact('introduction'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/worker/experiences",
     *      tags={"Worker"},
     *      summary="Worker Employment History / Experiences",
     *      security={{"BearerAuth":{}}},
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Experiences"
     *      )
     * )
     */
    public function experiences() {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        $experiences = $user->experiences;

        $experiences->map(function($exp) {

            $exp->responsibilities;
        });

        return $this->apiSuccessResponse( compact('experiences'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/worker/educations",
     *      tags={"Worker"},
     *      summary="Educations",
     *      security={{"BearerAuth":{}}},
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Experiences"
     *      )
     * )
     */

    public function educations() {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }


        $educations = Education::where('user_id', $user->id)
                    ->orderBy('end_year', 'asc')
                    ->get();

        $user->educations = $educations;

        return $this->apiSuccessResponse( compact('educations'), true, 'Successfully retrieved worker educations', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/worker/skill-options",
     *      tags={"Worker"},
     *      summary="Skill Options Default",
     *      security={{"BearerAuth":{}}},
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Experiences"
     *      )
     * )
     */
    public function skillOptions(SkillRepository $repo) {

        $skills = $repo->all();
        $levels = Level::all();
        return $this->apiSuccessResponse( compact('skills', 'levels'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/worker/personal-details",
     *      tags={"Work"},
     *      summary="Step 8: Worker Personal Details",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="gender",
     *                      type="string",
     *                      example="male"
     *                  ),
     *                  @OA\Property(
     *                      property="date_of_birth",
     *                      type="string",
     *                      example="08-10-1988"
     *                  ),
     *                  @OA\Property(
     *                      property="country_birth",
     *                      type="string",
     *                      example="australia"
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

    public function updatePersonalDetails( Request $request )
    {

        try {

            if( ! $result = $this->workerRepo->updatePersonalDetails($request) ) {

                return $this->apiErrorResponse(
                    false,
                    $this->workerRepo->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->workerRepo->workerDetail->getErrorsDetail()
                );
            }
        
        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
            
        return $this->apiSuccessResponse( compact('result'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }


    /**
     * @OA\Get(
     *      path="/worker/view/{userid}",
     *      tags={"Worker"},
     *      summary="Get user as worker",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="userid",
     *          description="user id",
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Authenticated User"
     *      )
     * )
     */
    public function getWorker( Request $request )
    {
        try {

            $user = $this->workerRepo->getWorker($request);

            if (!$user) {

                $errMessage = 'User not found';

                return $this->apiErrorResponse(
                    false,
                    $errMessage,
                    self::HTTP_STATUS_INVALID_INPUT,
                    ['not_worker' => $errMessage]
                );
            }

            if (!$user->workerDetail) {

                $notAWorkerMsg = 'This user profile is not a worker';

                return $this->apiErrorResponse(
                    false,
                    $notAWorkerMsg,
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    ['not_worker' => $notAWorkerMsg]
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'user' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }
}