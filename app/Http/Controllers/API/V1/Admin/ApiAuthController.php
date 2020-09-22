<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\API\V1\ApiBaseController;
use App\Models\Users\WorkerDetail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class ApiAuthController extends ApiBaseController
{


    public function login( Request $request )
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->apiErrorResponse(false, 'Invalid Credentials', self::HTTP_STATUS_BAD_REQUEST, 'invalidInput');
            }

        } catch (JWTException $e) {
            return $this->apiErrorResponse(false, 'Could Not Create Token '.$e->getMessage(), self::HTTP_STATUS_NOT_FOUND, 'tokenAbsent');

        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $user = JWTAuth::user();

        if (!$user->WorkerDetail) {

            WorkerDetail::create(['user_id' => $user->id, 'english_skill' => null, 'drivers_license' => null, 'right_to_work' => null]);
        }

        return $this->apiSuccessResponse( compact('user', 'token'), true, 'Login Success', self::HTTP_STATUS_REQUEST_OK);
    }

}
