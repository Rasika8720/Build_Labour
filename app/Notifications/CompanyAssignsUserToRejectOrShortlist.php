<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Companies\Company;

class CompanyAssignsUserToRejectOrShortlist extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($job, $sender, $status)
    {
        $this->job = $job;
        $this->status = $status;
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
        $cid = $this->job->company_id;
        $jid = $this->job->id;

        $message = $this->status == 'not_suitable' ? 'You have been rejected for a job' : 'You have been shortlisted for a job';

        return [
            'message' => $message,
            'link'  => "/job/view?cid=$cid&jid=$jid"
        ];
    }
}
