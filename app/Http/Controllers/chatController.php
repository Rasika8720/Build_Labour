<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class chatController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(Request $r)
    {
        $user_id = isset($r->user_id) ? ['requested_id' => $r->user_id] : ['requested_id' => null];

        return view('chat.index', $user_id);
    }
}
