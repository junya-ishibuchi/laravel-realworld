<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request, UserService $userService): JsonResponse
    {
        // TODO: move to Request class
        $request->validate([
            'user.username' => 'required|string|max:255',
            'user.email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'user.password' => ['required'],
        ]);

        // TODO: create service and repository layer
        $user = $userService->create($request->input('user.username'), $request->input('user.email'), $request->input('user.password'));

        $token = auth()->attempt(['email' => $request->input('user.email'), 'password' => $request->input('user.password')]);

        return response()->json([
            "user" => [
                "email" => $user->email,
                "token" => $token,
                "username" => $user->name,
                "bio" => $user->bio,
                "image" => $user->image,
            ],
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        if (!$token = auth()->attempt(['email' => $request->input('user.email'), 'password' => $request->input('user.password')])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            "user" => [
                "email" => auth()->user()->email,
                "token" => $token,
                "username" => auth()->user()->name,
                "bio" => auth()->user()->bio,
                "image" => auth()->user()->image,
            ],
        ]);
    }
}
