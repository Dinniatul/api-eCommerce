<?php

namespace App\Http\Controllers\AdminController;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register()
    {
        return view('auth.register');
    }

    public function register_action(Request $request)
    {
        $validateData = $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed', // Ensure the password is confirmed
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        // Hash the password before storing
        $validateData['password'] = Hash::make($validateData['password']);
        $validateData['role'] = 'admin'; // Default role assignment

        User::create($validateData);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function login(){

        return view('auth.login');
    }

    public function login_action(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);

        if(Auth::attempt($request->only('email', 'password'))){
            if(Auth::user()->role === 'admin'){
                return redirect('user.index');
            }else{
                Auth::logout();
                return redirect()->route('login')->withErrors('Anda tidak memiliki izin untuk akses halaman ini');
            }
        }else {
            return redirect()->route('login')->withErrors('Username dan password tidak valid');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
