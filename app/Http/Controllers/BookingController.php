<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
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
    public function update(StoreBookingRequest $request, Booking $booking)
    {
        $this->foundBooking($booking)->update($this->bookingDataValidated($request));
        return response()->json(['message' => 'Booking Updated successfully!'], 202);
    }

    private function foundBooking(Booking $booking) {
        return Booking::find($booking->id);
    }

    private function bookingDataValidated(StoreBookingRequest $request) {
        // also handle validation error exceptions
        return $request->validated();
    }
}
