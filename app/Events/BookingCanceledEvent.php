<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCanceledEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct($booking, $room)
    {
        $this->booking = $booking;
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('hotel-booking'),
        ];
    }
}
