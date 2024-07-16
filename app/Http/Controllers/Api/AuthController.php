<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login_user(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'error' => 'Credentials Do Not Match'
            ], 401);
        }
        $user = User::where('email', $request->email)->first();
        return response()->json([
            'status' => 'User Login Success',
            'token' => $user->createToken('Api Token of -' . $request->name)->plainTextToken,
            'user' => $user
        ], 200);
    }

    public function register_user(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        try{
            $user = User::create([
                'name' => $request->name,
                'user_name' => $request->user_name,
                'image' => 'b',
                'email' => $request->email,
                'password' =>Hash::make($request->password),
            ]);
            return response()->json([
                'status' => 'User Registered',
                'token' => $user->createToken('Api Token of -' . $request->name)->plainTextToken,
                'user' => $user
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'status' => 'User Register Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout_user(){
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'User Logout Successfully and Your Token has been deleted'
        ], 200);
    }
}
