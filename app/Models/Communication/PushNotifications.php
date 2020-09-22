<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Users\FirebaseUsers;
use App\Models\Users\Users;
use Illuminate\Http\Request;

class PushNotifications extends BaseModel{

    protected $table        = 'push_notifications';
    protected $primaryKey   = 'pn_id';

    public $timestamps = false;

    protected $fillable = [ 'message', 'message_category' ];

    /**
     * @param Request $r
     * @return $this
     */
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
            $this->added_at = date( 'Y-m-d H:i:s' );
            $this->added_by = $r->user()->id;
            $this->sent_count = 0;
        }

        $this->save();

        return $this;
    }

    /**
     * @param Request $r
     */
    public function send( Request $r )
    {
        //get all
        $firebase_users = new FirebaseUsers();

        if( $r->send_to_all ){

            // update pn count of all users
            Users::where( 'id' , '>' , 1 )
                ->increment(  'unread_pn' );

            // send the notification for all active devices
            $devices        = $firebase_users->getActive();
            $device_tokens  = [];
            $user_ids = [];

            foreach( $devices as $device ){
                $device_tokens[]    =   $device->device_token;
                $user_ids[] = $device->user_id;
            }

            /**
            $device_tokens = $firebase_users->getActive()
                ->pluck('device_token')->toArray();
            **/
            if( ! count( $device_tokens ) ){

                $this->total_expected_count =  0 ;
                $this->sent_count           =  0 ;
                $this->last_sent_at         = date( 'Y-m-d H:i:s' );

                $this->save();

                return $this;
            }

            $chat_service = \App::make('ChatServiceInterface');

            // @TODO
            // create a cron if firebase users are more than 500

            if( ! $response   =   $response = $chat_service->sendMessageToDevices( $device_tokens  , $r->message , [] ) ){
                return [
                    'success' => false,
                    'message' => 'Sending of push notification failed'
                ];
            }

            $this->sent_count = count( $device_tokens );
            $this->response = $response;
        }

        return $this;

    }

    /**
     * Get collection of objects
     *
     * @param Request $r
     * @return mixed
     */
    public function getCollection( Request $r )
    {
        $this->setLpo( $r );
        $this->fields = [ 'a.*' ];

        $this->query = static::from( $this->table.' as a' );
        // apply filters here

        if( $r->return_total ){
           $this->total = $this->query->count( );
        }

        $this->assignLpo();

        if( $r->return_builder ){
            return $this->query;
        }

        return $this->query->get( $this->fields );
    }

}