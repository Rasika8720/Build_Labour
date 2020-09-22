<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PasswordResetsController
{
    /**
     * Show the forgot password form.
     *
     */
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Show the reset password form.
     *
     */
    public function showResetForm()
    {
        return view('auth.passwords.reset');
    }
}