<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ApiMessageController extends ApiBaseController
{

    public function index()
    {
        $messages = Message::with(['user'])->get();

        return response()->json($messages);
    }
}
