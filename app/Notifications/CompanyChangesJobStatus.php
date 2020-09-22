<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Companies\Company;

class CompanyChangesJobStatus extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job, $status)
    {
        $this->job = $job;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
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
        $cid = $this->job->company_id;
        $jid = $this->job->id;
        return (new MailMessage)
            ->from('noreply@buildlabour.com.au', 'BuildLabour')
            ->subject('A job you have applied for has been ' . $this->status)
             ->line('A job you have applied to has been ' . $this->status)
            ->action('View The Job', url("/job/applicants?cid=$cid&jid=$jid"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $cid = $this->job->company_id;
        $jid = $this->job->id;

        return [
            'message' => "A job you've applied to is $this->status",
            'link'  => "/job/applicants?cid=$cid&jid=$jid"
        ];
    }
}
