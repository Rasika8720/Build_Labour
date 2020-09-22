<?php


namespace App\Http\Controllers\API\V1;

use App;
use App\Interfaces\ChatServices\ChatServiceInterface;
use App\Interfaces\ChatServices\Drivers\FirebaseChatService;
use App\Models\Communication\ChatRequest;
use App\Models\Communication\ChatChannelMembers;
use App\Models\Communication\ChatChannels;
use App\Models\Communication\ChatHistory;
use App\Models\Connections\Connections;
use App\Models\Users\Users;
use App\Models\DayLabour\Requirements;
use Helpers\Utils;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiDayLabourController extends ApiBaseController
{
    public function index()
    {
        if( !$user = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $requirements = (new Requirements)->getAll();

        return $this->apiSuccessResponse(  $requirements , true, null );
    }
}
