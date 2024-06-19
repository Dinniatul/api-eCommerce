<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{


    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|unique:users',
    //         'email' => 'required|unique:users',
    //         'password' => 'required|min:8',
    //         'first_name' => 'required',
    //         'last_name' => 'required',
    //         'phone' => 'required',
    //         'address' => 'required',
    //         'role' => 'nullable'
    //     ], [
    //         'username.required' => 'Username tidak boleh kosong',
    //         'username.unique' => 'Username sudah ada, silahkan buat username yang lain',
    //         'email.unique' => 'Email sudah digunakan, silahkan gunakan email yang lain',
    //         'password.required' => ' Password tidak boleh kosong',
    //         'password.min' => 'Password minimal 8 karakter',

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Register gagal',
    //             'data' => $validator->errors()
    //         ], 400);
    //     }

    //     $user = User::create([
    //         'username' => $request->input('username'),
    //         'email' => $request->input('email'),
    //         'password' => Hash::make($request->password),
    //         'first_name' => $request->input('first_name'),
    //         'last_name' => $request->input('last_name'),
    //         'phone' => $request->input('phone'),
    //         'address' => $request->input('address'),
    //         'role' => 'customer',
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Registrasi berhasil, silahkan login',
    //         'data' => $user,
    //     ]);
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
            'password.required' => 'Password tidak boleh kosong',
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

        $otp = Str::random(6); // Menggunakan Str untuk generate OTP
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $this->sendOTPEmail($user);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil, cek email Anda untuk kode OTP',
            'data' => $user,
        ]);
    }

    protected function sendOTPEmail($user)
    {
        $data = [
            'name' => $user->first_name,
            'otp' => $user->otp
        ];

        Mail::send('emails.otp', $data, function ($message) use ($user) {
            $message->to($user->email, $user->first_name)
                ->subject('Kode OTP Anda');
            $message->from('no-reply@enchantebeuty.com', 'Enchante Beuty');
        });
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Verifikasi OTP gagal',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || $user->otp !== $request->input('otp') || Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP tidak valid atau telah kadaluarsa'
            ], 400);
        }

        $user->otp = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email berhasil diverifikasi'
        ]);
    }


    // public function login(Request $request)
    // {
    //     $rules = [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Login gagal",
    //             'data' => $validator->errors()
    //         ], 401);
    //     }

    //     if (!Auth::attempt($request->only(['email', 'password']))) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Email dan password tidak sesuai'
    //         ], 401);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Berhasil login',
    //         'email' => $user->email,
    //         'token' => $user->createToken('api-eCommerce')->plainTextToken,
    //         'role' => $user->role, // Ambil peran pengguna dari objek $user
    //         'username' => $user->username,
    //         'first_name' => $user->first_name,
    //         'last_name' => $user->last_name,
    //         'address' => $user->address,
    //         'phone' => $user->phone,
    //         'id' => $user->id,
    //     ]);
    // }
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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email tidak ditemukan'
            ], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'status' => false,
                'message' => 'Email belum diverifikasi. Silahkan cek email Anda untuk kode OTP.'
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email dan password tidak sesuai'
            ], 401);
        }

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


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan reset password gagal',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email tidak ditemukan'
            ], 404);
        }

        $token = Str::random(60);
        $user->reset_password_token = $token;
        $user->reset_password_expires_at = Carbon::now()->addMinutes(30);
        $user->save();

        $this->sendResetPasswordEmail($user, $token);

        return response()->json([
            'status' => true,
            'message' => 'Permintaan reset password berhasil, silahkan cek email Anda',
        ]);
    }

    protected function sendResetPasswordEmail($user, $token)
    {
        $data = [
            'name' => $user->first_name,
            'token' => $token,
        ];

        Mail::send('emails.reset_password', $data, function ($message) use ($user) {
            $message->to($user->email, $user->first_name)
                ->subject('Reset Password Anda');
            $message->from('no-reply@enchantebeuty.com', 'Enchante Beuty');
        });
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Reset password gagal',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || $user->reset_password_token !== $request->input('token') || Carbon::now()->greaterThan($user->reset_password_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Token reset password tidak valid atau telah kadaluarsa'
            ], 400);
        }

        $user->password = Hash::make($request->input('password'));
        $user->reset_password_token = null;
        $user->reset_password_expires_at = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil direset',
        ]);
    }
}
