<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Creates a user based on certain validation rules and creates a token assigned to it.
     *
     * @param RegisterUserRequest $userRequest - used to validate a set of rules for registration.
     * @return Response - returns JSON formatted response.
     */
    public function register(RegisterUserRequest $userRequest): Response
    {
        $validatedUserData = $userRequest->validated() ?? response($userRequest->validated()->error(), 422);
        $user = User::create(array_merge($validatedUserData,
            ['password' => bcrypt($validatedUserData['password'])]));
        $token = $user->createToken('appToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    /**
     * Authenticates Existing User and creates a token to authorize routes access.
     *
     * @param LoginUserRequest $userRequest - used to validate a set of rules for login.
     * @return Response - returns JSON formatted response.
     */
    public function login(LoginUserRequest $userRequest): Response
    {
        $validatedUserData = $userRequest->validated() ?? response($userRequest->validated()->error(), 422);
        $user = User::where('email', $validatedUserData['email'])->first();
        if(!$user || !Hash::check($validatedUserData['password'], $user->password)) {
            return response(["message" => "Wrong email or password"], 401);
        }
        $token = $user->createToken('appToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    /**
     * Destroys logged session for current user and token.
     *
     * @return Response - returns JSON formatted response.
     */
    public function logout(): Response
    {
        auth()->user()->currentAccessToken()->delete();
        return response(['message' => 'Logged out!'], 200);
    }
}
