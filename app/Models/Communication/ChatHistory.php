<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Users\Users;
use App\Notifications\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class ChatHistory extends BaseModel{

    protected $table        = 'chat_history';
    protected $primaryKey   = 'history_id';

    public $timestamps = false;

    protected $fillable = [ 'history_id', 'channel_id', 'message', 'sent_by', 'message_type' ];


    private function validate( Request $r )
    {

        $validator = \Validator::make( $r->all() , [
            // validation rules here
            'message'       => 'required',
            'channel_id'    => 'required'
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

        if( ! $this->validate( $r ) ){
            return false;
        }

        $this->fill( $r->all() );
        $pk = $this->primaryKey;

        if( $r->$pk  ){
            $this->exists = true;
        }else{
            $this->sent_at = $r->sent_at;
        }

        try{
            $this->save();
        }catch( \Exception $e  ){
            $this->addError( $e->getMessage() ) ;
            return false;
        }



        return $this;
    }

    public function getCollection( Request $r )
    {
        $this->setLpo( $r );
        $this->fields = [
            'a.*',
            \DB::raw('DATE_FORMAT(a.sent_at, "%d/%m/%Y") as formatted_date'),
            \DB::raw('DATE_FORMAT(a.sent_at, "%h:%i:%s%p") as formatted_time')
        ];

        $this->query = static::from( $this->table.' as a' );
        // apply filters here
        if( $r->channel_id ){
            $this->query->where( 'channel_id' , $r->channel_id );
        }

        if( $r->after ){
            $this->query->where( 'sent_at' , '>',  $r->after );
        }

        if( $r->with_sender ){
//            $this->query->with( [ 'sender'] );
        }

        if( $r->return_total ){
           $this->total = $this->query->count( );
        }

//        $this->assignLpo();

        if( $r->return_builder ){
            return $this->query;
        }

        return $this->query->get( $this->fields );
    }

    public function sender()
    {
        return $this->hasOne( Users::class , 'id' , 'sent_by' );
    }

    public function updateSeenBy(Request $r)
    {
        $update = \DB::table('chat_history')
            ->where('channel_id', '=', $r->channel_id)
            ->where('sent_by', '=', $r->chat_partner_id)
            ->update(['seen_by' => 'true']);

        return $update;
    }
}
