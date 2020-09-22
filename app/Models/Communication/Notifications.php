<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Notifications extends BaseModel
{

    public static function clearNotification(Request $r)
    {
        $notification = $r->notification;

        $user_notification = Users::find($r->userId)
            ->notifications()
            ->find($notification['id']);

        $user_notification->markAsRead();
    }

    private function clearSingleNotification($notification)
    {
        $notification->markAsRead();
        return true;
    }
}
