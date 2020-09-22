<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Users\WorkerDetail;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use JWTAuth;

class ApiUserSkillsController extends ApiBaseController
{

    /**
     * @var UserRepository
     */
    protected $userRepo;

    public function __construct(UserRepository $repository) {

        $this->userRepo = $repository;
    }

    /**
     * @OA\POST(
     *      path="/user/skills",
     *      tags={"Worker"},
     *      summary="Update worker main skill and skills or Step 5",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="Skill Id",
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
     *                      property="main_skill",
     *                      description="<b>Required</b> Main Skill",
     *                      type="string",
     *                      example="I'm a good worker"
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

        try {

            if (!$data = $this->userRepo->saveMainSkill($request)) {

                return $this->apiErrorResponse(
                    false,
                    $this->userRepo->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->userRepo->workerDetail->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( $data , true, 'Successfully updated worker skills', self::HTTP_STATUS_REQUEST_OK);
    }


    public function deleteMainSkills( Request $request )
    {

        try {

            $this->userRepo->deleteMainSkills($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'skills', 'main_skill' ), true, 'Successfully deleted worker skills', self::HTTP_STATUS_REQUEST_OK);
    }

}
