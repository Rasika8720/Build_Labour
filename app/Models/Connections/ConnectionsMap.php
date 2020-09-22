<?php

namespace App\Models\Connections;

use App\Models\BaseModel;
use App\Models\Communication\ChatChannels;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ConnectionsMap extends BaseModel{

    protected $table        = 'connections_map';
    protected $primaryKey   = 'map_id';

    public $timestamps = false;

    protected $fillable = [ 'user_id', 'connected_to', 'connection_id' ];


    public function store( Request $r )
    {
        $validator = \Validator::make( $r->all() , [
            // validation rules here
        ] );

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        $this->fill( $r->all() );
        $pk = $this->primaryKey;

        if( $r->$pk  ){
            $this->exists = true;
        }else{

        }

        $this->save();

        return $this;
    }
}