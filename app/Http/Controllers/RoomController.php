<?php

namespace App\Http\Controllers;

use App\Exceptions\RoomNotFoundException;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'rooms',
            'rooms' => RoomResource::collection(Room::all())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRoomRequest $request The request containing the input data.
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = Room::create($request->validated());
        return response()->json([
            'message' => 'Room created successfully!',
            'data' => new RoomResource($room)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Room $room The model of the searched room.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws RoomNotFoundException If searched room is not found.
     */
    public function show(Room $room): JsonResponse
    {
        $roomFound = Room::find($room->id);

        if (!$roomFound) {
            throw new RoomNotFoundException();
        }

        return response()->json([
            'message' => 'Room found!',
            'data' => new RoomResource($roomFound)
        ]);
    }
}
