<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Companies\Company;

class ChatMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sender, $message)
    {
        $this->sender = $sender;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $last_message = \DB::table('notifications')
            ->select('created_at')
            ->where('notifiable_id', $notifiable->id)
            ->where('type', 'like', '%ChatMessage%')
            ->where('data', 'like', '%sentBy":'.$this->sender->id.'%')
            ->orderBy('created_at', 'DESC')
            ->first();

        if($last_message) {
            $last_message_timestamp = strtotime($last_message->created_at);
            $test_value = strtotime(now()) - $last_message_timestamp;
            $minute = 15 * 60;

            if( $test_value > $minute) {
                return ['mail', 'database'];
            }
        }

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $name = $this->sender->first_name . ' ' . substr($this->sender->last_name, 0, 1) . '.';

        if($this->sender->role_id == 2) {
            $name = \DB::table('companies')->select('name')->where('created_by', $this->sender->id)->first()->name;
        }
        return (new MailMessage)
            ->from('noreply@buildlabour.com.au', 'BuildLabour')
            ->subject('A user has messaged you')
            ->line("$name has sent you a new message.")
            ->action('Go To Your Messages', url('/messages'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $name = $this->sender->first_name . ' ' . substr($this->sender->last_name, 0, 1) . '.';

        if($this->sender->role_id == 2) {
            $name = \DB::table('companies')->select('name')->where('created_by', $this->sender->id)->first()->name;
        }

        return [
            'message' => "$name has sent you a new message",
            'link'  => "/messages",
            'sentBy' => $this->sender->id
        ];
    }
}
