<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Users\Users;
use App\Repositories\UserRepository;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\Validator;
use App\Validators\UpdateUserValidator;
use App\Validators\MailValidator;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

/**
 * Class UsersController
 * @property User user
 * @package App\Http\Controllers
 */
class ApiUsersController extends ApiBaseController
{

    public function getId()
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        return $user->id;
    }

    public function resendVerificationCode( Request $r )
    {

        if( ! $user = Users::findByCid( $r->cid ) ){

            return response()->json(
                [
                    'status' => 400,
                    'error_code' => 'userNotFound',
                    'message'=> 'User  not found'
                ],
                400
            );
        }

        if( $user->is_verified ){
            return response()->json(
                [
                    'status' => 400,
                    'error_code' => 'userAlreadyVerified',
                    'message'=> 'User was already verified'
                ],
                400
            );
        }

        // \Mail::to( $user->email )->send( new ResendVerificationCode( $user ) );

        return response()->json(
            [
                'status' => 200,
                'message'=> 'Email containing verification code successfully sent'
            ],
            200
        );
    }

    /**
     * @OA\POST(
     *      path="/user",
     *      tags={"User"},
     *      summary="Update user's information",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          description="User ID",
     *          in="query",
     *          name="id",
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
     *                      property="first_name",
     *                      description="First Name",
     *                      type="string",
     *                      example="John"
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      description="Last Name",
     *                      type="string",
     *                      example="Doe"
     *                  ),
     *                  @OA\Property(
     *                      property="country",
     *                      description="Country",
     *                      type="string",
     *                      example="Philippines"
     *                  ),
     *                  @OA\Property(
     *                      property="mobile_number",
     *                      description="Mobile Number",
     *                      type="string",
     *                      example="+63 9277468888"
     *                  ),
     *                  @OA\Property(
     *                      property="date_of_birth",
     *                      description="Date of Birth",
     *                      type="string",
     *                      example="1989-01-03"
     *                  ),
     *                  @OA\Property(
     *                      property="gender",
     *                      description="Gender",
     *                      type="string",
     *                      example="female"
     *                  ),
     *                  @OA\Property(
     *                      property="marital_status",
     *                      description="Marital Status",
     *                      type="string",
     *                      example="married"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      description="Address",
     *                      type="string",
     *                      example="216 Cabreros Street Cebu City"
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
     *          description="User Not Found / Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
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

        if( ! $user  = JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        if( ! $user->store( $request ) ){
            return $this->apiErrorResponse( false, $user->getErrors( true ) , 422 , 'savingError', $user->getErrorsDetail() );
        }

        return $this->apiSuccessResponse( ['user' => $user ] , true , 'User successfully updated');
    }

    /**
     * @OA\Post(
     *      path="/user/photo",
     *      tags={"User"},
     *      summary="Upload profile photo",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="photo",
     *                      description="Image File",
     *                      format="file",
     *                      type="string",
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
    public function uploadProfilePhoto( Request $request )
    {
        $user = JWTAuth::toUser();

        if( ! $user->uploadProfilePhoto( $request ) ){
            return $this->apiErrorResponse(false, $user->getErrors( true ), self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
        }

        return $this->apiSuccessResponse([ 'user'=>$user ], true, 'Profile Photo Uploaded Successfully ', self::HTTP_STATUS_REQUEST_OK);
    }

}
