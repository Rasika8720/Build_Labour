<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatRequest extends BaseModel
{
    protected $table        = 'chat_request';
    protected $primaryKey   = 'id';

    public $timestamps = false;

    protected $fillable = [ 'requesting_user', 'requested_user', 'status' ];

    public function store(request $r)
    {

        if( !$this->validate($r))
            return 'Validation Failed';

        $this->fill($r->all());
        $pk = $this->primaryKey;

        if( $r->$pk )
            $this->exists = true;

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
            return $e->getMessage();
        }

        return $this;
    }

    private function validate( Request $r )
    {
        $validator = \Validator::make( $r->all() , [
            // validation rules here
            'requesting_user'   => 'required',
            'requested_user'    => 'required'
        ] );

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        return true;
    }

    public static function findChatRequest($recipient, $sender)
    {
        $request = DB::table('chat_request')
            ->select()
            ->whereIn('requesting_user', [$recipient, $sender])
            ->whereIn('requested_user', [$recipient, $sender])
            ->first();

        return $request;
    }
}
