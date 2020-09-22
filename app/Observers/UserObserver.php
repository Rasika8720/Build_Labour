<?php

namespace App\Observers;

use App\Mails\ResendVerificationCodeEmail;
use App\Models\Companies\Company;
use App\Models\Users\WorkerDetail;
use App\User;
use Illuminate\Http\Request;
use Mail;

class UserObserver {

    public function saved($user)
    {

        $user = User::find($user->id);

//        if (!$user->isEmployerSignup) {
//
//            if (!isset($user->workerDetail)) {
//
//                $request = Request::capture();
//
//                WorkerDetail::create([
//                    'user_id' => $user->id,
//                    'english_skill' => null,
//                    'drivers_license' => null,
//                    'right_to_work' => null,
//                    'suburb' => $request->suburb,
//                    'most_recent_role' => $request->most_recent_role
//                ]);
//
//            }
//
//            // Mail::to( $user->email )->send( new ResendVerificationCodeEmail( $user ) );
//
//        }

    }
}
