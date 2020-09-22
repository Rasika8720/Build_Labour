<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Users\FirebaseUsers;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Client;
use Illuminate\Http\Request;
use JWTAuth;

class ApiPushNotificationsController extends ApiBaseController
{
    /**
     *  Create a new controller instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * @OA\Post(
     *      path="/notification/test",
     *      tags={"Push Notifications"},
     *      summary="Test send a message. Auth Bearer authentication",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="device_token",
     *                      description="<b>Required</b><br />Device token",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function test( Request $r )
    {
        if( ! $r->device_token ){
            $this->apiErrorResponse( false, 'Device Token is required', 400, 'deviceTokenRequired' );
        }

        $apiKey = env( 'FCM_SERVER_KEY' );

        $client = new Client();
        $client->setApiKey( $apiKey );
        $client->injectHttpClient(new \GuzzleHttp\Client());

        $note = new Notification('Test Title', 'Message Successfully Sent');
        $note->setIcon('notification_icon_resource_name')
            ->setColor('#2ea8ff')
            ->setBadge(1);

        $message = new Message();
        $message->addRecipient(new Device( $r->device_token ));
        $message->setNotification( $note )
            ->setData( array() );

        try{
            $response = $client->send( $message );
        }catch( \Exception $e ){
            $this->apiErrorResponse( false, $e->getMessage(), 400, 'messageSendingException' );
        }

        return $this->apiSuccessResponse( compact( ['response'] ), true, ' Message successfully sent ' );
    }

}
