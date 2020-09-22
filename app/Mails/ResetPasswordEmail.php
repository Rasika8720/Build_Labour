<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user , $token )
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset')
                ->subject('Reset Password')
                ->from( env('APP_EMAIL'), env('APP_NAME') )
                ->with('token', $this->token )
                ->with('user', $this->user );
    }
}
