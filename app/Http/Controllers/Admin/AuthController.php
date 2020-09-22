<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function showAdmin()
    {
        return view('admin.index');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }
}

