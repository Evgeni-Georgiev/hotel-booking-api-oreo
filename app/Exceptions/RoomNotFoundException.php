<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        $param = $request->route()->parameterNames()[0];
        return response()->json([
            "message" => ucfirst($param) . ' not found!',
        ], 404);
    }
}
