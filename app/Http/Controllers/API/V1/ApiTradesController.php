<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DayLabour\TradeList;
use Illuminate\Http\Request;
use JWTAuth;

class ApiTradesController extends ApiBaseController
{

    public function listTradeParents()
    {

        try {

            $trade_list = new TradeList();
            $items = $trade_list->getParentTrades();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( $items, true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    public function listTradeChildren( Request $r )
    {

        try {

            $trade_list = new TradeList();
            $items = $trade_list->getChildrenByParentId($r->parentId);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( $items, true, '', self::HTTP_STATUS_REQUEST_OK);
    }
}
