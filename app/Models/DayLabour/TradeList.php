<?php
namespace App\Models\DayLabour;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeList extends BaseModel
{

    public $timestamps = false;

    protected $fillable = ['name', 'description'];
    protected $table        = 'trade_list';
    protected $primaryKey   = 'id';

    public function getParentTrades()
    {
        try {
            $parent_trade_list = DB::table($this->table)
                ->select(
                    'trade_list.id',
                    'trade_list.name'
                )
                ->join('trade_list_parent as parent', 'parent.trade_id', '=', 'trade_list.id')
                ->where('parent.parent_trade_id', '=', 0)
                ->get();

            return $parent_trade_list;
        } catch ( \Exception $e) {
            return $e->getMessage();
        }
    }

    public function getChildrenByParentId($parentId)
    {
        $child_trade_list = DB::table($this->table)
            ->select(
                'trade_list.id',
                'trade_list.name'
            )
            ->join('trade_list_parent as parent', 'parent.trade_id', '=', 'trade_list.id')
            ->where('parent.parent_trade_id', '=', $parentId)
            ->get();

        return $child_trade_list;
    }
}
