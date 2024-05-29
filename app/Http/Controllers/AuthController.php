<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'string|max:255|nullable',
            'last_name' => 'string|max:255|nullable',
            'phone' => 'string|max:20|nullable',
            'address' => 'string|nullable',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'customer',  // default to customer
            'email_verified_at' => Carbon::now(),  // Set email verified at registration
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Login gagal",
                'data' => $validator->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email dan password tidak sesuai'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        // Tambahkan peran pengguna ke dalam respons JSON
        return response()->json([
            'status' => true,
            'message' => 'Berhasil login',
            'email' => $user->email,
            // 'token' => $user->createToken('api-kejaksaan')->plainTextToken,
            'role' => $user->role // Ambil peran pengguna dari objek $user
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
