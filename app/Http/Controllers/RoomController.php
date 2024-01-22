<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['rooms' => Room::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = Room::create($this->roomDataValidated($request));
        return response()->json($room, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json(['room' => $this->foundRoom($room)]);
    }

    // ...

    public function update(StoreRoomRequest $request, Room $room): JsonResponse
    {
        $this->foundRoom($room)->update($this->roomDataValidated($request));
        return response()->json(['message' => 'Room Updated successfully!'], 202);
    }

    private function foundRoom(Room $room) {
        return Room::find($room->id);
    }

    private function roomDataValidated(StoreRoomRequest $request) {
        // also handle validation error exceptions
        return $request->validated();
    }
}
