<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Requests\API\Auth\UpdatePasswordRequest;
use App\Http\Requests\API\Auth\UpdateRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only('register');
        $this->middleware('auth:sanctum')->except('register');
    }

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

    public function destroy()
    {
        auth()->user()->tokens()->delete();
        auth()->user()->delete();

        return response()->noContent();
    }
}
