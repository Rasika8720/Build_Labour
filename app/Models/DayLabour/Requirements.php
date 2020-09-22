<?php
namespace App\Models\DayLabour;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Requirements extends BaseModel
{

    public $timestamps = false;

    protected $fillable = ['type', 'name', 'created_by'];
    protected $table        = 'job_daylabour_requirements';
    protected $primaryKey   = 'id';

    public function getAll()
    {
        try {
            $requirements = DB::table($this->table)
                ->orderBy('id')
                ->get()
                ->groupBy('type');

            return $requirements;
        } catch ( \Exception $e) {
            return $e->getMessage();
        }
    }

    public function getByType($type)
    {
        try {
            $requirements = DB::table($this->table)
                ->where('type', '=', $type)
                ->get();

            return $requirements;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getById($id)
    {
        try {
            $requirements = DB::table($this->table)
                ->where('id', '=', $id)
                ->get();

            return $requirements;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
