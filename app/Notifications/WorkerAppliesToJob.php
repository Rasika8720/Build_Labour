<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Companies\Company;

class WorkerAppliesToJob extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job, $sender)
    {
        $this->job = $job;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            ->subject('A user has messaged you')
            ->line('A new applicant has arrived.')
            ->action('View Your Job', url("/job/applicants?cid=$cid&jid=$jid"));
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
        $cid = $this->job->company_id;
        $jid = $this->job->id;

        return [
            'message' => "$name has applied to your job",
            'link'  => "/job/applicants?cid=$cid&jid=$jid"
        ];
    }
}
