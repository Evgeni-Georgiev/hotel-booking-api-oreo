<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnavailableRoomForSpecifiedRangeException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            "message" => 'Room is not available for the specified dates range.'
        ], 422);
    }
}
