<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Companies\JobResponsibility;
use Illuminate\Http\Request;

use JWTAuth;

class ApiJobResponsibilitiesController extends ApiBaseController
{
    /**
     *  Create a new controller instance.
     *
     */
    public function __construct()
    {

    }

    public function getCollection( Request $r )
    {
        $responsibilities = JobResponsibility::query();

        if (isset($r->byCategory)) {

            $responsibilities = $responsibilities->where('title', 'like', "%{$r->keyword}%")->groupBy('title');
            $responsibilities = $responsibilities->get(['title'])->take(10)->pluck('title');

        } else {

            $tempResponsibilities = [];

            $responsibilities = $responsibilities->where('items_json', 'like', "%{$r->keyword}%")->groupBy('title');
            $responsibilities = $responsibilities->get()->take(10);

            foreach ($responsibilities as $r) {

                $tempResponsibilities = array_merge($r->items, $tempResponsibilities);
            }

            $responsibilities = array_filter($tempResponsibilities);

            return $this->apiSuccessResponse( compact( 'responsibilities' ), true, 'Collected responsibilities' );
        }


        return $this->apiSuccessResponse( compact( 'responsibilities' ), true, 'Collected responsibilities' );
    }


}
