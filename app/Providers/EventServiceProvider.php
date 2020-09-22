<?php

namespace App\Providers;

use App\Events\NewRegisteredUser;
use App\Events\Users\DeleteEducation;
use App\Models\Users\Users;
use App\Observers\UserObserver;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            DeleteEducation::class, // trigger if the user deletes an education

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Users::observe(new UserObserver());
        //
    }
}
