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
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string|max:255|unique:users',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //         'first_name' => 'string|max:255|nullable',
    //         'last_name' => 'string|max:255|nullable',
    //         'phone' => 'string|max:20|nullable',
    //         'address' => 'string|nullable',
    //     ]);

    //     $user = User::create([
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'phone' => $request->phone,
    //         'address' => $request->address,
    //         'role' => 'customer',  // default to customer
    //         'email_verified_at' => Carbon::now(),  // Set email verified at registration
    //     ]);

    //     Auth::login($user);

    //     return response()->json([
    //         'message' => 'User registered successfully',
    //         'user' => $user,
    //     ]);
    // }

    // public function getUser(){
    //     if(Auth::user()->role == 'customer'){
    //         $user = User::where()
    //     }
    // }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'role' => 'nullable'
        ], [
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah ada, silahkan buat username yang lain',
            'email.unique' => 'Email sudah digunakan, silahkan gunakan email yang lain',
            'password.required' => ' Password tidak boleh kosong',
            'password.min' => 'Password minimal 8 karakter',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Register gagal',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->password),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'role' => 'customer',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil, silahkan login',
            'data' => $user,
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

        return response()->json([
            'status' => true,
            'message' => 'Berhasil login',
            'email' => $user->email,
            'token' => $user->createToken('api-eCommerce')->plainTextToken,
            'role' => $user->role, // Ambil peran pengguna dari objek $user
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'address' => $user->address,
            'phone' => $user->phone,
            'id' => $user->id,
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Anda berhasil logout']);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|nullable',
            'role' => 'sometimes|string|in:customer,admin' // Update roles as needed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'User gagal diperbaharui',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->only(['username', 'email', 'first_name', 'last_name', 'phone', 'address', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diperbaharui',
            'data' => $user,
        ]);
    }
}
