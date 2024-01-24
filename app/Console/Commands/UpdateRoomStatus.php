<?php

namespace App\Console\Commands;

use App\Enum\RoomStatusEnum;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Console\Command;

class UpdateRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-room-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update room status based on check-out dates';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $rooms = Room::all();
        foreach ($rooms as $room) {
            // Check if there are any bookings for the current room
            $hasBookings = Booking::where('room_id', $room->id)->exists();

            // Check if there is a booking for the current room that has not passed
            $hasUnexpiredBooking = Booking::where('room_id', $room->id)
                ->whereDate('check_out_date', '>=', now())
                ->exists();

            if ($hasBookings && $hasUnexpiredBooking) {
                $roomStatus = RoomStatusEnum::OCCUPIED;
            } elseif ($hasBookings) {
                $roomStatus = RoomStatusEnum::AVAILABLE;
            } else {
                $roomStatus = RoomStatusEnum::AVAILABLE;
            }

            $room->update(['status' => $roomStatus]);
        }
    }
}
