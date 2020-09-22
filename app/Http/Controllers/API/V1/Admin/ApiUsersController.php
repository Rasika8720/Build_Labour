<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\API\V1\ApiBaseController;
use Illuminate\Http\Request;
use App\User;
use App\Models\Users\Users;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class ApiUsersController extends ApiBaseController
{
    public function get(Request $request)
    {
        try {
            $record = User::find($request->id)
                        ->makeHidden([
                            'identifier',
                            'date_of_birth',
                            'country',
                            'mobile_number',
                            'marital_status',
                            'full_name',
                            'dob_formatted',
                            'device_token',
                            'role_id',
                        ])->makeVisible([

                        ]);

            if ($record) {
                return $this->apiSuccessResponse(compact('record'), true, 'Request OK', self::HTTP_STATUS_REQUEST_OK);
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    public function upload(Request $request)
    {
    	try {
            $user  = Users::find($request->get('id'));
            
            if( ! $user->uploadProfilePhoto( $request ) ){

                return $this->apiErrorResponse(false, $user->getErrors( true ), self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
            }
    
            return $this->apiSuccessResponse([ 'user' => $user ], true, 'Profile Photo Uploaded Successfully ', self::HTTP_STATUS_REQUEST_OK);

        } catch (\Exception $e) {
            
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
    
    public function deletePhoto(Request $request)
    {
        try {
            $user = User::find($request->id);
            $directory = 'photos/' . $user->id;
            $destination = storage_path() . '/app/public/' . $directory . '/' . basename($user->profile_photo_url);

            File::delete($destination);

            $user->profile_photo_url = null;
            $user->save();

            return $this->apiSuccessResponse($destination, true, 'Image successfully deleted', self::HTTP_STATUS_REQUEST_OK);

        } catch(Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    public function delete(Request $request)
    {
        try {
            $user = User::where('id', '=', $request->id)->first();
            
            // NOTE: Remove this if account reactivation is possible
            $user->email = uniqid('deleted_') . '_' . $user->email;
            $user->save();

            $user->delete();

            return $this->apiSuccessResponse([], true, 'Account successfully deleted.', self::HTTP_STATUS_REQUEST_OK);

        } catch(\Exception $e) {
            
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

}
