<?php

namespace App\Listeners;

use App\Events\NewRegisteredUser;
use App\Mail\NewUserMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewRegisteredUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewRegisteredUser  $event
     * @return void
     */
    public function handle(NewRegisteredUser $event)
    {
       // Mail::to( $event->getUser()->email )->send( new NewUserMail( $event->getUser() ));
    }
}
