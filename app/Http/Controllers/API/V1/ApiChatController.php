<?php


namespace App\Http\Controllers\API\V1;

use App;
use App\Interfaces\ChatServices\ChatServiceInterface;
use App\Interfaces\ChatServices\Drivers\FirebaseChatService;
use App\Models\Communication\ChatRequest;
use App\Models\Communication\ChatChannelMembers;
use App\Models\Communication\ChatChannels;
use App\Models\Communication\ChatHistory;
use App\Models\Connections\Connections;
use App\Models\Users\Users;
use App\Notifications\ChatMessage;
use Helpers\Utils;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use  App\Models\Communication\UsersNotificationInfo;

class ApiChatController extends ApiBaseController{

    /*
     * ChatServiceInterface
     */
    private  $chat_service;

    public function __construct(){
//        $this->chat_service = \App::make('ChatServiceInterface');
//        $this->chat_service = $chat_service;
//        $s = 'Firebase';
//        App::bind( 'ChatServiceInterface', function( $app ) use($s)
//        {
//            switch( $s ){
//                case 'Socket.io':
//                    break;
//                case 'Pusher':
//                    break;
//                case 'Firebase':
//                default:
//                    return new FirebaseChatService();
//                    break;
//            }
//        });
//
//        $this->chat_service = App::make('ChatServiceInterface');
    }

    /**
     * @OA\Post(
     *      path="/chat/send",
     *      tags={"Chat"},
     *      summary="Send a chat message",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="channel_id",
     *                      description="<b>Required</b><br /> Channel ID where message will be sent",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      description="<b>Required</b><br /> Chat message",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function send( Request $r )
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        $sender_id      = $user->id;
        $recipient_id   = $r->recipient;
        $chat_request   = ChatRequest::findChatRequest($recipient_id, $sender_id);
        $channel_id     = ChatChannels::returnChatChannelByIds($recipient_id, $sender_id);

        if( $r->hasFile('file_message') !== false ) {
            $upload = $this->uploadFile($r, $channel_id);
            $r->message = $upload;
        }

        // Check to see if the Request passes a few checks
        if( ! $r->message || (isset($upload) && $upload === false) ){
            return $this->apiErrorResponse( false, 'Message must not be empty', 400 , 'messageRequired' );
        }

        if( !$chat_request )
        return $this->apiErrorResponse(false, 'No chat request found. Please send this user a chat request', 400, 'chatRequestNotFound');

        if( $chat_request->status !== 'Accepted' ) {
            $message = $user->role_id === 1 ? 'You need to accept the chat request first' : 'Chat Request has not been accepted by the worker';
            return $this->apiErrorResponse(false, $message, 400, 'chatRequestNotFound');
        }

        if( ! $channel_id ){
            return $this->apiErrorResponse( false, 'Channel ID is required', 400 , 'channelIDRequired' );
        }

        if( ! $channel = ChatChannels::find( $channel_id) ){
            return $this->apiErrorResponse( false, 'Channel not found', 400 , 'channelNotFound' );
        }

        // Start the DB Transaction for new chat insert
        \DB::beginTransaction();
        $r->merge( [ 'channel_id' => $channel->channel_id ,   'message' => $r->message, 'sent_by' => $user->id, 'message_type' => $r->message_type ] );

        $chat_history = new ChatHistory();
        if( ! $chat_history->store( $r ) ){
            \DB::rollback();
            return $this->apiErrorResponse( false, 'Saving Chat Failed : '.$chat_history->getErrors( true ), 400 , 'savingChatFailed' );
        }

        $userNotification = (new Users)->f($recipient_id);

        $userNotification->notify(new ChatMessage($user, $chat_history));

        try{
            $channel->save();
        }catch( \Exception $e ){
            \DB::rollback();
            return $this->apiErrorResponse( false, $e->getMessage(), 400 , 'savingChannelException' );
        }

        $chat_data = [
            'pn_type'       =>  'chat',
            'channel_id'    =>  $r->channel_id,
            'sender_id'     =>  $sender_id,
            'recipient_id'  =>  $recipient_id,
            'sender_name'   =>  $user->first_name,
            'title' => $user->first_name,
            'sound' => 'message.wav'
        ];

        // update the unread count
        $notification = new UsersNotificationInfo();

        if( $list_exists = $notification->byUserId($r->recipient) ) {
            $r->merge(['unread_messages' => $list_exists->unread_messages + 1, 'unid' => $list_exists->unid]);
        } else {
            $r->merge( ['unread_messages' => 1]);
        }

        $r->merge(['user_id' => $r->recipient]);
        try{
            $store_notification = $notification->store( $r );
        }catch( \Exception $e ){
            \DB::rollback();
            return $this->apiErrorResponse( false, $e->getMessage(), 400 , 'updateUnreadCountException' );
        }

        // add unread chat count to recipient
//            $user_recipient = ( new Users )->f( $recipient_id )->addChatUnreadCount();
//            $badge  = $user_recipient->unread_chat;

        \DB::commit();

//            if( ! $response  =   $this->chat_service->sendMessageToUser(  $recipient_id, $sender_id, $r->message , $chat_data , $badge ) ){
//                return $this->apiSuccessResponse( ['chat_message' => $chat_history ], true, 'Message successfully saved but failed to send to chat service. Recipient might not be signed in' );
//            }

        $data   =  [
                'message' => $chat_history,
                'recepient_id'=>    $recipient_id,
                'sender_id' => $sender_id,
            'notification' => $store_notification
            ];

        return $this->apiSuccessResponse(  $data , true, 'Message successfully saved' );

        // TODO Add in multiuser chat
    }


    /**
     * @OA\Post(
     *      path="/chat/chat-request",
     *      tags={"Chat"},
     *      summary="Send a chat request to a user",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="requesting_user",
     *                      description="<b>Required</b><br /> ID of user sending the request",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="requested_user",
     *                      description="<b>Required</b><br /> ID of user being sent the request",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      description="<b>Required</b><br /> Initialised chat message",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function createChatRequest(Request $r)
    {
        if( ! $user  = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        if( !$r->requested_user )
            return $this->apiErrorResponse(false, 'No User selected to request a Chat with', 400, 'invalidChatRequestUser');

        $requesting_user    = $user->id;
        $requested_user     = $r->requested_user;
        $status             = 'Accepted';

        $r->merge(['requesting_user' => $requesting_user, 'status' => $status, 'created' => date('Y-m-d H:i:s')]);
        $findChatRequest = $this->doesRequestExist($r);

        if( $findChatRequest !== 'false' )
            return $this->apiErrorResponse(false, 'Chat Request Already Exists', 400, 'existingChatRequest');

        if( (new Users)->f($requested_user)->role_id !== 1)
            return $this->apiErrorResponse(false, 'You can only request a chat with a Worker', 400, 'invalidUserRoleChat');

        \DB::beginTransaction();
        if( is_string($new_request = (new ChatRequest())->store($r)) ) {
            return $this->apiErrorResponse(false, $new_request, 400, 'chatRequestError');
        }

        $r->merge(['connection_id' => $requesting_user . $requested_user]);
        if( !$channel = (new ChatChannels)->store($r)  ) {
            return $this->apiErrorResponse(false, 'Error creating Channel', 400, 'channelCreationError');
        }

        $r->merge(['channel_id' => $channel->channel_id, 'member_id' => $requesting_user]);
        if( !$channel_map_requester = (new ChatChannelMembers())->store($r))
            return $this->apiErrorResponse(false, 'Error Adding Requesting Member To Channel', 400, 'channelCreationError');

        $r->merge(['member_id' => $requested_user]);
        if( !$channel_map_requested = (new ChatChannelMembers())->store($r))
            return $this->apiErrorResponse(false, 'Error Adding Requested Member To Channel', 400, 'channelCreationError');
        \DB::commit();

        // TODO send notification to Worker they have a message request
        // TODO update worker notification system

        return $this->apiSuccessResponse($channel, true, 'Success', 200);
    }

    public function acceptChatRequest(Request $r)
    {
        if( ! $user  = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $id = $r->id;
    }

    public function doesRequestExist(Request $r)
    {
        $requesting_user = isset($r->requesting_user) ? $r->requesting_user : \JWTAuth::toUser()->id;
        $request = ChatRequest::whereRaw("requesting_user = $requesting_user AND requested_user = {$r->requested_user}")->first();

        if( $request ) {
            switch ($request->status) {
                case 'Accepted':
                    return $request;
                    break;
                case 'Pending':
                    return 'Pending';
                    break;
                case 'Rejected':
                    return 'Rejected';
                    break;
                default:
                    return false;
            }
        }

        return 'false';
    }

    /**
     * @OA\Post(
     *      path="/chat/reset_unread",
     *      tags={"Chat"},
     *      summary="Given a channel id, reset the unread count of the user",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="channel_id",
     *                      description="<b>Required</b><br /> Channel ID ",
     *                      type="integer",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function resetUnread( Request $r )
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        \DB::beginTransaction();
        try{

            $notification = new UsersNotificationInfo();

            if( $list_exists = $notification->byUserId($user->id) ) {
                $r->merge(['unread_messages' => 0, 'unid' => $list_exists->unid, 'last_message_check' => date('Y-m-d H:i:s')]);
            } else {
                return $this->apiSuccessResponse([], true, 'Unread Count Already at 0');
            }
            $notification->store( $r );
        }catch( \Exception $e ){
            \DB::rollback();
            return $this->apiErrorResponse( false, $e->getMessage(), 400 , 'updateUnreadCountException' );
        }
        \DB::commit();

        return $this->apiSuccessResponse([], true, 'Resetting unread count successful');
    }

    public function unread()
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }

        try{

            $notification = new UsersNotificationInfo();
            $exists = $notification->byUserId($user->id);
        }catch( \Exception $e ){
            return $this->apiErrorResponse( false, $e->getMessage(), 400 , 'getUnreadCountException' );
        }

        return $this->apiSuccessResponse($exists->unread_messages, true, 'Found unread message number');
    }

    /**
     * @OA\GET(
     *      path="/chat/unread/count",
     *      tags={"Chat"},
     *      summary="Get the number of channels that has unread messages",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function unReadCount( Request $r )
    {
        if( ! $user  = \JWTAuth::toUser() ){
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );
        }
        // get all channels of the user
        $r->merge( [ 'member_id' => $user->id ] );
        $channel_ids = ( new ChatChannelMembers() )
            ->getCollection( $r )->pluck( 'channel_id' );

        $messages = [];
        if( count( $channel_ids ) ){
            $messages = ChatHistory::whereIn( 'channel_id' , $channel_ids )
                ->where( 'sent_by' , '!=' , $user->id  )
                ->get();
        }

        $data = [
            'messages' => $messages,
            'channels' => $channel_ids
        ];

        return $this->apiSuccessResponse( $data, true, ' ' );

    }

    /**
     * @OA\GET(
     *      path="/chat/history",
     *      tags={"Chat"},
     *      summary="Get the number of channels that has unread messages",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          description="Channel ID",
     *          in="query",
     *          name="channel_id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          description="Date format (yyyy-mm-dd hh:ii:ss). Returns only the messages after the given date ",
     *          in="query",
     *          name="after",
     *          required=false,
     *          @OA\Schema(
     *              type="dateTime",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          description="Number of messages returned per query. Max 200",
     *          in="query",
     *          name="limit",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          description="Offset to query older messages",
     *          in="query",
     *          name="page",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function historyByChannel( Request $r )
    {
        if( ! $user  = \JWTAuth::toUser() )
            return response()->json( $this->response , $this->response['status'] );

        $channel_id = $this->getChatChannelByIds($user->id, (int)$r->requested_user);

        if( ! $channel_id ){
            return $this->apiErrorResponse(false, 'Channel Not Found', 400);
        }

        $limit = 20;
        if( $r->limit ){
            $limit = $r->limit > 200 ? 200 :  $r->limit;
        }

        $order_by = $r->order_by ? $r->order_by : 'history_id';
        $order_direction = $r->order_direction ? $r->order_direction : 'ASC';

        $r->merge( [ 'order_by' => $order_by, 'order_direction'=> $order_direction,
            'limit' => $limit , 'with_sender'=>1, 'channel_id' => $channel_id, 'current_id' => $user->id ] );

        $messages = (new ChatHistory)->getCollection($r);
        $messages_with_id = $this->setCurrentUser($messages, $user->id);

//        ChatChannelMembers::seen( $channel->channel_id , $user->id );

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message'=> 'success',
                'data' => [
                        'messages' => $messages_with_id,
                    ],
                200
            ]
        );
    }

    private function setCurrentUser($messages, $userId)
    {
        foreach($messages as $key => $message) {
            $messages[$key]['current_user'] = $userId;
        }

        return $messages;
    }

    public function connectedUsers()
    {
        if( ! $user  = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $request_type = $user->role_id === 1 ? 'requested_user' : 'requesting_user';
        $user_id_type = $user->role_id === 1 ? 'requesting_user' : 'requested_user';
        $connections = ChatRequest::where($request_type, $user->id)->get();
        $users = Users::findUsersFromChatRequestArray($connections, $user_id_type);

        return $this->apiSuccessResponse($users, true);
    }

    public function acceptConnection(Request $r)
    {
        if( !$user = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $chat_request = ChatRequest::findChatRequest($r->id, $user->id);
        $update_chat = ChatRequest::find($chat_request->id);
        $update_chat->status = 'Accepted';
        $save_status = $update_chat->save();

        return $this->apiSuccessResponse($save_status);
    }

    public function rejectConnection(Request $r)
    {
        if( !$user = \JWTAuth::toUser() )
            return $this->apiErrorResponse( false, 'Invalid JWT Token', 400 , 'invalidToken' );

        $chat_request = ChatRequest::findChatRequest($r->id, $user->id);
        $update_chat = ChatRequest::find($chat_request->id);
        $update_chat->status = 'Rejected';
        $save_status = $update_chat->save();

        return $this->apiSuccessResponse($save_status);
    }

    private function getChatChannelByIds($user, $recipientId)
    {
        $channel_id = ChatChannels::returnChatChannelByIds($user, $recipientId);

        if(!$channel_id)
            return $this->apiErrorResponse(false, $channel_id, 400);

        return $channel_id;
    }

    private function uploadFile(Request $r, $channelId)
    {

        try {

            $validator = \Validator::make($r->all(), ['file_message' => 'required|mimes:jpeg,jpg,png,pdf']);

            if ($validator->fails()) {
                return false;
            }

            $r->file('file_message')->storeAs('chats/' . $channelId, $r->file('file_message')->getClientOriginalName(), 'public');

            return '/storage/chats/' . $channelId . '/' .$r->file('file_message')->getClientOriginalName();


        } catch (\Exception $e) {

            echo $e->getMessage();
            return false;
        }
    }

    public function updateSeenBy(Request $r)
    {
        $user = \JWTAuth::toUser();
        $r->merge(['channel_id' => $this->getChatChannelByIds($user->id, $r->chat_partner_id)]);
        $chat_history = (new ChatHistory)->updateSeenBy($r);
        return $this->apiSuccessResponse($r->all(), 'success', 'Update Successful');
    }

}
