<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class PageController extends Controller
{

    protected function index()
    {

       // return redirect('/user/profile');

        $token = isset($_COOKIE['bl_token']) ? $_COOKIE['bl_token'] : null;

        if ($token) {

            return redirect('/user/profile');
        }

        return view('front');
    }

    protected function resetEmail(Request $request)
    {

        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {

            $user = User::whereEmail($request->email)->first();

            if ($user) {

                $user->email = $this->generateRandomString(6) . "-" . $user->email;
                $user->update();
            } else {

                dd('email not found');
            }

        } else {

            dd('invalid email');
        }

        return 'Email successfully reset';
    }

    private function generateRandomString($length = 10) {

        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

}
