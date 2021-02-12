<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Requests\API\Auth\UpdatePasswordRequest;
use App\Http\Requests\API\Auth\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    public function update(UpdateRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json(auth()->user(), Response::HTTP_OK);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        auth()->user()->update(['password' => $request->password]);

        return response()->json(auth()->user(), Response::HTTP_OK);
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

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        auth()->user()->tokens()->delete();
        auth()->user()->delete();

        return response()->noContent();
    }
}
