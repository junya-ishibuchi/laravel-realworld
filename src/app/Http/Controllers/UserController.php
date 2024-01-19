<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        // TODO: move to Request class
        $request->validate([
            'user.username' => 'required|string|max:255',
            'user.email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'user.password' => ['required'],
        ]);

        // TODO: create service and repository layer
        $user = User::create([
            'name' => $request->input('user.username'),
            'email' => $request->input('user.email'),
            'password' => Hash::make($request->input('user.password')),
        ]);

        $token = auth()->attempt(['email' => $request->input('user.email'), 'password' => $request->input('user.password')]);

        return response()->json([
            "user" => [
                "email" => $user->email,
                "token" => $token,
                "username" => $user->name,
                "bio" => $user->bio,
                "image" => $user->image
            ]
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
                "image" => auth()->user()->image
            ]
        ]);
    }
}
