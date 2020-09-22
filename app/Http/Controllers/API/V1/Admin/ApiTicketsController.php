<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\API\V1\ApiBaseController;
use App\Models\Tickets\Ticket;
use Illuminate\Http\Request;

class ApiTicketsController extends ApiBaseController
{
    public function get(Request $request)
    {
        try {
            $record = Ticket::find($request->id)
                        ->makeHidden([
                        
                        ])->makeVisible([

                        ]);

            if ($record) {
                return $this->apiSuccessResponse(compact('record'), true, 'Request OK', self::HTTP_STATUS_REQUEST_OK);
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
