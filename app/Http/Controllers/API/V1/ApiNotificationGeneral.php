<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Communication\Notifications;

class ApiNotificationGeneral extends ApiBaseController
{

    public function clear(Request $r)
    {
        if( !$user = \JWTAuth::toUser() )
            $this->apiErrorResponse(false, 'Invalid User Token', '401');

//        return $this->apiErrorResponse(false, $r->notification, '401');
        $r->merge(['userId' => $user->id]);
        $notification = Notifications::clearNotification($r);

        return $this->apiSuccessResponse('Notification Cleared');
    }
}
