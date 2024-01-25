<?php

namespace App\Http\Controllers;

use App\Exceptions\RoomNotFoundException;
use App\Http\Requests\StoreRoomRequest;
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
            'rooms' => Room::all()
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
            'data' => $room
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id The ID of the searched room.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws RoomNotFoundException If searched room is not found.
     */
    public function show(int $id): JsonResponse
    {
        $roomFound = Room::find($id);
        if (!$roomFound) {
            throw new RoomNotFoundException('Room not found!');
        }

        return response()->json([
            'message' => 'Room found!',
            'data' => $roomFound
        ]);
    }
}
