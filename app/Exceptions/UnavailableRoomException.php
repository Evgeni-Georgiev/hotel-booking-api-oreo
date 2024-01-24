<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnavailableRoomException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            "error" => true,
            "message" => $this->getMessage()
        ], 404);
    }
}