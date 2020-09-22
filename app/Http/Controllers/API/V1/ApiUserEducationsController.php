<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Users\Education;
use App\Repositories\WorkerRepository;
use Illuminate\Http\Request;
use JWTAuth;

class ApiUserEducationsController extends ApiBaseController
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
     *      path="/user/education",
     *      tags={"User Education"},
     *      summary="Add a user education or Step 3",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="course_name",
     *                      description="Course",
     *                      type="string",
     *                      example="Bachelor of Psychology"
     *                  ),
     *                  @OA\Property(
     *                      property="school",
     *                      description="School",
     *                      type="string",
     *                      example="University of Melbourne"
     *                  ),
     *                  @OA\Property(
     *                      property="school_id",
     *                      description="School ID",
     *                      type="integer",
     *                      example=2
     *                  ),
     *                  @OA\Property(
     *                      property="start_day",
     *                      description="Start Day",
     *                      type="integer",
     *                      example="5"
     *                  ),
     *                  @OA\Property(
     *                      property="start_year",
     *                      description="Start Year",
     *                      type="integer",
     *                      example="2016"
     *                  ),
     *                  @OA\Property(
     *                      property="start_month",
     *                      description="Start Month",
     *                      type="integer",
     *                      example="5"
     *                  ),
     *                  @OA\Property(
     *                      property="end_day",
     *                      description="End Day",
     *                      type="integer",
     *                      example=2018
     *                  ),
     *                  @OA\Property(
     *                      property="end_year",
     *                      description="End Year",
     *                      type="integer",
     *                      example=2018
     *                  ),
     *                  @OA\Property(
     *                      property="end_month",
     *                      description="End Month",
     *                      type="integer",
     *                      example=3
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      description="Description",
     *                      type="string",
     *                      example="Where I study 4 years on this course."
     *                  ),
     *                  @OA\Property(
     *                      property="course_id",
     *                      description="Existing course",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="education_status",
     *                      description="Education Status",
     *                      type="string",
     *                      example="Completed S"
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
    public function add(Request $request)
    {

        $user = JWTAuth::toUser();

        $education = new Education();
        $education->setUserId($user->id);

        try {

            if (!$education->store($request)) {

                return $this->apiErrorResponse(
                    false,
                    $education->getErrors(true),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $education->getErrorsDetail()
                );
            }

        } catch (\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse(compact('education'), true, 'Successfully Added an Education', self::HTTP_STATUS_REQUEST_OK);
    }


    /**
     * @OA\Post(
     *      path="/user/education/{id}",
     *      tags={"User Education"},
     *      summary="Update a user education",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="Education Id",
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
     *                      property="course",
     *                      description="<b>Required</b> Course",
     *                      type="string",
     *                      example="Bachelor of Amateur Psychology"
     *                  ),
     *                  @OA\Property(
     *                      property="school",
     *                      description="<b>Required</b> School",
     *                      type="string",
     *                      example="University of Victoria"
     *                  ),
     *                  @OA\Property(
     *                      property="start_year",
     *                      description="<b>Required</b> Start Year",
     *                      type="integer",
     *                      example="11"
     *                  ),
     *                  @OA\Property(
     *                      property="end_year",
     *                      description="<b>Required</b> End Year",
     *                      type="integer",
     *                      example="12"
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
    public function update(Request $request)
    {

        $user = JWTAuth::toUser();

        $education = Education::find($request->id);

        if (!$education) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            $education->setUserId($user->id);

            if( !$education->store($request) ){

                return $this->apiErrorResponse(
                    false,
                    $education->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $education->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'education' ), true, 'Successfully updated a Education', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Delete(
     *      path="/user/education/{id}",
     *      tags={"User Education"},
     *      summary="Delete a Education",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="Education ID",
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

    public function delete(Request $request)
    {

        try {

            $result = $this->workerRepo->deleteEducation($request);

            if (!$result) {

                return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'education' ), true, 'Successfully deleted an education', self::HTTP_STATUS_REQUEST_OK);
    }
}
