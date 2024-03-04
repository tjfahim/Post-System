<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
    
        if (!Auth::attempt($credentials)) {

            $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 401);
        }

        return response()->json(['message' => 'Incorrect password'], 401);
        }
       
        $user = Auth::user();
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->status == 0) {
                return response()->json(['message' => 'Wait For Admin Approval'], 200);
            } elseif ($user->role == 1) {
                $role = 'user';
            } elseif ($user->role == 2) {
                $role = 'admin';
            } elseif ($user->role == 3) {
                $role = 'superadmin';
            }
    
            $token = $user->createToken('Token')->accessToken;
    
            return response()->json([
                'message' => ucwords($role) . ' Login Successful',
                'token' => $token,
                'role' => $role,
            ], 200);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    
    
}
