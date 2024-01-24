<?php

namespace App\Listeners;

use App\Enum\RoomStatusEnum;
use App\Events\CheckOutDatePassedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateRoomStatusListener implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(CheckOutDatePassedEvent $event): void
    {
        $room = $event->booking->room();
        $room->update(['status' => RoomStatusEnum::AVAILABLE]);
    }
}
