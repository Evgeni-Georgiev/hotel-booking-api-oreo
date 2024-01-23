<?php

namespace App\Listeners;

use App\Notifications\BookingMadeNotification;
use Illuminate\Support\Facades\Notification;

class BookingMadeListener
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
        Notification::route('mail', auth()->user()->email)->notify(new BookingMadeNotification($booking, $room));
    }
}
