<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\Connections\Connections;
use App\Models\Users\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatChannels extends BaseModel{

    protected $table        = 'chat_channels';
    protected $primaryKey   = 'channel_id';

    public $timestamps = false;

    protected $fillable = [ 'channel_id', 'name', 'type', 'connection_id' ];

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
            $this->created_at = date( 'Y-m-d H:i:s' );
        }

        $this->save();

        return $this;
    }

    public static function returnChatChannelByIds($userOne, $userTwo)
    {
        if(!is_numeric($userOne) || !is_numeric($userTwo))
            return false;

        $channel_id = DB::table('chat_channels')
            ->where('connection_id', '=', $userOne . $userTwo)
            ->orWhere('connection_id', '=', $userTwo . $userOne)
            ->first();

        if(!empty($channel_id))
            return $channel_id->channel_id;
        else
            return false;
    }

    public static function byUserId( $user_id )
    {

    }

    public function createChatChannel(Request $r, $type)
    {
        $r->merge(['type' => 'one-on-one']);
        $new_channel = $this->store($r);
        $new_channel_id = $new_channel->id;

        return $new_channel_id;
    }

    public function createConnectionChannel( Connections $c )
    {
        if( ! $c->connection_id ){
            $this->errors[] = 'Invalid connection id ';
            return false;
        }

        $r = new Request();
        $r->merge( ['type'=>'one-on-one' , 'connection_id'=> $c->connection_id ] );

        // check if the connection_id already has a chat channel
        $channel = ChatChannels::where( 'connection_id', $c->connection_id )->first();

        if( $channel ){
            return $channel;
        }

        $channel = ( new ChatChannels )->store( $r ); // create a connection channel

        ( new ChatChannelMembers )->store( ['member_id' => $c->user1 , 'channel_id' => $channel->channel_id ] );
        ( new ChatChannelMembers )->store( ['member_id' => $c->user2 , 'channel_id' => $channel->channel_id ] );

        // add the intro message of the connection as the first message of the chat

        if( $c->message ){
            $r->merge( [ 'channel_id' => $channel->channel_id ,   'message' => $c->message,
                'sent_by' => $c->requested_by , 'sent_at' => $c->added_at  ] );
            ( new ChatHistory )->store( $r );
        }

        return $channel;
    }

    public function isMember( $user_id )
    {
        return ChatChannelMembers::where( 'channel_id', $this->channel_id )->where( 'member_id' , $user_id )->first();
    }
}
