<?php

namespace App\Providers;

use App\Events\BookingCanceledEvent;
use App\Events\BookingMadeEvent;
use App\Events\CheckOutDatePassedEvent;
use App\Listeners\BookingCanceledListener;
use App\Listeners\BookingMadeListener;
use App\Listeners\UpdateRoomStatusListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BookingMadeEvent::class => [
            BookingMadeListener::class,
        ],
        BookingCanceledEvent::class => [
            BookingCanceledListener::class,
        ],
        CheckOutDatePassedEvent::class => [
            UpdateRoomStatusListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
