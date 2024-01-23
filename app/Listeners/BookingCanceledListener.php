<?php

namespace App\Listeners;

use App\Notifications\BookingCanceledNotification;
use Illuminate\Support\Facades\Notification;

class BookingCanceledListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $booking = $event->booking;
        $room = $event->room;
        Notification::route('mail', auth()->user()->email)->notify(new BookingCanceledNotification($booking, $room));
    }
}
