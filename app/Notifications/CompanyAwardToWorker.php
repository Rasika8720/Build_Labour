<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Companies\Company;

class CompanyAwardToWorker extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job)
    {
        $this->job = $job;
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
            ->subject('You\'ve been given a job!')
            ->line('You\'ve been given a job!')
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
            'message' => "You've been given a job",
            'link'  => "/job/applicants?cid=$cid&jid=$jid"
        ];
    }
}
