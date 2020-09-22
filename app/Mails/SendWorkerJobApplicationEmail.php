<?php

namespace App\Mails;

use App\Models\Companies\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendWorkerJobApplicationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $job;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( Job $job )
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->job->title = $this->job->title ? $this->job->title : $this->job->job_role->job_role_name;
        $this->job->url = URL::to('/job/applicants?cid='.$this->job->company_id.'&jid='. $this->job->id);

        $subject = "Job Application for {$this->job->title}";

        return $this->view('emails.send_worker_job_application')
                ->subject( $subject )
                ->from(env('APP_EMAIL'), env('SITE_NAME'))
                ->with('job', $this->job );
    }
}
