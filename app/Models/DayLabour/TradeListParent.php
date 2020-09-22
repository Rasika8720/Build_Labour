<?php
namespace App\Models\DayLabour;

use App\Models\BaseModel;

class TradeListParent extends BaseModel
{

    protected $fillable = ['trade_id', 'parent_trade_id'];

    public function tradeList()
    {
        return $this->belongsTo(TradeList::class);
    }
}
