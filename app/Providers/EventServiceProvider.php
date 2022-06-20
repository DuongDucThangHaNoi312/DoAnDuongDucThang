<?php

namespace App\Providers;

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
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        // 'App\Events\SendActiveCode' => [
        //     'App\Listeners\SendActiveCodeListener',
        // ],
        // 'App\Events\ActiveSuccessfully' => [
        //     'App\Listeners\ActiveSuccessfullyListener',
        // ],
        // 'App\Events\RegisterSuccessfully' => [
        //     'App\Listeners\RegisterSuccessfullyListener',
        // ],
        'App\Events\OrderSuccessfully' => [
            // 'App\Listeners\OrderSuccessfullyListener',
        ],
        'App\Events\OrderSendMail' => [
            'App\Listeners\OrderSendMailListener',
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

    }
}
