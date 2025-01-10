<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $fields = $request->validate([
            'name' => 'required | max:255',
            'email' => 'required | email | unique:users',
            'password' => 'required | min:8 | confirmed'
        ]);   

        $user = User::create($fields);

        $token = $user->createToken($request->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }  

    public function login(Request $request) 
    {
        $fields = $request->validate([
            'email' => 'required | email | exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !password_verify($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response([   
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request) 
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
