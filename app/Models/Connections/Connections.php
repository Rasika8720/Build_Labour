<?php

namespace App\Models\Connections;

use App\Models\BaseModel;
use App\Models\Communication\ChatChannelMembers;
use App\Models\Communication\ChatChannels;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Connections extends BaseModel{

    protected $table        = 'connections';
    protected $primaryKey   = 'connection_id';

    public $timestamps = false;

    protected $fillable = [ 'connection_id' , 'user1' , 'user2' , 'status' , 'requested_by' , 'requested_to' , 'message' ];
    protected $hidden = [ 'last_message_user1', 'last_message_user2' ];

    private $connection_of = null;

    public function store( Request $r )
    {
        $validator = \Validator::make( $r->all() , [
            'user1'       => 'required|numeric',
            'user2'  => 'required|numeric'
        ] );

        if( $validator->fails() ){
            $this->errors = $validator->errors()->all();
            return false;
        }

        $this->fill( $r->all() );
        // swap of user1 is a larger number
        if( $this->user1 > $this->user2 ){
            $temp        = $this->user1;
            $this->user1 = $this->user2;
            $this->user2 = $temp;
        }


        $pk = $this->primaryKey;

        if( $r->$pk  ){
            $this->exists = true;
        }else{
            $this->added_at = date( 'Y-m-d H:i:s' );
            $this->status = 'pending';
        }

        $this->save();

        return $this;
    }

    /**
     * @param Request $r
     * @return collection of static
     */
    public function getCollection( Request $r )
    {
        $this->setLpo( $r );
        $this->fields = [ 'a.*' ];

        $this->query = static::from( $this->table.' as a' );

        // this will return connections that has chat channel
        // joined by default

        if( strtolower( $r->status ) == 'pending' ){
            $this->query->join( 'chat_channels as cc' , 'a.channel_id' ,'=' , 'cc.channel_id' , 'left' );
        }else{
            $this->query->join( 'chat_channels as cc' , 'a.channel_id' ,'=' , 'cc.channel_id'  );
        }

        $this->fields[]     = 'cc.last_message_at';
        $this->fields[]     = 'cc.last_message';

        if( $r->sort_by_last_message_date ){
            $this->order_by     = 'last_message_at';
            $this->order_direction  = 'DESC';
        }

        if( $r->with ){
            //$this->query->with( [ 'firstUser', 'secondUser' ] );
            $this->query->with( $r->with );
        }

        $this->query->where( function( $query ) use ( $r ){
            return $query->where( 'user1' , $r->user_id )->orWhere( 'user2' ,  $r->user_id );
        });

        if( $r->connection_id ){
            $this->query->where( 'a.connection_id', $r->connection_id );
        }

        if( $r->status ){
            if( $r->status != 'all'){
                $this->query->where( 'status', $r->status );
            }
        }else{
            $this->query->where( 'status' , 'active' );
        }

        if( $r->requested_to ){
            $this->query->where( 'requested_to' , $r->requested_to );
        }

        if( $r->return_total ){
           $this->total = $this->query->count();
        }

        $this->assignLpo();

        if( $r->return_builder ){
            return $this->query;
        }

        if( $r->with_sql ){
            $this->sql = $this->query->toSql();
            $this->sql_bindings = $this->query->getBindings();
        }

        return $this->query->get( $this->fields );
    }

    public function requestedBy()
    {
        return $this->hasOne( Users::class , 'id', 'requested_by' );
    }

    public function requestingUser()
    {
        return $this->hasOne( Users::class , 'id', 'requested_by' );
    }

    public function firstUser()
    {
        return $this->hasOne( Users::class , 'id', 'user1' );
    }

    public function secondUser()
    {
        return $this->hasOne( Users::class , 'id' , 'user2' );
    }

    public function chatChannel()
    {
        return $this->hasOne( ChatChannels::class , 'connection_id' , 'connection_id' );
    }

    public function chatChannelMember()
    {
        return $this->hasOne( ChatChannelMembers::class , 'channel_id' , 'channel_id' )
            ->where( 'member_id', $this->connection_of );
    }

    public function connectionOf( $user_id )
    {
        $this->connection_of = $user_id;
    }

    public static function isConnected( $user1 , $user2 )
    {
        // let  $u1 always be the smaller userid
        if( $user1 < $user2 ){
            $u1 =  $user1;
            $u2 =  $user2;
        }else{
            $u1 =  $user2;
            $u2 =  $user1;
        }

        return static::where( 'user1' , $u1 )
            ->where( 'user2' , $u2 )
            ->first();
    }

    public function isPart( $user_id )
    {
        return in_array( $user_id, [ $this->user1 , $this->user2 ] );
    }

    /**
     * Returns the other user given a user id
     *
     * @param $user_id
     * @return integer|null
     */
    public function theOther( $user_id )
    {
        if( $this->user1 == $user_id ){
            return $this->user2;
        }

        if( $this->user2 == $user_id ){
            return $this->user1;
        }

        return null;
    }

    /**
     * Add the last message of
     * @param $from
     * @param $message
     */
    public function saveLastMessage( $from , $message )
    {
        if( $this->user1 == $from ){
            $this->last_message_user1 = $message;
        }

        if( $this->user2 == $from ){
            $this->last_message_user2 = $message;
        }

        $this->save();

        return $this;
    }

    public static function getConnectionIds( $user_id , $status = 'all'  )
    {
        $req = new Request();
        $req->merge( [ 'user_id' => $user_id,
            'status' => $status, 'limit' => 1000  ] );
        $connections = ( new Connections )->getCollection( $req );

        $connection_array   = [];

        foreach( $connections as $c ){
            $connected_with = $c->theOther( $user_id );
            $connection_array[] =  $connected_with;
        }

        return $connection_array;
    }

}