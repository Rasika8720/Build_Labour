<?php

namespace App\Http\Controllers\Api\V1;


use App\Events\ConnectionApproved;
use App\Models\Communication\ChatChannels;
use App\Models\Connections\Connections;
use App\Models\Users\Bookmarks;
use App\Models\Users\Users;
use App\Events\ConnectionRequest;
use Helpers\Utils;
use Illuminate\Http\Request;
use JWTAuth;

class ApiConnectionsController extends ApiBaseController{

    /**
     * @OA\Post(
     *      path="/connections/connect",
     *      tags={"Connections"},
     *      summary="Connect user to another",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="connect_with",
     *                      description="<b>Required</b><br /> User id to connect with",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      description="This will be sent as an introductory message",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function connect( Request $r )
    {

        if ( !$user = JWTAuth::toUser() ){

            return $this->apiErrorResponse(false, 'Invalid Token', self::HTTP_STATUS_INVALID_INPUT, 'invalidToken');
        }

        $user   = Users::find( $user->id );
        
        $user1  = (int) $user->id;
        $user2  = (int) $r->connect_with;

        if( $user1 == $user2 ){

            return $this->apiErrorResponse(false, 'Connecting to self is not allowed', self::HTTP_STATUS_INVALID_INPUT, 'sameUserError');
        }


        if( $connection = Connections::isConnected( 1 , 2 ) ){

            return $this->apiErrorResponse(false, 'You already sent a connection request before. Current status is '.$connection->status, self::HTTP_STATUS_INVALID_INPUT, 'userPreviouslyEstablished');

        }

        $r->merge( [ 'user1' => $user1 , 'user2' => $user2,
            'requested_by'=>$user->id , 'requested_to' => $user2 ] );

        $connection = new Connections();

        \DB::beginTransaction();
        if( ! $connection->store( $r ) ){

            return $this->apiErrorResponse(false, $connection->getErrors( true ), self::HTTP_STATUS_BAD_REQUEST, 'savingConnectionFailed');
        }

        // add to connection_map
        // trigger new connect request event

        try{
            event( new ConnectionRequest( $user2, $user->id, $user->first_name.' wants to connect with you! ' , $connection->connection_id , $user->name ) );
        }catch( \Exception $e ){
            \DB::rollback();

            return $this->apiErrorResponse(false, $e->getMessage(), self::HTTP_STATUS_BAD_REQUEST, 'userPreviouslyEstablished');

        }

        \DB::commit();

        $data  =  [
            'connection' => $connection,
            'recipient' => $user2,
            'changeStatuser' => $user->id
        ];

        return $this->apiSuccessResponse( $data, true, 'Connection successful pending approval', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Post(
     *      path="/connection/status/change",
     *      tags={"Connections"},
     *      summary="Change connection status from pending to active or blocked ",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *
     *                  @OA\Property(
     *                      property="connection_id",
     *                      description=" <b>Required</b> <br /> User id to connect with ",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="connection_request",
     *                      description=" <b>Required</b> <br /> Possible values : accept | deny",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function changeStatus( Request $r )
    {
        if( ! $user  = JWTAuth::toUser() ){

            return $this->apiErrorResponse(false, 'Invalid Token', self::HTTP_STATUS_INVALID_INPUT, 'invalidToken');
        }

        if( ! $connection = ( new Connections )->f( $r->connection_id ) ){

            return $this->apiErrorResponse(false, 'You do not have permission to change status of this connection', self::HTTP_STATUS_INVALID_INPUT, 'permissionDenied');
        }

        if( !in_array( $user->id, [ $connection->user1 , $connection->user2 ] )){

            return $this->apiErrorResponse( 'You do not have permission to change status of this connection' , 400 , 'permissionDenied' );
        }

        // only requested_to user can accept / deny request
        if( $user->id != $connection->requested_to ){
            return $this->apiErrorResponse( 'Requested user id mismatch' , 400 , 'requestedToMismatch' );
        }

        \DB::beginTransaction();

        switch( $r->connection_request ){
            case 'accept':
            case 'activate':
                $connection->status = 'active';

                // check if chat channel is up
                if( ! $connection->channel_id ){
                    if( ! $channel = ( new ChatChannels )->createConnectionChannel( $connection ) ){
                        return $this->apiErrorResponse( 'Channel creation failed : '.$channel->getError( true ), 400 , 'failedChannelCreation' );
                    }

                    $connection->channel_id = $channel->channel_id;

                    try{
                        $connection->save();
                    }catch( \Exception $e ){
                        \DB::rollback();
                        return $this->apiErrorResponse(  $connection->getErrors( true ) , 400 , 'notificationException' );
                    }

                    // inform the person requesting that his request has been approved
                    try{
                        event( new ConnectionApproved( $connection->requested_by, $user->id,
                                $user->first_name.' has accepted your request ',
                                $connection->connection_id , $user->name )
                        );
                    }catch( \Exception $e ){
                        \DB::rollback();
                        return $this->apiErrorResponse(  $e->getMessage() , 400 , 'notificationException' );
                    }
                }
            break;
            case 'deny':
            case 'block':
                $connection->status = 'blocked';
                try{
                    $connection->save();
                }catch( \Exception $e ){
                    \DB::rollback();
                    return $this->apiErrorResponse(  $connection->getErrors( true ) , 400 , 'notificationException' );
                }
            break;
            default:
                \DB::rollback();
                return $this->apiErrorResponse(  'Invalid Status' , 400 , 'invalidStatus' );
            break;
        }

        \DB::commit();

        $this->apiSuccessResponse( [ 'connection' => $connection ] , 'Connection status succesfully set to '.$connection->status );
    }

    /**
     * @OA\GET(
     *      path="/connections/active",
     *      tags={"Connections"},
     *      summary="Get active connections",
     *      security={{"BearerAuth":{}}},
     *     @OA\Parameter(
     *          name="sort_by_last_message_date",
     *          description="Sort result by chat message date",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="connection_id",
     *          description="Get a single active connection (optional)",
     *          required=false,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function activeConnections( Request $r )
    {
        if( ! $user  = JWTAuth::toUser() ){
            return $this->apiErrorResponse( 'Invalid Token', 400 , 'invalidToken' );
        }

        $current_user = Users::find( $user->id );

        $r->merge( [ 'limit' => 200, 'user_id' => $user->id ,
            'status' => 'active',  'with'=>[ 'firstUser', 'secondUser' , 'chatChannelMember' ] , 'return_total' => true ]);

        $connection     =   new Connections();
        $connection->connectionOf( $user->id );

        $active_connections = $connection->getCollection( $r );
        $c_array    =   [];

        foreach( $active_connections as $c ){

            $c->user = $c->firstUser->cid == $current_user->cid  ?  $c->secondUser :  $c->firstUser;
            unset( $c->firstUser);
            unset( $c->secondUser);

            $c_array[] = $c;
        }

        $data = [
            'user_id' => $user->id,
            'connections' => $c_array,
            'total'         => $connection->getTotal(),
            'current_user'  => $current_user
        ];

        return $this->apiSuccessResponse( $data );

    }

    /**
     * @OA\GET(
     *      path="/connections/pending",
     *      tags={"Connections"},
     *      summary="Get list of pending request connections",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function pendingConnections( Request $r )
    {
        if( ! $user  = JWTAuth::toUser() ){
            return $this->apiErrorResponse( 'Invalid Token', 400 , 'invalidToken' );
        }

        $r->merge( [ 'limit' => 100, 'user_id'=>$user->id, 'with'=>[ 'requestedBy' ],
            'requested_to' => $user->id , 'status' => 'pending' ]);

        $connection = new Connections();
        $current_user = Users::find( $user->id );

        $data = [
            'connections' => $connection->getCollection( $r ),
            'current_user' => $current_user
        ];

        $data['user_id'] = $user->id;
        $this->apiSuccessResponse( $data );
    }

}