<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    // register
    public function register(Request $request)
    {
        $user_exist_check = User::where('email', $request->email)->first();
        if ($user_exist_check)
        {
            return response()->json([
                "msg" => "User already exists",
            ], 400);
        } 
        else 
        {
            $validate_input = $request->validate([
                'name' => "required|string|max:255",
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6'
            ]);
            $user = User::create([
                'name' => $validate_input['name'],
                'email' => $validate_input['email'],
                'password' => Hash::make($validate_input['password']),
            ]);
            return response()->json([
                'msg' => 'User Registered',
                'user' => $user,
            ], 201);
        }
    }


    // login
    public function login(Request $request)
    {
        $validate_input = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        $user = User::where('email',$validate_input['email'])->first();
        if(!$user || !Hash::check($validate_input['password'],$user->password))
        {
            return response()->json([
                'msg' => 'Invalid Credentials'
            ],400);
        }
        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            'msg' => 'User Logged In',
            'user' => $user,
            'token' => $token
        ],200);
    }

    public function logout(Request $request)
    {
        // delete current user token
        $request->user()->tokens()->delete();
        return response()->json([
            'msg' => 'User Logged Out'
        ],200);
    }
}