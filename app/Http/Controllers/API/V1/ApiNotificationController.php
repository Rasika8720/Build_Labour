<?php


namespace App\Http\Controllers\API\V1;

use App;
use  App\Models\Users\Users;
use Helpers\Utils;
use Illuminate\Http\Request;
class ApiNotificationController extends ApiBaseController{

    public function getUserNotifications(Request $r)
    {
        if( ! $user  = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $message = (new App\Models\Users\Users)->f($user->id);

        return $this->apiSuccessResponse($message->unreadNotifications, true, 'Found Unread Notifications', self::HTTP_STATUS_REQUEST_OK);;
    }
}
