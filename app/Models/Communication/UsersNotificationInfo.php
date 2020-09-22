<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Users\Users;
use Illuminate\Http\Request;

class UsersNotificationInfo extends BaseModel{

    protected $table        = 'users_notification_info';
    protected $primaryKey   = 'unid';

    public $timestamps = false;

    protected $fillable = [ 'unid', 'user_id' , 'unread_notifications', 'unread_messages', 'badge_count',
        'last_notification_check', 'last_message_check'
    ];


    private function validate( Request $r )
    {

        $validator = \Validator::make( $r->all() , [

        ] );

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        return true;
    }
    /**
     * @param Request $r
     * @return $this
     */
    public function store( Request $r )
    {
        if( ! $this->validate( $r ) )
            return false;

        if( $r->unid) {
            $this->exists = true;
        }

        $this->fill( $r->all() );

        try{
            $this->save();
        }catch( \Exception $e  ){
            $this->addError( $e->getMessage() ) ;
            return false;
        }

        return $this;
    }

    public function byUserId( $user_id )
    {
        return static::where( 'user_id' , $user_id )->first();
    }

}
