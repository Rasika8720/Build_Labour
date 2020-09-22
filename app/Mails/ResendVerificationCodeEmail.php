<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResendVerificationCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $user )
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Email Verification';
        $template = $this->user->isWeb ? 'emails.verify_web' : 'emails.resend_verification_code';
        return $this->view($template)
                ->subject( $subject )
                ->from(env('APP_EMAIL'), env('SITE_NAME'))
                ->with('user', $this->user );
    }
}
