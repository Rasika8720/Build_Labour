<?php

namespace App\Models\Tickets;

use App\Models\BaseModel;
use Illuminate\Http\Request;

class Ticket extends BaseModel
{

    private $userId = null;

    protected $table = 'tickets';
    protected $primaryKey = 'id';

    protected $fillable = [ 'ticket', 'description', 'created_by' ];

    protected $hidden = [ 'created_at', 'updated_at' ];

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'ticket'         => 'required',
            'description'    => 'nullable',
            'created_by'     => 'nullable|integer'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate( $data ){

        $validator = \Validator::make($data, $this->rules());

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        return true;
    }

    public function setUserId($userId) {

        $this->userId = $userId;
    }

    public function CreatedBy() {

        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function store(Request $r) {

        $data = $r->all();

        if( ! $this->validate( $data )) {

            return false;
        }

        $this->fill( $data );

        $pk = $this->primaryKey;

        if ($r->$pk) {

            $this->exists = true;
        }

        try{

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }
}
