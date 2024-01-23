<?php

namespace App\Http\Controllers;

use App\Events\BookingCanceledEvent;
use App\Events\BookingMadeEvent;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'bookings', 'data' => Booking::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBookingRequest $request The request containing the input data for the new booking.
     *
     * @throws Exception If there are validation errors or no available rooms.
     *
     * @return JsonResponse A JSON response indicating the success of the operation.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = Booking::create($this->bookingDataValidated($request));
        return response()->json(['message' => 'Booking created successfully!', 'data' => $booking], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): JsonResponse
    {
        return response()->json(['booking' => $this->foundBooking($booking)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookingRequest $request, Booking $booking): JsonResponse
    {
        if (!$this->foundBooking($booking)) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }
        $this->foundBooking($booking)->update($this->bookingDataValidated($request));
        return response()->json(['message' => 'Booking Updated successfully!'], 202);
    }

    /**
     * Cancel booked room.
     *
     * @param Booking $booking
     * @return JsonResponse
     */
    public function destroy(Booking $booking): JsonResponse
    {
        $booking = Booking::find($booking->id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }
        $booking->delete();
        return response()->json(['message' => 'Booking canceled successfully.']);
    }

    private function foundBooking(Booking $booking) {
        return Booking::find($booking->id);
    }

    /**
     * Validates input data for creating a booking.
     *
     * @param StoreBookingRequest $request The request containing the input data.
     *
     * @throws Exception If there are validation errors or no available rooms.
     *
     * @return array The validated booking data, including the room object and calculated total price.
     */
    private function bookingDataValidated(StoreBookingRequest $request): array
    {
        $validatedData = $request->validated();

        // If room_id is not set explicitly, get a random available room
        if (!isset($validatedData['room_id'])) {
            $room = $this->getRandomAvailableRoom();
            $validatedData['room_id'] = $room->id;
        } else {
            // If room_id is set explicitly, fetch the Room object
            $room = Room::find($validatedData['room_id']);

            if (!$room || !$this->isRoomAvailableForDateRange($room, $validatedData['check_in_date'], $validatedData['check_out_date'])) {
                throw new Exception('Room is not available for the specified dates range.');
            }
        }
        $validatedData['room'] = $room;
        $durationInDays = Carbon::parse($validatedData['check_in_date'])->diffInDays($validatedData['check_out_date']);
        $validatedData['total_price'] = $durationInDays * $validatedData['room']->price_per_night;

        return $validatedData;
    }

    /**
     * Get a random available room.
     *
     * @throws Exception If there are no available rooms.
     *
     * @return Room The random available room.
     */
    private function getRandomAvailableRoom(): Room
    {
        $availableRooms = Room::where('status', 'available');
        if ($availableRooms->count() == 0) {
            throw new Exception('No available rooms.');
        }
        return $availableRooms->get()->random();
    }

    /**
     * Check if the room is available for the specified date range.
     *
     * @param Room $room The room to check for availability.
     * @param string $checkInDate The check-in date.
     * @param string $checkOutDate The check-out date.
     *
     * @return bool True if the room is available; false otherwise.
     */
    private function isRoomAvailableForDateRange(Room $room, string $checkInDate, string $checkOutDate): bool
    {
        return $room->booking()
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('check_out_date', '>', $checkInDate)
                    ->where('check_in_date', '<', $checkOutDate);
            })
            ->doesntExist();
    }
}
