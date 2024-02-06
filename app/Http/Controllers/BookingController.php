<?php

namespace App\Http\Controllers;

use App\Enum\PaymentStatusEnum;
use App\Events\BookingCanceledEvent;
use App\Events\BookingMadeEvent;
use App\Exceptions\BookingNotFoundException;
use App\Exceptions\UnavailableRoomException;
use App\Exceptions\UnavailableRoomForSpecifiedRangeException;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'bookings',
            'data' => Booking::select(
                'room_id',
                'customer_id',
                'check_in_date',
                'check_out_date',
                'total_price'
            )->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBookingRequest $request The request containing the input data for the new booking.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws Exception If there are validation errors or no available rooms.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = Booking::create($this->bookingDataValidated($request));
        $this->createPayment($booking);
        $room = Room::find($booking->room_id);
        event(new BookingMadeEvent($booking, $room));
        return response()->json([
            'message' => 'Booking created successfully!',
            'data' => new BookingResource($booking)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Booking $booking The model of the searched booking.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws BookingNotFoundException If searched booking is not found.
     */
    public function show(Booking $booking): JsonResponse
    {
        return response()->json([
            'message' => 'Booking found!',
            'data' => new BookingResource($this->foundBooking($booking))
        ]);
    }

    /**
     * Cancel booked room.
     *
     * @param Booking $booking The model of the searched booking.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws BookingNotFoundException If searched booking is not found.
     */
    public function destroy(Booking $booking): JsonResponse
    {
        $booking = $this->foundBooking($booking);
        $room = Room::find($booking->room_id);

        event(new BookingCanceledEvent($booking, $room));
        $booking->delete();

        return response()->json([
            'message' => 'Booking canceled successfully.'
        ]);
    }

    /**
     * Search for a booking by id.
     *
     * @param Booking $booking The model of the searched booking.
     * @return Booking The found booking.
     * @throws BookingNotFoundException If the booking is not found.
     */
    private function foundBooking(Booking $booking): Booking
    {
        $foundBooking = Booking::find($booking->id);
        if (!$foundBooking) {
            throw new BookingNotFoundException();
        }
        return $foundBooking;
    }

    /**
     * Perform payment when making a booking.
     *
     * @param Booking $booking The booking instance to associate with payment.
     * @return void
     */
    private function createPayment(Booking $booking): void
    {
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price / 2,
            'payment_date' => now(),
            'status' => PaymentStatusEnum::DOWN_PAYMENT
        ]);
    }

    /**
     * Validates input data for creating a booking.
     *
     * @param StoreBookingRequest $request The request containing the input data.
     * @return array The validated booking data, including the room object and calculated total price.
     * @throws UnavailableRoomForSpecifiedRangeException|UnavailableRoomException If there are unavailable date ranges or unavailable rooms.
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
        }

        if (!$room || !$this->isRoomAvailableForDateRange(
                $room,
                $validatedData['check_in_date'],
                $validatedData['check_out_date'])
        ) {
            throw new UnavailableRoomForSpecifiedRangeException();
        }

        $validatedData['room'] = $room;
        $durationInDays = Carbon::parse($validatedData['check_in_date'])->diffInDays($validatedData['check_out_date']);
        $validatedData['total_price'] = $durationInDays * $validatedData['room']->price_per_night;

        return $validatedData;
    }

    /**
     * Get a random available room.
     *
     * @return Room The random available room.
     * @throws UnavailableRoomException If there are no available rooms.
     */
    private function getRandomAvailableRoom(): Room
    {
        $availableRooms = Room::where('status', 'available');
        if ($availableRooms->count() == 0) {
            throw new UnavailableRoomException();
        }
        return $availableRooms->get()->random();
    }

    /**
     * Check if the room is available for the specified date range.
     *
     * @param Room $room The room to check for availability.
     * @param string $checkInDate The check-in date.
     * @param string $checkOutDate The check-out date.
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
