<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ConnectionApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $recipient_id;
    public $sender_id;
    public $message;
    public $connection_id;
    public $sender_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $recipient_id , $sender_id , $message, $connection_id , $sender_name = '' )
    {
        $this->recipient_id = $recipient_id;
        $this->sender_id = $sender_id;
        $this->message = $message;
        $this->connection_id = $connection_id;
        $this->sender_name = $sender_name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
