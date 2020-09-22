<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Users\FirebaseUsers;
use Illuminate\Http\Request;
use JWTAuth;

class ApiDevicesController extends ApiBaseController
{
    /**
     *  Create a new controller instance.
     *
     */
    public function __construct()
    {

    }
    /**
     * @OA\Post(
     *      path="/device/register",
     *      tags={"Device"},
     *      summary="Register an Android, iOS or web device for push notifications and chat. Auth Bearer authentication",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="device_token",
     *                      description="<b>Required</b><br />Device token",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function registerDevice( Request $r )
    {

        $user  = JWTAuth::toUser();

        if( ! $firebase_user = FirebaseUsers::byUserId( $user->id ) ){
            $firebase_user = new FirebaseUsers();
        }

        $r->merge( ['user_id' => $user->id ] );

        if( ! $firebase_user->store( $r ) ){
            $this->apiErrorResponse( false, $firebase_user->getErrors( true ), 400, 'savingDeviceTokenFailed' );
        }

        return $this->apiSuccessResponse( compact( 'firebase_user' ), true, ' Device successfully registered' );
    }

    /**
     * @OA\Post(
     *      path="/device/unregister",
     *      tags={"Device"},
     *      summary="Unregister an Android, iOS or web device for push notifications and chat. Auth Bearer authentication",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="device_token",
     *                      description="<b>Required</b><br />Device token",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function unregisterDevice( Request $r )
    {
        $user  = JWTAuth::toUser();

        if( $firebase_user = FirebaseUsers::byUserId( $user->id ) ){
            $firebase_user->delete();
        }

        return $this->apiSuccessResponse( [], true, ' Device successfully unregistered' );
    }

}
