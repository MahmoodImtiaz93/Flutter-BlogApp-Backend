<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register User
    public function register(Request $request)
    {
        //Validate Fields
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        //Create Users
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        //Return user and token in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);
    }

    //Login User
    public function login(Request $request)
    {

        //Validate Fields
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        //attempt login
        if (!Auth::attempt($fields)) {
            return response([
                'message' => 'Invalid Crenditials'
            ], 403);
        }

        //Return user and token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    // user_details
    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }

    //logout
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout Success'
        ], 200);
    }
}
