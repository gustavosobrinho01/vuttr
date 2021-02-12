<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only('login');
        $this->middleware('auth:sanctum')->except('login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        auth()->user()->tokens()->delete();

        $token = auth()->user()->createToken('my-app-token')->plainTextToken;

        return response()->json([
            'user' => auth()->user(),
            'token' => $token
        ], Response::HTTP_OK);
    }

    public function me()
    {
        return response()->json([
            'user' => auth()->user()
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->noContent();
    }
}
